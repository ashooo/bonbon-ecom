@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-md rounded-lg bg-white p-8 shadow-md">
        <h1 class="mb-4 text-center text-3xl font-bold text-[#5A3A3A]">Admin Password Reset</h1>
        <p class="mb-6 text-center text-sm text-gray-600">
            Enter your admin email address and we will send a password reset link.
        </p>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.password.email') }}" class="space-y-6">
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

            <button type="submit" class="w-full rounded-lg bg-[#5A3A3A] px-6 py-3 font-bold text-white transition duration-300 hover:bg-[#7A5252]">
                Send Reset Link
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('admin.login') }}" class="text-sm text-gray-500 hover:text-gray-700">Back to admin sign in</a>
        </div>
    </div>
@endsection

