<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'image',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'display_order' => 'integer',
        'is_active' => 'boolean',
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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Get all descendants (for nested categories)
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    // Check if category is active
    public function isActive()
    {
        return (bool) $this->is_active;
    }

    // Backward-compatible alias used by existing views/controllers.
    public function getStatusAttribute()
    {
        return (bool) $this->is_active;
    }

    public function setStatusAttribute($value): void
    {
        $this->attributes['is_active'] = (bool) $value;
    }
}
