<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        $query = array_filter([
            'section' => 'orders',
            'status' => $request->string('status')->value(),
            'search' => $request->string('search')->value(),
            'page' => $request->integer('page') ?: null,
        ], fn ($value) => $value !== null && $value !== '');

        return redirect()->route('admin.dashboard', $query);
    }

    public function show(Request $request, Order $order)
    {
        $order->load(['items.variant.product', 'user', 'invoice']);

        $backQuery = array_filter([
            'section' => 'orders',
            'status' => $request->string('status')->value(),
            'search' => $request->string('search')->value(),
            'page' => $request->integer('page') ?: null,
        ], fn ($value) => $value !== null && $value !== '');

        return view('admin.orders.show', compact('order', 'backQuery'));
    }

    public function printSlip(Order $order): Response
    {
        $order->load(['items.variant.product', 'user']);

        return response()
            ->view('admin.orders.print-slip', compact('order'))
            ->header('Content-Disposition', 'inline; filename="slip-' . $order->order_number . '.html"');
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => 'required|in:pending,confirmed,ready,completed,cancelled',
        ]);

        $previousStatus = $order->status;

        $order->update([
            'status' => $data['status'],
        ]);

        if ($previousStatus !== 'cancelled' && $data['status'] === 'cancelled' && $order->stock_deducted_at) {
            $order->loadMissing('items.variant');

            foreach ($order->items as $item) {
                if (! $item->variant) {
                    continue;
                }

                $previousStock = (int) $item->variant->stock_quantity;
                $newStock = $previousStock + (int) $item->quantity;

                $item->variant->update(['stock_quantity' => $newStock]);

                InventoryMovement::create([
                    'variant_id' => $item->variant->id,
                    'product_id' => $item->variant->product_id,
                    'acted_by_user_id' => $request->user()->id,
                    'type' => 'order_restore',
                    'quantity_change' => (int) $item->quantity,
                    'previous_stock' => $previousStock,
                    'new_stock' => $newStock,
                    'reason' => 'Admin cancelled order ' . $order->order_number,
                ]);
            }

            $order->update(['stock_deducted_at' => null]);
        }

        if ($order->user_id && $previousStatus !== $data['status']) {
            UserNotification::create([
                'user_id' => $order->user_id,
                'type' => 'order_status',
                'title' => 'Order status updated',
                'body' => 'Your order ' . $order->order_number . ' is now ' . ucfirst($data['status']) . '.',
                'url' => route('profile') . '#order-history',
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'previous_status' => $previousStatus,
                    'current_status' => $data['status'],
                ],
            ]);
        }

        $query = array_filter([
            'section' => 'orders',
            'status' => $request->string('redirect_status')->value(),
            'search' => $request->string('redirect_search')->value(),
            'page' => $request->integer('redirect_page') ?: null,
        ], fn ($value) => $value !== null && $value !== '');

        return redirect()
            ->route('admin.dashboard', $query)
            ->with('success', 'Order ' . $order->order_number . ' status updated to ' . ucfirst($data['status']) . '.');
    }
}
