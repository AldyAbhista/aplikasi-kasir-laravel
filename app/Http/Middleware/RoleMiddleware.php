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
     * @param  string  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Jika user belum login, redirect ke login
        if (!auth()->check()) {
            return redirect('/login');
        }

        // Cek apakah role user ada di dalam daftar roles yang diperbolehkan
        if (in_array(auth()->user()->role, $roles)) {
            return $next($request);
        }

        // Jika role tidak sesuai, redirect ke dashboard user
        return redirect('/');
    }
}
