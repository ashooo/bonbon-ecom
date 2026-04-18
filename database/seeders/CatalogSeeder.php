<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Variant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $cakes = Category::updateOrCreate(
            ['slug' => 'cakes'],
            [
                'name' => 'Cakes',
                'description' => 'Celebration and signature cakes.',
                'display_order' => 1,
                'is_active' => true,
            ]
        );

        $pastries = Category::updateOrCreate(
            ['slug' => 'pastries'],
            [
                'name' => 'Pastries',
                'description' => 'Daily bakes and sweet treats.',
                'display_order' => 2,
                'is_active' => true,
            ]
        );

        $custom = Category::updateOrCreate(
            ['slug' => 'custom-orders'],
            [
                'name' => 'Custom Orders',
                'description' => 'Made-to-order cakes with personalized designs.',
                'parent_id' => $cakes->id,
                'display_order' => 1,
                'is_active' => true,
            ]
        );

        $categoryMap = [
            'cakes' => $cakes->id,
            'pastries' => $pastries->id,
            'custom-orders' => $custom->id,
        ];

        $products = [
            [
                'name' => 'Classic Chocolate Cake',
                'category' => 'cakes',
                'description' => 'Moist chocolate sponge with rich ganache frosting.',
                'price' => 980,
                'sale_price' => 890,
                'unit_size' => '6-inch',
                'is_preorder' => false,
                'preorder_days' => 0,
                'allows_customization' => true,
                'is_featured' => true,
                'is_active' => true,
                'stock_quantity' => 20,
                'is_best_seller' => true,
            ],
            [
                'name' => 'Strawberry Shortcake',
                'category' => 'cakes',
                'description' => 'Light vanilla sponge, cream, and fresh strawberries.',
                'price' => 1050,
                'sale_price' => null,
                'unit_size' => '6-inch',
                'is_preorder' => false,
                'preorder_days' => 0,
                'allows_customization' => true,
                'is_featured' => true,
                'is_active' => true,
                'stock_quantity' => 16,
                'is_best_seller' => false,
            ],
            [
                'name' => 'Ube Velvet Cake',
                'category' => 'custom-orders',
                'description' => 'Signature ube layers with cream cheese frosting.',
                'price' => 1200,
                'sale_price' => 1100,
                'unit_size' => '8-inch',
                'is_preorder' => true,
                'preorder_days' => 2,
                'allows_customization' => true,
                'is_featured' => true,
                'is_active' => true,
                'stock_quantity' => 8,
                'is_best_seller' => true,
            ],
            [
                'name' => 'Brown Butter Cookies',
                'category' => 'pastries',
                'description' => 'Chewy cookies with caramelized brown butter notes.',
                'price' => 280,
                'sale_price' => 250,
                'unit_size' => '6 pcs',
                'is_preorder' => false,
                'preorder_days' => 0,
                'allows_customization' => false,
                'is_featured' => false,
                'is_active' => true,
                'stock_quantity' => 50,
                'is_best_seller' => false,
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::updateOrCreate(
                ['slug' => Str::slug($productData['name'])],
                [
                    'category_id' => $categoryMap[$productData['category']],
                    'name' => $productData['name'],
                    'description' => $productData['description'],
                    'price' => $productData['price'],
                    'sale_price' => $productData['sale_price'],
                    'unit_size' => $productData['unit_size'],
                    'is_preorder' => $productData['is_preorder'],
                    'preorder_days' => $productData['preorder_days'],
                    'allows_customization' => $productData['allows_customization'],
                    'is_featured' => $productData['is_featured'],
                    'is_active' => $productData['is_active'],
                    'stock_quantity' => $productData['stock_quantity'],
                    'is_best_seller' => $productData['is_best_seller'],
                ]
            );

            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'display_order' => 0],
                [
                    'image_url' => 'products/demo/' . $product->slug . '-main.jpg',
                    'alt_text' => $product->name . ' main image',
                    'is_primary' => true,
                ]
            );

            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'display_order' => 1],
                [
                    'image_url' => 'products/demo/' . $product->slug . '-side.jpg',
                    'alt_text' => $product->name . ' side view',
                    'is_primary' => false,
                ]
            );

            Variant::updateOrCreate(
                ['sku' => strtoupper(Str::slug($product->name, '')) . '-REG'],
                [
                    'product_id' => $product->id,
                    'name' => 'Regular',
                    'price_adjustment' => 0,
                    'stock_quantity' => max(1, (int) floor($product->stock_quantity / 2)),
                    'is_default' => true,
                    'display_order' => 0,
                    'is_active' => true,
                ]
            );

            Variant::updateOrCreate(
                ['sku' => strtoupper(Str::slug($product->name, '')) . '-LRG'],
                [
                    'product_id' => $product->id,
                    'name' => 'Large',
                    'price_adjustment' => 120,
                    'stock_quantity' => max(1, (int) floor($product->stock_quantity / 3)),
                    'is_default' => false,
                    'display_order' => 1,
                    'is_active' => true,
                ]
            );
        }
    }
}
