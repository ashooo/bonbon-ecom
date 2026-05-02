@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto rounded-lg bg-white p-8 shadow-md">
        <h1 class="mb-4 text-center text-3xl font-bold">Forgot Password</h1>
        <p class="mb-6 text-center text-sm text-gray-600">
            Enter your email address and we will send you a password reset link.
        </p>

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="mb-2 block text-sm font-medium text-[#E6B7BE]">Email Address</label>
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

            <button type="submit" class="w-full rounded-lg bg-[#E6B7BE] px-6 py-3 font-bold text-[#5A3A3A] transition duration-300 hover:bg-[#D9A0A8]">
                Send
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700">Back to sign in</a>
        </div>
    </div>
@endsection
