<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::created(function (Order $order): void {
            if ($order->status) {
                $order->recordStatusTransition(null, (string) $order->status);
            }
        });

        static::updated(function (Order $order): void {
            if (! $order->wasChanged('status')) {
                return;
            }

            $order->recordStatusTransition(
                $order->getOriginal('status'),
                (string) $order->status
            );
        });
    }

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'order_type',
        'fulfillment_date',
        'fulfillment_time',
        'address_id',
        'delivery_address',
        'delivery_fee',
        'subtotal',
        'total',
        'special_instructions',
        'status',
        'payment_status',
    ];

    protected $casts = [
        'fulfillment_date' => 'date',
        'delivery_fee' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('changed_at');
    }

    private function recordStatusTransition(?string $fromStatus, string $toStatus): void
    {
        $this->statusHistory()->create([
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'changed_by_user_id' => Auth::id(),
            'changed_at' => now(),
        ]);
    }

    // Backward-compatible aliases used in current profile page.
    public function getTotalAmountAttribute()
    {
        return $this->total;
    }

    public function getPlacedAtAttribute()
    {
        return $this->created_at;
    }
}
