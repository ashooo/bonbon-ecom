@extends('layouts.app')

@section('content')
    <style>
        .orders-shell {
            border: 1px solid #E9C7D4;
            border-radius: 22px;
            background: linear-gradient(180deg, #FBEAF1 0%, #FFFFFF 42%, #FBF2F6 100%);
            box-shadow: 0 18px 48px rgba(77, 46, 56, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.65);
            padding: 1.25rem;
        }
        .orders-panel {
            border: 1px solid #ECD8E0;
            border-radius: 1.4rem;
            background: #FFFFFF;
            box-shadow: 0 8px 30px rgba(77, 46, 56, 0.06);
        }
        .orders-input {
            border: 1px solid #ECD8E0;
            color: #533843;
            background: #FFFFFF;
        }
        .orders-input:focus {
            outline: none;
            border-color: #C47A90;
            box-shadow: 0 0 0 3px rgba(196, 122, 144, 0.15);
        }
        .orders-btn-primary {
            background: linear-gradient(135deg, #C47A90, #B66880);
            color: #FFFFFF;
        }
        .orders-btn-primary:hover { filter: brightness(0.97); }
        .orders-btn-soft {
            background: #FBEAF1;
            color: #4D2E38;
            border: 1px solid #E9C7D4;
        }
        .orders-btn-soft:hover { background: #F6DFE9; }
        .orders-status {
            background: #FBF2F6;
            border: 1px solid #E9C7D4;
            color: #8F6172;
        }
        .orders-status.orders-status-cancelled {
            color: #B66880;
            border-color: #C47A90;
            background: #FBEAF1;
        }
    </style>

    <div class="orders-shell max-w-5xl mx-auto">
        <div class="mb-8 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-[#4D2E38]">Your Orders</h1>
            @if ($isGuestView)
                <span class="rounded-full border border-[#E9C7D4] bg-[#FBF2F6] px-3 py-1 text-xs font-semibold text-[#8F6172]">Guest View</span>
            @endif
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-[1.4rem] border border-[#E9C7D4] bg-[#FBEAF1] p-4 text-[#8F6172]">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-[1.4rem] border border-[#C47A90] bg-[#FBEAF1] p-4 text-[#B66880]">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($isGuestView)
            <section class="orders-panel mb-8 p-6">
                <h2 class="mb-2 text-xl font-bold text-[#4D2E38]">Find an Order</h2>
                <p class="mb-4 text-sm text-[#8A6A76]">Enter your order number and checkout email to add it to this device.</p>

                <form method="POST" action="{{ route('orders.lookup') }}" class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    @csrf
                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#4D2E38]">Order Number</label>
                        <input type="text" name="order_number" value="{{ old('order_number') }}" placeholder="ORD-XXXXXXXXXX" class="orders-input w-full rounded-md px-3 py-2" required>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#4D2E38]">Email</label>
                        <input type="email" name="customer_email" value="{{ old('customer_email') }}" placeholder="you@email.com" class="orders-input w-full rounded-md px-3 py-2" required>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="orders-btn-primary w-full rounded-md px-4 py-2 font-semibold transition">Find Order</button>
                    </div>
                </form>
            </section>
        @endif

        <section class="orders-panel p-6">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-[#4D2E38]">Order History</h2>
                <span class="text-sm text-[#8F6172]">{{ $orders->count() }} order(s)</span>
            </div>

            @forelse ($orders as $order)
                @php
                    $status = strtolower((string) $order->status);
                    $paymentMethodLabel = (string) $order->payment_method === 'paymongo' ? 'QRPH' : strtoupper((string) $order->payment_method);
                    $statusClass = match ($status) {
                        'cancelled' => 'orders-status orders-status-cancelled',
                        default => 'orders-status',
                    };

                    $cancellationMessage = match ($status) {
                        'cancelled' => 'This order is already cancelled.',
                        'ready' => 'This order is already ready and can no longer be cancelled.',
                        'preparing' => 'This order is already preparing and can no longer be cancelled.',
                        'completed' => 'This order is already completed and can no longer be cancelled.',
                        default => null,
                    };
                @endphp

                <div class="mb-4 rounded-[1.4rem] border border-[#ECD8E0] bg-[#FFFFFF] p-4 last:mb-0">
                    <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                        <div>
                            <h3 class="font-bold text-[#4D2E38]">{{ $order->order_number }}</h3>
                            <p class="text-sm text-[#8A6A76]">Placed on {{ $order->created_at?->format('F j, Y h:i A') }}</p>
                            <p class="mt-1 text-sm text-[#8A6A76]">{{ ucfirst((string) $order->order_type) }}</p>
                            <p class="text-xs text-[#8F6172]">Payment: {{ $paymentMethodLabel }} ({{ ucfirst((string) $order->payment_status) }})</p>
                            <span class="mt-2 inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">{{ ucfirst((string) $order->status) }}</span>
                        </div>

                        <div class="space-y-2 text-left md:text-right">
                            <p class="text-sm text-[#8F6172]">Total</p>
                            <p class="text-lg font-bold text-[#4D2E38]">&#8369;{{ number_format($order->total, 2) }}</p>

                            <a href="{{ route('orders.show', $order) }}" class="orders-btn-soft inline-block rounded-md px-3 py-2 text-sm font-semibold">
                                View Details
                            </a>
                            <a href="{{ route('orders.receipt', $order) }}" class="orders-btn-soft inline-block rounded-md px-3 py-2 text-sm font-semibold text-[#C47A90]">
                                Receipt
                            </a>


                            @if (in_array($status, ['pending', 'confirmed'], true))
                                <form method="POST" action="{{ route('orders.cancel', $order) }}" data-confirm data-confirm-title="Cancel order?" data-confirm-message="Cancel this order?" data-confirm-ok="Cancel order" class="space-y-2">
                                    @csrf
                                    @if ($isGuestView)
                                        <input type="email" name="customer_email" placeholder="Confirm checkout email" class="orders-input w-full rounded-md px-2 py-1 text-xs md:w-56" required>
                                    @endif
                                    <button type="submit" class="w-full rounded-md bg-[#B66880] px-3 py-2 text-sm font-semibold text-white hover:bg-[#A55D73] md:w-auto">Cancel Order</button>
                                </form>
                            @elseif ($cancellationMessage)
                                <p class="text-xs text-[#8F6172]">{{ $cancellationMessage }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-[2rem] border border-dashed border-[#ECD8E0] bg-[#FBF2F6] p-6 text-[#4D2E38]">No orders found yet.</div>
            @endforelse
        </section>
    </div>
@endsection
