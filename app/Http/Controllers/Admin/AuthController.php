<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private const INVALID_CREDENTIALS_MESSAGE = 'The provided credentials do not match our records.';

    public function showLoginForm()
    {
        if (Auth::check()) {
            return Auth::user()?->is_admin
                ? redirect()->route('admin.dashboard')
                : redirect('/');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            return Auth::user()?->is_admin
                ? redirect()->route('admin.dashboard')
                : redirect('/');
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember' => 'nullable|boolean',
        ]);

        $email = Str::lower(trim($request->string('email')->value()));

        $credentials = [
            'email' => $email,
            'password' => $request->input('password'),
            'is_admin' => true,
        ];

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return redirect()->route('admin.login')->withErrors([
                'email' => self::INVALID_CREDENTIALS_MESSAGE,
            ])->onlyInput('email');
        }
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'))->with('success', 'Welcome back!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'You have been logged out successfully.');
    }

    public function showForgotPasswordForm()
    {
        if (Auth::check()) {
            return Auth::user()?->is_admin
                ? redirect()->route('admin.dashboard')
                : redirect('/');
        }

        return view('admin.auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            return Auth::user()?->is_admin
                ? redirect()->route('admin.dashboard')
                : redirect('/');
        }

        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::query()
            ->where('email', $request->string('email')->value())
            ->where('is_admin', true)
            ->first();

        if ($user) {
            Password::sendResetLink(['email' => $user->email]);
        }

        return back()->with('success', 'If an eligible admin account exists, a reset link has been sent.');
    }

    public function showResetPasswordForm(Request $request, string $token)
    {
        if (Auth::check()) {
            return Auth::user()?->is_admin
                ? redirect()->route('admin.dashboard')
                : redirect('/');
        }

        return view('admin.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            return Auth::user()?->is_admin
                ? redirect()->route('admin.dashboard')
                : redirect('/');
        }

        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::query()
            ->where('email', $request->string('email')->value())
            ->where('is_admin', true)
            ->first();

        if (! $user) {
            return back()->withErrors([
                'email' => 'Unable to reset password for this account.',
            ])->onlyInput('email');
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                if (! $user->is_admin) {
                    return;
                }

                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withErrors(['email' => __($status)])->onlyInput('email');
        }

        return redirect()->route('admin.login')->with('success', 'Your password has been reset. You can now sign in.');
    }
}
