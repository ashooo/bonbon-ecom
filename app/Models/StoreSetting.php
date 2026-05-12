<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = [
        'brand_name',
        'chat_display_name',
        'hero_image',
        'chat_avatar',
        'store_description',
        'footer_email',
        'footer_phone',
        'footer_address',
        'footer_hours',
        'copyright_text',
        'customization_pricing',
    ];

    protected $casts = [
        'customization_pricing' => 'array',
    ];

    public function getHeroImageUrlAttribute(): ?string
    {
        return $this->hero_image ? asset('storage/' . $this->hero_image) : null;
    }

    public function getChatAvatarUrlAttribute(): ?string
    {
        return $this->chat_avatar ? asset('storage/' . $this->chat_avatar) : null;
    }
}
