<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        * { box-sizing: border-box; }
        @page { size: {{ $receiptWidth }} {{ $receiptHeight }}; margin: 8mm; }
        body { margin: 0; font-family: "DejaVu Sans", Arial, Helvetica, sans-serif; color: #4a3040; background: #fff; }
        .receipt { border: 1px solid #f2d9e1; border-radius: 18px; overflow: hidden; background: #fff; width: 100%; }

        /* Pink cake header with chocolate drip */
        .header {
            position: relative;
            padding: 14px 12px 22px;
            text-align: center;
            background:
                radial-gradient(120px 70px at 20% 20%, rgba(255,255,255,.75), transparent 60%),
                radial-gradient(140px 80px at 80% 0%, rgba(255,255,255,.6), transparent 60%),
                linear-gradient(180deg, #ffe7ef 0%, #ffd5e3 40%, #ffc1d7 100%);
            border-bottom: 1px solid #f4d6df;
        }
        .header:after {
            content: "";
            position: absolute;
            left: 0; right: 0; bottom: -1px;
            height: 18px;
            background:
                radial-gradient(18px 14px at 8% 0%, #5a3a3a 65%, transparent 66%),
                radial-gradient(22px 16px at 22% 0%, #5a3a3a 65%, transparent 66%),
                radial-gradient(16px 12px at 38% 0%, #5a3a3a 65%, transparent 66%),
                radial-gradient(24px 18px at 55% 0%, #5a3a3a 65%, transparent 66%),
                radial-gradient(18px 14px at 72% 0%, #5a3a3a 65%, transparent 66%),
                radial-gradient(22px 16px at 90% 0%, #5a3a3a 65%, transparent 66%),
                linear-gradient(180deg, #5a3a3a 0%, #3e262d 100%);
            opacity: 0.95;
        }

        .title { margin: 0; font-size: 16px; font-weight: 900; color: #7a2e4e; letter-spacing: .10em; text-transform: uppercase; }
        .subtitle { margin: 6px 0 0; font-size: 10px; font-weight: 800; color: rgba(122,46,78,.8); letter-spacing: .18em; text-transform: uppercase; }
        .store { margin: 8px 0 0 0; font-size: 10px; line-height: 1.5; color: #6d4b57; font-weight: 600; }

        .body { padding: 16px 14px 14px; }
        .section-title { margin: 12px 0 8px; font-size: 10px; font-weight: 900; color: #c94f7c; text-transform: uppercase; letter-spacing: .14em; }

        .meta { width: 100%; border-collapse: collapse; font-size: 10px; }
        .meta td { padding: 3px 0; vertical-align: top; }
        .meta .label { color: #8c6770; width: 42%; }
        .meta .value { font-weight: 700; text-align: right; word-break: break-word; }
        .meta-card {
            border: 1px solid #f1dce4;
            border-radius: 14px;
            padding: 10px 10px;
            background: linear-gradient(180deg, #fff7fa 0%, #ffffff 100%);
        }

        .items { width: 100%; border-collapse: collapse; table-layout: fixed; font-size: 10px; margin-top: 8px; border: 1px solid #f1dce4; border-radius: 14px; overflow: hidden; }
        .items th { text-align: left; padding: 7px 6px; background: #fff0f6; color: #7d5560; border-bottom: 1px solid #f0dce3; font-weight: 900; letter-spacing: .08em; text-transform: uppercase; font-size: 9px; }
        .items td { padding: 7px 6px; border-bottom: 1px dashed #f1dce4; }
        .items th:nth-child(2), .items th:nth-child(3), .items th:nth-child(4) { text-align: right; }
        .items td:nth-child(2), .items td:nth-child(3), .items td:nth-child(4) { text-align: right; }
        .items td:nth-child(1) { word-break: break-word; }
        .summary { margin-top: 10px; border: 1px solid #f1dce4; border-radius: 14px; padding: 10px; font-size: 10px; background: #fffbfc; }
        .summary-row { display: flex; justify-content: space-between; margin: 3px 0; }
        .summary-row.total { font-weight: 800; font-size: 12px; color: #5a3a3a; border-top: 1px solid #eec9d5; margin-top: 6px; padding-top: 6px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 9px; font-weight: 800; text-transform: uppercase; }
        .badge-paid { background: #e7f8ef; color: #1d7f4e; }
        .badge-pending { background: #fff3dd; color: #9a6a00; }
        .stamp-wrap { padding: 10px 14px 6px; display: flex; justify-content: center; }
        .stamp {
            width: 86%;
            max-width: 280px;
            opacity: 0.55;
            filter: saturate(1.05) contrast(1.05);
        }
        .footer { text-align: center; padding: 12px 10px; font-size: 9px; color: #8c6770; border-top: 1px solid #f2d9e1; background: #fffbfc; }
        .heart { color: #c94f7c; font-weight: 900; }
    </style>
</head>
<body>
    @php
        $paymentPaid = $order->payment_status === 'paid';
        $discount = ((float) $order->subtotal + (float) $order->delivery_fee) - (float) $order->total;
    @endphp
    <div class="receipt">
        <div class="header">
            <h1 class="title">{{ $storeName }}</h1>
            <p class="subtitle">Receipt / Invoice</p>
            <p class="store">{{ $storeAddress }}<br>{{ $storeEmail }}<br>{{ $storePhone }}</p>
        </div>

        <div class="body">
            <div class="meta-card">
                <table class="meta">
                    <tr><td class="label">Order Number</td><td class="value">{{ $order->order_number }}</td></tr>
                    <tr><td class="label">Placed At</td><td class="value">{{ $order->created_at?->format('M d, Y h:i A') }}</td></tr>
                    <tr><td class="label">Order Type</td><td class="value">{{ ucfirst((string) $order->order_type) }}</td></tr>
                    <tr>
                        <td class="label">Payment</td>
                        <td class="value">
                            <span class="badge {{ $paymentPaid ? 'badge-paid' : 'badge-pending' }}">{{ $paymentPaid ? 'Paid' : 'Pending' }}</span>
                        </td>
                    </tr>
                </table>
            </div>

            <p class="section-title">Customer</p>
            <div class="meta-card">
                <table class="meta">
                    <tr><td class="label">Name</td><td class="value">{{ $order->customer_name }}</td></tr>
                    <tr><td class="label">Phone</td><td class="value">{{ $order->customer_phone }}</td></tr>
                    <tr>
                        <td class="label">{{ $order->order_type === 'pickup' ? 'Pickup Location' : 'Delivery Address' }}</td>
                        <td class="value">{{ $fullAddress }}</td>
                    </tr>
                </table>
            </div>

            <p class="section-title">Items</p>
            <table class="items">
                <colgroup>
                    <col style="width: 46%">
                    <col style="width: 14%">
                    <col style="width: 20%">
                    <col style="width: 20%">
                </colgroup>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $item->variant?->product?->name ?? 'Unknown Product' }}</td>
                            <td>{{ (int) $item->quantity }}</td>
                            <td>&#8369;{{ number_format((float) $item->unit_price, 2) }}</td>
                            <td>&#8369;{{ number_format((float) $item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="summary">
                <div class="summary-row"><span>Subtotal</span><span>&#8369;{{ number_format((float) $order->subtotal, 2) }}</span></div>
                <div class="summary-row"><span>Delivery Fee</span><span>&#8369;{{ number_format((float) $order->delivery_fee, 2) }}</span></div>
                @if ($discount > 0)
                    <div class="summary-row"><span>Discount</span><span>-&#8369;{{ number_format((float) $discount, 2) }}</span></div>
                @endif
                <div class="summary-row total"><span>Total</span><span>&#8369;{{ number_format((float) $order->total, 2) }}</span></div>
            </div>
        </div>

        <div class="footer">
            Thank you for choosing BonBons PH <span class="heart">&#10084;</span><br>
            Printed {{ now()->format('M d, Y h:i A') }}
        </div>

        <div class="stamp-wrap">
            <img class="stamp" src="{{ $stampSrc ?? asset('images/receipt-stamp.png') }}" alt="BonBons PH Stamp">
        </div>
    </div>
</body>
</html>
