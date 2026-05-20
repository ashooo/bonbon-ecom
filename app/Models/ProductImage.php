<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'image_url',
        'alt_text',
        'is_primary',
        'display_order',
    ];

    // Product relationship
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Get the image URL
    public function getImageUrlAttribute($value)
    {
        if (! $value) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        $normalized = ltrim($value, '/');
        if (str_starts_with($normalized, 'images/products_image/') && ! str_starts_with($normalized, 'images/products_image/Images/')) {
            $normalized = 'images/products_image/Images/' . basename($normalized);
        }
        if (str_starts_with($normalized, 'images/')) {
            return asset($normalized);
        }
        if (str_starts_with($normalized, 'storage/')) {
            return asset($normalized);
        }
        if (! str_contains($normalized, '/')) {
            return asset('images/products_image/Images/' . $normalized);
        }

        return asset('storage/' . $normalized);
    }

    // Backward-compatible aliases used by current admin/product pages.
    public function getImagePathAttribute()
    {
        return $this->attributes['image_url'] ?? null;
    }

    public function setImagePathAttribute($value): void
    {
        $this->attributes['image_url'] = $value;
    }

    public function getSortOrderAttribute()
    {
        return $this->attributes['display_order'] ?? 0;
    }

    public function setSortOrderAttribute($value): void
    {
        $this->attributes['display_order'] = $value;
    }
}
