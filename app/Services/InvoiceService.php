<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    private const RECEIPT_WIDTH = '105mm';
    private const RECEIPT_HEIGHT = '260mm';

    public function generateInvoice(Order $order): Invoice
    {
        $order->loadMissing(['items.variant.product', 'user', 'address']);

        $html = $this->renderInvoiceHtml($order);
        $pdfBytes = $this->renderInvoicePdfBytes($html);
        $filename = 'invoice-' . $order->id . '-' . time() . '.pdf';
        $path = 'invoices/' . $filename;

        Storage::disk('local')->put($path, $pdfBytes);

        return Invoice::updateOrCreate(
            ['order_id' => $order->id],
            [
                'pdf_path' => $path,
                'print_count' => 0,
                'last_printed_at' => null,
            ]
        );
    }

    public function renderInvoiceHtml(Order $order): string
    {
        $storeName = 'BonBons PH';
        $storePhone = '(+63) 000-000-0000';
        $storeEmail = 'support@bonbon.ph';
        $storeAddress = 'Paranaque - Sucat Rd, Metro Manila';

        if ($order->order_type === 'pickup') {
            $fullAddress = 'BonBons Store';
        } else {
            $customerAddress = $order->delivery_address ?? ($order->address?->address_line_1 ?? 'N/A');
            $customerCity = $order->address?->city ?? '';
            $customerProvince = $order->address?->province ?? '';
            $fullAddress = trim(implode(', ', array_filter([$customerAddress, $customerCity, $customerProvince])));
        }

        $stampPath = public_path('images/receipt-stamp.png');
        $stampSrc = null;
        if (is_string($stampPath) && File::exists($stampPath)) {
            $stampSrc = 'data:image/png;base64,' . base64_encode((string) File::get($stampPath));
        }

        return view('admin.invoices.template', [
            'order' => $order,
            'storeName' => $storeName,
            'storePhone' => $storePhone,
            'storeEmail' => $storeEmail,
            'storeAddress' => $storeAddress,
            'fullAddress' => $fullAddress,
            'stampSrc' => $stampSrc,
            'receiptWidth' => self::RECEIPT_WIDTH,
            'receiptHeight' => self::RECEIPT_HEIGHT,
        ])->render();
    }

    public function ensureInvoicePdf(Invoice $invoice, bool $forceRegenerate = false): string
    {
        $invoice->loadMissing('order.items.variant.product', 'order.address');
        $path = (string) ($invoice->pdf_path ?? '');

        if (! $forceRegenerate && $path !== '' && str_ends_with(strtolower($path), '.pdf') && Storage::disk('local')->exists($path)) {
            return $path;
        }

        $html = $this->renderInvoiceHtml($invoice->order);
        $pdfBytes = $this->renderInvoicePdfBytes($html);
        $filename = 'invoice-' . $invoice->order->id . '-' . time() . '.pdf';
        $newPath = 'invoices/' . $filename;
        Storage::disk('local')->put($newPath, $pdfBytes);

        $invoice->update(['pdf_path' => $newPath]);

        return $newPath;
    }

    private function renderInvoicePdfBytes(string $html): string
    {
        $widthPoints = $this->mmToPoints((float) rtrim(self::RECEIPT_WIDTH, 'mm'));
        $heightPoints = $this->mmToPoints((float) rtrim(self::RECEIPT_HEIGHT, 'mm'));

        return Pdf::loadHTML($html)
            ->setPaper([0, 0, $widthPoints, $heightPoints])
            ->output();
    }

    private function mmToPoints(float $mm): float
    {
        return ($mm / 25.4) * 72.0;
    }
}
