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
        if (Auth::check() && Auth::user()->role === 'manager') {
            return redirect('/admin');
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
            if ($user->role !== 'manager') {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Access denied. Only managers can log in to the web admin portal.',
                ]);
            }

            $request->session()->regenerate();

            return redirect()->intended('/admin');
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
