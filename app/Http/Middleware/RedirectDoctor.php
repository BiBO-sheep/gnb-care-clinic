<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectDoctor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika user yang login adalah dokter, dan dia mencoba mengakses halaman Filament (/admin),
        // langsung arahkan ke Ruang Dokter.
        if (auth()->check() && auth()->user()->role === 'dokter' && $request->is('admin')) {
            return redirect('/klinik/doctor');
        }

        return $next($request);
    }
}
