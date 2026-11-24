<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdminOrTenant
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || (!$user->isAdmin() && !$user->isTenant())) {
            return redirect()->route('home')->with('error', 'Akses ditolak. Dashboard hanya dapat diakses oleh Admin Sekolah dan Pengelola Stand Kantin.');
        }

        return $next($request);
    }
}

