@extends('layouts.app')

@section('content')
    @php
        $nameParts = explode(' ', $user->name);
        $firstName = $nameParts[0] ?? '';
        $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';
    @endphp

    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">My Profile</h1>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
            <aside class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="text-center mb-6">
                        <div class="w-28 h-28 rounded-full overflow-hidden mx-auto mb-4 bg-pink-100">
                            <img src="{{ $user->profile_image_url }}" alt="" class="w-full h-full object-cover">
                        </div>
                        
                        <h2 class="text-xl font-bold">{{ $user->name }}</h2>
                        <p class="text-gray-600">{{ $user->email }}</p>
                        @if ($user->phone)
                            <p class="text-sm text-gray-500">{{ $user->phone }}</p>
                        @endif
                    </div>

                    <nav class="space-y-2">
                        <button type="button" data-tab="personal-info" class="tab-link block w-full text-left px-4 py-3 rounded-lg bg-pink-50 text-pink-700 font-semibold">Personal Information</button>
                        <button type="button" data-tab="order-history" class="tab-link block w-full text-left px-4 py-3 rounded-lg hover:bg-pink-50 text-gray-700">Order History</button>
                        <button type="button" data-tab="payment-methods" class="tab-link block w-full text-left px-4 py-3 rounded-lg hover:bg-pink-50 text-gray-700">Payment Methods</button>
                        <button type="button" data-tab="addresses" class="tab-link block w-full text-left px-4 py-3 rounded-lg hover:bg-pink-50 text-gray-700">Addresses</button>
                    </nav>
                </div>
            </aside>

            <main class="lg:col-span-3 space-y-6">
                <section id="personal-info" class="tab-section bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold mb-4">Personal Information</h3>
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                <input
                                    type="text"
                                    name="first_name"
                                    value="{{ old('first_name', $firstName) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                <input
                                    type="text"
                                    name="last_name"
                                    value="{{ old('last_name', $lastName) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"
                                >
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email', $user->email) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                <input
                                    type="tel"
                                    name="phone"
                                    value="{{ old('phone', $user->phone) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"
                                >
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                                <input
                                    type="date"
                                    name="dob"
                                    value="{{ old('dob', $user->dob?->format('Y-m-d')) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                                <input
                                    type="file"
                                    name="profile_picture"
                                    accept="image/*"
                                    class="w-full text-sm text-gray-700"
                                >
                            </div>
                        </div>

                        <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-6 rounded transition duration-300">
                            Save Personal Information
                        </button>
                    </form>
                </section>

                <section id="order-history" class="tab-section hidden bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold">Order History</h3>
                        <span class="text-sm text-gray-500">Showing {{ $orders->count() }} most recent orders</span>
                    </div>

                    @forelse ($orders as $order)
                        <div class="border border-gray-200 rounded-lg p-4 mb-4">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div>
                                    <h4 class="font-semibold">{{ $order->order_number }}</h4>
                                    <p class="text-sm text-gray-600">Placed on {{ $order->placed_at?->format('F j, Y') ?? 'N/A' }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $order->status === 'Delivered' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $order->status }}
                                    </span>
                                    <p class="text-lg font-bold">₱{{ number_format($order->total_amount, 2) }}</p>
                                </div>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-3">
                                <button type="button" data-action="toggle-order" data-target="order-details-{{ $order->id }}" class="text-pink-600 hover:text-pink-700 text-sm">View Details</button>
                                <form method="POST" action="{{ route('profile.order.reorder', $order) }}">
                                    @csrf
                                    <button type="submit" class="text-pink-600 hover:text-pink-700 text-sm">Reorder</button>
                                </form>
                            </div>

                            <div id="order-details-{{ $order->id }}" class="order-details mt-4 hidden rounded-lg border border-gray-200 bg-gray-50 p-4">
                                <p class="text-sm text-gray-700">{{ $order->description ?? 'No additional details available.' }}</p>
                                <p class="mt-2 text-sm text-gray-600">Order created at: {{ $order->created_at->format('F j, Y h:i A') }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 text-gray-700">
                            You have no orders yet. Your recent purchases will appear here.
                        </div>
                    @endforelse
                </section>

                <section id="payment-methods" class="tab-section hidden bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold mb-4">Payment Methods</h3>

                    <div class="space-y-4 mb-6">
                        @forelse ($paymentMethods as $method)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                                    <div>
                                        <p class="text-sm text-gray-600">{{ $method->card_brand }} •••• {{ $method->last_four }}</p>
                                        <p class="text-base font-semibold">{{ $method->card_holder_name }}</p>
                                        <p class="text-sm text-gray-500">Expires {{ sprintf('%02d', $method->expiry_month) }}/{{ $method->expiry_year }}</p>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <button type="button" data-action="toggle-payment" data-target="payment-edit-{{ $method->id }}" class="text-pink-600 hover:text-pink-700 text-sm">Edit</button>
                                        <form method="POST" action="{{ route('profile.payment.delete', $method) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 text-sm">Remove</button>
                                        </form>
                                    </div>
                                </div>

                                <form id="payment-edit-{{ $method->id }}" class="payment-edit-form mt-4 hidden space-y-4" method="POST" action="{{ route('profile.payment.update', $method) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Card Brand</label>
                                            <input type="text" name="card_brand" value="{{ old('card_brand', $method->card_brand) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Card Holder</label>
                                            <input type="text" name="card_holder_name" value="{{ old('card_holder_name', $method->card_holder_name) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Month</label>
                                            <input type="number" name="expiry_month" value="{{ old('expiry_month', $method->expiry_month) }}" min="1" max="12" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Year</label>
                                            <input type="number" name="expiry_year" value="{{ old('expiry_year', $method->expiry_year) }}" min="{{ date('Y') }}" max="{{ date('Y') + 20 }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" name="is_default" value="1" {{ $method->is_default ? 'checked' : '' }}>
                                        <label class="text-sm text-gray-700">Set as default payment method</label>
                                    </div>
                                    <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded transition duration-300">Save</button>
                                </form>
                            </div>
                        @empty
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 text-gray-700">No payment methods saved yet.</div>
                        @endforelse
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="font-semibold mb-4">Add New Payment Method</h4>
                        <form method="POST" action="{{ route('profile.payment.store') }}" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Card Brand</label>
                                    <input type="text" name="card_brand" value="{{ old('card_brand') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Card Holder Name</label>
                                    <input type="text" name="card_holder_name" value="{{ old('card_holder_name') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Card Number</label>
                                    <input type="text" name="card_number" value="{{ old('card_number') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Month</label>
                                        <input type="number" name="expiry_month" value="{{ old('expiry_month') }}" min="1" max="12" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Year</label>
                                        <input type="number" name="expiry_year" value="{{ old('expiry_year') }}" min="{{ date('Y') }}" max="{{ date('Y') + 20 }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                                <label class="text-sm text-gray-700">Set as default payment method</label>
                            </div>
                            <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-6 rounded transition duration-300">Save Payment Method</button>
                        </form>
                    </div>
                </section>

                <section id="addresses" class="tab-section hidden bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold mb-4">Addresses</h3>

                    <div class="space-y-4 mb-6">
                        @forelse ($addresses as $address)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                                    <div>
                                        <p class="font-semibold">{{ $address->label }}</p>
                                        <p class="text-sm text-gray-600">{{ $address->line1 }} {{ $address->line2 ? ', ' . $address->line2 : '' }}</p>
                                        <p class="text-sm text-gray-600">{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</p>
                                        <p class="text-sm text-gray-600">{{ $address->country }}</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <button type="button" data-action="toggle-address" data-target="address-edit-{{ $address->id }}" class="text-pink-600 hover:text-pink-700 text-sm">Edit</button>
                                        <form method="POST" action="{{ route('profile.address.delete', $address) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 text-sm">Remove</button>
                                        </form>
                                    </div>
                                </div>

                                <form id="address-edit-{{ $address->id }}" class="address-edit-form mt-4 hidden space-y-4" method="POST" action="{{ route('profile.address.update', $address) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Label</label>
                                            <input type="text" name="label" value="{{ old('label', $address->label) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                                            <input type="text" name="postal_code" value="{{ old('postal_code', $address->postal_code) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Line 1</label>
                                        <input type="text" name="line1" value="{{ old('line1', $address->line1) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Line 2</label>
                                        <input type="text" name="line2" value="{{ old('line2', $address->line2) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                            <input type="text" name="city" value="{{ old('city', $address->city) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">State</label>
                                            <input type="text" name="state" value="{{ old('state', $address->state) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                                        <input type="text" name="country" value="{{ old('country', $address->country) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                    </div>
                                    <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-6 rounded transition duration-300">Save Address</button>
                                </form>
                            </div>
                        @empty
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 text-gray-700">No saved addresses yet.</div>
                        @endforelse
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="font-semibold mb-4">Add New Address</h4>
                        <form method="POST" action="{{ route('profile.address.store') }}" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Label</label>
                                    <input type="text" name="label" value="{{ old('label', 'Home') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                                    <input type="text" name="postal_code" value="{{ old('postal_code') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Line 1</label>
                                <input type="text" name="line1" value="{{ old('line1') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Line 2</label>
                                <input type="text" name="line2" value="{{ old('line2') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                    <input type="text" name="city" value="{{ old('city') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">State</label>
                                    <input type="text" name="state" value="{{ old('state') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                                <input type="text" name="country" value="{{ old('country', 'Philippines') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                            </div>
                            <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-6 rounded transition duration-300">Add Address</button>
                        </form>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <script>
        const tabs = document.querySelectorAll('.tab-link');
        const sections = document.querySelectorAll('.tab-section');

        function showTab(tabId) {
            sections.forEach((section) => {
                section.classList.toggle('hidden', section.id !== tabId);
            });

            tabs.forEach((button) => {
                button.classList.toggle('bg-pink-50', button.dataset.tab === tabId);
                button.classList.toggle('text-pink-700', button.dataset.tab === tabId);
                button.classList.toggle('text-gray-700', button.dataset.tab !== tabId);
            });
        }

        tabs.forEach((button) => {
            button.addEventListener('click', () => {
                showTab(button.dataset.tab);
                history.replaceState(null, '', '#'+button.dataset.tab);
            });
        });

        const defaultTab = window.location.hash.replace('#', '') || 'personal-info';
        showTab(defaultTab);

        document.querySelectorAll('[data-action="toggle-order"]').forEach((button) => {
            button.addEventListener('click', () => {
                const target = document.getElementById(button.dataset.target);
                if (target) target.classList.toggle('hidden');
            });
        });

        document.querySelectorAll('[data-action="toggle-address"]').forEach((button) => {
            button.addEventListener('click', () => {
                const target = document.getElementById(button.dataset.target);
                if (target) target.classList.toggle('hidden');
            });
        });

        document.querySelectorAll('[data-action="toggle-payment"]').forEach((button) => {
            button.addEventListener('click', () => {
                const target = document.getElementById(button.dataset.target);
                if (target) target.classList.toggle('hidden');
            });
        });
    </script>
@endsection