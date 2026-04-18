<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'sale_price',
        'unit_size',
        'is_preorder',
        'preorder_days',
        'allows_customization',
        'is_active',
        // Compatibility fields still used by existing UI.
        'discount_price',
        'stock_quantity',
        'main_image',
        'is_featured',
        'is_best_seller',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'preorder_days' => 'integer',
        'pre_order_days' => 'integer',
        'is_preorder' => 'boolean',
        'allows_customization' => 'boolean',
        'is_active' => 'boolean',
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
        return $this->hasMany(ProductImage::class)->orderBy('display_order');
    }

    public function variants()
    {
        return $this->hasMany(Variant::class)->orderBy('display_order');
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
        return (bool) $this->is_active;
    }

    // Check if product is on pre-order
    public function isPreOrder()
    {
        return (bool) $this->is_preorder;
    }

    // Get the effective price (with discount if available)
    public function getEffectivePriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    // Check if product has discount
    public function hasDiscount()
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    // Get discount percentage
    public function getDiscountPercentageAttribute()
    {
        if ($this->hasDiscount()) {
            return round((($this->price - $this->sale_price) / $this->price) * 100);
        }
        return 0;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Backward-compatible alias used by existing front-end templates/controllers.
    public function getStatusAttribute()
    {
        if (! $this->is_active) {
            return 'inactive';
        }

        return $this->is_preorder ? 'pre_order' : 'active';
    }

    public function setStatusAttribute($value): void
    {
        $status = strtolower((string) $value);
        $this->attributes['is_active'] = $status !== 'inactive';
        $this->attributes['is_preorder'] = $status === 'pre_order';
    }

    public function getDiscountPriceAttribute()
    {
        return $this->sale_price;
    }

    public function setDiscountPriceAttribute($value): void
    {
        $this->attributes['sale_price'] = $value;
    }

    public function getPreOrderDaysAttribute()
    {
        return $this->preorder_days;
    }

    public function setPreOrderDaysAttribute($value): void
    {
        $this->attributes['preorder_days'] = $value;
    }
}
