<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderHistoryController extends Controller
{
    public function __construct(private InvoiceService $invoiceService) {}

    private function isOrderCancellable(string $status): bool
    {
        return in_array(strtolower($status), ['pending', 'confirmed'], true);
    }

    private function canGuestAccessOrder(Request $request, Order $order): bool
    {
        if ($order->user_id !== null) {
            return false;
        }

        $guestOrderNumbers = $this->parseGuestOrderNumbers($request);

        return in_array($order->order_number, $guestOrderNumbers, true);
    }

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
            $orders = Auth::user()->orders()->with('invoice')->latest()->get();

            return view('pages.orders', [
                'orders' => $orders,
                'isGuestView' => false,
            ]);
        }

        $orderNumbers = $this->parseGuestOrderNumbers($request);
        $orders = collect();

        if (! empty($orderNumbers)) {
            $orders = Order::query()
                ->with('invoice')
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

    public function show(Request $request, Order $order)
    {
        if (Auth::check()) {
            if ((int) $order->user_id !== (int) Auth::id()) {
                return redirect()->route('orders.index')->withErrors([
                    'order_view' => 'You are not allowed to view this order.',
                ]);
            }
        } elseif (! $this->canGuestAccessOrder($request, $order)) {
            return redirect()->route('orders.index')->withErrors([
                'order_view' => 'Please find the order first before viewing details.',
            ]);
        }

        $order->load(['items.variant.product', 'invoice', 'statusHistory']);

        return view('pages.orders-show', [
            'order' => $order,
            'isGuestView' => ! Auth::check(),
        ]);
    }

    public function receipt(Request $request, Order $order)
    {
        if (Auth::check()) {
            if ((int) $order->user_id !== (int) Auth::id()) {
                return redirect()->route('orders.index')->withErrors([
                    'order_receipt' => 'You are not allowed to view this receipt.',
                ]);
            }
        } elseif (! $this->canGuestAccessOrder($request, $order)) {
            return redirect()->route('orders.index')->withErrors([
                'order_receipt' => 'Please find the order first before viewing receipt.',
            ]);
        }

        $order->load(['items.variant.product', 'invoice']);
        $invoiceHtml = $this->invoiceService->renderInvoiceHtml($order);

        return view('pages.receipt', [
            'order' => $order,
            'invoiceHtml' => $invoiceHtml,
        ]);
    }

    public function receiptHtml(Request $request, Order $order)
    {
        if (Auth::check()) {
            if ((int) $order->user_id !== (int) Auth::id()) {
                abort(403);
            }
        } elseif (! $this->canGuestAccessOrder($request, $order)) {
            abort(403);
        }

        $order->load(['items.variant.product', 'invoice']);
        $invoiceHtml = $this->invoiceService->renderInvoiceHtml($order);

        return response($invoiceHtml, 200)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->header('X-Frame-Options', 'SAMEORIGIN');
    }

    public function receiptPdf(Request $request, Order $order)
    {
        if (Auth::check()) {
            if ((int) $order->user_id !== (int) Auth::id()) {
                abort(403);
            }
        } elseif (! $this->canGuestAccessOrder($request, $order)) {
            abort(403);
        }

        $order->loadMissing('invoice');
        $invoice = $order->invoice;
        if (! $invoice) {
            abort(404, 'Invoice not found');
        }

        $path = $this->invoiceService->ensureInvoicePdf($invoice, true);
        if (! Storage::disk('local')->exists($path)) {
            abort(404, 'Receipt file not found');
        }

        $invoice->incrementPrintCount();

        return Storage::disk('local')->download($path, 'receipt-' . $order->order_number . '.pdf');
    }

    public function cancel(Request $request, Order $order)
    {
        if (Auth::check()) {
            if ((int) $order->user_id !== (int) Auth::id()) {
                return redirect()->route('orders.index')->withErrors([
                    'order_cancel' => 'Unauthorized order cancellation request.',
                ]);
            }
        } else {
            $request->validate([
                'customer_email' => 'required|email|max:100',
            ]);

            if ($order->user_id !== null) {
                return redirect()->route('orders.index')->withErrors([
                    'order_cancel' => 'This order cannot be cancelled from guest view.',
                ]);
            }

            $email = strtolower(trim($request->string('customer_email')->value()));
            $orderEmail = strtolower(trim((string) $order->customer_email));

            if ($email !== $orderEmail) {
                return redirect()->route('orders.index')->withErrors([
                    'order_cancel' => 'Email does not match the order checkout email.',
                ]);
            }

            if (! $this->canGuestAccessOrder($request, $order)) {
                return redirect()->route('orders.index')->withErrors([
                    'order_cancel' => 'Please find the order first before cancellation.',
                ]);
            }
        }

        if (! $this->isOrderCancellable((string) $order->status)) {
            return redirect()->route('orders.index')->withErrors([
                'order_cancel' => 'Order cannot be cancelled once it is preparing, ready, completed, or already cancelled.',
            ]);
        }

        DB::transaction(function () use ($order): void {
            $order->update(['status' => 'cancelled']);

            if (! $order->stock_deducted_at) {
                return;
            }

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
                    'acted_by_user_id' => Auth::id(),
                    'type' => 'order_restore',
                    'quantity_change' => (int) $item->quantity,
                    'previous_stock' => $previousStock,
                    'new_stock' => $newStock,
                    'reason' => 'Cancelled order ' . $order->order_number . ' from web orders page',
                ]);
            }

            $order->update(['stock_deducted_at' => null]);
        });

        return redirect()->route('orders.index')->with('success', 'Order ' . $order->order_number . ' cancelled successfully.');
    }
}
