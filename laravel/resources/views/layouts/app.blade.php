<!DOCTYPE html>
<html lang="id" class="no-transition">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ── SEO ─────────────────────────────────────────────────────────── --}}
    <title>@yield('title', 'KantinKita') — Pesan Makanan Tanpa Antri</title>
    <meta name="description" content="@yield('meta_description', 'Pesan makanan dari kantin favoritmu secara digital, mudah, cepat, tanpa harus mengantri.')">

    {{-- ── Favicon (SVG inline, tidak perlu file eksternal) ───────────── --}}
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 36 36'><rect width='36' height='36' rx='10' fill='%23f97316'/><path d='M6 18 Q6 28 18 28 Q30 28 30 18 Z' fill='%23fed7aa'/><rect x='6' y='17' width='24' height='3' rx='1.5' fill='white' opacity='0.9'/><path d='M13 10 Q14 8 13 6' stroke='white' stroke-width='1.5' stroke-linecap='round'/><path d='M18 9 Q19 7 18 5' stroke='white' stroke-width='1.5' stroke-linecap='round'/><path d='M23 10 Q24 8 23 6' stroke='white' stroke-width='1.5' stroke-linecap='round'/></svg>">

    {{-- ── Google Fonts: Poppins, Plus Jakarta Sans & Inter ───────────────────── --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- ── Vite: Tailwind CSS + App JS ───────────────────────────────── --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- ── Alpine.js (CDN — di head agar x-cloak bekerja) ────────────── --}}
    <style>[x-cloak] { display: none !important; }</style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- ══════════════════════════════════════════════════════════════════
         ANTI-FLASH DARK MODE SCRIPT
         Harus berjalan SEBELUM browser me-render body apa pun.
         Script ini membaca localStorage dan menerapkan class .dark ke <html>
         secara sinkron sehingga tidak ada kedipan (FOUC).
         Fallback: jika tidak ada preferensi tersimpan, ikuti preferensi OS.
    ══════════════════════════════════════════════════════════════════════ --}}
    <script>
        (function () {
            var root = document.documentElement;
            root.classList.remove('dark');
            try {
                localStorage.removeItem('ekantin_theme');
            } catch (e) {}
            window.addEventListener('DOMContentLoaded', function () {
                setTimeout(function () {
                    root.classList.remove('no-transition');
                }, 50);
            });
        })();
    </script>

    @stack('head')
</head>

{{-- ══════════════════════════════════════════════════════════════════════════
     BODY — Alpine.js x-data untuk state dark mode di komponen seluruh halaman
════════════════════════════════════════════════════════════════════════════ --}}
<body
    x-data="{
        cartCount: 0,
        init() {
            this.cartCount = window.getCartCount ? window.getCartCount() : 0;
        }
    }"
    @cart-updated.window="cartCount = $event.detail.count"
    class="min-h-screen font-sans antialiased"
    style="color: var(--text-primary);"
