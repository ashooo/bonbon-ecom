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
    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image_url);
    }

    // Backward-compatible aliases used by current admin/product pages.
    public function getImagePathAttribute()
    {
        return $this->image_url;
    }

    public function setImagePathAttribute($value): void
    {
        $this->attributes['image_url'] = $value;
    }

    public function getSortOrderAttribute()
    {
        return $this->display_order;
    }

    public function setSortOrderAttribute($value): void
    {
        $this->attributes['display_order'] = $value;
    }
}
