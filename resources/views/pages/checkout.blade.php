@extends('layouts.app')

@section('content')
    <h1 class="text-3xl font-bold mb-8">Checkout</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <!-- Order Summary -->
        <div>
            <h2 class="text-2xl font-bold mb-6">Order Summary</h2>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="space-y-4">
                    <!-- Order Item 1 -->
                    <div class="flex items-center space-x-4">
                        <img src="https://via.placeholder.com/80x80?text=Chocolate+Cake" alt="Chocolate Cake" class="w-16 h-16 rounded">
                        <div class="flex-1">
                            <h3 class="font-semibold">Chocolate Delight Cake</h3>
                            <p class="text-gray-600">Small size</p>
                            <p class="text-pink-600">$25.99</p>
                        </div>
                        <span>Qty: 1</span>
                    </div>

                    <!-- Order Item 2 -->
                    <div class="flex items-center space-x-4">
                        <img src="https://via.placeholder.com/80x80?text=Vanilla+Cake" alt="Vanilla Cake" class="w-16 h-16 rounded">
                        <div class="flex-1">
                            <h3 class="font-semibold">Vanilla Dream Cake</h3>
                            <p class="text-gray-600">Medium size</p>
                            <p class="text-pink-600">$35.99</p>
                        </div>
                        <span>Qty: 2</span>
                    </div>
                </div>

                <hr class="my-6">

                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span>$61.98</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Delivery</span>
                        <span>$5.99</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Tax</span>
                        <span>$6.20</span>
                    </div>
                </div>

                <hr class="my-4">

                <div class="flex justify-between text-xl font-bold">
                    <span>Total</span>
                    <span>$74.17</span>
                </div>
            </div>
        </div>

        <!-- Delivery Information and Payment -->
        <div>
            <!-- Delivery Information -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-6">Delivery Information</h2>
                <form class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="tel" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <input type="text" placeholder="Street address" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 mb-2">
                        <input type="text" placeholder="Apartment, suite, etc." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">ZIP Code</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Date & Time</label>
                        <input type="datetime-local" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                    </div>
                </form>
            </div>

            <!-- Payment Method -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-6">Payment Method</h2>
                <div class="space-y-4">
                    <div class="border border-gray-300 rounded-md p-4">
                        <label class="flex items-center">
                            <input type="radio" name="payment" value="card" class="mr-3">
                            <span class="font-medium">Credit/Debit Card</span>
                        </label>
                        <div class="mt-4 space-y-2">
                            <input type="text" placeholder="Card Number" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                            <div class="grid grid-cols-2 gap-4">
                                <input type="text" placeholder="MM/YY" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                <input type="text" placeholder="CVV" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-300 rounded-md p-4">
                        <label class="flex items-center">
                            <input type="radio" name="payment" value="paypal" class="mr-3">
                            <span class="font-medium">PayPal</span>
                        </label>
                    </div>

                    <div class="border border-gray-300 rounded-md p-4">
                        <label class="flex items-center">
                            <input type="radio" name="payment" value="cash" class="mr-3">
                            <span class="font-medium">Cash on Delivery</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Place Order Button -->
            <button class="w-full bg-pink-600 hover:bg-pink-700 text-white font-bold py-4 px-6 rounded-lg text-lg transition duration-300">
                Place Order
            </button>
        </div>
    </div>
@endsection