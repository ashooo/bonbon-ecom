<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use App\Support\CustomizationPricing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->validate([
            'brand_name' => 'required|string|max:255',
            'chat_display_name' => 'nullable|string|max:255',
            'hero_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'chat_avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'store_description' => 'nullable|string|max:2000',
            'footer_email' => 'nullable|email|max:255',
            'footer_phone' => 'nullable|string|max:255',
            'footer_address' => 'nullable|string|max:255',
            'footer_hours' => 'nullable|string|max:1000',
            'copyright_text' => 'nullable|string|max:255',
        ]);

        $settings = StoreSetting::query()->firstOrCreate([], [
            'brand_name' => 'BonBon PH',
        ]);

        if ($request->hasFile('hero_image')) {
            if ($settings->hero_image) {
                Storage::disk('public')->delete($settings->hero_image);
            }

            $data['hero_image'] = $request->file('hero_image')->store('settings', 'public');
        }

        if ($request->hasFile('chat_avatar')) {
            if ($settings->chat_avatar) {
                Storage::disk('public')->delete($settings->chat_avatar);
            }

            $data['chat_avatar'] = $request->file('chat_avatar')->store('settings', 'public');
        }

        $settings->update($data);

        return redirect()
            ->route('admin.dashboard', ['section' => 'settings'])
            ->with('success', 'Settings updated successfully.');
    }

    public function updateCustomizationPricing(Request $request)
    {
        $request->validate([
            'customization_pricing' => 'nullable|array',
        ]);

        $settings = StoreSetting::query()->firstOrCreate([], [
            'brand_name' => 'BonBon PH',
        ]);

        $settings->update([
            'customization_pricing' => $this->sanitizeCustomizationPricing(
                (array) $request->input('customization_pricing', [])
            ),
        ]);

        return redirect()
            ->route('admin.dashboard', ['section' => 'customization'])
            ->with('success', 'Customization pricing updated successfully.');
    }

    public function updateShopFees(Request $request)
    {
        $data = $request->validate([
            'delivery_fee' => 'required|numeric|min:0|max:999999.99',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'service_fee' => 'required|numeric|min:0|max:999999.99',
        ]);

        $settings = StoreSetting::query()->firstOrCreate([], [
            'brand_name' => 'BonBon PH',
        ]);

        $settings->update([
            'delivery_fee' => round((float) $data['delivery_fee'], 2),
            'tax_rate' => round((float) $data['tax_rate'], 2),
            'service_fee' => round((float) $data['service_fee'], 2),
        ]);

        return redirect()
            ->route('admin.dashboard', ['section' => 'shop-fees'])
            ->with('success', 'Shop fees updated successfully.');
    }

    private function sanitizeCustomizationPricing(array $raw): array
    {
        $defaults = CustomizationPricing::defaults();
        $sanitized = [];

        foreach ($defaults as $group => $options) {
            $sanitized[$group] = [];
            $groupRaw = is_array($raw[$group] ?? null) ? $raw[$group] : [];
            foreach ($options as $key => $defaultValue) {
                $candidate = $groupRaw[$key] ?? null;
                $sanitized[$group][$key] = is_numeric($candidate)
                    ? max(0, (float) $candidate)
                    : (float) $defaultValue;
            }
        }

        return $sanitized;
    }
}
