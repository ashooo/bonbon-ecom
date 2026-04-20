<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
        $order->load(['items.variant.product', 'user']);

        $backQuery = array_filter([
            'section' => 'orders',
            'status' => $request->string('status')->value(),
            'search' => $request->string('search')->value(),
            'page' => $request->integer('page') ?: null,
        ], fn ($value) => $value !== null && $value !== '');

        return view('admin.orders.show', compact('order', 'backQuery'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => 'required|in:pending,confirmed,ready,completed,cancelled',
        ]);

        $order->update([
            'status' => $data['status'],
        ]);

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
