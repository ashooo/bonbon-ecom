<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatAutoReply extends Model
{
    protected $fillable = [
        'trigger_type',
        'keyword',
        'reply_text',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
