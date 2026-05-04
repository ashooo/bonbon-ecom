<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceController extends Controller
{
    public function download(Invoice $invoice): StreamedResponse
    {
        $invoice->loadMissing('order.user');

        // Verify the invoice belongs to the authenticated user's order
        if ((int) $invoice->order->user_id !== (int) Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $path = $invoice->pdf_path;

        if ($path && Storage::disk('local')->exists($path)) {
            $invoice->incrementPrintCount();

            return Storage::disk('local')->download(
                $path,
                'invoice-' . $invoice->order->order_number . '.html'
            );
        }

        abort(404, 'Invoice file not found');
    }
}
