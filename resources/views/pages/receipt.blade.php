@extends('layouts.app')

@section('content')
    @php
        $paymentMethodLabel = (string) $order->payment_method === 'paymongo' ? 'QRPH' : strtoupper((string) $order->payment_method);
    @endphp
    <div class="mx-auto max-w-6xl space-y-6 px-4 py-8">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-[#8C6770]">Official Receipt</p>
                <h1 class="text-3xl font-black text-[#5A3A3A]">{{ $order->order_number }}</h1>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('orders.show', $order) }}" class="rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Order Details</a>
                <a href="{{ route('orders.index') }}" class="rounded-full bg-[#5A3A3A] px-4 py-2 text-sm font-semibold text-white hover:bg-[#6e4a4a]">Back to Orders</a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-3xl border border-pink-100 bg-white p-6 shadow-[0_8px_30px_rgba(201,79,124,0.12)] lg:col-span-2">
                <h2 class="mb-4 text-lg font-black text-[#5A3A3A]">Receipt Preview</h2>
                <div class="receipt-frame">
                    <div class="receipt-frame__inner">
                        <iframe
                            title="Receipt Preview"
                            src="{{ route('orders.receipt.html', $order) }}"
                            class="receipt-iframe"
                            id="receiptPreviewFrame"
                        ></iframe>
                    </div>
                </div>
            </div>

            <div class="space-y-4 rounded-3xl border border-pink-100 bg-white p-6 shadow-[0_8px_30px_rgba(201,79,124,0.1)]">
                <h2 class="text-lg font-black text-[#5A3A3A]">Payment & Order</h2>
                <div class="space-y-2 text-sm text-[#5A3A3A]">
                    <div class="flex justify-between"><span class="text-[#8C6770]">Customer</span><span class="font-semibold">{{ $order->customer_name }}</span></div>
                    <div class="flex justify-between"><span class="text-[#8C6770]">Order Type</span><span class="font-semibold">{{ ucfirst((string) $order->order_type) }}</span></div>
                    <div class="flex justify-between"><span class="text-[#8C6770]">Payment</span><span class="font-semibold">{{ $paymentMethodLabel }}</span></div>
                    <div class="flex justify-between"><span class="text-[#8C6770]">Status</span><span class="font-semibold">{{ ucfirst((string) $order->payment_status) }}</span></div>
                    <div class="flex justify-between border-t border-pink-100 pt-2 text-base"><span>Total</span><span class="font-black">&#8369;{{ number_format((float) $order->total, 2) }}</span></div>
                </div>

                @if ($order->invoice)
                    <a href="{{ route('orders.receipt.pdf', $order) }}" class="inline-flex w-full items-center justify-center rounded-full bg-pink-600 px-4 py-3 text-sm font-bold text-white hover:bg-pink-700">
                        Download Receipt File
                    </a>
                @endif
            </div>
        </div>
    </div>

    <style>
        /* Cake-like frame (pink cake + chocolate drip) for receipt preview */
        .receipt-frame {
            position: relative;
            border-radius: 24px;
            padding: 14px;
            background:
                radial-gradient(160px 95px at 18% 18%, rgba(255,255,255,.78), transparent 62%),
                radial-gradient(190px 120px at 82% 6%, rgba(255,255,255,.58), transparent 60%),
                radial-gradient(120px 90px at 70% 55%, rgba(255,255,255,.18), transparent 60%),
                linear-gradient(180deg, #ffeff5 0%, #ffd2e2 48%, #ffb7d2 100%);
            box-shadow: 0 18px 48px rgba(201, 79, 124, 0.14);
            border: 1px solid rgba(240, 200, 214, 0.9);
            overflow: hidden;
        }

        .receipt-frame:before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            height: 18px;
            background:
                linear-gradient(180deg, rgba(255,255,255,0.7), rgba(255,255,255,0));
            pointer-events: none;
        }

        .receipt-frame:after {
            /* chocolate drip - smoother */
            content: "";
            position: absolute;
            left: -2px;
            right: -2px;
            top: 0;
            height: 28px;
            background:
                radial-gradient(20px 16px at 9% 100%, #5a3a3a 66%, transparent 67%),
                radial-gradient(26px 18px at 23% 100%, #5a3a3a 66%, transparent 67%),
                radial-gradient(18px 14px at 38% 100%, #5a3a3a 66%, transparent 67%),
                radial-gradient(30px 20px at 54% 100%, #5a3a3a 66%, transparent 67%),
                radial-gradient(22px 16px at 70% 100%, #5a3a3a 66%, transparent 67%),
                radial-gradient(26px 18px at 88% 100%, #5a3a3a 66%, transparent 67%),
                linear-gradient(180deg, #6b3d3d 0%, #3e262d 100%);
            opacity: 0.95;
            pointer-events: none;
        }

        .receipt-frame__inner {
            position: relative;
            border-radius: 18px;
            overflow: hidden;
            background: #ffffff;
            /* This creates a consistent “same-size border” around the actual receipt content */
            border: 1px solid rgba(241, 220, 228, 0.9);
        }

        .receipt-iframe {
            display: block;
            width: 100%;
            height: 980px; /* JS will auto-fit after load */
            border: 0;
            background: #ffffff;
        }

        @media (max-width: 640px) {
            .receipt-iframe { height: 1080px; }
        }
    </style>

    <script>
        (function () {
            const frame = document.getElementById('receiptPreviewFrame');
            if (!frame) return;

            const fit = () => {
                try {
                    const doc = frame.contentDocument || frame.contentWindow?.document;
                    if (!doc) return;
                    const height = Math.max(
                        doc.documentElement?.scrollHeight || 0,
                        doc.body?.scrollHeight || 0
                    );
                    if (height > 0) {
                        frame.style.height = `${height}px`;
                    }
                } catch (e) {
                    // ignore cross-origin / access issues
                }
            };

            frame.addEventListener('load', () => {
                fit();
                // second pass after fonts/layout settle
                setTimeout(fit, 150);
                setTimeout(fit, 500);
            });
        })();
    </script>
@endsection
