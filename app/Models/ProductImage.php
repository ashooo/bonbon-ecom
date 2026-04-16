<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'image_path',
        'sort_order'
    ];

    // Product relationship
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Get the image URL
    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }
}
