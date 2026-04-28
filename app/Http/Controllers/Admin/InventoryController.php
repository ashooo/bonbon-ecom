<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Models\Variant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    private function buildInventoryRedirectQuery(Request $request): array
    {
        return array_filter([
            'section' => 'inventory',
            'inventory_search' => $request->string('inventory_search')->trim()->value(),
            'inventory_status' => $request->string('inventory_status')->value(),
            'inventory_page' => $request->integer('inventory_page') ?: null,
        ], fn ($value) => $value !== null && $value !== '');
    }

    public function index(Request $request): RedirectResponse
    {
        return redirect()->route('admin.dashboard', $this->buildInventoryRedirectQuery($request));
    }

    public function adjust(Request $request, Variant $variant): RedirectResponse
    {
        $data = $request->validate([
            'action' => 'required|in:add,subtract,set',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        $previous = (int) $variant->stock_quantity;
        $quantity = (int) $data['quantity'];

        $newStock = match ($data['action']) {
            'add' => $previous + $quantity,
            'subtract' => max(0, $previous - $quantity),
            'set' => $quantity,
        };

        $delta = $newStock - $previous;

        if ($delta === 0) {
            return redirect()
                ->route('admin.dashboard', $this->buildInventoryRedirectQuery($request))
                ->with('success', 'No stock change was applied.');
        }

        $variant->update(['stock_quantity' => $newStock]);

        InventoryMovement::create([
            'variant_id' => $variant->id,
            'product_id' => $variant->product_id,
            'acted_by_user_id' => $request->user()->id,
            'type' => 'manual_adjustment',
            'quantity_change' => $delta,
            'previous_stock' => $previous,
            'new_stock' => $newStock,
            'reason' => $data['reason'] ? trim($data['reason']) : null,
        ]);

        return redirect()
            ->route('admin.dashboard', $this->buildInventoryRedirectQuery($request))
            ->with('success', 'Stock updated successfully.');
    }
}
