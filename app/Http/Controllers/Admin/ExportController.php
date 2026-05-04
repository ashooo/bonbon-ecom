<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $groupBy = $request->validate([
            'group_by' => 'nullable|in:day,month',
        ])['group_by'] ?? 'day';

        $basename = 'reports-export-' . $groupBy . '-' . $filters['start']->format('Ymd') . '-' . $filters['end']->format('Ymd');

        return $this->downloadRows($this->reportHeadings(), $this->reportRows($filters, $groupBy), $basename, $format, 'Reports Export');
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
        return [
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
            fputcsv($handle, $headings);

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $basename . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
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
