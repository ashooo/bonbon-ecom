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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

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
            'is_active' => true,
        ]);

        $user->sendEmailVerificationNotification();

        return redirect()->route('login')->with('success', 'Account created! Please check your email to verify your account before logging in.');
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
            'is_active' => true,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $fallback = '/';
            return redirect()->intended($fallback)->with('success', 'Welcome back!');
        }

        return redirect()
            ->route('login')
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
                    'avatar' => $this->downloadGoogleAvatar($googleUser->getAvatar(), $googleUser->getId()),
                    'password' => Hash::make(uniqid()), // Random password for Google users
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);
            } else {
                // Update Google data if changed
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $this->downloadGoogleAvatar($googleUser->getAvatar(), $googleUser->getId()),
                    'name' => $googleUser->getName(), // Sync name too
                    'email_verified_at' => $user->email_verified_at ?? now(), // Auto-verify if not already verified
                ]);
            }

            if ($user->is_admin) {
                return redirect('/login')->with('error', 'Google sign-in is not available for admin accounts.');
            }
            if (! $user->is_active) {
                return redirect('/login')->with('error', 'Your account is currently inactive. Please contact support.');
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

    /**
     * Download Google avatar and return local path
     */
    private function downloadGoogleAvatar($url, $googleId)
    {
        try {
            $response = Http::get($url);
            if ($response->successful()) {
                $filename = 'profile_pictures/google_' . $googleId . '.jpg';
                Storage::disk('public')->put($filename, $response->body());
                return $filename;
            }
        } catch (\Exception $e) {
            Log::warning('Failed to download Google avatar: ' . $e->getMessage());
        }

        return $url; // Fallback to URL if download fails
    }
}
