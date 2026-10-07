<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLevelTwo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || (int) $request->user()->level !== 2) {
            abort(403, 'Akses ditolak. Menu Kontak hanya dapat diakses oleh pengguna dengan Level 2.');
        }

        return $next($request);
    }
}
