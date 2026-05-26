<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return match (Auth::user()->role) {
                'lab' => redirect()->route('lab.dashboard'),
                'manager' => redirect()->route('admin.dashboard'),
                default => redirect()->route('admin.dashboard'),
            };
        }

        return view('auth.login');
    }

    /**
     * Handle the login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $email = $this->resolveEmailFromLogin($request->input('username'));

        if (Auth::attempt(
            ['email' => $email, 'password' => $request->input('password')],
            $request->boolean('remember')
        )) {
            $user = Auth::user();
            if (! in_array($user->role, ['manager', 'lab'], true)) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'username' => 'Access denied. Use the mobile app for supervisor login.',
                ]);
            }

            $request->session()->regenerate();

            return match ($user->role) {
                'lab' => redirect()->intended(route('lab.dashboard')),
                default => redirect()->intended(route('admin.dashboard')),
            };
        }

        throw ValidationException::withMessages([
            'username' => 'Invalid username or password.',
        ]);
    }

    /**
     * Map login username to the stored email address.
     */
    private function resolveEmailFromLogin(string $login): string
    {
        $login = trim($login);

        if (str_contains($login, '@')) {
            return strtolower($login);
        }

        return match (strtolower($login)) {
            'admin' => 'admin@shivedibles.com',
            'lab' => 'lab@shivedibles.com',
            default => strtolower($login),
        };
    }

    /**
     * Handle the logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
