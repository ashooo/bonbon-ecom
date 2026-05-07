<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $invoiceService) {}

    public function print(Invoice $invoice)
    {
        $invoice->loadMissing('order');

        $html = $this->invoiceService->renderInvoiceHtml($invoice->order);

        return response($html, 200)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->header('Content-Disposition', 'inline; filename="invoice-' . $invoice->order->order_number . '.html"');
    }

    public function trackPrint(Invoice $invoice)
    {
        $invoice->incrementPrintCount();

        return response()->json([
            'success' => true,
            'message' => 'Print tracked successfully',
            'print_count' => $invoice->print_count,
            'last_printed_at' => $invoice->last_printed_at,
        ]);
    }

    public function download(Invoice $invoice): StreamedResponse
    {
        $invoice->loadMissing('order');

        $path = $this->invoiceService->ensureInvoicePdf($invoice, true);

        if ($path && Storage::disk('local')->exists($path)) {
            $invoice->incrementPrintCount();

            return Storage::disk('local')->download(
                $path,
                'receipt-' . $invoice->order->order_number . '.pdf'
            );
        }

        abort(404, 'Invoice file not found');
    }
}
