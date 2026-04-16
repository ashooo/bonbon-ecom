<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_name',
        'product_size',
        'product_image',
        'unit_price',
        'quantity',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function getSubtotalAttribute()
    {
        return $this->unit_price * $this->quantity;
    }
}
