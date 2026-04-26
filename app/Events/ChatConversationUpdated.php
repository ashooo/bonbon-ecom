<?php

namespace App\Events;

use App\Models\ChatConversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatConversationUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ChatConversation $conversation,
        public string $type = 'sync',
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('chat.admin.inbox'),
            new Channel($this->conversation->broadcast_channel),
        ];
    }

    public function broadcastAs(): string
    {
        return 'chat.conversation.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversation->id,
            'channel' => $this->conversation->broadcast_channel,
            'type' => $this->type,
        ];
    }
}
