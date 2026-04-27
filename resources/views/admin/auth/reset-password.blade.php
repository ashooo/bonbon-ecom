@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-md rounded-lg bg-white p-8 shadow-md">
        <h1 class="mb-4 text-center text-3xl font-bold text-[#5A3A3A]">Reset Admin Password</h1>
        <p class="mb-6 text-center text-sm text-gray-600">
            Create a new password for your administrator account.
        </p>

        <form method="POST" action="{{ route('admin.password.update') }}" class="space-y-6">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="mb-2 block text-sm font-medium text-[#5A3A3A]">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email', $email) }}"
                    required
                    class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-black focus:outline-none focus:ring-2 focus:ring-pink-500"
                >
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="mb-2 block text-sm font-medium text-[#5A3A3A]">New Password</label>
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

            <div>
                <label for="password_confirmation" class="mb-2 block text-sm font-medium text-[#5A3A3A]">Confirm New Password</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                    class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-black focus:outline-none focus:ring-2 focus:ring-pink-500"
                >
            </div>

            <button type="submit" class="w-full rounded-lg bg-[#5A3A3A] px-6 py-3 font-bold text-white transition duration-300 hover:bg-[#7A5252]">
                Reset Password
            </button>
        </form>
    </div>
@endsection