>

    {{-- ══════════════════════════════════════════════════════════════════
         BACKGROUND ATMOSPHERE & AMBIENT GLOW EFFECTS (Halus & Tenang)
    ══════════════════════════════════════════════════════════════════════ --}}
    <div class="bg-ambient-container" aria-hidden="true">
        @include('partials.meteor-shower')
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         NAVBAR
    ══════════════════════════════════════════════════════════════════════ --}}
    <header class="navbar sticky top-0 z-40 backdrop-blur-md" id="main-navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">

            {{-- Brand Logo SVG Custom --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0 group" aria-label="KantinKita Beranda">
                {{-- Logo Mark: Bowl + Steam --}}
                <div class="relative w-9 h-9 shrink-0 transition-transform duration-200 group-hover:scale-105">
                    <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                        <defs>
                            <linearGradient id="logo-grad" x1="0" y1="0" x2="36" y2="36" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#f97316"/>
                                <stop offset="100%" stop-color="#92400e"/>
                            </linearGradient>
                            <linearGradient id="bowl-grad" x1="6" y1="18" x2="30" y2="30" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#fed7aa"/>
                                <stop offset="100%" stop-color="#fb923c"/>
                            </linearGradient>
                        </defs>
                        {{-- Background rounded rect --}}
                        <rect width="36" height="36" rx="10" fill="url(#logo-grad)"/>
                        {{-- Steam lines --}}
                        <path d="M13 10 Q14 8 13 6" stroke="white" stroke-width="1.5" stroke-linecap="round" opacity="0.8"/>
                        <path d="M18 9 Q19 7 18 5" stroke="white" stroke-width="1.5" stroke-linecap="round" opacity="0.9"/>
                        <path d="M23 10 Q24 8 23 6" stroke="white" stroke-width="1.5" stroke-linecap="round" opacity="0.8"/>
                        {{-- Bowl body --}}
                        <path d="M6 18 Q6 28 18 28 Q30 28 30 18 Z" fill="url(#bowl-grad)"/>
                        {{-- Bowl rim --}}
                        <rect x="6" y="17" width="24" height="3" rx="1.5" fill="white" opacity="0.9"/>
                        {{-- Food noodle swirl --}}
                        <path d="M12 22 Q15 20 18 22 Q21 24 24 22" stroke="#92400e" stroke-width="1.5" stroke-linecap="round" fill="none"/>
                        <path d="M13 25 Q16 23 18 25 Q20 27 23 25" stroke="#c2410c" stroke-width="1.2" stroke-linecap="round" fill="none" opacity="0.7"/>
                    </svg>
                </div>
                {{-- Brand Text --}}
                <div class="flex flex-col">
                    <span class="font-extrabold text-base tracking-tight leading-none" style="color: var(--brand);">KantinKita</span>
                    <span class="text-[0.6rem] font-semibold tracking-widest uppercase mt-0.5" style="color: var(--text-muted);">Pesan · Cepat · Tanpa Antri</span>
                </div>
            </a>


            {{-- Aksi Kanan (Padding & Jarak Seimbang Elegan) --}}
            <div class="flex items-center gap-2.5 sm:gap-3 shrink-0">


                {{-- Pesanan Saya (Desktop) --}}
                <a href="{{ route('orders.index') }}"
                   class="relative hidden md:flex items-center gap-2 px-3.5 py-2 sm:py-2.5 text-xs sm:text-sm font-semibold rounded-xl border transition-all duration-150 shadow-2xs {{ request()->routeIs('orders.index') ? 'bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 border-orange-200 dark:border-orange-900/60' : 'bg-white/70 dark:bg-slate-900/70 border-slate-200/80 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800' }}"
                   aria-label="Pesanan Saya"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span>Pesanan Saya</span>
                </a>

                {{-- Keranjang Belanja --}}
                <a href="{{ route('cart.index') }}"
                   class="relative flex items-center gap-2 px-3 sm:px-3.5 py-2 sm:py-2.5 text-xs sm:text-sm font-semibold rounded-xl border border-slate-200/80 dark:border-slate-800 bg-white/70 dark:bg-slate-900/70 hover:bg-white dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 transition-all duration-150 shadow-2xs active:scale-95"
                   id="cart-link"
                   aria-label="Keranjang belanja"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-10H5.4M7 13l-1.35 2.7A1 1 0 007 17h10M7 13L5.4 5M17 17a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                    <span class="hidden sm:inline">Keranjang</span>
                    {{-- Badge jumlah item --}}
                    <span
                        x-show="cartCount > 0"
                        x-text="cartCount > 99 ? '99+' : cartCount"
                        class="absolute -top-1.5 -right-1.5 min-w-[1.15rem] h-[1.15rem] px-1 text-[0.625rem] font-bold leading-none rounded-full flex items-center justify-center bg-orange-600 text-white shadow-xs"
                    ></span>
                </a>

                {{-- Lonceng Notifikasi Pesanan (Notification Bell + Popover Dropdown) --}}
                @php
                    $navUserOrders = auth()->check()
                        ? auth()->user()->orders()->with(['items', 'tenant'])->latest()->take(6)->get()
                        : collect();
                    $unreadNotifCount = $navUserOrders->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])->count();
                @endphp
                    <div class="relative" x-data="{
                        openNotif: false,
                        hasUnread: {{ $unreadNotifCount > 0 ? 'true' : 'false' }},
                        markAsRead() {
                            this.hasUnread = false;
                        }
                    }">
                        <button
                            @click="openNotif = !openNotif; if (openNotif) markAsRead()"
                            @click.outside="openNotif = false"
                            type="button"
                            class="relative flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-white/70 dark:bg-slate-900/70 hover:bg-white dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 transition-all shadow-2xs cursor-pointer active:scale-95"
                            id="btn-notification-bell"
                            aria-label="Notifikasi status pesanan"
                            title="Notifikasi Status Pesanan"
                        >
                            {{-- Ikon Lonceng SVG --}}
                            <svg class="w-4 h-4 transition-transform duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>

                            {{-- Indikator Titik Merah Jika Ada Notifikasi Baru --}}
                            <span
                                x-show="hasUnread"
                                class="absolute top-1.5 right-1.5 w-2.5 h-2.5 rounded-full bg-rose-500 border-2 border-white dark:border-slate-900 animate-pulse"
                                title="Ada notifikasi pesanan aktif"
                            ></span>
                        </button>

                        {{-- Popover Dropdown Notifikasi --}}
                        <div
                            x-show="openNotif"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                            class="absolute right-0 mt-2 w-80 sm:w-96 rounded-2xl shadow-xl z-50 overflow-hidden border backdrop-blur-md"
                            style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card-hover);"
                            id="notification-popover"
                        >
                            {{-- Header Popover --}}
                            <div class="px-4 py-3 border-b flex items-center justify-between"
                                 style="border-color: var(--border-default); background-color: var(--bg-surface-2);">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                    <h3 class="text-xs font-bold" style="color: var(--text-primary);">Status Pesanan Terbaru</h3>
                                </div>
                                <template x-if="hasUnread">
                                    <span class="text-[0.625rem] font-bold px-2 py-0.5 rounded-full"
                                          style="background-color: var(--cta-accent); color: #ffffff;">
                                        Baru
                                    </span>
                                </template>
                            </div>

                            {{-- Daftar Status Pesanan Terbaru --}}
                            <div class="max-h-80 overflow-y-auto divide-y" style="border-color: var(--border-default);">
                                @forelse($navUserOrders as $orderNotif)
                                    @php
                                        $mainItem = $orderNotif->items->first();
                                        $dishName = $mainItem ? $mainItem->product_name : 'Pesanan';
                                        $otherCount = $orderNotif->items->count() - 1;
                                        if ($otherCount > 0) {
                                            $dishName .= ' (+' . $otherCount . ' item)';
                                        }
                                    @endphp
                                    <a href="{{ route('orders.index') }}"
                                       class="block px-4 py-3 transition-colors duration-150 hover:bg-stone-500/5 group">
                                        <div class="flex items-start gap-3">
                                            {{-- Icon Status --}}
                                            <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 text-sm mt-0.5 shadow-2xs"
                                                 @if($orderNotif->status === 'ready')
                                                     style="background-color: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;"
                                                 @elseif($orderNotif->status === 'preparing')
                                                     style="background-color: #fff7ed; color: #ea580c; border: 1px solid #fed7aa;"
                                                 @elseif($orderNotif->status === 'confirmed')
                                                     style="background-color: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe;"
                                                 @elseif($orderNotif->status === 'completed')
                                                     style="background-color: #f5f5f4; color: #57534e; border: 1px solid #e7e5e4;"
                                                 @elseif($orderNotif->status === 'cancelled')
                                                     style="background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca;"
                                                 @else
                                                     style="background-color: var(--bg-surface-2); color: var(--text-muted); border: 1px solid var(--border-default);"
                                                 @endif
                                            >
                                                @if($orderNotif->status === 'ready')
                                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                                @elseif($orderNotif->status === 'preparing')
                                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @elseif($orderNotif->status === 'confirmed')
                                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @elseif($orderNotif->status === 'completed')
                                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                @elseif($orderNotif->status === 'cancelled')
                                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                @else
                                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                @endif
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-semibold leading-snug group-hover:underline" style="color: var(--text-primary);">
                                                    @if($orderNotif->status === 'ready')
                                                        Pesanan <b>{{ $dishName }}</b> siap diambil di kantin!
                                                    @elseif($orderNotif->status === 'preparing')
                                                        Pesanan <b>{{ $dishName }}</b> Anda sedang disiapkan oleh {{ $orderNotif->tenant->name ?? 'Kantin' }}.
                                                    @elseif($orderNotif->status === 'confirmed')
                                                        Pesanan <b>{{ $dishName }}</b> telah dikonfirmasi penjual.
                                                    @elseif($orderNotif->status === 'completed')
                                                        Pesanan <b>{{ $dishName }}</b> selesai diambil. Selamat menikmati!
                                                    @elseif($orderNotif->status === 'cancelled')
                                                        Pesanan <b>{{ $dishName }}</b> dibatalkan.
                                                        @if($orderNotif->cancel_reason)
                                                            <span class="inline-flex items-center gap-1 mt-1 text-[0.68rem] font-medium px-2 py-0.5 rounded-md border"
                                                                  style="background-color: var(--status-danger-bg); color: var(--status-danger); border-color: var(--status-danger-border);">
                                                                <span>⚠️</span>
                                                                <span class="truncate max-w-[200px]">Alasan: {{ $orderNotif->cancel_reason }}</span>
                                                            </span>
                                                        @endif
                                                    @else
                                                        Pesanan <b>{{ $dishName }}</b> sedang menunggu konfirmasi kasir.
                                                    @endif
                                                </p>
                                                <div class="flex items-center gap-2 mt-1 text-[0.68rem]" style="color: var(--text-muted);">
                                                    <span class="font-mono font-bold">{{ $orderNotif->order_number }}</span>
                                                    <span>•</span>
                                                    <span>{{ $orderNotif->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="py-8 px-4 text-center">
                                        <div class="w-10 h-10 mx-auto mb-2 rounded-xl flex items-center justify-center border"
                                             style="background-color: var(--bg-surface-2); border-color: var(--border-default); color: var(--text-muted);">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                        </div>
                                        <p class="text-xs font-semibold" style="color: var(--text-primary);">Belum Ada Status Pesanan</p>
                                        <p class="text-[0.7rem] mt-0.5 mb-2" style="color: var(--text-muted);">Pesanan yang Anda buat akan langsung terpantau di sini.</p>
                                    </div>
                                @endforelse
                            </div>

                            {{-- Footer Popover --}}
                            <div class="p-2.5 border-t text-center flex items-center justify-between px-4 text-xs"
                                 style="border-color: var(--border-default); background-color: var(--bg-surface-2);">
                                <a href="{{ route('orders.index') }}"
                                   class="font-bold inline-flex items-center gap-1 hover:underline transition-colors"
                                   style="color: var(--brand);">
                                    <span>Lihat Semua Pesanan Saya</span>
                                    <span>&rarr;</span>
                                </a>
                                <button
                                    @click="markAsRead(); openNotif = false"
                                    type="button"
                                    class="text-[0.68rem] font-medium opacity-70 hover:opacity-100 transition-opacity"
                                    style="color: var(--text-secondary);"
                                >
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Profil Pengguna (Jika Login) / Tombol Masuk (Jika Tamu) --}}
                    @auth
                    @if(auth()->user()->isAdmin() || auth()->user()->isTenant())
                    <a href="{{ route('dashboard.index') }}"
                       class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold border transition-all hover:scale-105"
                       style="background-color: var(--brand-subtle); color: var(--brand); border-color: var(--brand-border);"
                       title="Buka Dashboard Panel Pengelola">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>Dashboard</span>
                    </a>
                    @endif
                    <div class="relative" x-data="{ open: false }">
                        <button
                            @click="open = !open"
                            @click.outside="open = false"
                            class="flex items-center gap-1.5 sm:gap-2 text-xs sm:text-sm font-semibold p-1 sm:px-3 sm:py-2 rounded-xl transition-all border shadow-2xs hover:opacity-90 cursor-pointer"
                            style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                        >
                            <span class="w-7 h-7 sm:w-6 sm:h-6 rounded-full flex items-center justify-center text-xs font-bold shrink-0"
                                  style="background-color: var(--brand); color: #ffffff;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <span class="hidden sm:inline-block max-w-[7rem] sm:max-w-[10rem] truncate">{{ auth()->user()->name }}</span>
                            @if(auth()->user()->classroom)
                                <span class="hidden md:inline-block text-[10px] font-bold px-1.5 py-0.5 rounded-md"
                                      style="background-color: var(--brand-subtle); color: var(--brand); border: 1px solid var(--brand-border);">
                                    {{ auth()->user()->classroom }}
                                </span>
                            @endif
                            <svg class="hidden sm:block w-3.5 h-3.5 transition-transform duration-150" :class="{'rotate-180': open}"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            x-cloak
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                            class="absolute right-0 mt-2 w-56 rounded-2xl py-1.5 z-50 origin-top-right shadow-xl backdrop-blur-md border overflow-hidden"
                            style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card-hover);"
                        >
                            <div class="px-4 py-2.5 border-b" style="border-color: var(--border-default); background-color: var(--bg-surface-2);">
                                <p class="text-xs font-bold truncate" style="color: var(--text-primary);">{{ auth()->user()->name }}</p>
                                <p class="text-[11px] truncate mt-0.5" style="color: var(--text-muted);">
                                    @if(auth()->user()->isTeacher())
                                        <span class="font-semibold text-blue-600 dark:text-blue-400">Guru • {{ auth()->user()->classroom }}</span>
                                    @elseif(auth()->user()->classroom)
                                        <span>Siswa • {{ auth()->user()->classroom }}</span>
                                    @else
                                        <span>{{ auth()->user()->email }}</span>
                                    @endif
                                </p>
                            </div>

                            @if(auth()->user()->isAdmin() || auth()->user()->isTenant())
                            <a href="{{ route('dashboard.index') }}"
                               class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold transition-colors hover:opacity-85"
                               style="color: var(--brand);">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                <span>Dashboard Pengelola</span>
                            </a>
                            <a href="{{ route('dashboard.reports.index') }}"
                               class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium transition-colors hover:opacity-80"
                               style="color: var(--text-secondary);">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                <span>Laporan Keuangan</span>
                            </a>
                            <a href="{{ route('dashboard.vouchers.index') }}"
                               class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium transition-colors hover:opacity-80"
                               style="color: var(--text-secondary);">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                <span>Kelola Voucher Promo</span>
                            </a>
                            <a href="{{ route('dashboard.stock.index') }}"
                               class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium transition-colors hover:opacity-80"
                               style="color: var(--text-secondary);">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                <span>Stok Bahan & Produk</span>
                            </a>
                            @endif

                            <a href="{{ route('orders.index') }}"
                               class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold transition-colors hover:opacity-80"
                               style="color: var(--text-secondary);">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                <span>Pesanan Saya</span>
                            </a>
                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium transition-colors hover:opacity-80"
                               style="color: var(--text-secondary);">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span>Profil Akun</span>
                            </a>

                            <div class="border-t my-1" style="border-color: var(--border-default);"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-xs font-semibold transition-colors hover:opacity-80 cursor-pointer"
                                        style="color: var(--status-danger);">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    @else
                    <div class="flex items-center gap-1.5 sm:gap-2 ml-1">
                        {{-- Tombol Daftar Akun --}}
                        <a href="{{ route('register') }}"
                           class="text-xs sm:text-sm font-bold px-3 sm:px-3.5 py-2 sm:py-2.5 rounded-xl border border-orange-500/40 text-orange-600 dark:text-orange-400 bg-orange-50/70 dark:bg-orange-950/40 hover:bg-orange-100/80 dark:hover:bg-orange-900/60 inline-flex items-center gap-1.5 transition-all shadow-2xs hover:scale-[1.02] active:scale-95"
                           title="Daftar akun Siswa atau Guru baru"
                           id="nav-register-siswa-btn"
                        >
                            <span>Daftar Akun</span>
                        </a>

                        {{-- Tombol Masuk Pembeli (Siswa & Guru) --}}
                        <a href="{{ route('login') }}"
                           class="btn-brand text-xs sm:text-sm font-bold px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl shadow-sm shadow-orange-500/20 inline-flex items-center gap-1.5 transition-all hover:shadow-md hover:scale-[1.02] active:scale-95"
                           title="Masuk untuk Siswa & Guru"
                           id="nav-login-pembeli-btn"
                        >
                            <span>Masuk</span>
                        </a>
                    </div>
                    @endauth
                </div>
            </div>
    </header>

    {{-- ══════════════════════════════════════════════════════════════════
         FLASH MESSAGES (Warna Muted & Elegan)
    ══════════════════════════════════════════════════════════════════════ --}}
    @if (session('success') || session('error') || session('info'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4" role="alert">
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition
                     class="flex items-start gap-3 p-4 rounded-xl mb-3 text-sm border backdrop-blur-md"
                     style="background-color: var(--status-success-bg); color: var(--status-success); border-color: var(--status-success-border);">
                    <svg class="w-4 h-4 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="ml-auto opacity-60 hover:opacity-100" aria-label="Tutup">✕</button>
                </div>
            @endif
            @if (session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition
                     class="flex items-start gap-3 p-4 rounded-xl mb-3 text-sm border backdrop-blur-md"
                     style="background-color: var(--status-danger-bg); color: var(--status-danger); border-color: var(--status-danger-border);">
                    <svg class="w-4 h-4 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <span>{{ session('error') }}</span>
                    <button @click="show = false" class="ml-auto opacity-60 hover:opacity-100" aria-label="Tutup">✕</button>
                </div>
            @endif
            @if (session('info'))
                <div x-data="{ show: true }" x-show="show" x-transition
                     class="flex items-start gap-3 p-4 rounded-xl mb-3 text-sm border backdrop-blur-md"
                     style="background-color: var(--status-info-bg); color: var(--status-info); border-color: var(--status-info-border);">
                    <svg class="w-4 h-4 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    <span>{{ session('info') }}</span>
                    <button @click="show = false" class="ml-auto opacity-60 hover:opacity-100" aria-label="Tutup">✕</button>
                </div>
            @endif
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         KONTEN HALAMAN
    ══════════════════════════════════════════════════════════════════════ --}}
    <main id="main-content" class="relative z-10 pb-20 md:pb-6" tabindex="-1">
        @yield('content')
    </main>

    {{-- ══════════════════════════════════════════════════════════════════
         FOOTER MINIMALIS & RESPONSIF (KantinKita)
    ══════════════════════════════════════════════════════════════════════ --}}
    <footer class="mt-12 border-t relative z-10 backdrop-blur-md pb-20 md:pb-6"
            style="border-color: var(--border-default); background-color: color-mix(in srgb, var(--bg-surface) 80%, transparent);"
            x-data="footerSupportModal()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-5">
            <div class="flex flex-col md:flex-row items-center justify-between gap-3 text-xs" style="color: var(--text-muted);">
                
                {{-- Kiri: Brand & Hak Cipta --}}
                <div class="flex items-center gap-2 text-center md:text-left">
                    <span class="font-extrabold tracking-tight text-base" style="color: var(--brand);">KantinKita</span>
                    <span class="hidden sm:inline" style="color: var(--border-strong);">•</span>
                    <p class="text-[0.72rem] sm:text-xs">
                        &copy; {{ date('Y') }} KantinKita. Hak cipta dilindungi.
                    </p>
                </div>

                {{-- Kanan: Tautan Berguna Minimalis --}}
                <div class="flex items-center flex-wrap justify-center gap-x-4 gap-y-1.5 text-[0.72rem] sm:text-xs font-semibold">
                    <button
                        type="button"
                        @click="openFaq()"
                        class="hover:underline transition-colors cursor-pointer flex items-center gap-1.5"
                        style="color: var(--text-secondary);"
                    >
                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Bantuan & FAQ</span>
                    </button>
                    <span style="color: var(--border-strong);" aria-hidden="true">•</span>
                    <button
                        type="button"
                        @click="openReportIssue()"
                        class="hover:underline transition-colors cursor-pointer flex items-center gap-1.5"
                        style="color: var(--text-secondary);"
                    >
                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Laporkan Masalah Pesanan</span>
                    </button>
                    <span style="color: var(--border-strong);" aria-hidden="true">•</span>
                    <button
                        type="button"
                        @click="openContact()"
                        class="hover:underline transition-colors cursor-pointer flex items-center gap-1.5"
                        style="color: var(--text-secondary);"
                    >
                        <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>Kontak Pengelola Kantin</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Modal Dialog Interaktif untuk Bantuan, Masalah & Kontak --}}
        <div
            x-show="modalOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-950/60 backdrop-blur-xs"
            id="modal-footer-support"
        >
            <div
                @click.outside="modalOpen = false"
                class="w-full max-w-lg rounded-2xl border p-5 sm:p-6 shadow-2xl relative"
                style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card-hover);"
            >
                <div class="flex items-center justify-between mb-4 pb-2.5 border-b" style="border-color: var(--border-default);">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center border text-xs"
                             style="background-color: var(--brand-subtle); border-color: var(--brand-border); color: var(--brand);">
                            <template x-if="activeTab === 'faq'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </template>
                            <template x-if="activeTab === 'report'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </template>
                            <template x-if="activeTab === 'contact'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </template>
                        </div>
                        <h3 class="text-sm sm:text-base font-extrabold" style="color: var(--text-primary);" x-text="modalTitle"></h3>
                    </div>
                    <button
                        @click="modalOpen = false"
                        type="button"
                        class="w-7 h-7 rounded-lg border flex items-center justify-center text-xs opacity-70 hover:opacity-100 transition-opacity cursor-pointer"
                        style="border-color: var(--border-default); color: var(--text-secondary);"
                        aria-label="Tutup dialog"
                    >
                        ✕
                    </button>
                </div>

                {{-- Konten FAQ --}}
                <div x-show="activeTab === 'faq'" class="space-y-3 text-xs" style="color: var(--text-secondary);">
                    <div class="p-3 rounded-xl border" style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                        <p class="font-bold mb-1" style="color: var(--text-primary);">Q: Bagaimana cara memesan tanpa antri?</p>
                        <p>Pilih menu makanan atau minuman dari stand kantin favorit Anda, klik <b>Beli Sekarang</b> atau <b>+ Tambah ke Keranjang</b>, lalu selesaikan pemesanan. Pesanan langsung masuk ke dapur tenant kantin.</p>
                    </div>
                    <div class="p-3 rounded-xl border" style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                        <p class="font-bold mb-1" style="color: var(--text-primary);">Q: Kapan pesanan bisa diambil?</p>
                        <p>Pesanan dapat diambil pada jam istirahat sekolah (Sesi 1: 09:30–10:00 WIB & Sesi 2: 12:00–13:00 WIB). Pantau ikon lonceng notifikasi di atas untuk melihat status <b>Siap Diambil</b>.</p>
                    </div>
                    <div class="p-3 rounded-xl border" style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                        <p class="font-bold mb-1" style="color: var(--text-primary);">Q: Metode pembayaran apa saja yang diterima?</p>
                        <p>Kantin menerima pembayaran tunai di kasir stand tenant dan pembayaran non-tunai melalui scan QRIS langsung.</p>
                    </div>
                </div>

                {{-- Konten Laporkan Masalah --}}
                <div x-show="activeTab === 'report'" class="space-y-3 text-xs" style="color: var(--text-secondary);">
                    <p>Mengalami kendala dengan pesanan Anda (makanan belum siap, pesanan salah, atau kendala pembayaran)?</p>
                    <div class="p-3.5 rounded-xl border space-y-2" style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span><b>Lokasi Pos Layanan Kantin:</b> Kantor Koperasi Sekolah / Meja Kasir Utama</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <span><b>WhatsApp Layanan Siswa:</b> 0812-9876-5432</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span><b>Waktu Layanan:</b> Senin – Jumat (07:00 – 14:00 WIB)</span>
                        </div>
                    </div>
                    <div class="text-right pt-2">
                        <a href="https://wa.me/6281298765432?text=Halo%20Pengelola%20KantinKita,%20saya%20ingin%20melaporkan%20kendala%20pesanan"
                           target="_blank"
                           class="btn-brand text-xs px-4 py-2 rounded-xl font-bold inline-flex items-center gap-1.5 shadow-sm">
                            <span>Hubungi via WhatsApp</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>

                {{-- Konten Kontak Pengelola --}}
                <div x-show="activeTab === 'contact'" class="space-y-3 text-xs" style="color: var(--text-secondary);">
                    <p>Informasi resmi pengelola Kantin Sekolah Digital KantinKita:</p>
                    <div class="p-3.5 rounded-xl border space-y-2.5" style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                        <div class="flex justify-between border-b pb-2" style="border-color: var(--border-default);">
                            <span class="font-medium">Unit Pengelola:</span>
                            <span class="font-bold" style="color: var(--text-primary);">Tim Operasional Kantin & Koperasi Sekolah</span>
                        </div>
                        <div class="flex justify-between border-b pb-2" style="border-color: var(--border-default);">
                            <span class="font-medium">Email Bantuan:</span>
                            <span class="font-bold" style="color: var(--text-primary);">kantin@sekolah.sch.id</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Jam Buka Dapur:</span>
                            <span class="font-bold" style="color: var(--text-primary);">07:00 – 14:00 WIB</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t flex justify-end" style="border-color: var(--border-default);">
                    <button
                        @click="modalOpen = false"
                        type="button"
                        class="btn-secondary text-xs px-4 py-1.5 rounded-xl font-semibold"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </footer>

    <script>
        function footerSupportModal() {
            return {
                modalOpen: false,
                activeTab: 'faq',
                modalTitle: 'Bantuan & FAQ',
                openFaq() {
                    this.activeTab = 'faq';
                    this.modalTitle = 'Bantuan & Panduan FAQ';
                    this.modalOpen = true;
                },
                openReportIssue() {
                    this.activeTab = 'report';
                    this.modalTitle = 'Laporkan Masalah Pesanan';
                    this.modalOpen = true;
                },
                openContact() {
                    this.activeTab = 'contact';
                    this.modalTitle = 'Kontak Pengelola Kantin';
                    this.modalOpen = true;
                }
            };
        }
    </script>

    {{-- ══════════════════════════════════════════════════════════════════
         MOBILE BOTTOM NAVIGATION BAR (KHUSUS LAYAR HP & TABLET KECIL)
         Modern app dock (seperti GoFood / GrabFood) untuk kemudahan navigasi jempol
    ══════════════════════════════════════════════════════════════════ --}}
    <nav class="md:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-t border-slate-200/90 dark:border-slate-800/90 shadow-[0_-4px_20px_rgba(0,0,0,0.06)] px-2 py-1.5"
         aria-label="Navigasi Bawah">
        <div class="grid grid-cols-4 items-center max-w-md mx-auto">
            {{-- Tab 1: Menu / Beranda --}}
            <a href="{{ route('home') }}"
               class="flex flex-col items-center justify-center py-1 px-1 rounded-xl transition-all duration-150 {{ request()->routeIs('home') ? 'text-orange-600 dark:text-orange-500 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <div class="relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('home') ? '2.5' : '2' }}"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <span class="text-[0.6875rem] mt-0.5 font-medium tracking-tight">Menu</span>
            </a>

            {{-- Tab 2: Pesanan Saya --}}
            <a href="{{ route('orders.index') }}"
               class="flex flex-col items-center justify-center py-1 px-1 rounded-xl transition-all duration-150 relative {{ request()->routeIs('orders.index') ? 'text-orange-600 dark:text-orange-500 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <div class="relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('orders.index') ? '2.5' : '2' }}"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    @if(isset($navUserOrders) && $navUserOrders->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])->count() > 0)
                        <span class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse border-2 border-white dark:border-slate-900"></span>
                    @endif
                </div>
                <span class="text-[0.6875rem] mt-0.5 font-medium tracking-tight">Pesanan</span>
            </a>

            {{-- Tab 3: Keranjang Belanja --}}
            <a href="{{ route('cart.index') }}"
               class="flex flex-col items-center justify-center py-1 px-1 rounded-xl transition-all duration-150 relative {{ request()->routeIs('cart.index') ? 'text-orange-600 dark:text-orange-500 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <div class="relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('cart.index') ? '2.5' : '2' }}"
                              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <span
                        x-show="cartCount > 0"
                        x-text="cartCount > 99 ? '99+' : cartCount"
                        class="absolute -top-1.5 -right-2 min-w-[1.125rem] h-[1.125rem] px-1 text-[0.625rem] font-black rounded-full flex items-center justify-center bg-orange-600 text-white shadow-xs"
                    ></span>
                </div>
                <span class="text-[0.6875rem] mt-0.5 font-medium tracking-tight">Keranjang</span>
            </a>

            {{-- Tab 4: Profil / Akun --}}
            @auth
            <a href="{{ route('profile.edit') }}"
               class="flex flex-col items-center justify-center py-1 px-1 rounded-xl transition-all duration-150 {{ request()->routeIs('profile.*') ? 'text-orange-600 dark:text-orange-500 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <div class="relative">
                    <div class="w-5 h-5 rounded-full flex items-center justify-center text-[0.625rem] font-bold bg-orange-500 text-white">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </div>
                <span class="text-[0.6875rem] mt-0.5 font-medium tracking-tight truncate max-w-[4.5rem]">Akun</span>
            </a>
            @else
            <a href="{{ route('login') }}"
               class="flex flex-col items-center justify-center py-1 px-1 rounded-xl transition-all duration-150 {{ (request()->routeIs('login*') || request()->routeIs('register*')) ? 'text-orange-600 dark:text-orange-500 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <div class="relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <span class="text-[0.6875rem] mt-0.5 font-medium tracking-tight">Masuk / Daftar</span>
            </a>
            @endauth
        </div>
    </nav>

    @stack('modals')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>
</html>
