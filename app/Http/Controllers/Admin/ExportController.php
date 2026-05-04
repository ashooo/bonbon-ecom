<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function orders(Request $request): StreamedResponse
    {
        $filters = $this->validatedExportFilters($request);
        $filename = 'orders-export-' . $filters['start']->format('Ymd') . '-' . $filters['end']->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($filters): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Order Number',
                'Order Date',
                'Customer Name',
                'Customer Email',
                'Customer Phone',
                'Order Type',
                'Fulfillment Date',
                'Fulfillment Time',
                'Status',
                'Payment Status',
                'Product',
                'Variant',
                'SKU',
                'Quantity',
                'Unit Price',
                'Item Subtotal',
                'Order Subtotal',
                'Delivery Fee',
                'Order Total',
                'Delivery Address',
                'Special Instructions',
            ]);

            Order::query()
                ->with(['items.variant.product'])
                ->whereBetween('created_at', [$filters['start'], $filters['end']])
                ->orderBy('created_at')
                ->chunk(200, function ($orders) use ($handle): void {
                    foreach ($orders as $order) {
                        if ($order->items->isEmpty()) {
                            $this->writeOrderRow($handle, $order);
                            continue;
                        }

                        foreach ($order->items as $item) {
                            $this->writeOrderRow($handle, $order, $item);
                        }
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function reports(Request $request): StreamedResponse
    {
        $filters = $this->validatedExportFilters($request);
        $groupBy = $request->validate([
            'group_by' => 'nullable|in:day,month',
        ])['group_by'] ?? 'day';

        $filename = 'reports-export-' . $groupBy . '-' . $filters['start']->format('Ymd') . '-' . $filters['end']->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($filters, $groupBy): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Period',
                'Orders',
                'Pending',
                'Confirmed',
                'Ready',
                'Completed',
                'Cancelled',
                'Subtotal',
                'Delivery Fees',
                'Total Revenue',
                'Average Order Value',
            ]);

            $periodExpression = $groupBy === 'month'
                ? "DATE_FORMAT(created_at, '%Y-%m')"
                : 'DATE(created_at)';

            $rows = Order::query()
                ->selectRaw($periodExpression . ' as period')
                ->selectRaw('COUNT(*) as orders_count')
                ->selectRaw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count")
                ->selectRaw("SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_count")
                ->selectRaw("SUM(CASE WHEN status = 'ready' THEN 1 ELSE 0 END) as ready_count")
                ->selectRaw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count")
                ->selectRaw("SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_count")
                ->selectRaw('SUM(subtotal) as subtotal_sum')
                ->selectRaw('SUM(delivery_fee) as delivery_fee_sum')
                ->selectRaw('SUM(total) as total_sum')
                ->whereBetween('created_at', [$filters['start'], $filters['end']])
                ->groupBy(DB::raw($periodExpression))
                ->orderBy('period')
                ->get();

            foreach ($rows as $row) {
                $ordersCount = (int) $row->orders_count;
                $total = (float) $row->total_sum;

                fputcsv($handle, [
                    $row->period,
                    $ordersCount,
                    (int) $row->pending_count,
                    (int) $row->confirmed_count,
                    (int) $row->ready_count,
                    (int) $row->completed_count,
                    (int) $row->cancelled_count,
                    number_format((float) $row->subtotal_sum, 2, '.', ''),
                    number_format((float) $row->delivery_fee_sum, 2, '.', ''),
                    number_format($total, 2, '.', ''),
                    number_format($ordersCount > 0 ? $total / $ordersCount : 0, 2, '.', ''),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function validatedExportFilters(Request $request): array
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        return [
            'start' => Carbon::parse($validated['start_date'])->startOfDay(),
            'end' => Carbon::parse($validated['end_date'])->endOfDay(),
        ];
    }

    private function writeOrderRow($handle, Order $order, $item = null): void
    {
        $variant = $item?->variant;
        $product = $variant?->product;

        fputcsv($handle, [
            $order->order_number,
            $order->created_at?->format('Y-m-d H:i:s'),
            $order->customer_name,
            $order->customer_email,
            $order->customer_phone,
            $order->order_type,
            $order->fulfillment_date?->format('Y-m-d'),
            $order->fulfillment_time,
            $order->status,
            $order->payment_status,
            $product?->name,
            $variant?->name,
            $variant?->sku,
            $item ? (int) $item->quantity : 0,
            $item ? number_format((float) $item->unit_price, 2, '.', '') : '0.00',
            $item ? number_format((float) $item->subtotal, 2, '.', '') : '0.00',
            number_format((float) $order->subtotal, 2, '.', ''),
            number_format((float) $order->delivery_fee, 2, '.', ''),
            number_format((float) $order->total, 2, '.', ''),
            $order->delivery_address,
            $order->special_instructions,
        ]);
    }
}
