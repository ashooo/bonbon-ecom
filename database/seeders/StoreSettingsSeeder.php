<?php

namespace Database\Seeders;

use App\Models\StoreSetting;
use Illuminate\Database\Seeder;

class StoreSettingsSeeder extends Seeder
{
    public function run(): void
    {
        StoreSetting::updateOrCreate(
            ['id' => 1],
            [
                'brand_name' => 'BonBon PH',
                'chat_display_name' => 'Bonbon Chat',
                'store_description' => 'Handcrafted cakes and sweets for everyday celebrations.',
                'footer_email' => 'hello@bonbon.ph',
                'footer_phone' => '+63 917 123 4567',
                'footer_address' => 'Paranaque City, Metro Manila',
                'copyright_text' => 'Copyright ' . now()->year . ' BonBon PH. All rights reserved.',
            ]
        );
    }
}
