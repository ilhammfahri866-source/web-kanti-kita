<!DOCTYPE html>
<html lang="id" class="no-transition">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'KantinKita')</title>

    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23ea580c'><path d='M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'/></svg>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        (function () {
            var root = document.documentElement;
            root.classList.remove('dark');
            try {
                localStorage.removeItem('ekantin_theme');
            } catch (e) {}
            window.addEventListener('DOMContentLoaded', function () {
                setTimeout(() => root.classList.remove('no-transition'), 50);
            });
        })();
    </script>
</head>
<body
    class="min-h-screen font-sans antialiased flex flex-col items-center justify-center relative overflow-y-auto py-4 sm:py-6"
    style="color: var(--text-primary);"
>
    {{-- Ambient Effects (Halus & Tenang) --}}
    <div class="bg-ambient-container" aria-hidden="true">
        @include('partials.meteor-shower')
    </div>

    <div class="relative z-10 w-full max-w-md mx-auto px-3.5 sm:px-4 my-auto" style="max-width: 480px; width: 100%;">
        <div class="text-center mb-3 sm:mb-4">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-2xl border shadow-sm transition-all duration-200 hover:scale-105"
                 style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white" style="background: var(--brand);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <span class="text-xl sm:text-2xl font-black tracking-tight" style="color: var(--brand);">KantinKita</span>
            </a>
            <p class="mt-1 text-xs" style="color: var(--text-secondary);">Pesan makanan kantin cepat tanpa antri</p>
        </div>

        {{-- Flash Alerts --}}
        @if (session('error'))
            <div class="mb-3 p-3 rounded-xl border flex items-start gap-2.5 text-xs font-semibold shadow-xs"
                 style="background-color: var(--status-danger-bg); color: var(--status-danger); border-color: var(--status-danger-border);">
                <svg class="w-4 h-4 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <span class="leading-relaxed flex-1">{{ session('error') }}</span>
            </div>
        @endif
        @if (session('success'))
            <div class="mb-3 p-3 rounded-xl border flex items-start gap-2.5 text-xs font-semibold shadow-xs"
                 style="background-color: var(--status-success-bg); color: var(--status-success); border-color: var(--status-success-border);">
                <span class="text-base shrink-0 mt-0.5">✓</span>
                <span class="leading-relaxed flex-1">{{ session('success') }}</span>
            </div>
        @endif

        @yield('content')

        <div class="mt-4 text-center flex items-center justify-center flex-wrap gap-2.5 text-xs">
            <a href="{{ route('home') }}" class="hover:underline transition-colors" style="color: var(--text-muted);">
                &larr; Beranda
            </a>
            <span style="color: var(--border-strong);">•</span>
            <a href="{{ route('login') }}" class="hover:underline transition-colors font-medium" style="color: var(--text-muted);">
                Masuk Pembeli
            </a>
            <span style="color: var(--border-strong);">•</span>
            <a href="{{ route('register') }}" class="hover:underline transition-colors font-medium" style="color: var(--text-muted);">
                Daftar Akun
            </a>
        </div>
    </div>
</body>
</html>
