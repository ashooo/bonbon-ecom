<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InvoiceService
{
    private const RECEIPT_WIDTH = '80mm';
    private const RECEIPT_HEIGHT = '200mm';

    public function generateInvoice(Order $order): Invoice
    {
        $order->loadMissing(['items.variant.product', 'user', 'address']);

        $html = $this->renderInvoiceHtml($order);
        $filename = 'invoice-' . $order->id . '-' . time() . '.html';
        $path = 'invoices/' . $filename;

        Storage::disk('local')->put($path, $html);

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
        $storeName = config('app.name', 'BonBon');
        $storePhone = '(555) 123-4567';
        $storeEmail = 'support@bonbon.shop';
        $storeAddress = '123 Main Street, City, State 12345';

        $subtotal = (float) $order->subtotal;
        $deliveryFee = (float) $order->delivery_fee;
        $total = (float) $order->total;
        $discount = $subtotal + $deliveryFee - $total;

        $paymentStatus = $order->payment_status === 'paid' ? 'PAID' : 'PENDING';
        $paymentStatusClass = $order->payment_status === 'paid' ? 'status-paid' : 'status-pending';

        $itemsHtml = '';
        foreach ($order->items as $item) {
            $productName = $item->variant?->product?->name ?? 'Unknown Product';
            $quantity = (int) $item->quantity;
            $unitPrice = (float) $item->unit_price;
            $itemSubtotal = (float) $item->subtotal;

            $itemsHtml .= sprintf(
                '<tr>
                    <td class="item-name">%s</td>
                    <td class="item-qty">%d</td>
                    <td class="item-price">₱%.2f</td>
                    <td class="item-subtotal">₱%.2f</td>
                </tr>',
                htmlspecialchars($productName),
                $quantity,
                $unitPrice,
                $itemSubtotal
            );
        }

        $discountRow = $discount > 0 ? sprintf(
            '<tr class="summary-row">
                <td colspan="3" class="label">Discount:</td>
                <td class="amount">-₱%.2f</td>
            </tr>',
            $discount
        ) : '';

        $customerAddress = $order->delivery_address ?? ($order->address?->address_line_1 ?? 'N/A');
        $customerCity = $order->address?->city ?? '';
        $customerProvince = $order->address?->province ?? '';

        $fullAddress = trim(implode(', ', array_filter([
            $customerAddress,
            $customerCity,
            $customerProvince,
        ])));

        return sprintf(
            '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice - %s</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: %s %s;
            margin: 5mm;
        }

        body {
            font-family: "Courier New", monospace;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            width: 100%%;
        }

        .receipt {
            width: 100%%;
            text-align: center;
        }

        .header {
            border-bottom: 1px solid #000;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }

        .store-name {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 2px;
        }

        .store-contact {
            font-size: 9px;
            line-height: 1.2;
            margin-bottom: 4px;
        }

        .receipt-label {
            font-weight: bold;
            font-size: 10px;
            margin: 4px 0;
        }

        .section-title {
            font-weight: bold;
            text-align: left;
            font-size: 10px;
            margin-top: 6px;
            margin-bottom: 3px;
            border-bottom: 1px dashed #000;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            text-align: left;
            font-size: 10px;
            margin-bottom: 2px;
        }

        .info-label {
            font-weight: bold;
            width: 40%%;
        }

        .info-value {
            text-align: right;
            width: 60%%;
            word-break: break-word;
        }

        table {
            width: 100%%;
            border-collapse: collapse;
            margin: 6px 0;
            font-size: 10px;
        }

        thead {
            border-top: 1px solid #000;
            border-bottom: 1px dashed #000;
        }

        th {
            text-align: left;
            padding: 2px 0;
            font-weight: bold;
            font-size: 9px;
        }

        tbody tr {
            border-bottom: 1px dotted #ccc;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        td {
            padding: 2px 2px;
            text-align: left;
        }

        .item-name {
            max-width: 35px;
            word-break: break-word;
        }

        .item-qty,
        .item-price,
        .item-subtotal {
            text-align: right;
            width: auto;
        }

        .summary-section {
            border-top: 1px solid #000;
            margin-top: 4px;
            padding-top: 4px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
            font-size: 10px;
        }

        .summary-label {
            flex: 1;
            text-align: left;
        }

        .summary-amount {
            flex: 0 0 50px;
            text-align: right;
        }

        .total-row {
            font-weight: bold;
            font-size: 11px;
            padding: 3px 0;
            border-top: 1px solid #000;
            margin-top: 2px;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            font-weight: bold;
            font-size: 10px;
            margin: 4px 0;
            border: 1px solid #000;
        }

        .status-paid {
            background-color: #e8f5e9;
            border-color: #4caf50;
            color: #2e7d32;
        }

        .status-pending {
            background-color: #fff3e0;
            border-color: #ff9800;
            color: #e65100;
        }

        .footer {
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1px solid #000;
            font-size: 9px;
            text-align: center;
        }

        .thank-you {
            font-weight: bold;
            margin-bottom: 4px;
        }

        .date-time {
            font-size: 9px;
            margin-top: 4px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div class="store-name">%s</div>
            <div class="store-contact">
                %s<br>
                %s<br>
                %s
            </div>
        </div>

        <div class="receipt-label">RECEIPT / INVOICE</div>

        <div class="info-row">
            <div class="info-label">Order #:</div>
            <div class="info-value">%s</div>
        </div>
        <div class="info-row">
            <div class="info-label">Date:</div>
            <div class="info-value">%s</div>
        </div>

        <div class="section-title">CUSTOMER INFORMATION</div>
        <div class="info-row">
            <div class="info-label">Name:</div>
            <div class="info-value">%s</div>
        </div>
        <div class="info-row">
            <div class="info-label">Phone:</div>
            <div class="info-value">%s</div>
        </div>
        <div class="info-row">
            <div class="info-label">Address:</div>
            <div class="info-value">%s</div>
        </div>

        <div class="section-title">ORDER DETAILS</div>
        <div class="info-row">
            <div class="info-label">Type:</div>
            <div class="info-value">%s</div>
        </div>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value"><span class="status-badge %s">%s</span></div>
        </div>

        <div class="section-title">ITEMS</div>
        <table>
            <thead>
                <tr>
                    <th style="max-width: 35px;">Item</th>
                    <th style="text-align: right; width: 30px;">Qty</th>
                    <th style="text-align: right; width: 40px;">Price</th>
                    <th style="text-align: right; width: 40px;">Total</th>
                </tr>
            </thead>
            <tbody>
                %s
            </tbody>
        </table>

        <div class="summary-section">
            <div class="summary-row">
                <div class="summary-label">Subtotal:</div>
                <div class="summary-amount">₱%.2f</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Delivery Fee:</div>
                <div class="summary-amount">₱%.2f</div>
            </div>
            %s
            <div class="summary-row total-row">
                <div class="summary-label">TOTAL:</div>
                <div class="summary-amount">₱%.2f</div>
            </div>
        </div>

        <div class="footer">
            <div class="thank-you">Thank you for your order!</div>
            <div class="date-time">Printed: %s</div>
        </div>
    </div>
</body>
</html>',
            htmlspecialchars($order->order_number),
            self::RECEIPT_WIDTH,
            self::RECEIPT_HEIGHT,
            $storeName,
            $storeAddress,
            $storeEmail,
            $storePhone,
            htmlspecialchars($order->order_number),
            $order->created_at->format('Y-m-d H:i'),
            htmlspecialchars($order->customer_name),
            htmlspecialchars($order->customer_phone),
            htmlspecialchars($fullAddress),
            ucfirst($order->order_type),
            $paymentStatusClass,
            $paymentStatus,
            $itemsHtml,
            $subtotal,
            $deliveryFee,
            $discountRow,
            $total,
            now()->format('Y-m-d H:i:s')
        );
    }
}
