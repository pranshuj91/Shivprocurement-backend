<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPortalRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (Auth::check() && in_array(Auth::user()->role, $roles, true)) {
            return $next($request);
        }

        Auth::logout();

        return redirect()->route('login')->withErrors([
            'email' => 'Access denied. You do not have permission for this action.',
        ]);
    }
}
