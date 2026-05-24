<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt</title>
</head>
<body style="margin:0;padding:0;background:#fff6f8;font-family:Arial,Helvetica,sans-serif;color:#4a3340;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px;background:#ffffff;border:1px solid #f5dce4;border-radius:16px;overflow:hidden;">
                    <tr>
                        <td style="background:#fdf0f4;padding:24px;">
                            <p style="margin:0;font-size:12px;letter-spacing:0.15em;text-transform:uppercase;color:#c06b87;font-weight:700;">BonBons PH</p>
                            <h1 style="margin:8px 0 0 0;font-size:24px;color:#5a3a3a;">Your receipt is ready</h1>
                            <p style="margin:8px 0 0 0;font-size:14px;color:#7b5a65;">Order {{ $order->order_number }} has been recorded successfully.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 24px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="font-size:14px;">
                                <tr>
                                    <td style="padding:6px 0;color:#8c6770;">Customer</td>
                                    <td align="right" style="padding:6px 0;font-weight:700;">{{ $order->customer_name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:6px 0;color:#8c6770;">Payment Method</td>
                                    <td align="right" style="padding:6px 0;font-weight:700;">{{ strtoupper((string) $order->payment_method) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:6px 0;color:#8c6770;">Payment Status</td>
                                    <td align="right" style="padding:6px 0;font-weight:700;">{{ ucfirst((string) $order->payment_status) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 0 0 0;color:#8c6770;border-top:1px solid #f6e3e9;">Total</td>
                                    <td align="right" style="padding:10px 0 0 0;font-weight:800;font-size:18px;border-top:1px solid #f6e3e9;">&#8369;{{ number_format((float) $order->total, 2) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 24px 24px 24px;">
                            <p style="margin:0 0 10px 0;font-size:13px;color:#8c6770;">Items:</p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="font-size:13px;border:1px solid #f0d5dd;border-radius:12px;">
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td style="padding:8px 10px;border-bottom:1px solid #f7e6eb;">{{ $item->variant?->product?->name ?? 'Item' }}</td>
                                        <td align="center" style="padding:8px 10px;border-bottom:1px solid #f7e6eb;">x{{ (int) $item->quantity }}</td>
                                        <td align="right" style="padding:8px 10px;border-bottom:1px solid #f7e6eb;">&#8369;{{ number_format((float) $item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
