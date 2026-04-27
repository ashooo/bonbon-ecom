@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-md rounded-lg bg-white p-8 shadow-md">
        <h1 class="mb-3 text-center text-3xl font-bold text-[#5A3A3A]">Admin Sign In</h1>
        <p class="mb-6 text-center text-sm text-gray-600">Use your administrator credentials to access the dashboard.</p>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="mb-2 block text-sm font-medium text-[#5A3A3A]">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-black focus:outline-none focus:ring-2 focus:ring-pink-500"
                >
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="mb-2 block text-sm font-medium text-[#5A3A3A]">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-black focus:outline-none focus:ring-2 focus:ring-pink-500"
                >
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="remember" name="remember" value="1" class="mr-2">
                <label for="remember" class="text-sm text-black">Remember me</label>
            </div>

            <button type="submit" class="w-full rounded-lg bg-[#5A3A3A] px-6 py-3 font-bold text-white transition duration-300 hover:bg-[#7A5252]">
                Sign In as Admin
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('admin.password.request') }}" class="text-sm text-gray-500 hover:text-gray-700">Forgot your password?</a>
        </div>

        <div class="mt-4 text-center text-sm text-gray-500">
            Looking for a customer account? <a href="{{ route('login') }}" class="font-semibold text-pink-600 hover:text-pink-700">Go to customer login</a>
        </div>
    </div>
@endsection
