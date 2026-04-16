<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = [
        'brand_name',
        'hero_image',
        'store_description',
        'footer_email',
        'footer_phone',
        'footer_address',
        'copyright_text',
    ];

    public function getHeroImageUrlAttribute(): ?string
    {
        return $this->hero_image ? asset('storage/' . $this->hero_image) : null;
    }
}
