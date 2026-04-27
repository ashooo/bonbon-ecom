@extends('layouts.app')

@section('content')
    @php $showRegister = $showRegister ?? false; @endphp
    <style>
        #login-form input[type="email"],
        #login-form input[type="password"],
        #login-form label {
            color: #111827;
        }

        #login-form input[type="email"]::placeholder,
        #login-form input[type="password"]::placeholder {
            color: #9ca3af;
        }

        #login-form input:-webkit-autofill,
        #login-form input:-webkit-autofill:hover,
        #login-form input:-webkit-autofill:focus {
            -webkit-text-fill-color: #111827;
            -webkit-box-shadow: 0 0 0 1000px #ffffff inset;
            transition: background-color 9999s ease-in-out 0s;
        }
    </style>
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
        <h1 class="text-3xl font-bold text-center mb-8">Welcome Back</h1>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <!-- Login Form -->
        <form id="login-form" method="POST" action="{{ route('login') }}" class="space-y-6 {{ $showRegister ? 'hidden' : '' }}">
            @csrf
            <!-- Email -->
            <div>
                <label for="email" class="mb-2 block text-sm font-medium text-[#E6B7BE]">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-black focus:outline-none focus:ring-2 focus:ring-pink-500 transition-colors">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="mb-2 block text-sm font-medium text-[#E6B7BE]">Password</label>
                <input type="password" id="password" name="password" required class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-black focus:outline-none focus:ring-2 focus:ring-pink-500 transition-colors">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input type="checkbox" id="remember" name="remember" value="1" class="mr-2">
                <label for="remember" class="text-sm text-black">Remember me</label>
            </div>

            <!-- Login Button -->
            <button type="submit" class="w-full bg-[#E6B7BE] dark:bg-[#8B5A63] hover:bg-[#D9A0A8] dark:hover:bg-[#A67680] text-[#5A3A3A] dark:text-[#F5F5F5] font-bold py-3 px-6 rounded-lg transition duration-300">
                Sign In
            </button>
        </form>

        <!-- Divider -->
        <div class="mt-6 mb-6">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">Or continue with</span>
                </div>
            </div>
        </div>

        <!-- Google Login -->
        <a href="{{ route('google.login') }}" class="w-full bg-white dark:bg-[#1A1A1A] hover:bg-gray-50 dark:hover:bg-[#2E2E2E] text-gray-700 dark:text-[#E8E8E8] font-semibold py-3 px-6 border border-gray-300 dark:border-[#444444] rounded-lg transition duration-300 flex items-center justify-center">
            <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Sign in with Google
        </a>

        <!-- Register Link -->
        <div class="mt-6 text-center">
            <p class="text-gray-600">Don't have an account? <a href="/register" class="text-pink-600 hover:text-pink-700 font-semibold">Sign up</a></p>
        </div>

        <!-- Forgot Password -->
        <div class="mt-4 text-center">
            <a href="{{ route('password.request') }}" class="text-sm text-gray-500 hover:text-gray-700">Forgot your password?</a>
        </div>
    </div>

    <!-- Register Form (hidden by default, can be toggled) -->
    <div id="register-form" class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8 mt-8 {{ $showRegister ? '' : 'hidden' }}">
        <h2 class="text-2xl font-bold text-center mb-6">Create Account</h2>

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf
            <!-- Name -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="first-name" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                    <input type="text" id="first-name" name="first_name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                </div>
                <div>
                    <label for="last-name" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                    <input type="text" id="last-name" name="last_name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                </div>
            </div>

            <!-- Email -->
            <div>
                <label for="register-email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email" id="register-email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
            </div>

            <!-- Password -->
            <div>
                <label for="register-password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" id="register-password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="confirm-password" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                <input type="password" id="confirm-password" name="password_confirmation" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
            </div>

            <!-- Register Button -->
            <button type="submit" class="w-full bg-pink-600 hover:bg-pink-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300">
                Create Account
            </button>
        </form>

        <!-- Login Link -->
        <div class="mt-6 text-center">
            <p class="text-gray-600">Already have an account? <a href="/login" class="text-pink-600 hover:text-pink-700 font-semibold">Sign in</a></p>
        </div>
    </div>

    <script>
        // Toggle between login and register forms
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');
        const registerLink = document.querySelector('a[href="/register"]');
        const loginLink = registerForm.querySelector('a[href="/login"]');

        registerLink.addEventListener('click', (e) => {
            e.preventDefault();
            loginForm.classList.add('hidden');
            registerForm.classList.remove('hidden');
        });

        loginLink.addEventListener('click', (e) => {
            e.preventDefault();
            registerForm.classList.add('hidden');
            loginForm.classList.remove('hidden');
        });
    </script>
@endsection
