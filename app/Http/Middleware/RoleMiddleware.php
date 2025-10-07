<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // cek apakah user sudah login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // cek role
        if (auth()->user()->role !== $role) {
            abort(403, 'You do not have access to this page.');
        }

        return $next($request);
    }
}
