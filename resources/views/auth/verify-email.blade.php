@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
        <div class="text-center mb-6">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-pink-50">
                <svg class="h-8 w-8 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Verify Your Email</h1>
            <p class="mt-2 text-sm text-gray-600">
                We've sent a verification link to your email address. Please check your inbox and click the link to activate your account.
            </p>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('status') === 'verification-link-sent')
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
                A new verification link has been sent to your email address.
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
                    class="w-full rounded-lg bg-[#E6B7BE] px-6 py-3 font-bold text-[#5A3A3A] transition duration-300 hover:bg-[#D9A0A8]">
                Resend Verification Email
            </button>
        </form>

        <div class="mt-6 text-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">
                    Sign out
                </button>
            </form>
        </div>
    </div>
@endsection
