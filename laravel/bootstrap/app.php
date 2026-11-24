<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin_or_tenant' => \App\Http\Middleware\EnsureUserIsAdminOrTenant::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'checkout',
            'checkout/*',
            'voucher/claim',
            'pesanan-saya/*/batal',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Sesi telah kedaluwarsa. Silakan muat ulang halaman.',
                ], 419);
            }

            // Alihkan kembali dengan sesi baru dan pesan ramah pengguna jika token CSRF kedaluwarsa
            $previousUrl = url()->previous();
            $targetUrl = ($request->is('masuk') || $request->routeIs('login'))
                ? route('login')
                : ($previousUrl && $previousUrl !== $request->fullUrl() ? $previousUrl : route('login'));

            return redirect($targetUrl)
                ->withInput($request->except('password', 'password_confirmation', '_token'))
                ->with('error', 'Sesi Anda telah kedaluwarsa karena halaman terbuka terlalu lama. Silakan coba masuk kembali.');
        });
    })->create();

