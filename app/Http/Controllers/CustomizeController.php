<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StoreSetting;
use App\Support\CustomizationPricing;
use Illuminate\Http\Request;

class CustomizeController extends Controller
{
    public function index(Request $request)
    {
        if (! $request->boolean('desktop') && $this->isAndroidRequest($request)) {
            return redirect()->route('customize.android');
        }

        return $this->renderCustomizeView('pages.customize');
    }

    public function android()
    {
        return $this->renderCustomizeView('pages.customize-android');
    }

    private function renderCustomizeView(string $view)
    {
        $products = Product::query()
            ->with(['variants' => fn ($query) => $query->where('is_active', true)->orderBy('display_order')])
            ->where('is_active', true)
            ->where('allows_customization', true)
            ->orderBy('name')
            ->get();

        if ($products->isEmpty()) {
            $products = Product::query()
                ->with(['variants' => fn ($query) => $query->where('is_active', true)->orderBy('display_order')])
                ->where('is_active', true)
                ->orderBy('name')
                ->take(12)
                ->get();
        }

        $settings = StoreSetting::query()->first();
        $customizationPricing = CustomizationPricing::mergeWithDefaults($settings?->customization_pricing);

        return view($view, compact('products', 'customizationPricing'));
    }

    private function isAndroidRequest(Request $request): bool
    {
        $userAgent = strtolower((string) $request->userAgent());

        return str_contains($userAgent, 'android');
    }
}
