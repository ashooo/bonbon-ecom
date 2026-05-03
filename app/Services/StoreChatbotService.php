<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class StoreChatbotService
{
    public function reply(string $message, array $history = []): array
    {
        $message = trim($message);

        if ($message === '') {
            return [
                'reply' => 'Ask me about BonBon products, prices, delivery, categories, or recommendations and I can help.',
                'products' => [],
                'restricted' => false,
            ];
        }

        if (! $this->isStoreRelated($message)) {
            return [
                'reply' => 'BonBon AI can only help with store-related questions like products, prices, categories, delivery, store location, contact info, and ordering.',
                'products' => [],
                'restricted' => true,
            ];
        }

        $questionType = $this->classifyQuestion($message);
        $products = $questionType === 'product_search' ? $this->findRelevantProducts($message) : collect();
        $reply = $this->generateReply($message, $history, $products, $questionType);

        return [
            'reply' => $reply,
            'products' => $products
                ->take(4)
                ->map(fn (Product $product) => $this->serializeProduct($product))
                ->values()
                ->all(),
            'restricted' => false,
        ];
    }

    private function isStoreRelated(string $message): bool
    {
        $normalized = Str::lower($message);
        $keywords = [
            'bonbon', 'cake', 'cakes', 'cookie', 'cookies', 'cupcake', 'pastry', 'dessert', 'product',
            'price', 'cheap', 'budget', 'best seller', 'bestseller', 'delivery', 'shipping', 'checkout',
            'cart', 'buy', 'order', 'custom', 'peg', 'category', 'featured', 'stock', 'available',
            'location', 'address', 'where', 'contact', 'phone', 'email', 'hours', 'open', 'accept',
            'customization', 'customize', 'personalize', 'map', 'pinpoint', 'store',
        ];

        if (collect($keywords)->contains(fn (string $keyword) => Str::contains($normalized, $keyword))) {
            return true;
        }

        $categoryNames = Category::query()->where('is_active', true)->pluck('name');
        if ($categoryNames->contains(fn ($name) => Str::contains($normalized, Str::lower((string) $name)))) {
            return true;
        }

        $productNames = Product::query()->where('is_active', true)->pluck('name');

        return $productNames->contains(fn ($name) => Str::contains($normalized, Str::lower((string) $name)));
    }

    /**
     * Classify the type of question to determine response strategy
     */
    private function classifyQuestion(string $message): string
    {
        $normalized = Str::lower($message);

        // Store info questions
        if (Str::contains($normalized, ['where', 'location', 'address', 'map', 'pin', 'store located'])) {
            return 'store_location';
        }

        if (Str::contains($normalized, ['contact', 'phone', 'call', 'email', 'reach', 'message'])) {
            return 'store_contact';
        }

        if (Str::contains($normalized, ['deliver', 'shipping', 'delivery area', 'do you deliver', 'accept delivery'])) {
            return 'store_delivery';
        }

        if (Str::contains($normalized, ['custom', 'personali', 'design', 'peg', 'customize', 'how to customize'])) {
            return 'store_customization';
        }

        if (Str::contains($normalized, ['open', 'hours', 'when', 'available', 'schedule', 'timing'])) {
            return 'store_hours';
        }

        // Product-related questions
        return 'product_search';
    }

    /**
     * Calculate similarity between two strings using Levenshtein distance
     */
    private function calculateSimilarity(string $str1, string $str2): float
    {
        $len1 = strlen($str1);
        $len2 = strlen($str2);
        $maxLen = max($len1, $len2);

        if ($maxLen === 0) {
            return 1.0;
        }

        $distance = levenshtein($str1, $str2);

        return 1.0 - ($distance / $maxLen);
    }

    private function findRelevantProducts(string $message): Collection
    {
        $normalized = Str::lower($message);
        $budget = $this->extractBudget($message);
        $wantsCheap = Str::contains($normalized, ['cheap', 'affordable', 'budget', 'lowest', 'under', 'below']);
        $wantsBestSeller = Str::contains($normalized, ['best seller', 'bestseller', 'popular', 'top seller']);
        $wantsFeatured = Str::contains($normalized, ['featured', 'recommend', 'recommended', 'suggest']);
        $wantsCustom = Str::contains($normalized, ['custom', 'personalized', 'peg', 'design']);
        $wantsPreorder = Str::contains($normalized, ['preorder', 'pre-order', 'lead time']);
        $wantsAvailableNow = Str::contains($normalized, ['available now', 'ready now', 'same day', 'today']);

        $query = Product::query()
            ->with([
                'category',
                'images',
                'variants' => fn ($query) => $query->where('is_active', true)->orderByDesc('is_default')->orderBy('display_order'),
            ])
            ->where('is_active', true);

        if ($budget !== null) {
            $query->whereRaw('COALESCE(sale_price, price) <= ?', [$budget]);
        }

        if ($wantsCustom) {
            $query->where('allows_customization', true);
        }

        if ($wantsPreorder) {
            $query->where('is_preorder', true);
        }

        if ($wantsAvailableNow) {
            $query->where('is_preorder', false);
        }

        $matchedCategoryIds = Category::query()
            ->where('is_active', true)
            ->get()
            ->filter(fn (Category $category) => Str::contains($normalized, Str::lower($category->name)))
            ->pluck('id')
            ->all();

        if ($matchedCategoryIds !== []) {
            $query->whereIn('category_id', $matchedCategoryIds);
        }

        $terms = collect(preg_split('/[^a-z0-9]+/i', $normalized) ?: [])
            ->map(fn (string $term) => trim($term))
            ->filter(fn (string $term) => strlen($term) >= 3)
            ->reject(fn (string $term) => in_array($term, ['what', 'which', 'with', 'your', 'best', 'cheap', 'price', 'available', 'delivery', 'accept', 'do', 'you', 'have'], true))
            ->values();

        if ($terms->isNotEmpty()) {
            $query->where(function ($query) use ($terms) {
                foreach ($terms as $term) {
                    $query->orWhere('name', 'like', '%' . $term . '%')
                        ->orWhere('description', 'like', '%' . $term . '%');
                }
            });
        }

        if ($wantsBestSeller) {
            $query->orderByDesc('is_best_seller');
        } elseif ($wantsFeatured) {
            $query->orderByDesc('is_featured');
        }

        if ($wantsCheap || $budget !== null) {
            $query->orderByRaw('COALESCE(sale_price, price) asc');
        } else {
            $query->orderByDesc('is_best_seller')
                ->orderByDesc('is_featured')
                ->orderBy('name');
        }

        $products = $query->limit(8)->get();

        // Apply fuzzy matching to improve relevance
        if ($terms->isNotEmpty()) {
            $products = $products->map(function (Product $product) use ($terms) {
                $nameScore = $terms->map(fn (string $term) => $this->calculateSimilarity($term, Str::lower($product->name)))->max() ?? 0;
                $descScore = $terms->map(fn (string $term) => $this->calculateSimilarity($term, Str::lower((string) $product->description)))->max() ?? 0;
                $product->fuzzy_score = max($nameScore, $descScore);

                return $product;
            })->sortByDesc('fuzzy_score')->values();
        }

        if ($products->isEmpty()) {
            $products = Product::query()
                ->with([
                    'category',
                    'images',
                    'variants' => fn ($query) => $query->where('is_active', true)->orderByDesc('is_default')->orderBy('display_order'),
                ])
                ->where('is_active', true)
                ->orderByDesc('is_best_seller')
                ->orderByDesc('is_featured')
                ->orderByRaw('COALESCE(sale_price, price) asc')
                ->limit(4)
                ->get();
        }

        return $products;
    }

    private function extractBudget(string $message): ?float
    {
        if (preg_match('/(?:under|below|max|maximum|less than)\s*₱?\s*(\d+(?:\.\d+)?)/i', $message, $matches)) {
            return (float) $matches[1];
        }

        if (preg_match('/₱\s*(\d+(?:\.\d+)?)/i', $message, $matches)) {
            return (float) $matches[1];
        }

        return null;
    }

    private function generateReply(string $message, array $history, Collection $products, string $questionType): string
    {
        $apiKey = config('services.groq.api_key');

        if (! filled($apiKey)) {
            return $products->isNotEmpty()
                ? 'Here are the BonBon products that best match your request. You can open a product, add it to cart, or go straight to checkout.'
                : 'I could not find an exact BonBon match for that yet, but I can still help with products, prices, delivery, and ordering questions.';
        }

        $store = StoreSetting::query()->first();

        // Build store info section
        $storeInfo = [];
        if ($store) {
            $storeInfo[] = 'Brand: ' . $store->brand_name;
            if ($store->store_description) {
                $storeInfo[] = 'Store Description: ' . $store->store_description;
            }
            if ($store->footer_address) {
                $storeInfo[] = 'Store Location: ' . $store->footer_address;
            }
            if ($store->footer_phone) {
                $storeInfo[] = 'Phone: ' . $store->footer_phone;
            }
            if ($store->footer_email) {
                $storeInfo[] = 'Email: ' . $store->footer_email;
            }
            if ($store->footer_hours) {
                $storeInfo[] = 'Store Hours: ' . $store->footer_hours;
            }
        }

        // Add customization info
        $storeInfo[] = 'Customization: BonBon offers customization services for selected products. Customers can use the customization tab on the product page to personalize their orders.';

        // Build product recommendations only for product search questions
        $productLines = '';
        if ($questionType === 'product_search' && $products->isNotEmpty()) {
            $productLines = $products->map(function (Product $product) {
                $effectivePrice = (float) ($product->sale_price ?? $product->price);

                return implode(' | ', [
                    'name=' . $product->name,
                    'category=' . ($product->category?->name ?? 'Uncategorized'),
                    'price=' . number_format($effectivePrice, 2, '.', ''),
                    'best_seller=' . ($product->is_best_seller ? 'yes' : 'no'),
                    'preorder=' . ($product->is_preorder ? 'yes' : 'no'),
                    'customizable=' . ($product->allows_customization ? 'yes' : 'no'),
                    'stock=' . $product->stock_quantity,
                    'description=' . Str::limit((string) ($product->description ?? ''), 140),
                ]);
            })->implode("\n");
        }

        $systemPrompt = match ($questionType) {
            'store_location' => 'You are BonBon AI. Answer ONLY about store location. Be concise and helpful. Only provide the address information given. Do NOT recommend products.',
            'store_contact' => 'You are BonBon AI. Answer ONLY contact information questions. Provide phone, email, or other contact details. Be concise. Do NOT recommend products.',
            'store_delivery' => 'You are BonBon AI. Answer questions about delivery and shipping. Be clear and concise. Do NOT recommend products unless directly asked.',
            'store_customization' => 'You are BonBon AI. Answer questions about customization services. Guide customers to use the customization tab on product pages. Be helpful and clear. Do NOT recommend unrelated products.',
            'store_hours' => 'You are BonBon AI. Answer questions about store hours and availability. Be concise and helpful. Do NOT recommend products.',
            default => 'You are BonBon AI, a store-only shopping assistant for BonBon PH. Answer only with store-related information. Be concise, helpful, and natural. Only recommend products if relevant to the question. Use only the provided context.',
        };

        $messages = [['role' => 'system', 'content' => $systemPrompt]];

        foreach (array_slice($history, -6) as $item) {
            if (! is_array($item)) {
                continue;
            }

            $role = in_array(($item['role'] ?? ''), ['user', 'assistant', 'system'], true) ? $item['role'] : 'user';
            $content = trim((string) ($item['content'] ?? ''));

            if ($content !== '') {
                $messages[] = compact('role', 'content');
            }
        }

        $contextParts = array_merge($storeInfo, [
            'User question: ' . $message,
        ]);

        if ($productLines !== '') {
            $contextParts[] = "Relevant products:\n" . $productLines;
        }

        $messages[] = [
            'role' => 'user',
            'content' => trim(implode("\n", $contextParts)),
        ];

        $response = Http::timeout(20)
            ->acceptJson()
            ->withToken($apiKey)
            ->post(rtrim((string) config('services.groq.base_url'), '/') . '/chat/completions', [
                'model' => config('services.groq.model'),
                'messages' => $messages,
                'temperature' => 0.3,
            ]);

        if (! $response->successful()) {
            return $products->isNotEmpty()
                ? 'Here are the BonBon products that best match your request. You can open a product, add it to cart, or go straight to checkout.'
                : 'I could not find an exact BonBon match for that yet, but I can still help with products, prices, delivery, store location, contact info, and ordering questions.';
        }

        $content = trim((string) data_get($response->json(), 'choices.0.message.content', ''));

        return $content !== '' ? $content : 'I can help with BonBon products, prices, categories, store information, and ordering.';
    }

    private function serializeProduct(Product $product): array
    {
        $defaultVariant = $product->variants->firstWhere('is_default', true) ?? $product->variants->first();
        $effectivePrice = (float) ($product->sale_price ?? $product->price);
        $image = $product->images->firstWhere('is_primary', true)?->image_url
            ?? $product->images->first()?->image_url
            ?? $product->main_image_url;

        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'category' => $product->category?->name,
            'description' => Str::limit((string) ($product->description ?: 'Freshly baked and ready for your celebration.'), 110),
            'formatted_price' => number_format($effectivePrice, 2),
            'image_url' => $image,
            'product_url' => route('products.show', $product->slug),
            'checkout_url' => route('checkout.index'),
            'add_to_cart_url' => route('cart.add'),
            'product_id' => $product->id,
            'variant_id' => $defaultVariant?->id,
        ];
    }
}
