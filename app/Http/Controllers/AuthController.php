<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if (! in_array($user->role, ['manager', 'lab'], true)) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Access denied. Use the mobile app for supervisor login.',
                ]);
            }

            $request->session()->regenerate();

            return match ($user->role) {
                'lab' => redirect()->intended(route('lab.dashboard')),
                default => redirect()->intended(route('admin.dashboard')),
            };
        }

        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Show the signup form.
     */
    public function showSignup()
    {
        if (Auth::check() && Auth::user()->role === 'manager') {
            return redirect('/admin');
        }
        return view('auth.signup');
    }

    /**
     * Handle the signup request.
     */
    public function signup(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'], // cast in User model hashes it automatically
            'role' => 'manager',
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect('/admin');
    }

    /**
     * Handle the logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
