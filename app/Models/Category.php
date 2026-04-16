<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image',
        'slug',
        'status',
        'parent_id'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    // Automatically generate slug from name
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });

        static::updating(function ($category) {
            if ($category->isDirty('name')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // Parent category relationship
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Child categories relationship
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Products relationship
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Get all descendants (for nested categories)
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    // Check if category is active
    public function isActive()
    {
        return $this->status;
    }
}
