<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    private ?string $guestCartToken = null;

    private function resolveGuestCartToken(Request $request): string
    {
        $token = trim((string) ($request->cookie('cart_token') ?? ''));

        return $token !== '' ? $token : Str::random(40);
    }

    private function getCart(Request $request): Cart
    {
        if (Auth::check()) {
            $this->guestCartToken = null;

            return Auth::user()->getOrCreateCart()->load('items.product', 'items.variant');
        }

        $this->guestCartToken = $this->resolveGuestCartToken($request);

        return Cart::firstOrCreate(
            ['session_id' => $this->guestCartToken],
            ['user_id' => null, 'expires_at' => now()->addDays(30)]
        )->load('items.product', 'items.variant');
    }

    public function index(Request $request)
    {
        $cart = $this->getCart($request);
        $items = $cart->items;
        $subtotal = $cart->subtotal;
        $delivery = 5.99;
        $tax = $subtotal * 0.1;
        $total = $subtotal + $delivery + $tax;

        $response = response()->view('pages.checkout', compact('cart', 'items', 'subtotal', 'delivery', 'tax', 'total'));

        if ($this->guestCartToken) {
            $response->cookie('cart_token', $this->guestCartToken, 60 * 24 * 30);
        }

        return $response;
    }
}
