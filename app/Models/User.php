<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'profile_picture',
        'phone',
        'dob',
        'is_admin',
        'is_active',
    ];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function statusAudits(): HasMany
    {
        return $this->hasMany(UserStatusAudit::class)->latest();
    }

    public function performedStatusAudits(): HasMany
    {
        return $this->hasMany(UserStatusAudit::class, 'acted_by_user_id')->latest();
    }

    public function chatConversations(): HasMany
    {
        return $this->hasMany(ChatConversation::class);
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function userNotifications(): HasMany
    {
        return $this->hasMany(UserNotification::class)->latest();
    }

    public function unreadUserNotifications(): HasMany
    {
        return $this->hasMany(UserNotification::class)
            ->whereNull('read_at')
            ->whereNull('archived_at')
            ->latest();
    }

    public function activeUserNotifications(): HasMany
    {
        return $this->hasMany(UserNotification::class)
            ->whereNull('archived_at')
            ->latest();
    }

    public function archivedUserNotifications(): HasMany
    {
        return $this->hasMany(UserNotification::class)
            ->whereNotNull('archived_at')
            ->latest();
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function getOrCreateCart()
    {
        return $this->cart ?? $this->cart()->create();
    }

    public function getProfileImageUrlAttribute()
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }

        if ($this->avatar) {
            return $this->avatar;
        }

        return 'https://via.placeholder.com/150?text=Profile';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'dob' => 'date',
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }
}
