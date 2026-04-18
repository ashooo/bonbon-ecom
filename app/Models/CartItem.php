<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'variant_id',
        'special_instructions',
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

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    public function getSubtotalAttribute()
    {
        return $this->unit_price * $this->quantity;
    }

    // Backward-compatible dynamic values for current cart UI.
    public function getProductNameAttribute($value)
    {
        if ($value) {
            return $value;
        }

        return $this->product?->name;
    }

    public function getProductImageAttribute($value)
    {
        if ($value) {
            return $value;
        }

        return $this->product?->main_image_url;
    }
}
