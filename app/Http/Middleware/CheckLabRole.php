<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckLabRole
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'lab') {
            return $next($request);
        }

        Auth::logout();

        return redirect()->route('login')->withErrors([
            'email' => 'Access denied. Lab portal access only.',
        ]);
    }
}
