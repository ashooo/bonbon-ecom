@extends('layouts.app')

@section('content')
    @php
        $paymentMethodLabel = (string) $order->payment_method === 'paymongo' ? 'QRPH' : strtoupper((string) $order->payment_method);
    @endphp
    <div class="mx-auto max-w-3xl px-4 py-10">
        <div class="rounded-[2rem] border border-pink-100 bg-white p-8 shadow-[0_10px_35px_rgba(201,79,124,0.12)]">
            <div id="payment-loading" class="flex flex-col items-center justify-center py-10 text-center">
                <div class="h-12 w-12 animate-spin rounded-full border-4 border-pink-200 border-t-pink-500"></div>
                <p class="mt-4 text-sm font-semibold text-[#8C6770]">Checking your payment status...</p>
            </div>

            <div id="payment-content" class="hidden">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full {{ $isSuccess ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                    @if ($isSuccess)
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    @else
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    @endif
                </div>

                <h1 class="text-center text-2xl font-black text-[#5A3A3A]">
                    {{ $isSuccess ? 'Payment Successful' : 'Payment Not Completed' }}
                </h1>
                <p class="mx-auto mt-2 max-w-lg text-center text-sm text-[#8C6770]">{{ $message }}</p>

                <div class="mt-6 rounded-2xl bg-[#FFF6F8] p-5">
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#C88A92]">Order Summary</p>
                    <div class="mt-3 space-y-2 text-sm text-[#5A3A3A]">
                        <div class="flex justify-between"><span>Order Number</span><span class="font-bold">{{ $order->order_number }}</span></div>
                        <div class="flex justify-between"><span>Payment Method</span><span class="font-bold">{{ $paymentMethodLabel }}</span></div>
                        <div class="flex justify-between"><span>Payment Status</span><span class="font-bold">{{ ucfirst((string) $order->payment_status) }}</span></div>
                        <div class="flex justify-between border-t border-pink-100 pt-2"><span>Total</span><span class="font-black">&#8369;{{ number_format((float) $order->total, 2) }}</span></div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <a href="{{ route('orders.receipt', $order) }}" class="inline-flex items-center justify-center rounded-full bg-pink-600 px-5 py-3 text-sm font-bold text-white hover:bg-pink-700">
                        View Receipt
                    </a>
                    <a href="{{ route('orders.index') }}" class="inline-flex items-center justify-center rounded-full bg-[#5A3A3A] px-5 py-3 text-sm font-bold text-white hover:bg-[#6e4a4a]">
                        Go to Orders
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        setTimeout(() => {
            document.getElementById('payment-loading')?.classList.add('hidden');
            document.getElementById('payment-content')?.classList.remove('hidden');
        }, 850);
    </script>
@endsection
