<?php

namespace App\Http\Controllers;

use App\Events\ChatConversationUpdated;
use App\Models\ChatAutoReply;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\ChatPresence;
use App\Services\StoreChatbotService;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function __construct(
        private readonly StoreChatbotService $storeChatbotService
    ) {
    }

    public function show(Request $request)
    {
        $conversation = $this->resolveConversation($request, false);

        return view('pages.assistant', [
            'conversation' => $conversation,
            'chatIdentity' => [
                'name' => $conversation?->user?->name ?? $conversation?->guest_name ?? Auth::user()?->name,
                'email' => $conversation?->user?->email ?? $conversation?->guest_email ?? Auth::user()?->email,
            ],
        ]);
    }

    public function session(Request $request): JsonResponse
    {
        $conversation = $this->resolveConversation($request, false);

        if (! $conversation) {
            return response()->json([
                'conversation' => null,
                'messages' => [],
                'meta' => [
                    'allowed_file_types' => 'Images, PDF, DOC, DOCX up to 5MB',
                ],
            ]);
        }

        $this->touchPresence($request, $conversation, 'customer');
        $this->markConversationRead($conversation, 'customer');

        return response()->json([
            'conversation' => $this->serializeConversation($conversation->fresh(), 'customer'),
            'messages' => $this->serializeMessages($conversation->messages()->with('user')->oldest()->get(), 'customer'),
            'meta' => [
                'allowed_file_types' => 'Images, PDF, DOC, DOCX up to 5MB',
            ],
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $conversation = $this->resolveConversation($request, false);

        $data = $request->validate([
            'name' => 'nullable|string|max:120',
            'email' => 'nullable|email|max:255',
        ]);

        if ($conversation && ! Auth::check()) {
            $conversation->update([
                'guest_name' => $data['name'] ?: 'Guest Shopper',
                'guest_email' => $data['email'] ?: null,
            ]);
        }

        return response()->json([
            'conversation' => $conversation ? $this->serializeConversation($conversation->fresh(), 'customer') : null,
        ]);
    }

    public function storeMessage(Request $request): JsonResponse
    {
        $conversation = $this->resolveConversation($request, true);

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
            'sender_type' => 'customer',
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
            'customer_typing_at' => null,
            'status' => 'open',
        ]);

        $this->touchPresence($request, $conversation, 'customer');
        $this->sendAutomaticReplies($conversation, $message);
        $this->broadcastConversationUpdate($conversation->fresh(), 'message');

        return response()->json([
            'conversation' => $this->serializeConversation($conversation->fresh(), 'customer'),
            'message' => $this->serializeMessage($message->fresh('user'), 'customer'),
        ], 201);
    }

    public function typing(Request $request): JsonResponse
    {
        $conversation = $this->resolveConversation($request, false);
        if (! $conversation) {
            return response()->json(['ok' => true]);
        }

        $isTyping = $request->boolean('is_typing');

        $conversation->update([
            'customer_typing_at' => $isTyping ? now() : null,
        ]);

        $this->touchPresence($request, $conversation, 'customer');
        $this->broadcastConversationUpdate($conversation->fresh(), 'typing');

        return response()->json(['ok' => true]);
    }

    public function presence(Request $request): JsonResponse
    {
        $conversation = $this->resolveConversation($request, false);

        if (! $conversation) {
            return response()->json(['ok' => true]);
        }

        $this->touchPresence($request, $conversation, 'customer');
        $this->broadcastConversationUpdate($conversation->fresh(), 'presence');

        return response()->json(['ok' => true]);
    }

    public function attachment(Request $request, ChatMessage $message)
    {
        $this->authorizeMessageAccess($request, $message);
        abort_unless($message->attachment_path, 404);

        return Storage::disk('public')->response(
            $message->attachment_path,
            $message->attachment_name,
            ['Content-Type' => $message->attachment_mime ?: Storage::disk('public')->mimeType($message->attachment_path)]
        );
    }

    public function downloadAttachment(Request $request, ChatMessage $message)
    {
        $this->authorizeMessageAccess($request, $message);
        abort_unless($message->attachment_path, 404);

        return Storage::disk('public')->download(
            $message->attachment_path,
            $message->attachment_name
        );
    }

    public function aiMessage(Request $request): JsonResponse
    {
        $data = $request->validate([
            'body' => 'required|string|max:4000',
            'history' => 'nullable|array|max:12',
            'history.*.role' => 'required_with:history|string|max:20',
            'history.*.content' => 'required_with:history|string|max:4000',
        ]);

        $result = $this->storeChatbotService->reply(
            $data['body'],
            $data['history'] ?? []
        );

        return response()->json([
            'message' => [
                'id' => 'ai-' . Str::uuid(),
                'sender_type' => 'assistant',
                'sender_name' => 'Bonbon AI',
                'body' => $result['reply'],
                'products' => $result['products'],
                'attachment_url' => null,
                'attachment_download_url' => null,
                'attachment_view_url' => null,
                'attachment_name' => null,
                'attachment_mime' => null,
                'attachment_size' => null,
                'is_image' => false,
                'created_at' => now()->toIso8601String(),
                'timestamp' => now()->format('g:i A'),
                'full_timestamp' => now()->format('M j, Y g:i A'),
                'is_mine' => false,
                'receipt_label' => '',
                'restricted' => $result['restricted'],
            ],
        ]);
    }

    private function resolveConversation(Request $request, bool $createIfMissing): ?ChatConversation
    {
        $request->session()->start();

        $conversation = Auth::check()
            ? ChatConversation::query()
                ->where('user_id', Auth::id())
                ->latest('updated_at')
                ->first()
            : ChatConversation::query()
                ->whereNull('user_id')
                ->where('guest_session_id', $request->session()->getId())
                ->latest('updated_at')
                ->first();

        if (! $conversation && $createIfMissing) {
            $conversation = ChatConversation::create([
                'user_id' => Auth::id(),
                'guest_session_id' => Auth::check() ? null : $request->session()->getId(),
                'guest_name' => Auth::check() ? null : 'Guest Shopper',
                'guest_email' => null,
                'last_message_at' => now(),
            ]);
        }

        return $conversation;
    }

    private function touchPresence(Request $request, ChatConversation $conversation, string $role): void
    {
        ChatPresence::query()->updateOrCreate(
            [
                'conversation_id' => $conversation->id,
                'user_id' => Auth::id(),
                'session_id' => Auth::check() ? null : $request->session()->getId(),
                'role' => $role,
            ],
            [
                'last_seen_at' => now(),
            ]
        );
    }

    private function broadcastConversationUpdate(ChatConversation $conversation, string $context): void
    {
        try {
            event(new ChatConversationUpdated($conversation, $context));
        } catch (BroadcastException $exception) {
            Log::warning('Chat broadcast skipped because the realtime server is unavailable.', [
                'conversation_id' => $conversation->id,
                'context' => $context,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function authorizeMessageAccess(Request $request, ChatMessage $message): void
    {
        $conversation = $message->conversation;
        abort_unless($conversation, 404);

        if (Auth::check() && Auth::user()->is_admin) {
            return;
        }

        if (Auth::check() && $conversation->user_id === Auth::id()) {
            return;
        }

        $request->session()->start();

        if (! Auth::check() && $conversation->guest_session_id === $request->session()->getId()) {
            return;
        }

        abort(403);
    }

    private function markConversationRead(ChatConversation $conversation, string $viewer): void
    {
        $now = now();

        if ($viewer === 'customer') {
            $conversation->messages()
                ->whereIn('sender_type', ['admin', 'system'])
                ->whereNull('delivered_at')
                ->update(['delivered_at' => $now]);

            $conversation->messages()
                ->whereIn('sender_type', ['admin', 'system'])
                ->whereNull('seen_at')
                ->update(['seen_at' => $now]);

            $conversation->update(['customer_last_read_at' => $now]);

            return;
        }

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

    private function sendAutomaticReplies(ChatConversation $conversation, ChatMessage $message): void
    {
        $messageBody = Str::lower((string) $message->body);
        $replies = collect();

        if (! $conversation->admin_is_online) {
            $offlineReply = ChatAutoReply::query()
                ->where('trigger_type', 'offline')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->first();

            if ($offlineReply) {
                $replies->push($offlineReply->reply_text);
            }
        }

        $keywordReply = ChatAutoReply::query()
            ->where('trigger_type', 'keyword')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->first(function (ChatAutoReply $reply) use ($messageBody) {
                if (! filled($reply->keyword)) {
                    return false;
                }

                $keywords = collect(explode(',', $reply->keyword))
                    ->map(fn (string $keyword) => Str::lower(trim($keyword)))
                    ->filter();

                return $keywords->contains(fn (string $keyword) => Str::contains($messageBody, $keyword));
            });

        if ($keywordReply) {
            $replies->push($keywordReply->reply_text);
        }

        $replies
            ->filter()
            ->unique()
            ->each(function (string $replyText) use ($conversation): void {
                $recentDuplicateExists = $conversation->messages()
                    ->where('sender_type', 'system')
                    ->where('body', $replyText)
                    ->where('created_at', '>=', now()->subMinutes(5))
                    ->exists();

                if ($recentDuplicateExists) {
                    return;
                }

                ChatMessage::create([
                    'conversation_id' => $conversation->id,
                    'sender_type' => 'system',
                    'body' => $replyText,
                    'delivered_at' => now(),
                ]);
            });

        $conversation->update(['last_message_at' => now()]);
    }

    private function serializeConversation(ChatConversation $conversation, string $viewer): array
    {
        $latestMessage = $conversation->messages()->latest('created_at')->first();

        return [
            'id' => $conversation->id,
            'customer_name' => $conversation->customer_name,
            'customer_email' => $conversation->customer_email,
            'customer_initials' => $conversation->customer_initials,
            'admin_is_online' => $conversation->admin_is_online,
            'customer_is_online' => $conversation->customer_is_online,
            'admin_is_typing' => $conversation->admin_is_typing,
            'customer_is_typing' => $conversation->customer_is_typing,
            'broadcast_channel' => $conversation->broadcast_channel,
            'status' => $conversation->status,
            'last_message_preview' => Str::limit((string) ($latestMessage?->body ?: $latestMessage?->attachment_name ?: 'Start a conversation'), 60),
            'last_message_at' => optional($latestMessage?->created_at)->toIso8601String(),
            'last_message_time' => optional($latestMessage?->created_at)->format('M j, g:i A'),
            'unread_count' => $viewer === 'customer'
                ? $conversation->unread_for_customer
                : $conversation->unread_for_admin,
        ];
    }

    private function serializeMessages($messages, string $viewer): array
    {
        return $messages->map(fn (ChatMessage $message) => $this->serializeMessage($message, $viewer))->all();
    }

    private function serializeMessage(ChatMessage $message, string $viewer): array
    {
        $isMine = $viewer === 'customer'
            ? $message->sender_type === 'customer'
            : $message->sender_type === 'admin';

        return [
            'id' => $message->id,
            'sender_type' => $message->sender_type,
            'sender_name' => match ($message->sender_type) {
                'admin' => $message->user?->name ?? 'Bonbon Support',
                'system' => 'Bonbon Bot',
                default => $message->user?->name ?? $message->conversation?->customer_name ?? 'Guest Shopper',
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
            'is_mine' => $isMine,
            'receipt_label' => $message->receipt_label,
        ];
    }
}
