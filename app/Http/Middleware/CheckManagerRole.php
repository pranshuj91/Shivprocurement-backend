<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckManagerRole
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'manager') {
            return $next($request);
        }

        Auth::logout();

        return redirect()->route('login')->withErrors([
            'email' => 'Access denied. You do not have manager privileges.',
        ]);
    }
}
