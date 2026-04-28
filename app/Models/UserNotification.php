<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'body',
        'url',
        'data',
        'read_at',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'read_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        if ($this->read_at) {
            return;
        }

        $this->forceFill([
            'read_at' => now(),
        ])->save();
    }

    public function archive(): void
    {
        if ($this->archived_at) {
            return;
        }

        $this->forceFill([
            'archived_at' => now(),
            'read_at' => $this->read_at ?? now(),
        ])->save();
    }

    public function unarchive(): void
    {
        if (! $this->archived_at) {
            return;
        }

        $this->forceFill([
            'archived_at' => null,
        ])->save();
    }
}
