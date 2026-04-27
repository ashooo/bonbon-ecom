<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ChatConversation extends Model
{
    protected $fillable = [
        'user_id',
        'guest_session_id',
        'guest_name',
        'guest_email',
        'channel_key',
        'status',
        'last_message_at',
        'customer_last_read_at',
        'admin_last_read_at',
        'customer_typing_at',
        'admin_typing_at',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
            'customer_last_read_at' => 'datetime',
            'admin_last_read_at' => 'datetime',
            'customer_typing_at' => 'datetime',
            'admin_typing_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::creating(function (ChatConversation $conversation): void {
            if (! $conversation->channel_key) {
                $conversation->channel_key = Str::random(32);
            }
        });
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }

    public function presences(): HasMany
    {
        return $this->hasMany(ChatPresence::class, 'conversation_id');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(ChatMessage::class, 'conversation_id')->latestOfMany();
    }

    public function getCustomerNameAttribute(): string
    {
        return trim((string) ($this->user?->name ?? $this->guest_name ?? 'Guest Shopper'));
    }

    public function getCustomerEmailAttribute(): ?string
    {
        return $this->user?->email ?? $this->guest_email;
    }

    public function getCustomerInitialsAttribute(): string
    {
        $name = $this->customer_name;
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $initials = collect($parts)
            ->filter()
            ->take(2)
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->implode('');

        return $initials !== '' ? $initials : 'GS';
    }

    public function getCustomerIsOnlineAttribute(): bool
    {
        return $this->presences()
            ->where('role', 'customer')
            ->where('last_seen_at', '>=', now()->subSeconds(90))
            ->exists();
    }

    public function getAdminIsOnlineAttribute(): bool
    {
        return ChatPresence::query()
            ->where('role', 'admin')
            ->where('last_seen_at', '>=', now()->subSeconds(90))
            ->exists();
    }

    public function getCustomerIsTypingAttribute(): bool
    {
        return $this->customer_typing_at instanceof Carbon
            && $this->customer_typing_at->gte(now()->subSeconds(6));
    }

    public function getAdminIsTypingAttribute(): bool
    {
        return $this->admin_typing_at instanceof Carbon
            && $this->admin_typing_at->gte(now()->subSeconds(6));
    }

    public function getUnreadForAdminAttribute(): int
    {
        return $this->messages()
            ->where('sender_type', 'customer')
            ->when(
                $this->admin_last_read_at,
                fn ($query) => $query->where('created_at', '>', $this->admin_last_read_at)
            )
            ->count();
    }

    public function getUnreadForCustomerAttribute(): int
    {
        return $this->messages()
            ->whereIn('sender_type', ['admin', 'system'])
            ->when(
                $this->customer_last_read_at,
                fn ($query) => $query->where('created_at', '>', $this->customer_last_read_at)
            )
            ->count();
    }

    public function getBroadcastChannelAttribute(): string
    {
        return 'chat.conversation.'.$this->id.'.'.$this->channel_key;
    }
}
