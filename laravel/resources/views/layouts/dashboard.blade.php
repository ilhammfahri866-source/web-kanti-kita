<!DOCTYPE html>
<html lang="id" class="no-transition">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') — Panel Admin Kantin</title>
    <meta name="description" content="Panel pengelola kantin sekolah — kelola pesanan, produk, stok bahan, dan voucher.">

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 36 36'><rect width='36' height='36' rx='10' fill='%237c3aed'/><rect x='8' y='18' width='4' height='10' rx='1.5' fill='white'/><rect x='14' y='13' width='4' height='15' rx='1.5' fill='white'/><rect x='20' y='16' width='4' height='12' rx='1.5' fill='white'/><rect x='26' y='9' width='4' height='19' rx='1.5' fill='white'/></svg>">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Vite CSS/JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>

    {{-- Anti-Flash Script --}}
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

    @stack('head')
</head>
<body
    x-data="{
        mobileMenuOpen: false
    }"
    class="min-h-screen font-sans antialiased flex flex-col"
    style="background-color: var(--bg-base); color: var(--text-primary);"
>

    {{-- ══ SIDEBAR + TOPBAR LAYOUT ══════════════════════════════════════════ --}}

    {{-- ── TOPBAR / NAVBAR DASHBOARD ──────────────────────────────────────── --}}
    <header class="navbar sticky top-0 z-30 print:hidden" id="dashboard-navbar">
        <div class="max-w-full px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-3">

            {{-- Kiri: Brand Admin Panel --}}
            <div class="flex items-center gap-3 shrink-0">
                {{-- Hamburger Mobile --}}
                <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="md:hidden p-2 rounded-lg border transition-colors cursor-pointer"
                        style="border-color: var(--border-default); background-color: var(--bg-surface-2); color: var(--text-secondary);"
                        aria-label="Toggle menu">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <a href="{{ route('dashboard.index') }}" class="flex items-center gap-2.5 shrink-0 group" aria-label="Panel Admin Kantin">
                    <div class="relative w-9 h-9 shrink-0 transition-transform duration-200 group-hover:scale-105">
                        <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                            <defs>
                                <linearGradient id="admin-logo-grad" x1="0" y1="0" x2="36" y2="36" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#7c3aed"/>
                                    <stop offset="100%" stop-color="#1e1b4b"/>
                                </linearGradient>
                            </defs>
                            {{-- Background --}}
                            <rect width="36" height="36" rx="10" fill="url(#admin-logo-grad)"/>
                            {{-- Chart bars --}}
                            <rect x="7" y="20" width="4" height="10" rx="1.5" fill="white" opacity="0.5"/>
                            <rect x="13" y="14" width="4" height="16" rx="1.5" fill="white" opacity="0.75"/>
                            <rect x="19" y="17" width="4" height="13" rx="1.5" fill="white" opacity="0.6"/>
                            <rect x="25" y="10" width="4" height="20" rx="1.5" fill="white" opacity="0.95"/>
                            {{-- Trend line --}}
                            <path d="M9 19 L15 13 L21 16 L27 9" stroke="#a78bfa" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            {{-- Dot on line --}}
                            <circle cx="27" cy="9" r="2" fill="white"/>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-extrabold text-sm tracking-tight leading-none" style="color: var(--brand);">Panel Admin</span>
                        <span class="text-[0.6rem] font-semibold tracking-widest uppercase mt-0.5" style="color: var(--text-muted);">Kelola Kantin Sekolah</span>
                    </div>
                </a>
            </div>

            {{-- Tengah: Navigasi Desktop --}}
            <nav class="hidden md:flex items-center gap-1 text-xs font-semibold flex-1 justify-center">
                <a href="{{ route('dashboard.index') }}"
                   class="px-3.5 py-2 rounded-xl transition-all duration-150 flex items-center gap-1.5"
                   style="{{ request()->routeIs('dashboard.index') ? 'background-color: var(--brand); color: #ffffff;' : 'color: var(--text-secondary);' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Ringkasan</span>
                </a>
                <a href="{{ route('dashboard.stock.index') }}"
                   class="px-3.5 py-2 rounded-xl transition-all duration-150 flex items-center gap-1.5"
                   style="{{ request()->routeIs('dashboard.stock.*') ? 'background-color: var(--brand); color: #ffffff;' : 'color: var(--text-secondary);' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span>Stok Bahan</span>
                </a>
                <a href="{{ route('products.index') }}"
                   class="px-3.5 py-2 rounded-xl transition-all duration-150 flex items-center gap-1.5"
                   style="{{ request()->routeIs('products.*') ? 'background-color: var(--brand); color: #ffffff;' : 'color: var(--text-secondary);' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span>Produk</span>
                </a>
                <a href="{{ route('dashboard.vouchers.index') }}"
                   class="px-3.5 py-2 rounded-xl transition-all duration-150 flex items-center gap-1.5"
                   style="{{ request()->routeIs('dashboard.vouchers.*') ? 'background-color: var(--brand); color: #ffffff;' : 'color: var(--text-secondary);' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                    <span>Voucher</span>
                </a>
                <a href="{{ route('dashboard.reports.index') }}"
                   class="px-3.5 py-2 rounded-xl transition-all duration-150 flex items-center gap-1.5"
                   style="{{ request()->routeIs('dashboard.reports.*') || request()->routeIs('dashboard.finance.*') ? 'background-color: var(--brand); color: #ffffff;' : 'color: var(--text-secondary);' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span>Laporan Keuangan</span>
                </a>
            </nav>

            {{-- Kanan: Tema + Profil --}}
            <div class="flex items-center gap-2 shrink-0">

                {{-- Flash Messages kecil di topbar --}}
                @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border"
                     style="background-color: var(--status-success-bg); color: var(--status-success); border-color: var(--status-success-border);">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="max-w-[200px] truncate">{{ session('success') }}</span>
                </div>
                @endif


                {{-- Profil Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.outside="open = false"
                            class="flex items-center gap-2 text-sm font-medium px-2.5 py-1.5 rounded-xl transition-colors duration-150 border cursor-pointer"
                            style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold"
                              style="background-color: var(--brand); color: #ffffff;">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </span>
                        <span class="hidden lg:inline max-w-[7rem] truncate text-xs font-semibold">{{ auth()->user()->name ?? 'Admin' }}</span>
                        <svg class="w-3.5 h-3.5 transition-transform duration-150" :class="{'rotate-180': open}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                         class="absolute right-0 mt-2 w-56 rounded-2xl py-1 z-50 shadow-xl backdrop-blur-md"
                         style="background-color: var(--bg-surface); border: 1px solid var(--border-default); box-shadow: var(--shadow-card-hover);">

                        {{-- Info Akun --}}
                        <div class="px-4 py-2.5 border-b" style="border-color: var(--border-default);">
                            <p class="text-[0.65rem]" style="color: var(--text-muted);">Masuk sebagai</p>
                            <p class="text-xs font-bold truncate" style="color: var(--text-primary);">{{ auth()->user()->email ?? '-' }}</p>
                            <p class="text-[0.65rem] mt-0.5 font-semibold" style="color: var(--brand);">
                                {{ auth()->user()->isTenant() ? (auth()->user()->tenant->name ?? 'Tenant Kantin') : 'Super Admin' }}
                            </p>
                        </div>

                        {{-- Menu Admin --}}
                        <a href="{{ route('dashboard.index') }}"
                           class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold transition-colors hover:opacity-80"
                           style="color: var(--brand);">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span>Ringkasan Dashboard</span>
                        </a>
                        <a href="{{ route('dashboard.stock.index') }}"
                           class="flex items-center gap-2.5 px-4 py-2 text-xs transition-colors hover:opacity-80"
                           style="color: var(--text-secondary);">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <span>Manajemen Stok</span>
                        </a>
                        <a href="{{ route('products.index') }}"
                           class="flex items-center gap-2.5 px-4 py-2 text-xs transition-colors hover:opacity-80"
                           style="color: var(--text-secondary);">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <span>Kelola Produk</span>
                        </a>
                        <a href="{{ route('dashboard.vouchers.index') }}"
                           class="flex items-center gap-2.5 px-4 py-2 text-xs transition-colors hover:opacity-80"
                           style="color: var(--text-secondary);">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                            <span>Kelola Voucher</span>
                        </a>
                        <a href="{{ route('dashboard.reports.index') }}"
                           class="flex items-center gap-2.5 px-4 py-2 text-xs transition-colors hover:opacity-80"
                           style="color: var(--text-secondary);">
                            <svg class="w-4 h-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span>Laporan Keuangan</span>
                        </a>

                        <div class="border-t my-1" style="border-color: var(--border-default);"></div>

                        {{-- Link ke Website Publik --}}
                        <a href="{{ route('home') }}" target="_blank"
                           class="flex items-center gap-2.5 px-4 py-2 text-xs transition-colors hover:opacity-80"
                           style="color: var(--text-secondary);">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span>Lihat Halaman Toko</span>
                            <span class="ml-auto text-[0.6rem]">↗</span>
                        </a>

                        <div class="border-t my-1" style="border-color: var(--border-default);"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-xs font-bold transition-colors hover:opacity-80 cursor-pointer"
                                    style="color: var(--status-danger);">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mobile Nav Bar --}}
        <div x-show="mobileMenuOpen" x-cloak
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="md:hidden border-t px-3 py-2.5 flex flex-col gap-1"
             style="border-color: var(--border-default); background-color: var(--bg-surface);">
            <a href="{{ route('dashboard.index') }}"
               class="px-3 py-2 text-xs font-bold rounded-lg flex items-center gap-2 transition-colors"
               style="{{ request()->routeIs('dashboard.index') ? 'background-color: var(--brand); color: #fff;' : 'color: var(--text-secondary);' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span>Ringkasan</span>
            </a>
            <a href="{{ route('dashboard.stock.index') }}"
               class="px-3 py-2 text-xs font-bold rounded-lg flex items-center gap-2 transition-colors"
               style="{{ request()->routeIs('dashboard.stock.*') ? 'background-color: var(--brand); color: #fff;' : 'color: var(--text-secondary);' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <span>Stok Bahan</span>
            </a>
            <a href="{{ route('products.index') }}"
               class="px-3 py-2 text-xs font-bold rounded-lg flex items-center gap-2 transition-colors"
               style="{{ request()->routeIs('products.*') ? 'background-color: var(--brand); color: #fff;' : 'color: var(--text-secondary);' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span>Produk</span>
            </a>
            <a href="{{ route('dashboard.vouchers.index') }}"
               class="px-3 py-2 text-xs font-bold rounded-lg flex items-center gap-2 transition-colors"
               style="{{ request()->routeIs('dashboard.vouchers.*') ? 'background-color: var(--brand); color: #fff;' : 'color: var(--text-secondary);' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
                <span>Voucher</span>
            </a>
            <a href="{{ route('dashboard.reports.index') }}"
               class="px-3 py-2 text-xs font-bold rounded-lg flex items-center gap-2 transition-colors"
               style="{{ request()->routeIs('dashboard.reports.*') || request()->routeIs('dashboard.finance.*') ? 'background-color: var(--brand); color: #fff;' : 'color: var(--text-secondary);' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <span>Laporan Keuangan</span>
            </a>
            <div class="border-t my-1.5" style="border-color: var(--border-default);"></div>
            <a href="{{ route('home') }}" target="_blank"
               class="px-3 py-2 text-xs font-bold rounded-lg flex items-center gap-2"
               style="color: var(--brand);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Lihat Halaman Toko ↗</span>
            </a>
        </div>
    </header>

    {{-- Flash Messages (halaman penuh) --}}
    @if(session('success') || session('error'))
    <div class="max-w-full px-4 sm:px-6 lg:px-8 pt-3">
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)"
             class="flex items-center gap-3 p-3.5 rounded-xl text-sm border mb-2"
             style="background-color: var(--status-success-bg); color: var(--status-success); border-color: var(--status-success-border);">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('success') }}</span>
            <button @click="show = false" class="ml-auto opacity-60 hover:opacity-100 cursor-pointer">✕</button>
        </div>
        @endif
        @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-transition
             class="flex items-center gap-3 p-3.5 rounded-xl text-sm border mb-2"
             style="background-color: var(--status-danger-bg); color: var(--status-danger); border-color: var(--status-danger-border);">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('error') }}</span>
            <button @click="show = false" class="ml-auto opacity-60 hover:opacity-100 cursor-pointer">✕</button>
        </div>
        @endif
    </div>
    @endif

    {{-- MAIN CONTENT --}}
    <main class="flex-1 w-full relative z-10" id="dashboard-main-content">
        @yield('content')
    </main>

    {{-- FOOTER DASHBOARD --}}
    <footer class="border-t py-4 mt-auto relative z-10 print:hidden"
            style="border-color: var(--border-default); background-color: var(--bg-surface);">
        <div class="max-w-full px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs"
             style="color: var(--text-muted);">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--brand);">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="font-bold" style="color: var(--brand);">Panel Admin Kantin</span>
                <span>—</span>
                <span>Kelola pesanan, produk, stok & voucher.</span>
            </div>
            <p>&copy; {{ date('Y') }} KantinKita. Semua hak dilindungi.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>
</html>
