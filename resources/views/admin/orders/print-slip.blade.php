<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Delivery Slip {{ $order->order_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #2f1e26; margin: 16px; }
        .slip { border: 1px solid #e7ccd7; border-radius: 10px; overflow: hidden; }
        .header { background: #f8e8ef; padding: 12px 14px; }
        .header h1 { margin: 0; font-size: 18px; }
        .meta { font-size: 12px; color: #6b4a57; margin-top: 4px; }
        .section { padding: 12px 14px; border-top: 1px solid #f0dfe6; }
        .section h2 { margin: 0 0 8px; font-size: 13px; text-transform: uppercase; letter-spacing: .04em; color: #7d5a67; }
        .row { display: flex; justify-content: space-between; gap: 8px; font-size: 13px; margin: 4px 0; }
        .items { width: 100%; border-collapse: collapse; font-size: 12px; }
        .items th, .items td { border: 1px solid #f0dfe6; padding: 6px; text-align: left; }
        .items th { background: #fbf2f6; }
        .total { font-weight: 700; }
        @media print {
            body { margin: 0; }
            .print-controls { display: none; }
            .slip { border: 0; border-radius: 0; }
        }
    </style>
</head>
<body>
    <div class="print-controls" style="margin-bottom:12px;">
        <button onclick="window.print()" style="background:#c47a90;color:#fff;border:0;border-radius:8px;padding:8px 12px;font-weight:600;cursor:pointer;">Print Slip</button>
    </div>

    <div class="slip">
        <div class="header">
            <h1>BonBons PH Delivery Slip</h1>
            <div class="meta">Order #{{ $order->order_number }} | {{ $order->created_at?->format('M d, Y g:i A') }}</div>
        </div>

        <div class="section">
            <h2>Customer & Delivery</h2>
            <div class="row"><span>Name</span><strong>{{ $order->customer_name }}</strong></div>
            <div class="row"><span>Phone</span><strong>{{ $order->customer_phone ?: 'N/A' }}</strong></div>
            <div class="row"><span>Type</span><strong>{{ ucfirst((string) $order->order_type) }}</strong></div>
            <div class="row"><span>Date/Time</span><strong>{{ $order->fulfillment_date?->format('M d, Y') ?? 'N/A' }} {{ $order->fulfillment_time ? \Carbon\Carbon::createFromFormat('H:i:s', $order->fulfillment_time)->format('g:i A') : '' }}</strong></div>
            <div class="row" style="display:block;">
                <span style="display:block;color:#7d5a67;">Address</span>
                <strong style="display:block;margin-top:4px;">{{ $order->delivery_address ?: 'N/A' }}</strong>
            </div>
            @if ($order->special_instructions)
                <div class="row" style="display:block;">
                    <span style="display:block;color:#7d5a67;">Special Instructions</span>
                    <strong style="display:block;margin-top:4px;">{{ $order->special_instructions }}</strong>
                </div>
            @endif
        </div>

        <div class="section">
            <h2>Order Summary</h2>
            <table class="items">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Variant</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $item->variant?->product?->name ?? (($item->customization_payload['item_name'] ?? null) ?: 'Custom Cake') }}</td>
                            <td>{{ $item->variant?->name ?? 'Custom Design' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>&#8369;{{ number_format((float) $item->unit_price, 2) }}</td>
                            <td>&#8369;{{ number_format((float) $item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="row" style="margin-top:10px;"><span>Subtotal</span><strong>&#8369;{{ number_format((float) $order->subtotal, 2) }}</strong></div>
            <div class="row"><span>Delivery Fee</span><strong>&#8369;{{ number_format((float) $order->delivery_fee, 2) }}</strong></div>
            <div class="row total"><span>Total</span><strong>&#8369;{{ number_format((float) $order->total, 2) }}</strong></div>
        </div>
    </div>
</body>
</html>

