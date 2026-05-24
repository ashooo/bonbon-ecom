<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

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
            if (blank($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && ! $product->isDirty('slug')) {
                $product->slug = static::generateUniqueSlug($product->name, $product->id);
            }
        });
    }

    private static function generateUniqueSlug(string $name, ?int $ignoreProductId = null): string
    {
        $base = Str::slug($name);
        $seed = $base !== '' ? $base : 'product';
        $slug = $seed;
        $suffix = 1;

        while (
            static::query()
                ->where('slug', $slug)
                ->when($ignoreProductId, fn ($query) => $query->where('id', '!=', $ignoreProductId))
                ->exists()
        ) {
            $slug = $seed . '-' . $suffix;
            $suffix++;
        }

        return $slug;
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

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class)->latest();
    }

    // Cart items relationship
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    // Get the main image URL
    public function getMainImageUrlAttribute()
    {
        $value = $this->main_image;
        if (! $value) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        $normalized = ltrim($value, '/');
        if (str_starts_with($normalized, 'images/products_image/')) {
            $baseFile = basename($normalized);
            $directPath = 'images/products_image/' . $baseFile;
            $legacyPath = 'images/products_image/Images/' . $baseFile;
            if (file_exists(public_path($directPath))) {
                $normalized = $directPath;
            } elseif (file_exists(public_path($legacyPath))) {
                $normalized = $legacyPath;
            }
        }
        if (str_starts_with($normalized, 'images/')) {
            return asset($normalized);
        }
        if (str_starts_with($normalized, 'storage/')) {
            return asset($normalized);
        }

        // Backward-compatibility for filename-only records like "CMC.jpeg"
        if (! str_contains($normalized, '/')) {
            $directPath = 'images/products_image/' . $normalized;
            $legacyPath = 'images/products_image/Images/' . $normalized;
            if (file_exists(public_path($directPath))) {
                return asset($directPath);
            }
            return asset($legacyPath);
        }

        return asset('storage/' . $normalized);
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
        if ($this->sale_price !== null) {
            return $this->sale_price;
        }

        if ($this->price !== null) {
            return $this->price;
        }

        // Fallback for products whose price is defined at variant level.
        $defaultVariant = $this->relationLoaded('variants')
            ? $this->variants->sortByDesc('is_default')->first()
            : $this->variants()->orderByDesc('is_default')->orderBy('display_order')->first();

        return (float) ($defaultVariant?->price_adjustment ?? 0);
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

    // Backward-compatible alias used by existing front-end/admin pages.
    public function getPreOrderDaysAttribute($value): int
    {
        if ($value !== null) {
            return (int) $value;
        }

        return (int) ($this->attributes['preorder_days'] ?? 0);
    }

    public function setPreOrderDaysAttribute($value): void
    {
        $days = $value === null || $value === '' ? 0 : (int) $value;
        $this->attributes['preorder_days'] = max(0, $days);
    }
}
