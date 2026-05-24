@extends('layouts.app')

@section('content')
    @php
        $nameParts = explode(' ', $user->name);
        $firstName = $nameParts[0] ?? '';
        $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';
    @endphp

    <div class="max-w-6xl mx-auto">

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

        <div class="overflow-hidden rounded-2xl bg-white shadow-md">
            <div class="grid grid-cols-1 lg:grid-cols-4 items-start">
                <aside class="lg:col-span-1 border-b border-gray-200 lg:border-b-0 lg:border-r lg:border-gray-200 bg-gray-50/60">
                    <div class="p-6 lg:p-8">
                        <div class="text-center mb-8">
                            <div class="relative group w-28 h-28 mx-auto mb-4">
                                <div class="w-28 h-28 rounded-full overflow-hidden bg-pink-100 shadow-inner">
                                    <img src="{{ $user->profile_image_url }}" alt="Profile Picture" class="w-full h-full object-cover">
                                </div>
                                
                                @if($user->profile_picture)
                                    <form method="POST" action="{{ route('profile.picture.delete') }}" class="absolute -top-1 -right-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 text-white rounded-full p-1.5 shadow-md hover:bg-red-600 transition duration-200" title="Remove Profile Picture">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                            <p class="text-gray-600">{{ $user->email }}</p>
                            @if ($user->google_id)
                                <div class="mt-2 flex items-center justify-center gap-1.5 text-xs font-medium text-blue-600">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12.48 10.92v3.28h4.74c-.2 1.2-.92 2.22-1.94 2.92v2.44h3.14c1.84-1.68 2.9-4.16 2.9-7.08 0-.58-.06-1.14-.18-1.56H12.48z" fill="#4285F4"></path>
                                        <path d="M12 21c2.44 0 4.5-.8 6.02-2.18l-3.14-2.44c-.82.56-1.88.88-2.88.88-2.22 0-4.12-1.5-4.78-3.52H4.12v2.52C5.62 18.78 8.6 21 12 21z" fill="#34A853"></path>
                                        <path d="M7.22 13.74c-.16-.5-.26-1.04-.26-1.74s.1-1.24.26-1.74V7.74H4.12c-.54 1.08-.86 2.3-.86 3.6s.32 2.52.86 3.6l3.1-2.46z" fill="#FBBC05"></path>
                                        <path d="M12 6.38c1.32 0 2.5.46 3.44 1.34l2.58-2.58C16.5 3.6 14.44 3 12 3 8.6 3 5.62 5.22 4.12 7.74l3.1 2.46c.66-2.02 2.56-3.52 4.78-3.52z" fill="#EA4335"></path>
                                    </svg>
                                    Connected via Google
                                </div>
                            @endif
                        </div>

                        <nav class="space-y-2">
                            <button type="button" data-tab="personal-info" class="tab-link block w-full rounded-xl border border-transparent px-4 py-3 text-left font-medium text-gray-700 transition hover:bg-pink-50 hover:text-pink-700">Personal Information</button>
                            <button type="button" data-tab="order-history" class="tab-link block w-full rounded-xl border border-transparent px-4 py-3 text-left font-medium text-gray-700 transition hover:bg-pink-50 hover:text-pink-700">Order History</button>
                            <button type="button" data-tab="payment-methods" class="tab-link block w-full rounded-xl border border-transparent px-4 py-3 text-left font-medium text-gray-700 transition hover:bg-pink-50 hover:text-pink-700">Payment Methods</button>
                            <button type="button" data-tab="addresses" class="tab-link block w-full rounded-xl border border-transparent px-4 py-3 text-left font-medium text-gray-700 transition hover:bg-pink-50 hover:text-pink-700">Addresses</button>
                            <button type="button" data-tab="security" class="tab-link block w-full rounded-xl border border-transparent px-4 py-3 text-left font-medium text-gray-700 transition hover:bg-pink-50 hover:text-pink-700">Security & Verification</button>
                        </nav>
                    </div>
                </aside>

                <main class="lg:col-span-3 p-6 lg:p-8">
                    <section id="personal-info" class="tab-section">
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

                    <section id="order-history" class="tab-section hidden">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-[#4D2E38]">Order History</h3>
                        <span class="text-sm text-[#8A6A76]">Showing {{ $orders->count() }} most recent orders</span>
                    </div>

                    @forelse ($orders as $order)
                        <div class="mb-4 rounded-2xl border border-[#E9C7D4] bg-[linear-gradient(180deg,#FFFFFF_0%,#FBF2F6_100%)] p-4 shadow-[0_8px_24px_rgba(77,46,56,0.06)]">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div>
                                    <h4 class="font-semibold text-[#4D2E38]">{{ $order->order_number }}</h4>
                                    <p class="text-sm text-[#8A6A76]">Placed on {{ $order->placed_at?->format('F j, Y') ?? 'N/A' }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="rounded-full border border-[#E9C7D4] bg-[#FBEAF1] px-3 py-1 text-xs font-semibold text-[#8F6172]">
                                        {{ $order->status }}
                                    </span>
                                    <p class="text-lg font-bold text-[#4D2E38]">₱{{ number_format($order->total_amount, 2) }}</p>
                                </div>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-3">
                                <button type="button" data-action="toggle-order" data-target="order-details-{{ $order->id }}" class="rounded-md border border-[#E9C7D4] bg-[#FBEAF1] px-3 py-1.5 text-sm font-semibold text-[#4D2E38] hover:bg-[#F6DFE9]">View Details</button>
                                <form method="POST" action="{{ route('profile.order.reorder', $order) }}">
                                    @csrf
                                    <button type="submit" class="rounded-md bg-[#C47A90] px-3 py-1.5 text-sm font-semibold text-white hover:bg-[#B66880]">Reorder</button>
                                </form>
                            </div>

                            <div id="order-details-{{ $order->id }}" class="order-details mt-4 hidden rounded-xl border border-[#ECD8E0] bg-[#FBF2F6] p-4">
                                <p class="text-sm text-[#533843]">{{ $order->description ?? 'No additional details available.' }}</p>
                                <p class="mt-2 text-sm text-[#8A6A76]">Order created at: {{ $order->created_at->format('F j, Y h:i A') }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-[#E9C7D4] bg-[#FBF2F6] p-6 text-[#533843]">
                            You have no orders yet. Your recent purchases will appear here.
                        </div>
                    @endforelse
                    </section>

                    <section id="payment-methods" class="tab-section hidden">
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
                            <div class="rounded-2xl border border-dashed border-[#E9C7D4] bg-[#FBF2F6] p-6 text-[#533843]">No payment methods saved yet.</div>
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

                    <section id="addresses" class="tab-section hidden">
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
                                        <input id="edit-line1-{{ $address->id }}" type="text" name="line1" value="{{ old('line1', $address->line1) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                        <button type="button" data-action="open-address-map" data-line1-target="edit-line1-{{ $address->id }}" class="mt-2 inline-flex items-center gap-2 rounded-full bg-pink-50 px-3 py-1.5 text-xs font-semibold text-pink-700 transition hover:bg-pink-100">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            Pin home delivery location
                                        </button>
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
                            <div class="rounded-2xl border border-dashed border-[#E9C7D4] bg-[#FBF2F6] p-6 text-[#533843]">No saved addresses yet.</div>
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
                                <input id="new-address-line1" type="text" name="line1" value="{{ old('line1') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                <button type="button" data-action="open-address-map" data-line1-target="new-address-line1" class="mt-2 inline-flex items-center gap-2 rounded-full bg-pink-50 px-3 py-1.5 text-xs font-semibold text-pink-700 transition hover:bg-pink-100">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Pin home delivery location
                                </button>
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

                    <!-- Security & Verification Section -->
                    <section id="security" class="tab-section hidden">
                        <div class="space-y-8">
                            <!-- Change Password -->
                            <div>
                                <h3 class="text-xl font-bold mb-4">Change Password</h3>
                                <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-6">
                                    @csrf
                                    @method('PUT')

                                    @if(!$user->google_id || !empty($user->password))
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                            <input type="password" name="current_password" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                            @error('current_password')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @endif

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                            <input type="password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                            @error('password')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                            <input type="password" name="password_confirmation" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                        </div>
                                    </div>

                                    <button type="submit" class="bg-[#8B5A63] hover:bg-[#E6B7BE] text-[#F5F5F5] hover:text-[#5A3A3A] font-bold py-3 px-8 rounded-lg transition duration-300">
                                        Update Password
                                    </button>
                                </form>
                            </div>
                        </div>
                    </section>
                </main>
            </div>
        </div>
    </div>

    <div id="address-map-modal" class="fixed inset-0 z-[9999] hidden">
        <div id="address-map-backdrop" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="absolute inset-3 md:inset-6 lg:inset-10 z-10 flex flex-col overflow-hidden rounded-3xl bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-[#F5E6E8] px-6 py-4">
                <h3 class="text-base font-bold text-[#5A3A3A]">Pin Home Delivery Address</h3>
                <button type="button" id="address-map-close" class="flex h-10 w-10 items-center justify-center rounded-full bg-[#F9EFF1] text-[#5A3A3A] hover:bg-[#F0D5DB]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="border-b border-[#F5E6E8] bg-[#FFF9FA] px-6 py-3">
                <div class="flex items-center gap-2">
                    <input type="text" id="address-map-search" placeholder="Search place or address..." class="w-full rounded-xl border border-[#F5E6E8] px-4 py-3 text-sm text-[#5A3A3A] focus:border-[#C88A92] focus:outline-none focus:ring-2 focus:ring-[#C88A92]/20">
                    <button type="button" id="address-map-locate" class="inline-flex items-center gap-1 rounded-xl border border-[#EED9DE] bg-white px-3 py-3 text-xs font-semibold text-[#5A3A3A] hover:bg-[#F9EFF1]">
                        <svg class="h-4 w-4 text-[#C88A92]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3A8.994 8.994 0 0013 3.06V1h-2v2.06A8.994 8.994 0 003.06 11H1v2h2.06A8.994 8.994 0 0011 20.94V23h2v-2.06A8.994 8.994 0 0020.94 13H23v-2h-2.06z"></path>
                        </svg>
                        Use GPS
                    </button>
                </div>
            </div>
            <div id="address-map-picker" class="relative min-h-0 flex-1"></div>
            <div class="border-t border-[#F5E6E8] px-6 py-4">
                <p id="address-map-selected" class="mb-3 text-sm text-[#8C6770]">Move pin to choose location</p>
                <button type="button" id="address-map-confirm" class="w-full rounded-full bg-[#5A3A3A] py-3 text-sm font-bold text-white hover:bg-[#7A5252]">
                    Use This Location
                </button>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

    <script>
        const tabs = document.querySelectorAll('.tab-link');
        const sections = document.querySelectorAll('.tab-section');

        function showTab(tabId) {
            sections.forEach((section) => {
                section.classList.toggle('hidden', section.id !== tabId);
            });

            tabs.forEach((button) => {
                button.classList.toggle('bg-pink-50', button.dataset.tab === tabId);
                button.classList.toggle('border-pink-200', button.dataset.tab === tabId);
                button.classList.toggle('text-pink-700', button.dataset.tab === tabId);
                button.classList.toggle('font-semibold', button.dataset.tab === tabId);
                button.classList.toggle('border-transparent', button.dataset.tab !== tabId);
                button.classList.toggle('text-gray-700', button.dataset.tab !== tabId);
                button.classList.toggle('font-medium', button.dataset.tab !== tabId);
            });
        }

        tabs.forEach((button) => {
            button.addEventListener('click', () => {
                showTab(button.dataset.tab);
                history.replaceState(null, '', '#'+button.dataset.tab);
            });
        });

        const defaultTab = window.location.hash.replace('#', '') || 
            @if($errors->has('current_password') || $errors->has('password')) 'security' 
            @elseif($errors->hasAny(['first_name', 'last_name', 'email', 'phone'])) 'personal-info'
            @else 'personal-info' @endif;
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

        (function () {
            const mapModal = document.getElementById('address-map-modal');
            const mapBackdrop = document.getElementById('address-map-backdrop');
            const closeMapBtn = document.getElementById('address-map-close');
            const confirmMapBtn = document.getElementById('address-map-confirm');
            const selectedAddressEl = document.getElementById('address-map-selected');
            const searchInput = document.getElementById('address-map-search');
            const locateBtn = document.getElementById('address-map-locate');
            const openButtons = document.querySelectorAll('[data-action="open-address-map"]');

            if (!mapModal || !confirmMapBtn || openButtons.length === 0 || typeof L === 'undefined') return;

            let map = null;
            let marker = null;
            let targetInput = null;
            let selectedAddress = '';
            let selectedComponents = null;

            const defaultCenter = [14.4585, 120.9829];

            const composeLine1 = (addr) => {
                const segments = [
                    addr.house_number,
                    addr.road,
                    addr.neighbourhood,
                    addr.suburb,
                    addr.village,
                ].filter(Boolean);

                if (segments.length > 0) {
                    return segments.join(', ');
                }

                return null;
            };

            const extractAddressComponents = (addr) => ({
                line1: composeLine1(addr) ?? null,
                line2: addr.quarter ?? addr.hamlet ?? null,
                city: addr.city ?? addr.town ?? addr.municipality ?? addr.village ?? null,
                state: addr.state ?? addr.region ?? addr.province ?? null,
                postalCode: addr.postcode ?? null,
                country: addr.country ?? null,
            });

            const updateSelectedAddress = async (lat, lng) => {
                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}&addressdetails=1&accept-language=en`);
                    const result = await response.json();
                    selectedComponents = extractAddressComponents(result?.address ?? {});
                    selectedAddress = (result && (result.display_name || result.name)) || `Lat ${lat.toFixed(6)}, Lng ${lng.toFixed(6)}`;
                } catch (error) {
                    selectedComponents = null;
                    selectedAddress = `Lat ${lat.toFixed(6)}, Lng ${lng.toFixed(6)}`;
                }

                if (selectedAddressEl) {
                    selectedAddressEl.textContent = selectedAddress;
                }
            };

            const initMap = () => {
                if (map) return;

                map = L.map('address-map-picker', {
                    zoomControl: true,
                }).setView(defaultCenter, 14);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a>',
                    maxZoom: 19,
                }).addTo(map);

                marker = L.marker(defaultCenter, { draggable: true }).addTo(map);

                marker.on('dragend', () => {
                    const point = marker.getLatLng();
                    updateSelectedAddress(point.lat, point.lng);
                });

                map.on('click', (event) => {
                    marker.setLatLng(event.latlng);
                    updateSelectedAddress(event.latlng.lat, event.latlng.lng);
                });

                updateSelectedAddress(defaultCenter[0], defaultCenter[1]);
            };

            const openMap = (input) => {
                targetInput = input;
                mapModal.classList.remove('hidden');
                initMap();

                const currentValue = (targetInput?.value || '').trim();
                if (currentValue) {
                    selectedAddress = currentValue;
                    if (selectedAddressEl) {
                        selectedAddressEl.textContent = currentValue;
                    }
                }

                setTimeout(() => {
                    if (map) map.invalidateSize();
                }, 120);

                if (navigator.geolocation && map && marker) {
                    navigator.geolocation.getCurrentPosition((position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        map.flyTo([lat, lng], 17, { duration: 1.1 });
                        marker.setLatLng([lat, lng]);
                        updateSelectedAddress(lat, lng);
                    });
                }
            };

            const closeMap = () => {
                mapModal.classList.add('hidden');
                searchInput.value = '';
            };

            openButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const targetId = button.dataset.line1Target;
                    const input = targetId ? document.getElementById(targetId) : null;
                    if (!input) return;
                    openMap(input);
                });
            });

            closeMapBtn?.addEventListener('click', closeMap);
            mapBackdrop?.addEventListener('click', closeMap);

            confirmMapBtn.addEventListener('click', () => {
                if (targetInput && selectedAddress) {
                    const parentForm = targetInput.closest('form');
                    const line1Field = parentForm?.querySelector('input[name="line1"]');
                    const line2Field = parentForm?.querySelector('input[name="line2"]');
                    const cityField = parentForm?.querySelector('input[name="city"]');
                    const stateField = parentForm?.querySelector('input[name="state"]');
                    const postalField = parentForm?.querySelector('input[name="postal_code"]');
                    const countryField = parentForm?.querySelector('input[name="country"]');

                    line1Field.value = selectedComponents?.line1 || selectedAddress;
                    if (line2Field && selectedComponents?.line2) line2Field.value = selectedComponents.line2;
                    if (cityField && selectedComponents?.city) cityField.value = selectedComponents.city;
                    if (stateField && selectedComponents?.state) stateField.value = selectedComponents.state;
                    if (postalField && selectedComponents?.postalCode) postalField.value = selectedComponents.postalCode;
                    if (countryField && selectedComponents?.country) countryField.value = selectedComponents.country;
                }
                closeMap();
            });

            locateBtn?.addEventListener('click', () => {
                if (!navigator.geolocation || !map || !marker) return;
                navigator.geolocation.getCurrentPosition((position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    map.flyTo([lat, lng], 17, { duration: 1.1 });
                    marker.setLatLng([lat, lng]);
                    updateSelectedAddress(lat, lng);
                });
            });

            searchInput?.addEventListener('keydown', async (event) => {
                if (event.key !== 'Enter') return;
                event.preventDefault();

                const query = searchInput.value.trim();
                if (!query || !map || !marker) return;

                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&q=${encodeURIComponent(query)}&countrycodes=ph&limit=1`);
                    const results = await response.json();
                    if (!Array.isArray(results) || results.length === 0) return;

                    const lat = parseFloat(results[0].lat);
                    const lng = parseFloat(results[0].lon);
                    map.flyTo([lat, lng], 17, { duration: 1.1 });
                    marker.setLatLng([lat, lng]);
                    updateSelectedAddress(lat, lng);
                } catch (error) {
                    console.warn('Address map search failed.', error);
                }
            });
        })();
    </script>
@endsection

