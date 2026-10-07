<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRoleZero
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || (int) $request->user()->role !== 0) {
            abort(403, 'Akses ditolak. Menu ini hanya dapat diakses oleh akun dengan Role 0.');
        }

        return $next($request);
    }
}
