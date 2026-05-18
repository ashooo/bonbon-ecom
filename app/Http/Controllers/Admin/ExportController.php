<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\StoreSetting;
use Symfony\Component\HttpFoundation\Response;

class ExportController extends Controller
{
    public function orders(Request $request): Response
    {
        $filters = $this->validatedExportFilters($request);
        $format = $this->validatedFormat($request);
        $headings = $this->orderHeadings();
        $rows = $this->orderRows($filters);
        $basename = 'orders-export-' . $filters['start']->format('Ymd') . '-' . $filters['end']->format('Ymd');

        return $this->downloadRows($headings, $rows, $basename, $format, 'Orders Export');
    }

    public function reports(Request $request): Response
    {
        $filters = $this->validatedExportFilters($request);
        $format = $this->validatedFormat($request);
        $reportType = $request->validate(['report_type' => 'required|in:sales,product'])['report_type'];
        $groupBy = $request->validate([
            'group_by' => 'nullable|in:day,week,month,year',
        ])['group_by'] ?? 'day';

        if ($reportType === 'sales') {
            $periodExpression = match ($groupBy) {
                'month' => "DATE_FORMAT(created_at, '%Y-%m')",
                'year' => "DATE_FORMAT(created_at, '%Y')",
                'week' => "DATE_FORMAT(created_at, '%x-W%v')",
                default => 'DATE(created_at)',
            };

            $query = Order::query()
                ->selectRaw($periodExpression . ' as period')
                ->selectRaw('COUNT(*) as orders_count')
                ->selectRaw('SUM(subtotal) as subtotal_sum')
                ->selectRaw('SUM(delivery_fee) as delivery_fee_sum')
                ->selectRaw('SUM(total) as total_sum')
                ->selectRaw("SUM(CASE WHEN payment_status = 'refunded' THEN total ELSE 0 END) as refunded_sum")
                ->selectRaw("SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_count")
                ->whereBetween('created_at', [$filters['start'], $filters['end']])
                ->groupBy(DB::raw($periodExpression))
                ->orderBy('period')
                ->get();

            $headings = [
                'Period',
                'Total Revenue',
                'Total Orders',
                'Average Order Value',
                'Discounts Used',
                'Refunded Amount',
                'Net Sales',
            ];

            $rows = $query->map(function ($row) {
                $ordersCount = (int) $row->orders_count;
                $total = (float) $row->total_sum;
                $refunded = (float) $row->refunded_sum;
                $net = $total - $refunded;

                return [
                    $row->period,
                    number_format($total, 2, '.', ''),
                    $ordersCount,
                    number_format($ordersCount > 0 ? $total / $ordersCount : 0, 2, '.', ''),
                    '0.00', // Discounts not tracked per order in current schema
                    number_format($refunded, 2, '.', ''),
                    number_format($net, 2, '.', ''),
                ];
            })->all();

            // overall totals for summary
            $totalRevenue = (float) $query->sum(fn($r) => (float) $r->total_sum);
            $totalOrders = (int) $query->sum(fn($r) => (int) $r->orders_count);

            // active customers: count distinct customer_email within period for completed/confirmed/ready
            $activeCustomers = Order::query()
                ->whereBetween('created_at', [$filters['start'], $filters['end']])
                ->whereIn('status', ['confirmed', 'ready', 'completed'])
                ->distinct()
                ->count('customer_email');

            $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0.0;

            // Build matrix for export (header, summary, blank, trend table)
            $matrix = [];
            $matrix[] = ['Report Type', 'Sales Report'];
            $matrix[] = ['Start Date', $filters['start']->toDateString()];
            $matrix[] = ['End Date', $filters['end']->toDateString()];
            $matrix[] = ['Group By', ucfirst($groupBy)];
            $matrix[] = ['Generated', now()->toDateTimeString()];
            $matrix[] = [];
            $matrix[] = ['Sales Summary'];
            $matrix[] = ['Total Revenue', number_format($totalRevenue, 2, '.', '')];
            $matrix[] = ['Total Orders', $totalOrders];
            $matrix[] = ['Active Customers', $activeCustomers];
            $matrix[] = ['Average Order Value', number_format($averageOrderValue, 2, '.', '')];
            $matrix[] = ['Discounts Used', '0.00'];
            $matrix[] = ['Refunded Amount', number_format((float) $query->sum(fn($r) => (float) $r->refunded_sum), 2, '.', '')];
            $matrix[] = ['Net Sales', number_format($totalRevenue - (float) $query->sum(fn($r) => (float) $r->refunded_sum), 2, '.', '')];
            $matrix[] = [];
            $matrix[] = ['Revenue Trend'];
            $matrix[] = ['Period', 'Orders', 'Revenue'];

            foreach ($rows as $r) {
                $matrix[] = [$r[0], $r[2], $r[1]];
            }

            $basename = 'sales-report-' . $groupBy . '-' . $filters['start']->format('Ymd') . '-' . $filters['end']->format('Ymd');

            // Export according to format
            if ($format === 'csv') {
                return $this->streamCsvMatrix($matrix, $basename);
            }

            if ($format === 'excel') {
                return $this->downloadExcelHtml($matrix, $basename, 'Sales Report');
            }

            if ($format === 'pdf') {
                $settings = StoreSetting::query()->first();
                $logoPath = $settings && $settings->chat_avatar ? public_path('storage/' . $settings->chat_avatar) : public_path('images/logo.png');

                $pdf = Pdf::loadView('admin.reports.pdf-sales', [
                    'matrix' => $matrix,
                    'title' => 'Sales Report',
                    'logoPath' => $logoPath,
                ])->setPaper('a4', 'landscape');

                return $pdf->download($basename . '.pdf');
            }

            return $this->downloadPdfFromMatrix($matrix, $basename, 'Sales Report');
        }

        // Product sales report
        $items = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('product_variants as variants', 'variants.id', '=', 'order_items.variant_id')
            ->join('products', 'products.id', '=', 'variants.product_id')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->whereBetween('orders.created_at', [$filters['start'], $filters['end']])
            ->where('orders.status', '<>', 'cancelled')
            ->selectRaw('products.id as product_id, products.name as product_name, categories.name as category_name, SUM(order_items.quantity) as quantity_sold, COUNT(DISTINCT orders.id) as orders_count, SUM(order_items.subtotal) as revenue_generated, AVG(order_items.subtotal / NULLIF(order_items.quantity,0)) as avg_price')
            ->groupBy('products.id', 'products.name', 'categories.name')
            ->orderByDesc('quantity_sold')
            ->get();

        $matrix = [];
        $matrix[] = ['Report Type', 'Product Sales Report'];
        $matrix[] = ['Start Date', $filters['start']->toDateString()];
        $matrix[] = ['End Date', $filters['end']->toDateString()];
        $matrix[] = ['Group By', ucfirst($groupBy)];
        $matrix[] = ['Generated', now()->toDateTimeString()];
        $matrix[] = [];
        $matrix[] = ['Products'];
        $matrix[] = ['Product Name', 'Category', 'Qty Sold', 'Orders Count', 'Revenue', 'Avg. Price'];

        foreach ($items as $it) {
            $matrix[] = [
                $it->product_name,
                $it->category_name ?? '',
                (int) $it->quantity_sold,
                (int) $it->orders_count,
                number_format((float) $it->revenue_generated, 2, '.', ''),
                number_format((float) $it->avg_price, 2, '.', ''),
            ];
        }

        // summary
        $most = $items->first();
        $low = $items->sortBy('quantity_sold')->take(5);

        $matrix[] = [];
        if ($most) {
            $matrix[] = ['Most Ordered Product', $most->product_name, (int) $most->quantity_sold];
        }

        if ($low && $low->isNotEmpty()) {
            $matrix[] = [];
            $matrix[] = ['Low Performing Products', '', ''];
            foreach ($low as $l) {
                $matrix[] = [$l->product_name, (int) $l->quantity_sold, number_format((float) $l->revenue_generated, 2, '.', '')];
            }
        }

        $basename = 'product-sales-report-' . $filters['start']->format('Ymd') . '-' . $filters['end']->format('Ymd');

        if ($format === 'csv') {
            return $this->streamCsvMatrix($matrix, $basename);
        }

        if ($format === 'excel') {
            return $this->downloadExcelHtml($matrix, $basename, 'Product Sales Report');
        }

        if ($format === 'pdf') {
            $settings = StoreSetting::query()->first();
            $logoPath = $settings && $settings->chat_avatar ? public_path('storage/' . $settings->chat_avatar) : public_path('images/logo.png');

            $pdf = Pdf::loadView('admin.reports.pdf-product', [
                'matrix' => $matrix,
                'title' => 'Product Sales Report',
                'logoPath' => $logoPath,
            ])->setPaper('a4', 'landscape');

            return $pdf->download($basename . '.pdf');
        }

        return $this->downloadPdfFromMatrix($matrix, $basename, 'Product Sales Report');
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

    private function validatedFormat(Request $request): string
    {
        return $request->validate([
            'format' => 'nullable|in:csv,excel,pdf',
        ])['format'] ?? 'csv';
    }

    private function orderHeadings(): array
    {
        return [
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
        ];
    }

    private function reportHeadings(): array
    {
        return array_values($this->reportFields());
    }

    private function reportFields(): array
    {
        return [
            'period' => 'Period',
            'orders_count' => 'Orders',
            'pending_count' => 'Pending',
            'confirmed_count' => 'Confirmed',
            'ready_count' => 'Ready',
            'completed_count' => 'Completed',
            'cancelled_count' => 'Cancelled',
            'subtotal_sum' => 'Subtotal',
            'delivery_fee_sum' => 'Delivery Fees',
            'total_sum' => 'Total Revenue',
            'average_order_value' => 'Average Order Value',
        ];
    }

    private function orderRows(array $filters): array
    {
        $rows = [];

        Order::query()
            ->with(['items.variant.product'])
            ->whereBetween('created_at', [$filters['start'], $filters['end']])
            ->orderBy('created_at')
            ->chunk(200, function ($orders) use (&$rows): void {
                foreach ($orders as $order) {
                    if ($order->items->isEmpty()) {
                        $rows[] = $this->formatOrderRow($order);
                        continue;
                    }

                    foreach ($order->items as $item) {
                        $rows[] = $this->formatOrderRow($order, $item);
                    }
                }
            });

        return $rows;
    }

    private function reportRows(array $filters, string $groupBy): array
    {
        $periodExpression = $groupBy === 'month'
            ? "DATE_FORMAT(created_at, '%Y-%m')"
            : 'DATE(created_at)';

        return Order::query()
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
            ->get()
            ->map(function ($row): array {
                $ordersCount = (int) $row->orders_count;
                $total = (float) $row->total_sum;

                return [
                    'period' => $row->period,
                    'orders_count' => $ordersCount,
                    'pending_count' => (int) $row->pending_count,
                    'confirmed_count' => (int) $row->confirmed_count,
                    'ready_count' => (int) $row->ready_count,
                    'completed_count' => (int) $row->completed_count,
                    'cancelled_count' => (int) $row->cancelled_count,
                    'subtotal_sum' => number_format((float) $row->subtotal_sum, 2, '.', ''),
                    'delivery_fee_sum' => number_format((float) $row->delivery_fee_sum, 2, '.', ''),
                    'total_sum' => number_format($total, 2, '.', ''),
                    'average_order_value' => number_format($ordersCount > 0 ? $total / $ordersCount : 0, 2, '.', ''),
                ];
            })
            ->all();
    }

    private function formatOrderRow(Order $order, $item = null): array
    {
        $variant = $item?->variant;
        $product = $variant?->product;

        return [
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
        ];
    }

    private function downloadRows(array $headings, array $rows, string $basename, string $format, string $title): Response
    {
        return match ($format) {
            'excel' => $this->downloadExcel($headings, $rows, $basename, $title),
            'pdf' => $this->downloadPdf($headings, $rows, $basename, $title),
            default => $this->downloadCsv($headings, $rows, $basename),
        };
    }

    private function downloadCsv(array $headings, array $rows, string $basename): Response
    {
        return response()->streamDownload(function () use ($headings, $rows): void {
            $handle = fopen('php://output', 'w');
            if (!empty($headings)) {
                fputcsv($handle, $headings);
            }

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $basename . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function streamCsvMatrix(array $matrix, string $basename): Response
    {
        return response()->streamDownload(function () use ($matrix): void {
            $handle = fopen('php://output', 'w');
            foreach ($matrix as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $basename . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function downloadExcelHtml(array $matrix, string $basename, string $title): Response
    {
        $html = '<html><head><meta charset="UTF-8"></head><body>';
        $html .= '<h2>' . e($title) . '</h2>';

        // Build a single table with rows of varying length
        $html .= '<table border="1" cellpadding="6" cellspacing="0">';
        foreach ($matrix as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . e((string) $cell) . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';
        $html .= '</body></html>';

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $basename . '.xls"',
        ]);
    }

    private function downloadPdfFromMatrix(array $matrix, string $basename, string $title): Response
    {
        $lines = [];
        $lines[] = $title;
        $lines[] = 'Generated: ' . now()->format('Y-m-d H:i:s');
        $lines[] = '';

        foreach ($matrix as $row) {
            if (empty($row)) {
                $lines[] = '';
                continue;
            }

            $parts = array_map(fn($c) => (string) $c, $row);
            $lines[] = implode(' | ', $parts);
        }

        $pdf = $this->buildSimplePdf($lines);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $basename . '.pdf"',
        ]);
    }

    private function downloadExcel(array $headings, array $rows, string $basename, string $title): Response
    {
        $html = '<html><head><meta charset="UTF-8"></head><body>';
        $html .= '<h1>' . e($title) . '</h1><table border="1"><thead><tr>';

        foreach ($headings as $heading) {
            $html .= '<th>' . e($heading) . '</th>';
        }

        $html .= '</tr></thead><tbody>';

        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . e((string) $cell) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table></body></html>';

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $basename . '.xls"',
        ]);
    }

    private function downloadPdf(array $headings, array $rows, string $basename, string $title): Response
    {
        $lines = [$title, 'Generated: ' . now()->format('Y-m-d H:i:s'), ''];

        foreach ($rows as $index => $row) {
            $parts = [];
            foreach ($headings as $headingIndex => $heading) {
                $parts[] = $heading . ': ' . ($row[$headingIndex] ?? '');
            }

            $lines[] = ($index + 1) . '. ' . implode(' | ', $parts);
            $lines[] = '';
        }

        if (empty($rows)) {
            $lines[] = 'No records found for the selected filters.';
        }

        $pdf = $this->buildSimplePdf($lines);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $basename . '.pdf"',
        ]);
    }

    private function buildSimplePdf(array $lines): string
    {
        $objects = [];
        $pages = [];
        $chunks = array_chunk($this->wrapLines($lines, 110), 42);

        foreach ($chunks as $pageIndex => $chunk) {
            $content = "BT\n/F1 9 Tf\n36 806 Td\n12 TL\n";

            foreach ($chunk as $lineIndex => $line) {
                if ($lineIndex > 0) {
                    $content .= "T*\n";
                }

                $content .= '(' . $this->escapePdfText($line) . ") Tj\n";
            }

            $content .= "ET";
            $contentObjectNumber = count($objects) + 1;
            $objects[] = "<< /Length " . strlen($content) . " >>\nstream\n" . $content . "\nendstream";
            $pageObjectNumber = count($objects) + 1;
            $objects[] = "<< /Type /Page /Parent 0 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 0 0 R >> >> /Contents {$contentObjectNumber} 0 R >>";
            $pages[] = $pageObjectNumber;
        }

        $fontObjectNumber = count($objects) + 1;
        $objects[] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
        $pagesObjectNumber = count($objects) + 1;
        $catalogObjectNumber = count($objects) + 2;

        foreach ($pages as $pageObjectNumber) {
            $objects[$pageObjectNumber - 1] = str_replace('/Parent 0 0 R', '/Parent ' . $pagesObjectNumber . ' 0 R', $objects[$pageObjectNumber - 1]);
            $objects[$pageObjectNumber - 1] = str_replace('/F1 0 0 R', '/F1 ' . $fontObjectNumber . ' 0 R', $objects[$pageObjectNumber - 1]);
        }

        $objects[] = '<< /Type /Pages /Kids [' . implode(' ', array_map(fn ($page) => $page . ' 0 R', $pages)) . '] /Count ' . count($pages) . ' >>';
        $objects[] = "<< /Type /Catalog /Pages {$pagesObjectNumber} 0 R >>";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1) . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= str_pad((string) $offsets[$i], 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        }

        return $pdf . "trailer\n<< /Size " . (count($objects) + 1) . " /Root {$catalogObjectNumber} 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF";
    }

    private function wrapLines(array $lines, int $width): array
    {
        $wrapped = [];

        foreach ($lines as $line) {
            $parts = explode("\n", wordwrap((string) $line, $width, "\n", true));
            array_push($wrapped, ...$parts);
        }

        return $wrapped;
    }

    private function escapePdfText(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }
}
