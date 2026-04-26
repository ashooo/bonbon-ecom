<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('12345678'),
            'is_admin' => true,
        ]);

        User::updateOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'password' => Hash::make('12345678'),
        ]);

        User::updateOrCreate([
            'email' => 'customer@example.com',
        ], [
            'name' => 'Customer Demo',
            'password' => Hash::make('12345678'),
            'phone' => '09991234567',
        ]);

        $this->call([
            StoreSettingsSeeder::class,
            ChatSupportSeeder::class,
            ChatConversationSeeder::class,
            CatalogSeeder::class,
            CustomerFlowSeeder::class,
        ]);
    }
}           
