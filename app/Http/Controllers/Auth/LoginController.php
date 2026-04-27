<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    private const INVALID_CREDENTIALS_MESSAGE = 'The provided credentials do not match our records.';

    private function redirectIfAuthenticatedAdmin()
    {
        if (Auth::check() && Auth::user()?->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        return null;
    }

    public function showLoginForm()
    {
        if ($redirect = $this->redirectIfAuthenticatedAdmin()) {
            return $redirect;
        }

        return view('auth.login', ['showRegister' => false]);
    }

    public function showRegisterForm()
    {
        if ($redirect = $this->redirectIfAuthenticatedAdmin()) {
            return $redirect;
        }

        return view('auth.login', ['showRegister' => true]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => trim($request->input('first_name') . ' ' . $request->input('last_name')),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        return redirect()->route('login')->with('success', 'Account created successfully! Please log in with your credentials.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'remember' => 'nullable|boolean'
        ]);

        $email = Str::lower(trim($request->string('email')->value()));

        $credentials = [
            'email' => $email,
            'password' => $request->input('password'),
            'is_admin' => false,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $fallback = '/';
            return redirect()->intended($fallback)->with('success', 'Welcome back!');
        }

        return back()
            ->withErrors(['email' => self::INVALID_CREDENTIALS_MESSAGE])
            ->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }

    public function showForgotPasswordForm()
    {
        if ($redirect = $this->redirectIfAuthenticatedAdmin()) {
            return $redirect;
        }

        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', __($status))
            : back()->withErrors(['email' => __($status)])->onlyInput('email');
    }

    public function showResetPasswordForm(Request $request, string $token)
    {
        if ($redirect = $this->redirectIfAuthenticatedAdmin()) {
            return $redirect;
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Your password has been reset. You can now sign in.')
            : back()->withErrors(['email' => __($status)])->onlyInput('email');
    }

    // Google OAuth
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if (! $user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(uniqid()), // Random password for Google users
                ]);
            } else {
                // Update Google ID if not set
                if (! $user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                    ]);
                }
            }

            if ($user->is_admin) {
                return redirect('/login')->with('error', 'Google sign-in is not available for admin accounts.');
            }

            Auth::login($user);

            $fallback = $user->is_admin
                ? route('admin.dashboard')
                : '/';

            return redirect()->intended($fallback)->with('success', 'Welcome! You have been logged in with Google.');

        } catch (\Exception $e) {
            Log::error('Google login failed.', [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            return redirect('/login')->with('error', 'Something went wrong with Google login. Please try again.');
        }
    }
}
