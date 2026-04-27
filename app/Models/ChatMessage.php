<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ChatMessage extends Model
{
    protected $fillable = [
        'conversation_id',
        'user_id',
        'sender_type',
        'body',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'attachment_size',
        'delivered_at',
        'seen_at',
    ];

    protected $appends = [
        'attachment_url',
        'receipt_label',
    ];

    protected function casts(): array
    {
        return [
            'delivered_at' => 'datetime',
            'seen_at' => 'datetime',
            'attachment_size' => 'integer',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        if (! $this->attachment_path) {
            return null;
        }

        return Storage::disk('public')->url($this->attachment_path);
    }

    public function getReceiptLabelAttribute(): string
    {
        if ($this->seen_at) {
            return 'Seen';
        }

        if ($this->delivered_at) {
            return 'Delivered';
        }

        return 'Sent';
    }
}
