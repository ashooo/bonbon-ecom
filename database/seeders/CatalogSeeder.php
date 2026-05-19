<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Variant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['slug' => 'custom-orders', 'name' => 'Custom Orders', 'description' => 'Made-to-order cakes with personalized designs.', 'display_order' => 1],
            ['slug' => 'sponge-cake', 'name' => 'Sponge Cake', 'description' => null, 'display_order' => 2],
            ['slug' => 'premium-cakes', 'name' => 'Premium Cakes', 'description' => 'Flavored Cakes', 'display_order' => 3],
            ['slug' => 'bento-cake', 'name' => 'Bento Cake', 'description' => null, 'display_order' => 4],
            ['slug' => 'cupcakes', 'name' => 'Cupcakes', 'description' => null, 'display_order' => 5],
            ['slug' => 'chocolate', 'name' => 'Chocolate', 'description' => null, 'display_order' => 6],
            ['slug' => 'donuts', 'name' => 'Donuts', 'description' => null, 'display_order' => 7],
            ['slug' => 'brownies', 'name' => 'Brownies', 'description' => null, 'display_order' => 8],
        ];

        $categoryMap = [];
        foreach ($categories as $categoryData) {
            $category = Category::updateOrCreate(
                ['slug' => $categoryData['slug']],
                [
                    'name' => $categoryData['name'],
                    'description' => $categoryData['description'],
                    'display_order' => $categoryData['display_order'],
                    'is_active' => true,
                ]
            );

            $categoryMap[$categoryData['slug']] = $category->id;
        }

        $products = [
            [
                'name' => 'Chocolate Mousse Cake',
                'category' => 'premium-cakes',
                'description' => 'Our Chocolate Mousse Cake is made with rich and creamy chocolate mousse layered on soft chocolate cake, topped with smooth chocolate frosting and delicious chocolate toppings. The entire bottom edge of the cake is coated with crunchy chocolate bits for added texture and flavor, creating a decadent dessert perfect for any celebration.',
                'price' => 1200,
                'sale_price' => 999,
                'is_preorder' => true,
                'preorder_days' => 5,
                'is_featured' => true,
                'stock_quantity' => 10,
                'is_best_seller' => true,
                'image' => 'K83ym3Zp3PPsYpEFSGHMOcVKH1a5WyZvY0HOOhYV.jpg',
            ],
            [
                'name' => 'Vanilla Sponge Cake',
                'category' => 'sponge-cake',
                'description' => 'Our Vanilla Sponge Cake is made of soft, light, and fluffy sponge layers infused with rich vanilla flavor and baked to perfection. Its simple yet delicious taste makes it perfect as a base for custom cakes or for enjoying on its own.',
                'price' => 120,
                'sale_price' => null,
                'is_preorder' => false,
                'preorder_days' => 0,
                'is_featured' => false,
                'stock_quantity' => 33,
                'is_best_seller' => false,
                'image' => 'ZLDUNBeB290ZMBJQIT7bJTZOeVA93I6Iry9mAyeG.jpg',
            ],
            [
                'name' => 'Chocolate Sponge Cake',
                'category' => 'sponge-cake',
                'description' => 'Our Chocolate Sponge Cake is made of soft, moist, and fluffy sponge layers infused with rich chocolate flavor and baked to perfection. Its light texture and delicious cocoa taste make it perfect for desserts, celebrations, or custom cake creations.',
                'price' => 120,
                'sale_price' => null,
                'is_preorder' => false,
                'preorder_days' => 0,
                'is_featured' => true,
                'stock_quantity' => 20,
                'is_best_seller' => false,
                'image' => 'hPkLFeUE1RbsVQ8ung6R6Y3X6s55AnbU6vF6oRjP.jpg',
            ],
            [
                'name' => 'Strawberry Sponge Cake',
                'category' => 'sponge-cake',
                'description' => 'Our Strawberry Sponge Cake is made of light and fluffy sponge layers infused with sweet strawberry flavor and baked to perfection. Its soft texture and fruity taste make it a refreshing and delightful choice for any occasion.',
                'price' => 120,
                'sale_price' => null,
                'is_preorder' => false,
                'preorder_days' => 0,
                'is_featured' => false,
                'stock_quantity' => 30,
                'is_best_seller' => false,
                'image' => 'OiMgpN6Lqv1DqGjC3eIeSJGqfdHIXt6chR2CVHkS.jpg',
            ],
            [
                'name' => 'Strawberry Fresh Cream Cake',
                'category' => 'premium-cakes',
                'description' => 'Our Strawberry Fresh Cream Cake is made with soft and fluffy strawberry sponge cake layered with smooth white frosting in the middle and on top. Finished with fresh strawberries on the top and between the layers, this cake delivers a light, creamy, and refreshing fruity flavor perfect for any celebration.',
                'price' => 1250,
                'sale_price' => null,
                'is_preorder' => true,
                'preorder_days' => 5,
                'is_featured' => true,
                'stock_quantity' => 5,
                'is_best_seller' => true,
                'image' => 'abI6GWRsHZtKZhiz6Nraaxow4LAUKqXM4bTjuHIa.jpg',
            ],
            [
                'name' => 'Cinnamon Roll Cake',
                'category' => 'premium-cakes',
                'description' => 'Our Cinnamon Roll Cake is made with soft and fluffy cinnamon-infused sponge layers filled with smooth cream frosting and rich cinnamon filling. Topped with creamy white frosting and a sprinkle of cinnamon, this cake delivers a warm, sweet, and comforting flavor inspired by classic cinnamon rolls.',
                'price' => 590,
                'sale_price' => null,
                'is_preorder' => false,
                'preorder_days' => 0,
                'is_featured' => true,
                'stock_quantity' => 5,
                'is_best_seller' => false,
                'image' => 'nXMDjv5Nq8PcbCw9rbd4KbXWGvyacYqtKmhoiLne.jpg',
            ],
            [
                'name' => 'Choco Sprinkle Bliss Cupcake',
                'category' => 'cupcakes',
                'description' => 'Moist and rich chocolate cupcake topped with smooth creamy frosting and colorful chocolate sprinkles for the perfect sweet finish. A delicious treat packed with chocolate flavor in every bite, perfect for celebrations, gifts, or everyday cravings.',
                'price' => 340,
                'sale_price' => null,
                'is_preorder' => false,
                'preorder_days' => 0,
                'is_featured' => true,
                'stock_quantity' => 10,
                'is_best_seller' => true,
                'image' => '0gCYjrp8dJTfO9qfFrJ5KDoclcNj7OvQsl80xsbG.jpg',
            ],
            [
                'name' => 'Famous Dubai Chewy Choco',
                'category' => 'chocolate',
                'description' => 'Rich and chewy chocolate dessert made with premium cocoa flavor and a soft fudgy texture in every bite. Perfect for chocolate lovers craving a sweet and indulgent treat. Freshly baked with a deep chocolate taste that\'s satisfyingly chewy and delicious.',
                'price' => 400,
                'sale_price' => 399,
                'is_preorder' => false,
                'preorder_days' => 0,
                'is_featured' => true,
                'stock_quantity' => 10,
                'is_best_seller' => true,
                'image' => '0mdfEMdtpIkN49BnHTXyP1X6rW6P2bZAmiGm3l9a.jpg',
            ],
            [
                'name' => 'Sweet Mango Bento Cake',
                'category' => 'bento-cake',
                'description' => 'Soft and fluffy mango sponge cake layered with smooth mango cream and delicious fruity flavor in every bite. A perfectly sized bento cake for small celebrations, gifts, or personal cravings. Freshly made with a light and creamy texture for a sweet tropical treat.',
                'price' => 300,
                'sale_price' => null,
                'is_preorder' => false,
                'preorder_days' => 0,
                'is_featured' => false,
                'stock_quantity' => 10,
                'is_best_seller' => false,
                'image' => 'aGkZtqXhzUHsOJW7QXqch5932VdbFFMH7qPLhmwo.jpg',
            ],
            [
                'name' => 'Strawberry Bento Cake "Valentine Edition"',
                'category' => 'bento-cake',
                'description' => 'Soft and fluffy strawberry bento cake layered with smooth strawberry cream and sweet fruity flavor in every bite. Made fresh with a light and creamy texture, perfect for small celebrations, gifts, or satisfying your dessert cravings. A cute and delightful treat for strawberry lovers.',
                'price' => 450,
                'sale_price' => null,
                'is_preorder' => false,
                'preorder_days' => 0,
                'is_featured' => false,
                'stock_quantity' => 3,
                'is_best_seller' => false,
                'image' => 'XvEWsZITLHcX3BmIARXhqZB89NTB3I3lqYAwjtkv.jpg',
            ],
            [
                'name' => 'Assorted Flavor Cupcake',
                'category' => 'cupcakes',
                'description' => 'A delicious assortment of cupcakes featuring raspberry, cookies & cream, chocolate, and cheese flavors, each topped with smooth creamy frosting and delightful toppings. Perfect for sharing, celebrations, gifts, or enjoying a variety of sweet flavors in one box. Freshly made for a rich and satisfying dessert experience.',
                'price' => 345.50,
                'sale_price' => null,
                'is_preorder' => false,
                'preorder_days' => 0,
                'is_featured' => true,
                'stock_quantity' => 12,
                'is_best_seller' => true,
                'image' => 'MBAPYhfaYRiULvuWK8bguFEmB1UXDoqPPBE0mTjv.jpg',
            ],
            [
                'name' => 'Cookies & Cream flavoured Cupcake',
                'category' => 'cupcakes',
                'description' => 'Soft and moist cookies & cream cupcake topped with creamy frosting and crunchy cookie crumbs for the perfect balance of sweetness and chocolatey goodness. Made fresh with rich flavor in every bite, perfect for dessert cravings, celebrations, or gifting.',
                'price' => 300,
                'sale_price' => null,
                'is_preorder' => false,
                'preorder_days' => 0,
                'is_featured' => false,
                'stock_quantity' => 5,
                'is_best_seller' => false,
                'image' => '3OHrz308yIqLdtEHI85RWtJ3TgFhVAKKhsHdoJyW.jpg',
            ],
            [
                'name' => 'Mocha Sponge Cake',
                'category' => 'sponge-cake',
                'description' => 'Light and fluffy mocha sponge cake infused with rich coffee and chocolate flavor for a smooth and satisfying taste in every bite. Perfectly soft, aromatic, and ideal for coffee lovers.',
                'price' => 120,
                'sale_price' => null,
                'is_preorder' => false,
                'preorder_days' => 0,
                'is_featured' => false,
                'stock_quantity' => 25,
                'is_best_seller' => false,
                'image' => 'YEIP6jEKHQecuuR5OBA6hTv9QvEXwSlsl9PDwDaH.jpg',
            ],
            [
                'name' => 'Cheese Cupcake',
                'category' => 'cupcakes',
                'description' => 'Soft and moist cheese cupcake with a rich creamy cheese flavor and perfectly balanced sweetness. A deliciously comforting treat that\'s perfect for snacks, desserts, or gifting.',
                'price' => 250,
                'sale_price' => null,
                'is_preorder' => false,
                'preorder_days' => 0,
                'is_featured' => false,
                'stock_quantity' => 3,
                'is_best_seller' => false,
                'image' => 'QKwihVfcLAdM7DQysPA9yShHaEhPXiWvcCRdPHc5.jpg',
            ],
            [
                'name' => 'Mocha Cream Cake',
                'category' => 'premium-cakes',
                'description' => 'Rich and moist mocha cake layered with smooth coffee-chocolate flavor for a deliciously indulgent dessert experience. Freshly made and perfect for celebrations, coffee pairings, or sweet cravings.',
                'price' => 690,
                'sale_price' => null,
                'is_preorder' => true,
                'preorder_days' => 5,
                'is_featured' => true,
                'stock_quantity' => 5,
                'is_best_seller' => true,
                'image' => 'fCCCu3iOK15DwzrtEdasOTJvA3IXm8gU1vhHoix7.jpg',
            ],
            [
                'name' => 'Assorted 12 pieced Special Donuts',
                'category' => 'donuts',
                'description' => "A delightful box of assorted donuts featuring a variety of sweet flavors and delicious toppings. Soft, fluffy, and freshly made, perfect for sharing, gifting, or enjoying anytime of the day.\nStrawberry, Ube, Bavarian, and Chocolate Flavor.",
                'price' => 340,
                'sale_price' => null,
                'is_preorder' => false,
                'preorder_days' => 0,
                'is_featured' => false,
                'stock_quantity' => 2,
                'is_best_seller' => false,
                'image' => 'NBLEwSZ9R7urosYYEqn0xiaO3VD4aO3VWZkqbMWP.jpg',
            ],
            [
                'name' => 'Special Brownies',
                'category' => 'brownies',
                'description' => 'A rich assortment of fudgy brownies with delicious flavors and toppings in every bite. Soft, chewy, and chocolatey treats perfect for dessert lovers, sharing, or satisfying sweet cravings.',
                'price' => 300.90,
                'sale_price' => 280,
                'is_preorder' => false,
                'preorder_days' => 0,
                'is_featured' => false,
                'stock_quantity' => 10,
                'is_best_seller' => false,
                'image' => 'QzqtKp6MbeRwVDbu2y2Uwpp9k4kDHdqOTnmlcMQq.jpg',
            ],
        ];

        foreach ($products as $productData) {
            $slug = Str::slug($productData['name']);
            $imagePath = 'images/products_image/' . $productData['image'];
            $attributes = [
                'category_id' => $categoryMap[$productData['category']],
                'name' => $productData['name'],
                'description' => $productData['description'],
                'price' => $productData['price'],
                'sale_price' => $productData['sale_price'],
                'unit_size' => null,
                'is_preorder' => $productData['is_preorder'],
                'preorder_days' => $productData['preorder_days'],
                'is_featured' => $productData['is_featured'],
                'is_active' => true,
                'stock_quantity' => $productData['stock_quantity'],
                'is_best_seller' => $productData['is_best_seller'],
                'main_image' => $imagePath,
            ];

            if (Schema::hasColumn('products', 'allows_customization')) {
                $attributes['allows_customization'] = false;
            }

            $product = Product::updateOrCreate(
                ['slug' => $slug],
                $attributes
            );

            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'display_order' => 0],
                [
                    'image_url' => $imagePath,
                    'alt_text' => $product->name . ' main image',
                    'is_primary' => true,
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
