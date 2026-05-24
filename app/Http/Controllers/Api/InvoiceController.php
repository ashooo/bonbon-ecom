<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $invoiceService) {}

    public function download(Invoice $invoice): StreamedResponse
    {
        $invoice->loadMissing('order.user');

        // Verify the invoice belongs to the authenticated user's order
        if ((int) $invoice->order->user_id !== (int) Auth::id()) {
            abort(403, 'Unauthorized');
        }

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
