<?php

namespace App\Http\Controllers\Admin;

use App\Events\ChatConversationUpdated;
use App\Http\Controllers\Controller;
use App\Models\ChatAutoReply;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\ChatPresence;
use App\Models\UserNotification;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $query = ['section' => 'support'];

        if ($request->filled('conversation')) {
            $query['conversation'] = $request->integer('conversation');
        }

        return redirect()->route('admin.dashboard', $query);
    }

    public function data(Request $request): JsonResponse
    {
        $selectedConversation = $request->integer('conversation')
            ? ChatConversation::query()->find($request->integer('conversation'))
            : ChatConversation::query()->latest('last_message_at')->first();

        $this->touchPresence($selectedConversation);

        $conversations = ChatConversation::query()
            ->with(['user', 'messages' => fn ($query) => $query->latest('created_at')->limit(1)])
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->get();

        if ($selectedConversation) {
            $selectedConversation->load(['user', 'messages.user']);
            $this->markConversationRead($selectedConversation);
        }

        return response()->json([
            'conversations' => $conversations->map(fn (ChatConversation $conversation) => $this->serializeConversation($conversation))->all(),
            'selected_conversation' => $selectedConversation ? $this->serializeConversation($selectedConversation->fresh(), true) : null,
            'messages' => $selectedConversation
                ? $selectedConversation->messages()->with('user')->oldest()->get()->map(fn (ChatMessage $message) => $this->serializeMessage($message))->all()
                : [],
            'auto_replies' => ChatAutoReply::query()->orderBy('trigger_type')->orderBy('sort_order')->get()->values()->all(),
            'admin' => [
                'name' => Auth::user()?->name ?? 'Admin',
            ],
        ]);
    }

    public function storeMessage(Request $request, ChatConversation $conversation): JsonResponse
    {
        $data = $request->validate([
            'body' => 'nullable|string|max:4000',
            'attachment' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx',
        ]);

        if (! $request->hasFile('attachment') && blank($data['body'] ?? null)) {
            return response()->json([
                'message' => 'Write a message or attach a file before sending.',
            ], 422);
        }

        $payload = [
            'conversation_id' => $conversation->id,
            'user_id' => Auth::id(),
            'sender_type' => 'admin',
            'body' => filled($data['body'] ?? null) ? trim($data['body']) : null,
        ];

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('chat-attachments', 'public');
            $payload += [
                'attachment_path' => $path,
                'attachment_name' => $file->getClientOriginalName(),
                'attachment_mime' => $file->getClientMimeType(),
                'attachment_size' => $file->getSize(),
            ];
        }

        $message = ChatMessage::create($payload);

        $conversation->update([
            'last_message_at' => now(),
            'admin_typing_at' => null,
            'status' => 'open',
        ]);

        $this->touchPresence($conversation);
        $this->markConversationRead($conversation);
        $this->createUserNotification($conversation);
        $this->broadcastConversationUpdate($conversation->fresh(), 'message');

        return response()->json([
            'conversation' => $this->serializeConversation($conversation->fresh(), true),
            'message' => $this->serializeMessage($message->fresh('user')),
        ], 201);
    }

    public function typing(Request $request, ChatConversation $conversation): JsonResponse
    {
        $conversation->update([
            'admin_typing_at' => $request->boolean('is_typing') ? now() : null,
        ]);

        $this->touchPresence($conversation);
        $this->broadcastConversationUpdate($conversation->fresh(), 'typing');

        return response()->json(['ok' => true]);
    }

    public function presence(?ChatConversation $conversation = null): JsonResponse
    {
        $this->touchPresence($conversation);

        if ($conversation) {
            $this->broadcastConversationUpdate($conversation->fresh(), 'presence');
        }

        return response()->json(['ok' => true]);
    }

    public function upsertAutoReply(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id' => 'nullable|integer|exists:chat_auto_replies,id',
            'trigger_type' => 'required|in:offline,keyword',
            'keyword' => 'nullable|string|max:120',
            'reply_text' => 'required|string|max:4000',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0|max:999',
        ]);

        $attributes = [
            'trigger_type' => $data['trigger_type'],
            'keyword' => $data['trigger_type'] === 'keyword' ? ($data['keyword'] ?? null) : null,
            'reply_text' => trim($data['reply_text']),
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $data['sort_order'] ?? 0,
        ];

        $reply = isset($data['id'])
            ? tap(ChatAutoReply::query()->findOrFail($data['id']))->update($attributes)
            : ChatAutoReply::query()->create($attributes);

        return response()->json([
            'reply' => $reply,
        ]);
    }

    public function destroyAutoReply(ChatAutoReply $autoReply): JsonResponse
    {
        $autoReply->delete();

        return response()->json(['ok' => true]);
    }

    private function touchPresence(?ChatConversation $conversation = null): void
    {
        ChatPresence::query()->updateOrCreate(
            [
                'conversation_id' => $conversation?->id,
                'user_id' => Auth::id(),
                'session_id' => null,
                'role' => 'admin',
            ],
            [
                'last_seen_at' => now(),
            ]
        );
    }

    private function createUserNotification(ChatConversation $conversation): void
    {
        if (! $conversation->user_id) {
            return;
        }

        UserNotification::create([
            'user_id' => $conversation->user_id,
            'type' => 'chat_reply',
            'title' => 'New message from seller',
            'body' => 'The seller replied to your chat. Tap to open the conversation.',
            'url' => route('assistant'),
            'data' => [
                'conversation_id' => $conversation->id,
            ],
        ]);
    }

    private function broadcastConversationUpdate(ChatConversation $conversation, string $context): void
    {
        try {
            event(new ChatConversationUpdated($conversation, $context));
        } catch (BroadcastException $exception) {
            Log::warning('Admin chat broadcast skipped because the realtime server is unavailable.', [
                'conversation_id' => $conversation->id,
                'context' => $context,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function markConversationRead(ChatConversation $conversation): void
    {
        $now = now();

        $conversation->messages()
            ->where('sender_type', 'customer')
            ->whereNull('delivered_at')
            ->update(['delivered_at' => $now]);

        $conversation->messages()
            ->where('sender_type', 'customer')
            ->whereNull('seen_at')
            ->update(['seen_at' => $now]);

        $conversation->update(['admin_last_read_at' => $now]);
    }

    private function serializeConversation(ChatConversation $conversation, bool $includeFull = false): array
    {
        $latestMessage = $conversation->messages->sortByDesc('created_at')->first()
            ?? $conversation->messages()->latest('created_at')->first();

        return [
            'id' => $conversation->id,
            'customer_name' => $conversation->customer_name,
            'customer_email' => $conversation->customer_email,
            'customer_initials' => $conversation->customer_initials,
            'customer_is_online' => $conversation->customer_is_online,
            'admin_is_online' => $conversation->admin_is_online,
            'customer_is_typing' => $conversation->customer_is_typing,
            'admin_is_typing' => $conversation->admin_is_typing,
            'broadcast_channel' => $conversation->broadcast_channel,
            'status' => $conversation->status,
            'unread_count' => $conversation->unread_for_admin,
            'last_message_preview' => Str::limit((string) ($latestMessage?->body ?: $latestMessage?->attachment_name ?: 'No messages yet'), 60),
            'last_message_at' => optional($latestMessage?->created_at)->toIso8601String(),
            'last_message_time' => optional($latestMessage?->created_at)->diffForHumans(),
            'meta' => $includeFull ? [
                'created_at' => $conversation->created_at->format('M j, Y g:i A'),
                'total_messages' => $conversation->messages()->count(),
            ] : null,
        ];
    }

    private function serializeMessage(ChatMessage $message): array
    {
        return [
            'id' => $message->id,
            'sender_type' => $message->sender_type,
            'sender_name' => match ($message->sender_type) {
                'customer' => $message->user?->name ?? $message->conversation?->customer_name ?? 'Guest Shopper',
                'system' => 'Bonbon Bot',
                default => $message->user?->name ?? 'Bonbon Support',
            },
            'body' => $message->body,
            'attachment_url' => $message->attachment_path ? route('chat.attachments.show', $message) : null,
            'attachment_download_url' => $message->attachment_path ? route('chat.attachments.download', $message) : null,
            'attachment_view_url' => $message->attachment_path ? route('chat.attachments.show', $message) : null,
            'attachment_name' => $message->attachment_name,
            'attachment_mime' => $message->attachment_mime,
            'attachment_size' => $message->attachment_size,
            'is_image' => filled($message->attachment_mime) && Str::startsWith($message->attachment_mime, 'image/'),
            'created_at' => $message->created_at->toIso8601String(),
            'timestamp' => $message->created_at->format('g:i A'),
            'full_timestamp' => $message->created_at->format('M j, Y g:i A'),
            'is_mine' => $message->sender_type === 'admin',
            'receipt_label' => $message->receipt_label,
        ];
    }
}
