<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'discount_price',
        'stock_quantity',
        'main_image',
        'status',
        'pre_order_days',
        'is_featured',
        'is_best_seller',
        'slug',
        'category_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'pre_order_days' => 'integer',
        'is_featured' => 'boolean',
        'is_best_seller' => 'boolean',
    ];

    // Automatically generate slug from name
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->slug = Str::slug($product->name);
        });

        static::updating(function ($product) {
            if ($product->isDirty('name')) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    // Category relationship
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Product images relationship
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    // Cart items relationship
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    // Get the main image URL
    public function getMainImageUrlAttribute()
    {
        return $this->main_image ? asset('storage/' . $this->main_image) : null;
    }

    // Check if product is in stock
    public function isInStock()
    {
        return $this->stock_quantity > 0;
    }

    // Check if product is active
    public function isActive()
    {
        return $this->status === 'active';
    }

    // Check if product is on pre-order
    public function isPreOrder()
    {
        return $this->status === 'pre_order';
    }

    // Get the effective price (with discount if available)
    public function getEffectivePriceAttribute()
    {
        return $this->discount_price ?? $this->price;
    }

    // Check if product has discount
    public function hasDiscount()
    {
        return $this->discount_price !== null && $this->discount_price < $this->price;
    }

    // Get discount percentage
    public function getDiscountPercentageAttribute()
    {
        if ($this->hasDiscount()) {
            return round((($this->price - $this->discount_price) / $this->price) * 100);
        }
        return 0;
    }
}
