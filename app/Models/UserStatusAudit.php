<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserStatusAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'acted_by_user_id',
        'action',
        'from_is_active',
        'to_is_active',
    ];

    protected $casts = [
        'from_is_active' => 'boolean',
        'to_is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acted_by_user_id');
    }
}
