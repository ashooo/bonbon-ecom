<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderHistoryController extends Controller
{
    private function parseGuestOrderNumbers(Request $request): array
    {
        $raw = $request->cookie('guest_orders', '[]');
        $decoded = json_decode((string) $raw, true);

        if (! is_array($decoded)) {
            return [];
        }

        $normalized = array_map(
            fn ($value) => strtoupper(trim((string) $value)),
            $decoded
        );

        $filtered = array_values(array_filter($normalized, fn ($value) => $value !== ''));

        return array_slice(array_values(array_unique($filtered)), 0, 20);
    }

    private function attachGuestOrdersCookie($response, array $orderNumbers)
    {
        $payload = json_encode(array_values(array_unique($orderNumbers)));

        return $response->cookie('guest_orders', $payload, 60 * 24 * 180);
    }

    public function index(Request $request)
    {
        if (Auth::check()) {
            $orders = Auth::user()->orders()->latest()->get();

            return view('pages.orders', [
                'orders' => $orders,
                'isGuestView' => false,
            ]);
        }

        $orderNumbers = $this->parseGuestOrderNumbers($request);
        $orders = collect();

        if (! empty($orderNumbers)) {
            $orders = Order::query()
                ->whereNull('user_id')
                ->whereIn('order_number', $orderNumbers)
                ->latest()
                ->get();
        }

        return view('pages.orders', [
            'orders' => $orders,
            'isGuestView' => true,
        ]);
    }

    public function lookup(Request $request)
    {
        $validated = $request->validate([
            'order_number' => 'required|string|max:30',
            'customer_email' => 'required|email|max:100',
        ]);

        $orderNumber = strtoupper(trim($validated['order_number']));
        $email = trim($validated['customer_email']);

        $order = Order::query()
            ->where('order_number', $orderNumber)
            ->where('customer_email', $email)
            ->first();

        if (! $order) {
            return redirect()->route('orders.index')->withErrors([
                'order_lookup' => 'Order not found. Check the order number and email you used at checkout.',
            ])->withInput();
        }

        $orderNumbers = $this->parseGuestOrderNumbers($request);
        array_unshift($orderNumbers, $orderNumber);
        $orderNumbers = array_slice(array_values(array_unique($orderNumbers)), 0, 20);

        $response = redirect()->route('orders.index')->with('success', 'Order found and saved to your guest order history.');

        return $this->attachGuestOrdersCookie($response, $orderNumbers);
    }
}
