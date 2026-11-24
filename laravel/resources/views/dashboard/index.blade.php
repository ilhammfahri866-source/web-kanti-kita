@extends('layouts.dashboard')

@section('title', 'Dashboard Kantin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="dashboardManager()">

    {{-- ── HEADER DASHBOARD & AKSI UTAMA ─────────────────────────────────── --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold mb-2 border shadow-2xs"
                 style="background: var(--brand-subtle); border-color: var(--brand-border); color: var(--brand);">
                <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                <span>KantinKita Management System</span>
                <span class="opacity-40">•</span>
                <span>Live Sync Dapur</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight mb-1" style="color: var(--text-primary);">
                Dashboard Pengelola Kantin
            </h1>
            <p class="text-xs sm:text-sm" style="color: var(--text-secondary);">
                Kelola antrian pesanan masuk, pantau ketersediaan stok, dan perbarui harga menu secara real-time.
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            {{-- Tombol Bel Notifikasi Suara --}}
            <button
                type="button"
                @click="toggleSound()"
                class="btn-secondary text-xs font-semibold py-2.5 px-3.5 rounded-xl border flex items-center gap-1.5 transition-colors cursor-pointer"
                :title="soundEnabled ? 'Suara Bel Aktif (Klik untuk Matikan)' : 'Suara Bel Mati (Klik untuk Aktifkan)'"
            >
                <template x-if="soundEnabled">
                    <span class="inline-flex items-center gap-1.5 text-emerald-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span>Bel: Aktif</span>
                    </span>
                </template>
                <template x-if="!soundEnabled">
                    <span class="inline-flex items-center gap-1.5 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/></svg>
                        <span>Bel: Senyap</span>
                    </span>
                </template>
            </button>

            {{-- Tombol Tambah Produk Baru (10% CTA Accent) --}}
            <button
                type="button"
                @click="showCreateModal = true"
                class="btn-brand text-xs font-bold py-2.5 px-4 rounded-xl shadow-md transition-all hover:scale-[1.02] active:scale-95 flex items-center gap-2"
                id="btn-add-product"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Menu Baru</span>
            </button>

            <a href="{{ route('dashboard.reports.index') }}"
               class="btn-secondary text-xs font-semibold py-2.5 px-3.5 rounded-xl border flex items-center gap-2 transition-colors"
               title="Buka Laporan Keuangan & Hitung Penghasilan Berdasarkan Tanggal">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>Laporan Keuangan</span>
            </a>

            <a href="{{ route('dashboard.stock.index') }}"
               class="btn-secondary text-xs font-semibold py-2.5 px-3.5 rounded-xl border flex items-center gap-2 transition-colors"
               title="Buka Halaman Manajemen Stok Bahan & Produk">
                <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <span>Stok Bahan & Produk</span>
            </a>

            <a href="{{ route('home') }}"
               class="btn-secondary text-xs font-semibold py-2.5 px-3.5 rounded-xl border flex items-center gap-1.5 transition-colors"
               title="Lihat katalog toko sebagai pelanggan">
                <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <span>Menu Toko</span>
                <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>
    </div>

    {{-- ── FLASH ALERTS ─────────────────────────────────────────────────── --}}
    @if(session('order_cancelled_success'))
        @php $cancelledInfo = session('order_cancelled_success'); @endphp
        <div x-data="{ show: true }" x-show="show" x-transition
             class="mb-6 p-4 sm:p-5 rounded-2xl border flex items-start justify-between shadow-md"
             style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.12) 0%, rgba(245, 158, 11, 0.08) 100%); border-color: var(--status-danger-border);">
            <div class="flex items-start gap-3">
                <span class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 border shadow-xs"
                      style="background-color: var(--status-danger-bg); color: var(--status-danger); border-color: var(--status-danger-border);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                </span>
                <div>
                    <h4 class="font-extrabold text-sm sm:text-base flex items-center gap-2" style="color: var(--status-danger);">
                        <span>Berhasil Tolak Pesanan</span>
                        <span class="font-mono text-xs px-2 py-0.5 rounded-md border" style="background-color: var(--bg-surface); border-color: var(--border-default); color: var(--text-primary);">
                            #{{ $cancelledInfo['order_number'] ?? '' }}
                        </span>
                    </h4>
                    <p class="text-xs mt-1 font-medium" style="color: var(--text-secondary);">
                        Pesanan milik <b>{{ $cancelledInfo['customer_name'] ?? 'Pelanggan' }}</b> telah berhasil ditolak.
                    </p>
                    @if(!empty($cancelledInfo['reason']))
                        <div class="mt-2 p-2.5 rounded-xl border text-xs flex items-center gap-2 font-medium"
                             style="background-color: var(--bg-surface); border-color: var(--status-danger-border); color: var(--status-danger);">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span><b>Alasan Diberikan:</b> "{{ $cancelledInfo['reason'] }}"</span>
                        </div>
                    @endif
                    <p class="text-[11px] mt-2 flex items-center gap-1.5 font-semibold" style="color: var(--text-muted);">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        <span>Stok produk & porsi pesanan telah otomatis dikembalikan ke menu kantin.</span>
                    </p>
                </div>
            </div>
            <button @click="show = false" class="text-xs font-bold w-7 h-7 rounded-lg border flex items-center justify-center opacity-70 hover:opacity-100 transition-opacity cursor-pointer shrink-0 ml-3"
                    style="border-color: var(--status-danger-border); color: var(--status-danger); background-color: var(--bg-surface);">
                ✕
            </button>
        </div>
    @elseif(session('order_confirmed_success'))
        @php $confirmedInfo = session('order_confirmed_success'); @endphp
        <div x-data="{ show: true }" x-show="show" x-transition
             class="mb-6 p-4 sm:p-5 rounded-2xl border flex items-start justify-between shadow-md"
             style="background: linear-gradient(135deg, rgba(37, 99, 235, 0.12) 0%, rgba(16, 185, 129, 0.08) 100%); border-color: rgba(37, 99, 235, 0.3);">
            <div class="flex items-start gap-3">
                <span class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 border shadow-xs"
                      style="background-color: rgba(37, 99, 235, 0.1); color: #2563EB; border-color: rgba(37, 99, 235, 0.3);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <div>
                    <h4 class="font-extrabold text-sm sm:text-base flex items-center gap-2" style="color: #2563EB;">
                        <span>Pesanan Telah Dikonfirmasi</span>
                        <span class="font-mono text-xs px-2 py-0.5 rounded-md border" style="background-color: var(--bg-surface); border-color: var(--border-default); color: var(--text-primary);">
                            #{{ $confirmedInfo['order_number'] ?? '' }}
                        </span>
                    </h4>
                    <p class="text-xs mt-1 font-medium" style="color: var(--text-secondary);">
                        Pesanan milik <b>{{ $confirmedInfo['customer_name'] ?? 'Pelanggan' }}</b> telah berhasil dikonfirmasi penjual! Dapur dapat segera mulai memasak.
                    </p>
                </div>
            </div>
            <button @click="show = false" class="text-xs font-bold w-7 h-7 rounded-lg border flex items-center justify-center opacity-70 hover:opacity-100 transition-opacity cursor-pointer shrink-0 ml-3"
                    style="border-color: rgba(37, 99, 235, 0.3); color: #2563EB; background-color: var(--bg-surface);">
                ✕
            </button>
        </div>
    @elseif(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition
             class="mb-6 p-4 rounded-xl border text-sm font-semibold flex items-center justify-between backdrop-blur-md shadow-sm"
             style="background-color: var(--status-success-bg); color: var(--status-success); border-color: var(--status-success-border);">
            <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="text-sm font-bold opacity-70 hover:opacity-100">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div x-data="{ show: true }" x-show="show" x-transition
             class="mb-6 p-4 rounded-xl border text-xs font-medium backdrop-blur-md shadow-sm"
             style="background-color: var(--status-danger-bg); color: var(--status-danger); border-color: var(--status-danger-border);">
            <div class="font-bold mb-1">Terjadi kesalahan pada formulir:</div>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ── 4 KPI STATISTIK UTAMA ────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">

        {{-- Card 1: Pesanan Aktif --}}
        <div class="rounded-2xl p-4 sm:p-5 border shadow-sm transition-all hover:scale-[1.01] cursor-default"
             style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <h3 class="text-[10px] sm:text-xs font-bold uppercase tracking-wider leading-tight" style="color: var(--text-muted);">Pesanan Aktif</h3>
                    <p class="text-2xl sm:text-3xl font-black mt-1" style="color: var(--cta-accent);">{{ $activeOrdersCount }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: rgba(249, 115, 22, 0.12); color: #ea580c;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
            </div>
            <div class="flex items-center justify-between mt-2">
                <p class="text-[11px] font-medium" style="color: var(--text-secondary);">{{ $pendingOrdersCount }} menunggu konfirmasi</p>
                @if($ordersChange != 0)
                    <span class="text-[10px] font-extrabold px-1.5 py-0.5 rounded-full"
                          style="background-color: {{ $ordersChange >= 0 ? 'var(--status-success-bg)' : 'var(--status-danger-bg)' }}; color: {{ $ordersChange >= 0 ? 'var(--status-success)' : 'var(--status-danger)' }};">
                        {{ $ordersChange >= 0 ? '+' : '' }}{{ $ordersChange }}%
                    </span>
                @endif
            </div>
            <p class="text-[10px] mt-1" style="color: var(--text-muted);">Hari ini: <b>{{ $todayOrders }}</b> | Kemarin: {{ $yesterdayOrders }}</p>
        </div>

        {{-- Card 2: Pendapatan Hari Ini --}}
        <div class="rounded-2xl p-4 sm:p-5 border shadow-sm transition-all hover:scale-[1.01] cursor-default"
             style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
            <div class="flex items-start justify-between mb-2">
                <div class="min-w-0 flex-1">
                    <h3 class="text-[10px] sm:text-xs font-bold uppercase tracking-wider leading-tight" style="color: var(--text-muted);">Pendapatan Selesai</h3>
                    <p class="text-lg sm:text-2xl font-black mt-1 truncate" style="color: var(--brand);">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 ml-2" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="flex items-center justify-between mt-2">
                <p class="text-[11px] font-medium truncate" style="color: var(--text-secondary);">{{ $completedOrdersCount }} pesanan selesai</p>
                @if($revenueChange != 0)
                    <span class="text-[10px] font-extrabold px-1.5 py-0.5 rounded-full shrink-0 ml-1"
                          style="background-color: {{ $revenueChange >= 0 ? 'var(--status-success-bg)' : 'var(--status-danger-bg)' }}; color: {{ $revenueChange >= 0 ? 'var(--status-success)' : 'var(--status-danger)' }};">
                        {{ $revenueChange >= 0 ? '+' : '' }}{{ $revenueChange }}%
                    </span>
                @endif
            </div>
            <div class="mt-2 pt-2 border-t flex items-center justify-between" style="border-color: var(--border-default);">
                <p class="text-[10px]" style="color: var(--text-muted);">Hari ini: <b>Rp {{ number_format($todayRevenue, 0, ',', '.') }}</b></p>
                <a href="{{ route('dashboard.reports.index') }}" class="text-[10.5px] font-bold hover:underline inline-flex items-center gap-0.5" style="color: var(--brand);">
                    <span>Laporan &rarr;</span>
                </a>
            </div>
        </div>

        {{-- Card 3: Katalog Menu --}}
        <div class="rounded-2xl p-4 sm:p-5 border shadow-sm transition-all cursor-pointer hover:scale-[1.01]"
             @click="activeTab = 'products'; productFilter = 'all'"
             style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <h3 class="text-[10px] sm:text-xs font-bold uppercase tracking-wider leading-tight" style="color: var(--text-muted);">Katalog Menu</h3>
                    <p class="text-2xl sm:text-3xl font-black mt-1" style="color: var(--text-primary);">{{ $totalProductsCount }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: rgba(59, 130, 246, 0.12); color: #2563eb;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </div>
            </div>
            <p class="text-[11px] font-medium hover:underline" style="color: var(--brand);">Lihat semua menu &rarr;</p>
            @if($lowStockCount > 0)
                <p class="text-[10px] mt-1 flex items-center gap-1 font-semibold" style="color: var(--status-warning);">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    <span>{{ $lowStockCount }} menu stok menipis</span>
                </p>
            @endif
        </div>

        {{-- Card 4: Stok Kosong --}}
        <div class="rounded-2xl p-4 sm:p-5 border shadow-sm transition-all cursor-pointer hover:scale-[1.01]"
             @click="activeTab = 'products'; productFilter = 'out_of_stock'"
             style="{{ $outOfStockCount > 0 ? 'background-color: var(--status-danger-bg); border-color: var(--status-danger-border);' : 'background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);' }}">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <h3 class="text-[10px] sm:text-xs font-bold uppercase tracking-wider leading-tight"
                        style="color: {{ $outOfStockCount > 0 ? 'var(--status-danger)' : 'var(--text-muted)' }};">
                        Stok Kosong
                    </h3>
                    <p class="text-2xl sm:text-3xl font-black mt-1"
                       style="color: {{ $outOfStockCount > 0 ? 'var(--status-danger)' : 'var(--status-success)' }};">
                        {{ $outOfStockCount }}
                    </p>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                     style="background: {{ $outOfStockCount > 0 ? 'rgba(239, 68, 68, 0.15)' : 'rgba(16, 185, 129, 0.12)' }}; color: {{ $outOfStockCount > 0 ? '#dc2626' : '#059669' }};">
                    @if($outOfStockCount > 0)
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @endif
                </div>
            </div>
            <p class="text-[11px] font-bold"
               style="color: {{ $outOfStockCount > 0 ? 'var(--status-danger)' : 'var(--status-success)' }};">
                @if($outOfStockCount > 0) Perlu diisi! Klik &rarr; @else Stok semua aman @endif
            </p>
        </div>
    </div>

    {{-- ── WIDGET HITUNG PENGHASILAN BERDASARKAN TANGGAL DI DASHBOARD ─────────────── --}}
    <div class="rounded-2xl border shadow-sm mb-6 p-5 sm:p-6"
         style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4 pb-4 border-b"
             style="border-color: var(--border-default);">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-xs"
                      style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </span>
                <div>
                    <h2 class="font-bold text-base sm:text-lg tracking-tight" style="color: var(--text-primary);">
                        Hitung Penghasilan Berdasarkan Tanggal
                    </h2>
                    <p class="text-xs" style="color: var(--text-secondary);">
                        Pilih rentang tanggal untuk menghitung total pemasukan dan omzet kantin secara instan.
                    </p>
                </div>
            </div>

            <a href="{{ route('dashboard.reports.index', ['period' => 'custom', 'start_date' => $calcDateFrom, 'end_date' => $calcDateTo]) }}"
               class="btn-secondary text-xs font-bold py-2 px-3.5 rounded-xl border flex items-center gap-1.5 transition-colors self-start md:self-auto hover:scale-[1.02]">
                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Buka Rekapitulasi Lengkap & Cetak PDF &rarr;</span>
            </a>
        </div>

        {{-- Form Pemilihan Tanggal --}}
        <form action="{{ route('dashboard.index') }}" method="GET" class="mb-5">
            <div class="flex flex-col sm:flex-row items-end gap-3">
                <div class="w-full sm:w-auto flex-1">
                    <label class="block text-[11px] font-bold uppercase tracking-wider mb-1" style="color: var(--text-muted);">
                        Dari Tanggal:
                    </label>
                    <input
                        type="date"
                        name="calc_date_from"
                        id="calc-date-from"
                        value="{{ $calcDateFrom }}"
                        class="w-full text-xs font-medium px-3 py-2 rounded-xl border focus:outline-none"
                        style="background-color: var(--bg-surface-2); border-color: var(--border-default); color: var(--text-primary);"
                    >
                </div>

                <div class="w-full sm:w-auto flex-1">
                    <label class="block text-[11px] font-bold uppercase tracking-wider mb-1" style="color: var(--text-muted);">
                        Sampai Tanggal:
                    </label>
                    <input
                        type="date"
                        name="calc_date_to"
                        id="calc-date-to"
                        value="{{ $calcDateTo }}"
                        class="w-full text-xs font-medium px-3 py-2 rounded-xl border focus:outline-none"
                        style="background-color: var(--bg-surface-2); border-color: var(--border-default); color: var(--text-primary);"
                    >
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button
                        type="submit"
                        class="btn-brand text-xs font-bold py-2 px-4 rounded-xl flex items-center justify-center gap-1.5 cursor-pointer shadow-xs w-full sm:w-auto"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span>Hitung Penghasilan</span>
                    </button>
                    @if($calcDateFrom !== \Carbon\Carbon::today()->format('Y-m-d') || $calcDateTo !== \Carbon\Carbon::today()->format('Y-m-d'))
                        <a href="{{ route('dashboard.index') }}"
                           class="btn-secondary text-xs font-semibold py-2 px-3 rounded-xl border transition-colors"
                           title="Reset ke Hari Ini">
                            Hari Ini
                        </a>
                    @endif
                </div>
            </div>
        </form>

        {{-- Hasil Perhitungan Penghasilan Berdasarkan Tanggal --}}
        <div class="p-4 sm:p-5 rounded-2xl border"
             style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.06) 0%, rgba(249, 115, 22, 0.04) 100%); border-color: var(--border-default);">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                <div>
                    <span class="text-[10.5px] font-bold uppercase tracking-wider block" style="color: var(--text-muted);">
                        Total Penghasilan Bersih
                    </span>
                    <p class="text-xl sm:text-2xl font-black font-mono mt-0.5" style="color: var(--brand);">
                        Rp {{ number_format($calcRevenue, 0, ',', '.') }}
                    </p>
                    <span class="text-[11px] font-medium" style="color: var(--text-secondary);">
                        Periode: <b>{{ \Carbon\Carbon::parse($calcDateFrom)->format('d/m/Y') }}</b> s/d <b>{{ \Carbon\Carbon::parse($calcDateTo)->format('d/m/Y') }}</b>
                    </span>
                </div>

                <div>
                    <span class="text-[10.5px] font-bold uppercase tracking-wider block" style="color: var(--text-muted);">
                        Pesanan Selesai
                    </span>
                    <p class="text-xl sm:text-2xl font-black mt-0.5" style="color: var(--text-primary);">
                        {{ $calcOrdersCount }} Transaksi
                    </p>
                    <span class="text-[11px] font-medium" style="color: var(--status-success);">
                        Status Lunas
                    </span>
                </div>

                <div>
                    <span class="text-[10.5px] font-bold uppercase tracking-wider block" style="color: var(--text-muted);">
                        Penerimaan Tunai (Cash)
                    </span>
                    <p class="text-lg font-black font-mono mt-0.5 text-emerald-600">
                        Rp {{ number_format($calcCashRevenue, 0, ',', '.') }}
                    </p>
                    <span class="text-[11px] font-medium" style="color: var(--text-muted);">
                        Uang fisik diterima di kasir
                    </span>
                </div>

                <div>
                    <span class="text-[10.5px] font-bold uppercase tracking-wider block" style="color: var(--text-muted);">
                        Penerimaan QRIS
                    </span>
                    <p class="text-lg font-black font-mono mt-0.5 text-blue-600">
                        Rp {{ number_format($calcQrisRevenue, 0, ',', '.') }}
                    </p>
                    <span class="text-[11px] font-medium" style="color: var(--text-muted);">
                        Pembayaran non-tunai digital
                    </span>
                </div>

            </div>
        </div>
    </div>

    {{-- ── PESANAN TERBARU MASUK (MENGGANTIKAN GRAFIK 7 HARI) ────────────────────── --}}
    <div class="rounded-2xl border shadow-sm mb-6 overflow-hidden"
         style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
        <div class="px-5 py-3.5 border-b flex items-center justify-between" style="border-color: var(--border-default);">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background: rgba(249, 115, 22, 0.1); color: var(--brand);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <div>
                    <h2 class="font-bold text-sm tracking-tight" style="color: var(--text-primary);">Pesanan Terbaru Masuk</h2>
                    <p class="text-xs" style="color: var(--text-muted);">Daftar transaksi terkini yang dipesan oleh siswa/pelanggan</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold px-2.5 py-1 rounded-full border"
                      style="background-color: var(--bg-surface-2); border-color: var(--border-default); color: var(--cta-accent);">
                    {{ $pendingOrdersCount }} Menunggu Konfirmasi
                </span>
                <button type="button" @click="activeTab = 'orders'; orderFilter = 'all'"
                        class="text-xs font-bold hover:underline" style="color: var(--brand);">
                    Lihat Semua &rarr;
                </button>
            </div>
        </div>
        <div class="p-4 sm:p-5">
            @php
                $recentOrdersList = $orders->take(6);
            @endphp

            @if($recentOrdersList->isEmpty())
                <div class="text-center py-6">
                    <div class="w-12 h-12 mx-auto rounded-full flex items-center justify-center mb-2" style="background: var(--bg-surface-3); color: var(--text-muted);">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                    </div>
                    <p class="text-xs font-medium" style="color: var(--text-muted);">Belum ada pesanan masuk saat ini.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($recentOrdersList as $recentOrder)
                        <div class="p-3.5 rounded-xl border flex flex-col justify-between transition-all hover:scale-[1.01]"
                             style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-mono text-xs font-bold" style="color: var(--brand);">{{ $recentOrder->order_number }}</span>
                                    @if($recentOrder->status === 'pending')
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-full" style="background-color: var(--status-danger-bg); color: var(--status-danger);">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                            <span>Menunggu</span>
                                        </span>
                                    @elseif(in_array($recentOrder->status, ['confirmed', 'preparing']))
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-full" style="background-color: var(--status-warning-bg); color: var(--status-warning);">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            <span>Dimasak</span>
                                        </span>
                                    @elseif($recentOrder->status === 'ready')
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-full" style="background-color: var(--status-success-bg); color: var(--status-success);">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Siap Ambil</span>
                                        </span>
                                    @elseif($recentOrder->status === 'completed')
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-full" style="background-color: var(--bg-surface-3); color: var(--text-muted);">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            <span>Selesai</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-full" style="background-color: var(--bg-surface-3); color: var(--text-muted);">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            <span>{{ ucfirst($recentOrder->status) }}</span>
                                        </span>
                                    @endif
                                </div>
                                <div class="mb-2">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <p class="text-xs font-bold" style="color: var(--text-primary);">{{ $recentOrder->customer_name }} <span class="font-normal opacity-75">({{ $recentOrder->customer_class }})</span></p>
                                        @if($recentOrder->isTeacherOrder())
                                            <span class="inline-flex items-center gap-0.5 text-[9px] font-black px-1.5 py-0.5 rounded border"
                                                  style="background-color: rgba(59, 130, 246, 0.12); color: #1d4ed8; border-color: rgba(59, 130, 246, 0.3);"
                                                  title="Pesanan dari Guru / Tenaga Pendidik">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
                                                <span>GURU</span>
                                            </span>
                                        @endif
                                        @if($recentOrder->isInSchool())
                                            <span class="inline-flex items-center gap-1 text-[9px] font-bold px-1.5 py-0.5 rounded border"
                                                  style="background-color: rgba(16, 185, 129, 0.1); color: #059669; border-color: rgba(16, 185, 129, 0.25);"
                                                  title="Lokasi terverifikasi di area sekolah">
                                                <svg class="w-2.5 h-2.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <span>Di Sekolah</span>
                                            </span>
                                        @elseif($recentOrder->location_status === 'outside')
                                            <span class="inline-flex items-center gap-1 text-[9px] font-bold px-1.5 py-0.5 rounded border"
                                                  style="background-color: rgba(239, 68, 68, 0.1); color: #dc2626; border-color: rgba(239, 68, 68, 0.25);"
                                                  title="Di luar sekolah (~{{ $recentOrder->distance_meters }}m)">
                                                <svg class="w-2.5 h-2.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                <span>Di Luar</span>
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] truncate mt-0.5" style="color: var(--text-secondary);">
                                        {{ $recentOrder->items->map(fn($i) => $i->quantity . 'x ' . $i->product_name)->join(', ') }}
                                    </p>
                                </div>
                            </div>
                            <div class="pt-2 border-t flex items-center justify-between mt-2" style="border-color: var(--border-default);">
                                <span class="text-xs font-black" style="color: var(--text-primary);">Rp {{ number_format($recentOrder->total, 0, ',', '.') }}</span>
                                <span class="text-[10px]" style="color: var(--text-muted);">{{ $recentOrder->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ── TAB NAVIGASI UTAMA DASHBOARD (MENYATU DENGAN GAYA WEB) ─────────── --}}
    <div class="flex items-center gap-2 mb-6 border-b pb-3 overflow-x-auto scrollbar-none" style="border-color: var(--border-default);">
        {{-- Tab 1: Pesanan Masuk --}}
        <button
            type="button"
            @click="activeTab = 'orders'"
            class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm flex items-center gap-2 transition-all cursor-pointer whitespace-nowrap"
            :class="activeTab === 'orders' ? 'category-tab-active shadow-sm' : 'category-tab-inactive'"
        >
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            <span>Antrian Pesanan</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-extrabold"
                  :style="activeTab === 'orders' ? 'background: rgba(255,255,255,0.25); color: #ffffff;' : 'background: var(--bg-surface-3); color: var(--text-secondary);'">
                {{ $orders->count() }}
            </span>
        </button>

        {{-- Tab 2: Daftar Produk & Menu --}}
        <button
            type="button"
            @click="activeTab = 'products'"
            class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm flex items-center gap-2 transition-all cursor-pointer whitespace-nowrap"
            :class="activeTab === 'products' ? 'category-tab-active shadow-sm' : 'category-tab-inactive'"
            id="tab-btn-products"
        >
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <span>Daftar Produk & Menu</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-extrabold"
                  :style="activeTab === 'products' ? 'background: rgba(255,255,255,0.25); color: #ffffff;' : 'background: var(--bg-surface-3); color: var(--text-secondary);'">
                {{ $products->count() }}
            </span>

            @if($outOfStockCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase animate-pulse shadow-sm"
                      style="background-color: var(--status-danger); color: #ffffff;">
                    {{ $outOfStockCount }} Kosong
                </span>
            @endif
        </button>

        {{-- Tab 3: Daftar Bahan Baku & Persediaan Dapur --}}
        <button
            type="button"
            @click="activeTab = 'ingredients'"
            class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm flex items-center gap-2 transition-all cursor-pointer whitespace-nowrap"
            :class="activeTab === 'ingredients' ? 'category-tab-active shadow-sm' : 'category-tab-inactive'"
            id="tab-btn-stock-management"
        >
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <span>Bahan Baku & Dapur</span>
            <span>Bahan Baku & Dapur</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-extrabold"
                  :style="activeTab === 'ingredients' ? 'background: rgba(255,255,255,0.25); color: #ffffff;' : 'background: var(--bg-surface-3); color: var(--text-secondary);'">
                {{ $ingredients->count() }} Bahan
            </span>

            @if(($outOfStockIngredientsCount + $lowStockIngredientsCount) > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase animate-pulse shadow-sm"
                      style="background-color: var(--status-warning); color: #ffffff;">
                    {{ $outOfStockIngredientsCount + $lowStockIngredientsCount }} Perlu Belanja
                </span>
            @endif
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         TAB KONTEN 1: ANTRIAN PESANAN
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'orders'" x-transition:enter="transition ease-out duration-150">
        <div class="rounded-2xl border shadow-sm overflow-hidden"
             style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">

            {{-- Header + Filter Chips + Countdown --}}
            <div class="px-6 py-4 border-b" style="border-color: var(--border-default);">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                    <div>
                        <h2 class="font-bold text-base tracking-tight" style="color: var(--text-primary);">Daftar Antrian Pesanan</h2>
                        <p class="text-xs mt-0.5" style="color: var(--text-muted);">Perbarui status saat pesanan mulai dimasak hingga siap diambil.</p>
                    </div>
                    {{-- Countdown Visual Auto-Refresh --}}
                    <div class="flex items-center gap-2 shrink-0 self-start sm:self-auto">
                        <div class="flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-full border"
                             style="background-color: var(--bg-surface-2); color: var(--text-muted); border-color: var(--border-default);">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse inline-block"></span>
                            <span>Refresh dalam </span>
                            <span class="font-extrabold tabular-nums" style="color: var(--brand);" x-text="refreshCountdown + 's'">15s</span>
                        </div>
                    </div>
                </div>

                {{-- Filter Chip Buttons Status Pesanan --}}
                <div class="flex items-center gap-1.5 mt-3 overflow-x-auto pb-1 scrollbar-none">
                    <button type="button"
                        @click="orderFilter = 'all'"
                        class="px-3 py-1 text-[11px] font-bold rounded-full border transition-all whitespace-nowrap"
                        :class="orderFilter === 'all' ? 'category-tab-active' : 'btn-secondary'"
                    >
                        Semua
                        <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] font-extrabold"
                              :style="orderFilter === 'all' ? 'background:rgba(255,255,255,0.25);color:#fff' : 'background:var(--bg-surface-3);color:var(--text-secondary)'"
                        >{{ $orders->count() }}</span>
                    </button>

                    <button type="button"
                        @click="orderFilter = 'pending'"
                        class="px-3 py-1 text-[11px] font-bold rounded-full border transition-all whitespace-nowrap"
                        :class="orderFilter === 'pending' ? 'category-tab-active' : 'btn-secondary'"
                        style="--tab-active-bg: var(--status-danger);"
                    >
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            <span>Menunggu</span>
                        </span>
                        <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] font-extrabold"
                              :style="orderFilter === 'pending' ? 'background:rgba(255,255,255,0.25);color:#fff' : 'background:var(--status-danger-bg);color:var(--status-danger)'"
                        >{{ $orders->where('status', 'pending')->count() }}</span>
                    </button>

                    <button type="button"
                        @click="orderFilter = 'cooking'"
                        class="px-3 py-1 text-[11px] font-bold rounded-full border transition-all whitespace-nowrap"
                        :class="orderFilter === 'cooking' ? 'category-tab-active' : 'btn-secondary'"
                    >
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            <span>Dimasak</span>
                        </span>
                        <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] font-extrabold"
                              :style="orderFilter === 'cooking' ? 'background:rgba(255,255,255,0.25);color:#fff' : 'background:var(--status-warning-bg);color:var(--status-warning)'"
                        >{{ $orders->whereIn('status', ['confirmed','preparing'])->count() }}</span>
                    </button>

                    <button type="button"
                        @click="orderFilter = 'ready'"
                        class="px-3 py-1 text-[11px] font-bold rounded-full border transition-all whitespace-nowrap"
                        :class="orderFilter === 'ready' ? 'category-tab-active' : 'btn-secondary'"
                    >
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>Siap Ambil</span>
                        </span>
                        <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] font-extrabold"
                              :style="orderFilter === 'ready' ? 'background:rgba(255,255,255,0.25);color:#fff' : 'background:var(--status-success-bg);color:var(--status-success)'"
                        >{{ $orders->where('status', 'ready')->count() }}</span>
                    </button>

                    <button type="button"
                        @click="orderFilter = 'completed'"
                        class="px-3 py-1 text-[11px] font-bold rounded-full border transition-all whitespace-nowrap"
                        :class="orderFilter === 'completed' ? 'category-tab-active' : 'btn-secondary'"
                    >
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                            <span>Selesai</span>
                        </span>
                        <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] font-extrabold"
                              :style="orderFilter === 'completed' ? 'background:rgba(255,255,255,0.25);color:#fff' : 'background:var(--bg-surface-3);color:var(--text-secondary)'"
                        >{{ $orders->where('status', 'completed')->count() }}</span>
                    </button>

                    <button type="button"
                        @click="orderFilter = 'cancelled'"
                        class="px-3 py-1 text-[11px] font-bold rounded-full border transition-all whitespace-nowrap"
                        :class="orderFilter === 'cancelled' ? 'category-tab-active' : 'btn-secondary'"
                    >
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                            <span>Dibatalkan</span>
                        </span>
                        <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] font-extrabold"
                              :style="orderFilter === 'cancelled' ? 'background:rgba(255,255,255,0.25);color:#fff' : 'background:var(--bg-surface-3);color:var(--text-secondary)'"
                        >{{ $orders->where('status', 'cancelled')->count() }}</span>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b text-xs font-bold uppercase tracking-wider"
                            style="background-color: var(--bg-surface-2); border-color: var(--border-default); color: var(--text-secondary);">
                            <th class="px-6 py-4">Pesanan</th>
                            <th class="px-6 py-4">Pelanggan</th>
                            <th class="px-6 py-4">Detail Menu</th>
                            <th class="px-6 py-4">Pembayaran</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--border-default);">
                        @forelse($orders as $order)
                            @php
                                /* Tentukan warna highlight baris & status group untuk filter */
                                $rowBg = '';
                                $orderStatusGroup = match($order->status) {
                                    'pending'   => 'pending',
                                    'confirmed', 'preparing' => 'cooking',
                                    'ready'     => 'ready',
                                    'completed' => 'completed',
                                    'cancelled' => 'cancelled',
                                    default     => 'all',
                                };
                                $rowHighlight = match($order->status) {
                                    'pending'              => 'background-color: var(--status-danger-bg);',
                                    'confirmed','preparing'=> 'background-color: var(--status-warning-bg);',
                                    'ready'                => 'background-color: var(--status-success-bg);',
                                    default                => '',
                                };
                                $leftBorderColor = match($order->status) {
                                    'pending'              => 'border-left: 3px solid #EF4444;',
                                    'confirmed','preparing'=> 'border-left: 3px solid #F59E0B;',
                                    'ready'                => 'border-left: 3px solid #10B981;',
                                    'completed'            => 'border-left: 3px solid #6B7280;',
                                    'cancelled'            => 'border-left: 3px solid #9CA3AF;',
                                    default                => '',
                                };
                            @endphp
                            <tr
                                x-show="filterOrder('{{ $orderStatusGroup }}')"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                class="transition-all"
                                style="border-color: var(--border-default); {{ $rowHighlight }} {{ $leftBorderColor }}"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="font-mono text-xs font-bold" style="color: var(--brand);">{{ $order->order_number }}</p>
                                    <p class="text-xs mt-0.5" style="color: var(--text-muted);">
                                        {{ $order->created_at->format('d M, H:i') }} WIB
                                    </p>
                                    <p class="text-[11px] font-semibold mt-0.5"
                                       style="color: {{ $order->created_at->diffInMinutes(now()) < 5 ? 'var(--status-danger)' : 'var(--text-muted)' }};">
                                        {{ $order->created_at->diffForHumans(null, true, true) }} lalu
                                    </p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <p class="text-sm font-bold" style="color: var(--text-primary);">{{ $order->customer_name ?? '-' }}</p>
                                        @if($order->isTeacherOrder())
                                            <span class="inline-flex items-center gap-1 text-[9.5px] font-black px-1.5 py-0.5 rounded border"
                                                  style="background-color: rgba(59, 130, 246, 0.12); color: #1d4ed8; border-color: rgba(59, 130, 246, 0.3);"
                                                  title="Pesanan dari Guru / Tenaga Pendidik">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
                                                <span>GURU</span>
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs mt-1" style="color: var(--text-muted);">
                                        {{ $order->isTeacherOrder() ? 'Ruangan: ' : 'Kelas: ' }}{{ $order->customer_class ?? '-' }}
                                    </p>
                                    @if($order->isInSchool())
                                        <div class="mt-1.5">
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded border"
                                                  style="background-color: rgba(16, 185, 129, 0.1); color: #059669; border-color: rgba(16, 185, 129, 0.25);"
                                                  title="Koordinat valid di area sekolah (Radius: {{ $order->distance_meters ? $order->distance_meters . 'm' : '<500m' }})">
                                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <span>Di Sekolah{{ $order->distance_meters ? ' (' . $order->distance_meters . 'm)' : '' }}</span>
                                            </span>
                                        </div>
                                    @elseif($order->location_status === 'outside')
                                        <div class="mt-1.5">
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded border"
                                                  style="background-color: rgba(239, 68, 68, 0.1); color: #dc2626; border-color: rgba(239, 68, 68, 0.25);"
                                                  title="Terdeteksi di luar area sekolah (~{{ $order->distance_meters }}m)">
                                                <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                <span>Di Luar ({{ $order->distance_meters }}m)</span>
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm" style="color: var(--text-secondary);">
                                    <div class="space-y-1">
                                        @foreach($order->items as $item)
                                            <div class="flex items-start gap-2">
                                                <span class="font-bold text-xs" style="color: var(--text-primary);">{{ $item->quantity }}x</span>
                                                <span class="text-xs">{{ $item->product_name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($order->notes)
                                        <p class="mt-2 text-xs italic p-2 rounded-lg border"
                                           style="background-color: var(--brand-subtle); color: var(--brand); border-color: var(--brand-border);">
                                            "{{ $order->notes }}"
                                        </p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-sm font-bold" style="color: var(--text-primary);">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                                    <div class="mt-1">
                                        @if($order->payment_method === 'qris')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold border"
                                                  style="background-color: var(--brand-subtle); color: var(--brand); border-color: var(--brand-border);">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                                <span>QRIS</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold border"
                                                  style="background-color: var(--bg-surface-2); color: var(--text-secondary); border-color: var(--border-default);">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                <span>Tunai (Cash)</span>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($order->status === 'pending')
                                        <span class="status-pill status-pill-pending"><span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5 inline-block animate-pulse"></span>Menunggu</span>
                                    @elseif($order->status === 'confirmed')
                                        <div class="inline-flex flex-col items-start gap-1">
                                            <span class="status-pill status-pill-ready" style="background-color: rgba(59, 130, 246, 0.12); color: #2563EB; border-color: rgba(59, 130, 246, 0.3);">
                                                ✓ Pesanan Dikonfirmasi
                                            </span>
                                            <span class="text-[10px] font-semibold" style="color: var(--text-muted);">Siap dimasak</span>
                                        </div>
                                    @elseif($order->status === 'preparing')
                                        <span class="status-pill status-pill-processing"><span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 inline-block"></span>Dimasak</span>
                                    @elseif($order->status === 'ready')
                                        <span class="status-pill status-pill-ready"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 inline-block"></span>Siap Ambil</span>
                                    @elseif($order->status === 'cancelled')
                                        <span class="status-pill status-pill-cancelled"><span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-1.5 inline-block"></span>Dibatalkan</span>
                                        @if($order->cancel_reason)
                                            <div class="mt-1.5 p-2 rounded-lg border text-[11px] leading-tight flex items-start gap-1.5 max-w-[220px] whitespace-normal text-left shadow-2xs"
                                                 style="background-color: var(--status-danger-bg); border-color: var(--status-danger-border); color: var(--status-danger);">
                                                <svg class="w-3.5 h-3.5 shrink-0 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                <div>
                                                    <span class="font-bold block text-[10px] uppercase tracking-wider">Alasan Ditolak:</span>
                                                    <span>{{ $order->cancel_reason }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <span class="status-pill status-pill-completed"><span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-1.5 inline-block"></span>Selesai</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    @if($order->status !== 'completed' && $order->status !== 'cancelled')
                                        @php
                                            $nextStatus = '';
                                            $btnLabel = '';
                                            if ($order->status === 'pending') {
                                                $nextStatus = 'confirmed';
                                                $btnLabel = 'Konfirmasi Pesanan';
                                            } elseif ($order->status === 'confirmed') {
                                                $nextStatus = 'preparing';
                                                $btnLabel = 'Mulai Masak';
                                            } elseif ($order->status === 'preparing') {
                                                $nextStatus = 'ready';
                                                $btnLabel = 'Siap Diambil';
                                            } elseif ($order->status === 'ready') {
                                                $nextStatus = 'completed';
                                                $btnLabel = 'Selesai ✔';
                                            }
                                        @endphp
                                        <div class="flex flex-col gap-2 items-end">
                                            @if($nextStatus)
                                                <form action="{{ route('dashboard.orders.status', $order->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="{{ $nextStatus }}">
                                                    <button type="submit" class="btn-brand text-xs px-3 py-1.5 rounded-lg shadow-sm">
                                                        {{ $btnLabel }} &rarr;
                                                    </button>
                                                </form>
                                            @endif
                                            @if(in_array($order->status, ['pending', 'confirmed', 'preparing']))
                                                <button
                                                    type="button"
                                                    data-id="{{ $order->id }}"
                                                    data-order-number="{{ $order->order_number }}"
                                                    data-customer-name="{{ $order->customer_name ?? 'Pelanggan' }}"
                                                    data-customer-class="{{ $order->customer_class ?? '-' }}"
                                                    data-total="Rp {{ number_format($order->total, 0, ',', '.') }}"
                                                    data-items-count="{{ $order->items->sum('quantity') }}"
                                                    data-action-url="{{ route('dashboard.orders.status', $order->id) }}"
                                                    @click="openRejectModal($el)"
                                                    class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-lg border transition-all hover:scale-105 active:scale-95 cursor-pointer shadow-2xs"
                                                    style="background-color: var(--status-danger-bg); color: var(--status-danger); border-color: var(--status-danger-border);"
                                                    title="Tolak pesanan dan beri alasan">
                                                    <span>✕</span>
                                                    <span>Tolak</span>
                                                </button>
                                            @endif
                                        </div>
                                    @else
                                        @if($order->status === 'cancelled')
                                            <div class="inline-flex flex-col items-end">
                                                <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-lg border"
                                                      style="background-color: var(--status-danger-bg); color: var(--status-danger); border-color: var(--status-danger-border);">
                                                    <span>✕</span>
                                                    <span>Pesanan Ditolak</span>
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-xs" style="color: var(--text-muted);">—</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full border mb-3"
                                         style="background-color: var(--bg-surface-2); border-color: var(--border-default); color: var(--text-muted);">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    </div>
                                    <h3 class="text-sm font-bold" style="color: var(--text-primary);">Belum Ada Pesanan Masuk</h3>
                                    <p class="text-xs mt-1" style="color: var(--text-muted);">Pesanan dari pelanggan akan otomatis muncul di sini secara real-time.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Info baris yang tersembunyi akibat filter --}}
            <div class="px-6 py-3 border-t flex items-center justify-between text-xs"
                 style="border-color: var(--border-default); color: var(--text-muted);">
                <span x-show="orderFilter !== 'all'">
                    Menampilkan <b style="color: var(--brand);">1 filter</b> status pesanan.
                    <button @click="orderFilter = 'all'" class="ml-1 hover:underline" style="color: var(--brand);">Lihat semua &rarr;</button>
                </span>
                <span x-show="orderFilter === 'all'">Total: <b>{{ $orders->count() }}</b> pesanan.</span>
                <span class="tabular-nums">Refresh: <b x-text="refreshCountdown + 's'">15s</b></span>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         TAB KONTEN 2: DAFTAR PRODUK & MENU (UBAH STOK & UPDATE HARGA LANGSUNG)
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'products'" x-transition:enter="transition ease-out duration-150">
        
        {{-- Baris Filter & Pencarian Menu --}}
        <div class="mb-5 flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
            {{-- Search Box Menu --}}
            <div class="relative flex-1 max-w-md">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none"
                     style="color: var(--text-muted);"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    type="search"
                    x-model="productSearch"
                    placeholder="Cari menu makanan, minuman, tenant..."
                    class="w-full pl-10 pr-4 py-2 text-xs sm:text-sm rounded-xl border outline-none transition-colors"
                    style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);"
                    aria-label="Cari menu kantin"
                >
            </div>

            {{-- Filter Kategori & Status Stok --}}
            <div class="flex items-center gap-1.5 flex-wrap">
                <button
                    type="button"
                    @click="productFilter = 'all'"
                    class="px-3 py-1.5 text-xs font-bold rounded-lg border transition-all"
                    :class="productFilter === 'all' ? 'category-tab-active' : 'btn-secondary'"
                >
                    Semua ({{ $products->count() }})
                </button>

                <button
                    type="button"
                    @click="productFilter = 'out_of_stock'"
                    class="px-3 py-1.5 text-xs font-bold rounded-lg border transition-all flex items-center gap-1.5"
                    :class="productFilter === 'out_of_stock' ? 'category-tab-active' : 'btn-secondary'"
                    :style="productFilter !== 'out_of_stock' && {{ $outOfStockCount }} > 0 ? 'border-color: var(--status-danger); color: var(--status-danger);' : ''"
                >
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                    <span>Stok Habis</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black"
                          :style="productFilter === 'out_of_stock' ? 'background: #fff; color: #000;' : 'background: var(--status-danger); color: #fff;'">
                        {{ $outOfStockCount }}
                    </span>
                </button>

                <button
                    type="button"
                    @click="productFilter = 'low_stock'"
                    class="px-3 py-1.5 text-xs font-bold rounded-lg border transition-all flex items-center gap-1.5"
                    :class="productFilter === 'low_stock' ? 'category-tab-active' : 'btn-secondary'"
                >
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    <span>Menipis ({{ $lowStockCount }})</span>
                </button>

                <button
                    type="button"
                    @click="productFilter = 'makanan_berat'"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all"
                    :class="productFilter === 'makanan_berat' ? 'category-tab-active' : 'btn-secondary'"
                >
                    Makanan Berat
                </button>

                <button
                    type="button"
                    @click="productFilter = 'minuman'"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all"
                    :class="productFilter === 'minuman' ? 'category-tab-active' : 'btn-secondary'"
                >
                    Minuman
                </button>

                <button
                    type="button"
                    @click="productFilter = 'makanan_ringan'"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all"
                    :class="productFilter === 'makanan_ringan' ? 'category-tab-active' : 'btn-secondary'"
                >
                    Camilan
                </button>
            </div>
        </div>

        {{-- Tabel Produk & Menu --}}
        <div class="rounded-2xl border shadow-sm overflow-hidden"
             style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
            
            <div class="px-6 py-4 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
                 style="border-color: var(--border-default);">
                <div>
                    <h2 class="font-bold text-base tracking-tight" style="color: var(--text-primary);">
                        Katalog Menu & Pengaturan Stok
                    </h2>
                    <p class="text-xs" style="color: var(--text-muted);">
                        Anda dapat mengubah stok yang kosong atau mengupdate harga langsung di kolom tabel bawah ini.
                    </p>
                </div>

                <button
                    type="button"
                    @click="showCreateModal = true"
                    class="btn-brand text-xs font-bold py-2 px-3.5 rounded-xl self-start sm:self-auto flex items-center gap-1.5"
                >
                    <span>+ Tambah Menu Baru</span>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b text-xs font-bold uppercase tracking-wider"
                            style="background-color: var(--bg-surface-2); border-color: var(--border-default); color: var(--text-secondary);">
                            <th class="px-6 py-3.5">Menu / Produk</th>
                            <th class="px-6 py-3.5">Kategori</th>
                            <th class="px-6 py-3.5">Harga Satuan (Rp)</th>
                            <th class="px-6 py-3.5">Stok Porsi</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--border-default);">
                        @forelse($products as $product)
                            <tr
                                x-show="filterProduct({
                                    id: {{ $product->id }},
                                    name: '{{ addslashes($product->name) }}',
                                    tenant: '{{ addslashes($product->tenant->name ?? '') }}',
                                    category: '{{ $product->category }}',
                                    stock: {{ $product->stock }},
                                    price: {{ $product->price }}
                                })"
                                class="transition-colors hover:opacity-95"
                                style="border-color: var(--border-default);"
                                id="row-product-{{ $product->id }}"
                            >
                                {{-- Kolom 1: Info Produk & Gambar --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3.5">
                                        @if($product->image)
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                                 class="w-12 h-12 rounded-xl object-cover border shrink-0"
                                                 style="border-color: var(--border-default);">
                                        @else
                                            <div class="w-12 h-12 rounded-xl border flex items-center justify-center shrink-0"
                                                 style="background-color: var(--bg-surface-2); border-color: var(--border-default); color: var(--text-muted);">
                                                <svg class="w-5 h-5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <div class="font-extrabold text-sm truncate" style="color: var(--text-primary);">
                                                {{ $product->name }}
                                            </div>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-xs font-semibold" style="color: var(--brand);">
                                                    {{ $product->category == 'minuman' ? 'Minuman' : ($product->category == 'makanan_berat' ? 'Makanan Utama' : ($product->category == 'dessert' ? 'Dessert' : 'Camilan')) }}
                                                </span>
                                                @if($product->is_featured)
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-extrabold px-1.5 py-0.2 rounded-full uppercase"
                                                          style="background: var(--brand-subtle); color: var(--brand); border: 1px solid var(--brand-border);">
                                                        <svg class="w-2.5 h-2.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                        <span>Favorit</span>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kolom 2: Kategori --}}
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-medium" style="color: var(--text-secondary);">
                                    <span class="px-2.5 py-1 rounded-lg border"
                                          style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                                        {{ $product->category_label }}
                                    </span>
                                </td>

                                {{-- Kolom 3: Update Harga Langsung --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('dashboard.products.quick-update', $product->id) }}" method="POST"
                                          class="flex items-center gap-1.5"
                                          @submit.prevent="submitQuickUpdate($event)">
                                        @csrf
                                        @method('PATCH')
                                        <div class="relative flex items-center">
                                            <span class="absolute left-2.5 text-xs font-bold" style="color: var(--text-muted);">Rp</span>
                                            <input
                                                type="number"
                                                name="price"
                                                value="{{ (int) $product->price }}"
                                                min="0"
                                                step="500"
                                                required
                                                class="w-28 pl-8 pr-2 py-1.5 text-xs font-extrabold rounded-lg border outline-none focus:ring-1"
                                                style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                                                title="Ubah harga produk ini"
                                            >
                                        </div>
                                        <button
                                            type="submit"
                                            class="btn-secondary text-[11px] font-bold px-2 py-1.5 rounded-lg border transition-all"
                                            title="Simpan harga baru"
                                        >
                                            Update
                                        </button>
                                    </form>
                                </td>

                                {{-- Kolom 4: Ubah Stok yang Kosong Langsung --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('dashboard.products.quick-update', $product->id) }}" method="POST"
                                          class="flex items-center gap-1.5"
                                          @submit.prevent="submitQuickUpdate($event)">
                                        @csrf
                                        @method('PATCH')
                                        
                                        <div class="flex items-center">
                                            <input
                                                type="number"
                                                name="stock"
                                                value="{{ $product->stock }}"
                                                min="0"
                                                required
                                                class="w-20 px-2.5 py-1.5 text-xs font-black text-center rounded-lg border outline-none focus:ring-1"
                                                :style="'{{ $product->stock }}' <= 0 
                                                    ? 'background-color: var(--status-danger-bg); color: var(--status-danger); border-color: var(--status-danger-border);' 
                                                    : 'background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);'"
                                                title="Ubah jumlah stok tersedia"
                                            >
                                        </div>

                                        <button
                                            type="submit"
                                            class="text-[11px] font-bold px-2.5 py-1.5 rounded-lg border transition-all"
                                            :class="'{{ $product->stock }}' <= 0 ? 'btn-brand shadow-sm' : 'btn-secondary'"
                                            title="Simpan stok baru"
                                        >
                                            @if($product->stock <= 0)
                                                Isi Stok!
                                            @else
                                                Simpan
                                            @endif
                                        </button>
                                    </form>

                                    @if($product->stock <= 0)
                                        <span class="inline-block mt-1 text-[10px] font-extrabold uppercase tracking-wider" style="color: var(--status-danger);">
                                            ● Stok Kosong
                                        </span>
                                    @elseif($product->stock <= 5)
                                        <span class="inline-block mt-1 text-[10px] font-bold uppercase tracking-wider" style="color: var(--status-warning);">
                                            ● Menipis ({{ $product->stock }})
                                        </span>
                                    @endif
                                </td>

                                {{-- Kolom 5: Status Ketersediaan --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($product->stock <= 0)
                                        <span class="status-pill status-pill-cancelled">Habis (0)</span>
                                    @elseif($product->is_available)
                                        <span class="status-pill status-pill-ready">Tersedia</span>
                                    @else
                                        <span class="status-pill status-pill-completed">Nonaktif</span>
                                    @endif
                                </td>

                                {{-- Kolom 6: Aksi Edit Lengkap & Hapus --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2.5 text-xs font-semibold">
                                        {{-- Tombol Edit Lengkap (buka modal quick edit / ke halaman edit) --}}
                                        <button
                                            type="button"
                                            @click="openQuickEdit({
                                                id: {{ $product->id }},
                                                name: '{{ addslashes($product->name) }}',
                                                price: {{ $product->price }},
                                                stock: {{ $product->stock }},
                                                is_available: {{ $product->is_available ? 'true' : 'false' }}
                                            })"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border btn-secondary transition-opacity hover:opacity-80"
                                            style="color: var(--brand);"
                                            title="Edit lengkap harga, stok & status"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            <span>Edit</span>
                                        </button>

                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                              class="inline-block"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu {{ addslashes($product->name) }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="from_dashboard" value="1">
                                            <button type="submit"
                                                    class="p-1.5 rounded-lg transition-colors hover:opacity-80"
                                                    style="color: var(--status-danger);"
                                                    title="Hapus Menu">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full border mb-3"
                                         style="background-color: var(--bg-surface-2); border-color: var(--border-default); color: var(--text-muted);">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    </div>
                                    <h3 class="text-sm font-bold" style="color: var(--text-primary);">Belum Ada Produk di Menu Kantin</h3>
                                    <p class="text-xs mt-1 mb-4" style="color: var(--text-muted);">Mulai tambahkan hidangan makanan dan minuman pertama Anda.</p>
                                    <button
                                        type="button"
                                        @click="showCreateModal = true"
                                        class="btn-brand text-xs font-bold px-4 py-2 rounded-xl"
                                    >
                                        + Tambah Menu Sekarang
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Info Jumlah Menu yang Tampil --}}
            <div class="px-6 py-3.5 border-t flex items-center justify-between text-xs"
                 style="border-color: var(--border-default); color: var(--text-muted);">
                <span>Total: <b>{{ $products->count() }}</b> menu terdaftar di kantin.</span>
                <span>Klik <b>Update/Simpan</b> untuk menerapkan perubahan seketika.</span>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         TAB KONTEN 3: DAFTAR BAHAN BAKU & PERSEDIAAN DAPUR
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'ingredients'" x-transition:enter="transition ease-out duration-150">
        <div class="rounded-2xl border shadow-sm overflow-hidden"
             style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">

            {{-- Header Tabel & Tombol Aksi --}}
            <div class="px-6 py-4 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
                 style="border-color: var(--border-default);">
                <div>
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background: rgba(249, 115, 22, 0.1); color: var(--brand);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </span>
                        <h2 class="font-bold text-base tracking-tight" style="color: var(--text-primary);">
                            Daftar Bahan Baku & Persediaan Dapur
                        </h2>
                    </div>
                    <p class="text-xs mt-0.5" style="color: var(--text-muted);">
                        Catat stok bahan dapur, sembako, bumbu masakan, sayuran, dan persediaan kantin harian.
                    </p>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <button
                        type="button"
                        @click="showCreateIngredientModal = true"
                        class="btn-brand text-xs font-bold py-2 px-3.5 rounded-xl flex items-center gap-1.5 cursor-pointer shadow-sm"
                    >
                        <span>+ Tambah Bahan Baru</span>
                    </button>

                    <a
                        href="{{ route('dashboard.stock.index') }}"
                        class="btn-secondary text-xs font-semibold py-2 px-3.5 rounded-xl border flex items-center gap-1.5 transition-colors"
                        title="Buka halaman penuh manajemen inventaris dapur & produk"
                    >
                        <span>↗ Halaman Penuh Stok</span>
                    </a>
                </div>
            </div>

            {{-- Filter & Kolom Pencarian --}}
            <div class="px-6 py-3.5 border-b space-y-3"
                 style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none" style="color: var(--text-muted);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input
                        type="text"
                        x-model="ingredientSearch"
                        placeholder="Cari bahan baku (beras, ayam, minyak, telur, sayur, cup, gas...)..."
                        class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border outline-none"
                        style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);"
                    >
                </div>

                {{-- Filter Chips Kategori --}}
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none">
                    <button
                        type="button"
                        @click="ingredientFilter = 'all'"
                        class="px-3 py-1.5 text-xs font-bold rounded-lg border transition-all whitespace-nowrap cursor-pointer"
                        :class="ingredientFilter === 'all' ? 'category-tab-active' : 'btn-secondary'"
                    >
                        Semua ({{ $ingredients->count() }})
                    </button>

                    <button
                        type="button"
                        @click="ingredientFilter = 'need_restock'"
                        class="px-3 py-1.5 text-xs font-bold rounded-lg border transition-all flex items-center gap-1.5 whitespace-nowrap cursor-pointer"
                        :class="ingredientFilter === 'need_restock' ? 'category-tab-active' : 'btn-secondary'"
                        :style="ingredientFilter !== 'need_restock' && {{ ($outOfStockIngredientsCount + $lowStockIngredientsCount) }} > 0 ? 'border-color: var(--status-warning); color: var(--status-warning);' : ''"
                    >
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        <span>Perlu Belanja</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black"
                              :style="ingredientFilter === 'need_restock' ? 'background: #fff; color: #000;' : 'background: var(--status-warning); color: #fff;'">
                            {{ $outOfStockIngredientsCount + $lowStockIngredientsCount }}
                        </span>
                    </button>

                    <button
                        type="button"
                        @click="ingredientFilter = 'bahan_pokok'"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all whitespace-nowrap cursor-pointer"
                        :class="ingredientFilter === 'bahan_pokok' ? 'category-tab-active' : 'btn-secondary'"
                    >
                        Bahan Pokok
                    </button>

                    <button
                        type="button"
                        @click="ingredientFilter = 'daging_telur'"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all whitespace-nowrap cursor-pointer"
                        :class="ingredientFilter === 'daging_telur' ? 'category-tab-active' : 'btn-secondary'"
                    >
                        Daging & Telur
                    </button>

                    <button
                        type="button"
                        @click="ingredientFilter = 'bumbu_dapur'"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all whitespace-nowrap cursor-pointer"
                        :class="ingredientFilter === 'bumbu_dapur' ? 'category-tab-active' : 'btn-secondary'"
                    >
                        Bumbu Dapur
                    </button>

                    <button
                        type="button"
                        @click="ingredientFilter = 'sayuran'"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all whitespace-nowrap cursor-pointer"
                        :class="ingredientFilter === 'sayuran' ? 'category-tab-active' : 'btn-secondary'"
                    >
                        Sayuran Segar
                    </button>

                    <button
                        type="button"
                        @click="ingredientFilter = 'minuman'"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all whitespace-nowrap cursor-pointer"
                        :class="ingredientFilter === 'minuman' ? 'category-tab-active' : 'btn-secondary'"
                    >
                        Bahan Minuman
                    </button>

                    <button
                        type="button"
                        @click="ingredientFilter = 'kemasan'"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all whitespace-nowrap cursor-pointer"
                        :class="ingredientFilter === 'kemasan' ? 'category-tab-active' : 'btn-secondary'"
                    >
                        Kemasan & Cup
                    </button>

                    <button
                        type="button"
                        @click="ingredientFilter = 'lainnya'"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all whitespace-nowrap cursor-pointer"
                        :class="ingredientFilter === 'lainnya' ? 'category-tab-active' : 'btn-secondary'"
                    >
                        Persediaan & Gas
                    </button>
                </div>
            </div>

            {{-- Tabel Bahan Baku --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b text-xs font-bold uppercase tracking-wider"
                            style="background-color: var(--bg-surface-2); border-color: var(--border-default); color: var(--text-secondary);">
                            <th class="px-6 py-3.5">Nama Bahan</th>
                            <th class="px-6 py-3.5">Kategori</th>
                            <th class="px-6 py-3.5">Stok Saat Ini</th>
                            <th class="px-6 py-3.5">Batas Minimum</th>
                            <th class="px-6 py-3.5">Status Bahan</th>
                            <th class="px-6 py-3.5 text-right">Aksi Cepat Restock</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--border-default);">
                        @forelse($ingredients as $ing)
                            <tr
                                x-show="filterIngredient({
                                    id: {{ $ing->id }},
                                    name: '{{ addslashes($ing->name) }}',
                                    category: '{{ $ing->category }}',
                                    stock: {{ $ing->stock }},
                                    min_stock: {{ $ing->min_stock }},
                                    is_out_of_stock: {{ $ing->is_out_of_stock ? 'true' : 'false' }},
                                    is_low_stock: {{ $ing->is_low_stock ? 'true' : 'false' }}
                                })"
                                class="transition-colors hover:opacity-95"
                                style="border-color: var(--border-default);"
                                id="row-dash-ingredient-{{ $ing->id }}"
                            >
                                {{-- Nama Bahan & Info --}}
                                <td class="px-6 py-4">
                                    <div class="font-extrabold text-sm" style="color: var(--text-primary);">
                                        {{ $ing->name }}
                                    </div>
                                    <div class="flex items-center gap-2 mt-0.5 text-xs" style="color: var(--text-muted);">
                                        <span>Tenant: {{ $ing->tenant->name ?? 'Semua' }}</span>
                                        @if($ing->supplier)
                                            <span>• Pasar: {{ $ing->supplier }}</span>
                                        @endif
                                    </div>
                                    @if($ing->notes)
                                        <div class="text-[11px] mt-1 italic" style="color: var(--brand);">
                                            {{ $ing->notes }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Kategori --}}
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold" style="color: var(--text-secondary);">
                                    <span class="px-2.5 py-1 rounded-lg border"
                                          style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                                        {{ $ing->category_label }}
                                    </span>
                                </td>

                                {{-- Stok Saat Ini --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-black tracking-tight"
                                         style="color: {{ $ing->is_out_of_stock ? 'var(--status-danger)' : ($ing->is_low_stock ? 'var(--status-warning)' : 'var(--text-primary)') }};">
                                        {{ $ing->formatted_stock }}
                                    </div>
                                </td>

                                {{-- Batas Minimum --}}
                                <td class="px-6 py-4 whitespace-nowrap text-xs" style="color: var(--text-muted);">
                                    {{ $ing->min_stock }} {{ $ing->unit }}
                                </td>

                                {{-- Status Bahan --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($ing->is_out_of_stock)
                                        <span class="status-pill status-pill-cancelled animate-pulse">Habis (0)</span>
                                    @elseif($ing->is_low_stock)
                                        <span class="status-pill" style="background-color: var(--status-warning-bg); color: var(--status-warning);">Menipis</span>
                                    @else
                                        <span class="status-pill status-pill-ready">Aman</span>
                                    @endif
                                </td>

                                {{-- Aksi Restock / Pakai --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        {{-- Tombol + Masuk --}}
                                        <button
                                            type="button"
                                            @click="openAdjustIngredient({
                                                id: {{ $ing->id }},
                                                name: '{{ addslashes($ing->name) }}',
                                                stock: {{ $ing->stock }},
                                                unit: '{{ $ing->unit }}'
                                            }, 'in')"
                                            class="px-2.5 py-1 text-xs font-bold rounded-lg border transition-all hover:scale-105 cursor-pointer"
                                            style="background-color: var(--status-success-bg); color: var(--status-success); border-color: var(--status-success-border);"
                                            title="Catat barang belanjaan baru masuk"
                                        >
                                            + Masuk
                                        </button>

                                        {{-- Tombol - Pakai --}}
                                        <button
                                            type="button"
                                            @click="openAdjustIngredient({
                                                id: {{ $ing->id }},
                                                name: '{{ addslashes($ing->name) }}',
                                                stock: {{ $ing->stock }},
                                                unit: '{{ $ing->unit }}'
                                            }, 'out')"
                                            class="px-2.5 py-1 text-xs font-bold rounded-lg border transition-all hover:scale-105 cursor-pointer"
                                            style="background-color: var(--bg-surface-2); color: var(--text-secondary); border-color: var(--border-default);"
                                            title="Catat pemakaian untuk memasak"
                                        >
                                            - Pakai
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12">
                                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full border mb-3"
                                         style="background-color: var(--bg-surface-2); border-color: var(--border-default); color: var(--text-muted);">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    </div>
                                    <h3 class="text-sm font-bold" style="color: var(--text-primary);">Belum Ada Bahan Baku Tercatat</h3>
                                    <p class="text-xs mt-1 mb-4" style="color: var(--text-muted);">Mulai catat bahan baku masakan dan persediaan kantin Anda.</p>
                                    <button
                                        type="button"
                                        @click="showCreateIngredientModal = true"
                                        class="btn-brand text-xs font-bold py-2 px-4 rounded-xl shadow-md cursor-pointer"
                                    >
                                        + Tambah Bahan Baku Sekarang
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer Info --}}
            <div class="px-6 py-3.5 border-t flex flex-col sm:flex-row items-center justify-between gap-2 text-xs"
                 style="border-color: var(--border-default); color: var(--text-muted);">
                <span>Total <b>{{ $ingredients->count() }}</b> jenis bahan baku & persediaan dapur tercatat.</span>
                <span>Klik <b>+ Masuk</b> untuk restock belanjaan atau <b>- Pakai</b> untuk catat masak.</span>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL: TAMBAH PRODUK BARU (LANGSUNG DARI DASHBOARD)
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div
        x-show="showCreateModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
    >
        {{-- Backdrop --}}
        <div
            x-show="showCreateModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="showCreateModal = false"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
        ></div>

        {{-- Modal Dialog --}}
        <div
            x-show="showCreateModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-2"
            class="relative w-full max-w-xl rounded-2xl border p-6 sm:p-7 shadow-2xl z-10 overflow-hidden"
            style="background-color: var(--bg-surface); border-color: var(--border-default);"
        >
            <div class="flex items-center justify-between pb-4 border-b mb-5" style="border-color: var(--border-default);">
                <div>
                    <h3 class="text-lg font-bold" style="color: var(--text-primary);">Tambah Produk / Menu Baru</h3>
                    <p class="text-xs" style="color: var(--text-muted);">Menu baru akan langsung muncul di katalog pembeli.</p>
                </div>
                <button
                    type="button"
                    @click="showCreateModal = false"
                    class="p-2 rounded-lg transition-colors hover:bg-stone-200/50"
                    style="color: var(--text-secondary);"
                >
                    &times;
                </button>
            </div>

            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="from_dashboard" value="1">

                <div>
                    <label for="modal-name" class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                        Nama Menu Makanan / Minuman <span style="color: var(--status-danger);">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="modal-name"
                        required
                        placeholder="Contoh: Ayam Geprek Sambal Matah"
                        class="w-full rounded-xl px-3.5 py-2.5 text-xs sm:text-sm border outline-none"
                        style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                    >
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label for="modal-category" class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                            Kategori <span style="color: var(--status-danger);">*</span>
                        </label>
                        <select
                            name="category"
                            id="modal-category"
                            required
                            class="w-full rounded-xl px-3.5 py-2.5 text-xs sm:text-sm border outline-none"
                            style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                        >
                            <option value="makanan_berat">Makanan Berat</option>
                            <option value="makanan_ringan">Makanan Ringan (Camilan)</option>
                            <option value="minuman">Minuman Segar</option>
                            <option value="dessert">Dessert / Penutup</option>
                        </select>
                    </div>

                    @if(auth()->user()->isAdmin() && isset($tenants) && $tenants->count() > 1)
                    <div>
                        <label for="modal-tenant" class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                            Pilih Tenant Kantin <span style="color: var(--status-danger);">*</span>
                        </label>
                        <select
                            name="tenant_id"
                            id="modal-tenant"
                            class="w-full rounded-xl px-3.5 py-2.5 text-xs sm:text-sm border outline-none"
                            style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                        >
                            @foreach($tenants as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-3.5">
                    <div>
                        <label for="modal-price" class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                            Harga Satuan (Rp) <span style="color: var(--status-danger);">*</span>
                        </label>
                        <input
                            type="number"
                            name="price"
                            id="modal-price"
                            min="0"
                            step="500"
                            required
                            placeholder="15000"
                            class="w-full rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-bold border outline-none"
                            style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                        >
                    </div>

                    <div>
                        <label for="modal-stock" class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                            Stok Porsi Awal <span style="color: var(--status-danger);">*</span>
                        </label>
                        <input
                            type="number"
                            name="stock"
                            id="modal-stock"
                            min="0"
                            required
                            value="20"
                            class="w-full rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-bold border outline-none"
                            style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                        >
                    </div>
                </div>

                <div>
                    <label for="modal-image" class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                        Foto Makanan (Opsional)
                    </label>
                    <input
                        type="file"
                        name="image"
                        id="modal-image"
                        accept="image/*"
                        class="w-full rounded-xl px-3 py-2 text-xs border outline-none"
                        style="background-color: var(--bg-surface-2); color: var(--text-secondary); border-color: var(--border-default);"
                    >
                </div>

                <div>
                    <label for="modal-description" class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                        Deskripsi Singkat (Opsional)
                    </label>
                    <textarea
                        name="description"
                        id="modal-description"
                        rows="2"
                        placeholder="Rasa gurih pedas, porsi kenyang lengkap sambal dan lalapan..."
                        class="w-full rounded-xl px-3.5 py-2 text-xs sm:text-sm border outline-none resize-none"
                        style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                    ></textarea>
                </div>

                <div class="flex items-center gap-6 pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_available" value="1" checked style="accent-color: var(--brand);" class="rounded w-4 h-4">
                        <span class="text-xs font-semibold" style="color: var(--text-primary);">Langsung Tersedia (Bisa Dipesan)</span>
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" style="accent-color: var(--brand);" class="rounded w-4 h-4">
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold" style="color: var(--text-primary);">
                            <svg class="w-3.5 h-3.5 fill-amber-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <span>Menu Unggulan</span>
                        </span>
                    </label>
                </div>

                <div class="pt-5 border-t flex justify-end gap-3" style="border-color: var(--border-default);">
                    <button
                        type="button"
                        @click="showCreateModal = false"
                        class="btn-secondary px-4 py-2 text-xs font-semibold rounded-xl"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="btn-brand px-5 py-2 text-xs font-extrabold rounded-xl shadow-md"
                    >
                        Simpan & Tambahkan ke Menu
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL: EDIT LENGKAP PRODUK (Nama, Harga, Stok, Kategori, Deskripsi, Gambar, Status)
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div
        x-show="showQuickEditModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4"
        role="dialog"
        aria-modal="true"
    >
        <div
            x-show="showQuickEditModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="showQuickEditModal = false"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm"
        ></div>

        <div
            x-show="showQuickEditModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-2"
            class="relative w-full max-w-lg rounded-2xl border shadow-2xl z-10 overflow-hidden"
            style="background-color: var(--bg-surface); border-color: var(--border-default);"
        >
            {{-- Header Modal Edit --}}
            <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color: var(--border-default); background-color: var(--bg-surface-2);">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl flex items-center justify-center" style="background: var(--brand-subtle); color: var(--brand); border: 1px solid var(--brand-border);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </span>
                    <div>
                        <h3 class="font-extrabold text-sm tracking-tight" style="color: var(--text-primary);">Edit Menu Lengkap</h3>
                        <p class="text-[11px] truncate max-w-[200px] sm:max-w-xs" style="color: var(--brand);" x-text="editingProduct.name"></p>
                    </div>
                </div>
                <button type="button" @click="showQuickEditModal = false"
                        class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-stone-500/20 transition-colors cursor-pointer"
                        style="color: var(--text-secondary);">✕</button>
            </div>

            {{-- Body Modal Edit (scrollable) --}}
            <div class="max-h-[75vh] overflow-y-auto">
                <form :action="'{{ url('/dashboard/products') }}/' + editingProduct.id + '/quick-update'" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="from_dashboard" value="1">

                    {{-- Nama Menu --}}
                    <div>
                        <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">Nama Menu <span style="color: var(--status-danger);">*</span></label>
                        <input type="text" name="name" x-model="editingProduct.name" required
                               placeholder="Contoh: Ayam Geprek Sambal Matah"
                               class="w-full rounded-xl px-3.5 py-2 text-sm border outline-none transition-colors"
                               style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                    </div>

                    {{-- Kategori + Harga --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">Kategori</label>
                            <select name="category" x-model="editingProduct.category"
                                    class="w-full rounded-xl px-3 py-2 text-xs border outline-none"
                                    style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                                <option value="makanan_berat">Makanan Berat</option>
                                <option value="makanan_ringan">Makanan Ringan</option>
                                <option value="minuman">Minuman</option>
                                <option value="dessert">Dessert</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">Harga (Rp) <span style="color: var(--status-danger);">*</span></label>
                            <input type="number" name="price" x-model="editingProduct.price" min="0" step="500" required
                                   class="w-full rounded-xl px-3 py-2 text-sm font-bold border outline-none"
                                   style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                        </div>
                    </div>

                    {{-- Stok --}}
                    <div>
                        <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">Stok Porsi</label>
                        <input type="number" name="stock" x-model="editingProduct.stock" min="0" required
                               class="w-full rounded-xl px-3 py-2 text-sm font-black border outline-none"
                               style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                        <p class="text-[11px] mt-1" style="color: var(--text-muted);">Stok 0 = menu otomatis dinonaktifkan.</p>
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">Deskripsi <span class="font-normal opacity-70">(opsional)</span></label>
                        <textarea name="description" x-model="editingProduct.description" rows="2"
                                  placeholder="Rasa, porsi, bahan utama, dll..."
                                  class="w-full rounded-xl px-3 py-2 text-xs border outline-none resize-none"
                                  style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"></textarea>
                    </div>

                    {{-- Foto Baru --}}
                    <div>
                        <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">Ganti Foto <span class="font-normal opacity-70">(biarkan kosong jika tidak diganti)</span></label>
                        <input type="file" name="image" accept="image/*"
                               class="w-full rounded-xl px-3 py-2 text-xs border outline-none"
                               style="background-color: var(--bg-surface-2); color: var(--text-secondary); border-color: var(--border-default);">
                    </div>

                    {{-- Toggle Status --}}
                    <div class="flex items-center gap-6 pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_available" value="1" x-model="editingProduct.is_available"
                                   style="accent-color: var(--brand);" class="rounded w-4 h-4">
                            <span class="text-xs font-semibold" style="color: var(--text-primary);">Tersedia (bisa dipesan)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" x-model="editingProduct.is_featured"
                                   style="accent-color: var(--brand);" class="rounded w-4 h-4">
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold" style="color: var(--text-primary);">
                                <svg class="w-3.5 h-3.5 fill-amber-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span>Menu Unggulan</span>
                            </span>
                        </label>
                    </div>

                    {{-- Footer Aksi --}}
                    <div class="pt-4 border-t flex justify-end gap-2.5" style="border-color: var(--border-default);">
                        <button type="button" @click="showQuickEditModal = false"
                                class="btn-secondary px-4 py-2 text-xs font-medium rounded-xl cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                class="btn-brand px-5 py-2 text-xs font-bold rounded-xl shadow-md cursor-pointer flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL: TAMBAH BAHAN BAKU BARU (LANGSUNG DARI DASHBOARD)
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div
        x-show="showCreateIngredientModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
        role="dialog"
    >
        <div x-show="showCreateIngredientModal" @click="showCreateIngredientModal = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div
            x-show="showCreateIngredientModal"
            class="relative w-full max-w-lg rounded-2xl border p-6 sm:p-7 shadow-2xl z-10"
            style="background-color: var(--bg-surface); border-color: var(--border-default);"
        >
            <div class="flex items-center justify-between pb-3 border-b mb-4" style="border-color: var(--border-default);">
                <div>
                    <h3 class="text-base font-bold" style="color: var(--text-primary);">Tambah Bahan Baku Dapur Baru</h3>
                    <p class="text-xs" style="color: var(--text-muted);">Catat bahan persediaan dapur kantin untuk memudahkan kontrol stok.</p>
                </div>
                <button type="button" @click="showCreateIngredientModal = false" class="text-lg font-bold cursor-pointer">&times;</button>
            </div>

            <form action="{{ route('dashboard.ingredients.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="redirect_to" value="dashboard.index">

                <div>
                    <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                        Nama Bahan Baku <span style="color: var(--status-danger);">*</span>
                    </label>
                    <input type="text" name="name" required placeholder="Contoh: Beras Pulen, Daging Ayam, Minyak Goreng"
                           class="w-full rounded-xl px-3.5 py-2 text-xs sm:text-sm border outline-none"
                           style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                            Kategori Bahan <span style="color: var(--status-danger);">*</span>
                        </label>
                        <select name="category" required class="w-full rounded-xl px-3 py-2 text-xs border outline-none"
                                style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                            <option value="bahan_pokok">Bahan Pokok (Beras, Minyak, Gula)</option>
                            <option value="daging_telur">Daging & Telur</option>
                            <option value="bumbu_dapur">Bumbu Dapur (Cabai, Bawang, Garam)</option>
                            <option value="sayuran">Sayuran Segar</option>
                            <option value="minuman">Bahan Minuman (Teh, Kopi, Sirup)</option>
                            <option value="kemasan">Kemasan & Cup (Dus, Plastik, Sedotan)</option>
                            <option value="lainnya">Persediaan & Gas</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                            Satuan Ukur <span style="color: var(--status-danger);">*</span>
                        </label>
                        <select name="unit" required class="w-full rounded-xl px-3 py-2 text-xs border outline-none"
                                style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                            <option value="kg">kg (Kilogram)</option>
                            <option value="gram">gram</option>
                            <option value="liter">liter</option>
                            <option value="ml">ml (Mililiter)</option>
                            <option value="butir">butir</option>
                            <option value="pcs">pcs / biji</option>
                            <option value="pack">pack / bungkus</option>
                            <option value="ikat">ikat</option>
                            <option value="dus">dus / karton</option>
                            <option value="tabung">tabung</option>
                            <option value="pouch">pouch</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                            Stok Saat Ini <span style="color: var(--status-danger);">*</span>
                        </label>
                        <input type="number" name="stock" step="0.1" min="0" required value="10"
                               class="w-full rounded-xl px-3 py-2 text-xs font-bold border outline-none"
                               style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                            Batas Minimum (Peringatan) <span style="color: var(--status-danger);">*</span>
                        </label>
                        <input type="number" name="min_stock" step="0.1" min="0" required value="5"
                               class="w-full rounded-xl px-3 py-2 text-xs font-bold border outline-none"
                               style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                            Estimasi Modal / Satuan (Rp)
                        </label>
                        <input type="number" name="cost_per_unit" step="500" min="0" placeholder="Contoh: 15000"
                               class="w-full rounded-xl px-3 py-2 text-xs border outline-none"
                               style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                            Suplier / Tempat Beli (Opsional)
                        </label>
                        <input type="text" name="supplier" placeholder="Contoh: Pasar Induk, Toko Sembako"
                               class="w-full rounded-xl px-3 py-2 text-xs border outline-none"
                               style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                        Catatan Penggunaan (Opsional)
                    </label>
                    <input type="text" name="notes" placeholder="Contoh: Untuk bumbu ayam geprek & suwir soto"
                           class="w-full rounded-xl px-3 py-2 text-xs border outline-none"
                           style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                </div>

                <div class="pt-4 border-t flex justify-end gap-2.5" style="border-color: var(--border-default);">
                    <button type="button" @click="showCreateIngredientModal = false" class="btn-secondary px-4 py-2 text-xs font-semibold rounded-xl cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="btn-brand px-5 py-2 text-xs font-extrabold rounded-xl shadow-md cursor-pointer">
                        Simpan Bahan Baku
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL: RESTOCK / PENYESUAIAN STOK BAHAN CEPAT (+ MASUK / - PAKAI)
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div
        x-show="showAdjustIngredientModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
        role="dialog"
    >
        <div x-show="showAdjustIngredientModal" @click="showAdjustIngredientModal = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div
            x-show="showAdjustIngredientModal"
            class="relative w-full max-w-sm rounded-2xl border p-6 shadow-2xl z-10"
            style="background-color: var(--bg-surface); border-color: var(--border-default);"
        >
            <div class="flex items-center justify-between pb-3 border-b mb-4" style="border-color: var(--border-default);">
                <div>
                    <h3 class="text-base font-bold" style="color: var(--text-primary);"
                        x-text="adjustIngredientType === 'in' ? '+ Restock Belanjaan Masuk' : '- Catat Pemakaian Bahan'"></h3>
                    <p class="text-xs truncate font-bold" style="color: var(--brand);" x-text="adjustIngredientItem.name"></p>
                </div>
                <button type="button" @click="showAdjustIngredientModal = false" class="text-lg font-bold cursor-pointer">&times;</button>
            </div>

            <form :action="'{{ url('/dashboard/ingredients') }}/' + adjustIngredientItem.id + '/adjust'" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="redirect_to" value="dashboard.index">
                <input type="hidden" name="type" :value="adjustIngredientType">

                <div class="p-3 rounded-xl border text-xs" style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                    <div class="flex justify-between">
                        <span style="color: var(--text-muted);">Stok Saat Ini:</span>
                        <span class="font-extrabold" style="color: var(--text-primary);" x-text="adjustIngredientItem.stock + ' ' + adjustIngredientItem.unit"></span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);"
                           x-text="adjustIngredientType === 'in' ? 'Jumlah Belanjaan Masuk (' + adjustIngredientItem.unit + ')' : 'Jumlah Terpakai Memasak (' + adjustIngredientItem.unit + ')'">
                    </label>
                    <input type="number" name="amount" step="0.1" min="0.01" required placeholder="Contoh: 5"
                           class="w-full rounded-xl px-3.5 py-2 text-sm font-black border outline-none"
                           style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                </div>

                <div class="pt-4 border-t flex justify-end gap-2.5" style="border-color: var(--border-default);">
                    <button type="button" @click="showAdjustIngredientModal = false" class="btn-secondary px-4 py-2 text-xs font-semibold rounded-xl cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="btn-brand px-5 py-2 text-xs font-extrabold rounded-xl shadow-md cursor-pointer"
                            x-text="adjustIngredientType === 'in' ? 'Tambah Stok' : 'Catat Pemakaian'">
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- ══════════════════════════════════════════════════════════════════
         MODAL: TOLAK PESANAN DENGAN ALASAN JELAS & MENARIK
    ══════════════════════════════════════════════════════════════════════ --}}
    <div
        x-show="showRejectOrderModal"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-950/70 backdrop-blur-xs"
        @keydown.escape.window="showRejectOrderModal = false"
    >
        <div
            @click.outside="showRejectOrderModal = false"
            x-show="showRejectOrderModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-2"
            class="w-full max-w-lg rounded-2xl border shadow-2xl overflow-hidden relative"
            style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card-hover);"
        >
            {{-- Header Modal --}}
            <div class="px-5 py-4 border-b flex items-center justify-between"
                 style="background-color: var(--status-danger-bg); border-color: var(--status-danger-border);">
                <div class="flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl flex items-center justify-center shadow-2xs"
                          style="background-color: #FFFFFF; color: var(--status-danger); border: 1px solid var(--status-danger-border);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    </span>
                    <div>
                        <h3 class="font-black text-sm sm:text-base leading-snug" style="color: var(--status-danger);">
                            Tolak Pesanan <span x-text="rejectingOrder.order_number"></span>
                        </h3>
                        <p class="text-[0.7rem] sm:text-xs font-medium" style="color: var(--text-secondary);">
                            Berikan alasan penolakan agar pemesan mengetahui sebabnya
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    @click="showRejectOrderModal = false"
                    class="w-7 h-7 rounded-lg border flex items-center justify-center text-xs opacity-70 hover:opacity-100 transition-opacity cursor-pointer"
                    style="border-color: var(--status-danger-border); color: var(--status-danger); background-color: #FFFFFF;"
                    aria-label="Tutup modal"
                >
                    ✕
                </button>
            </div>

            {{-- Form Tolak Pesanan --}}
            <form :action="rejectingOrder.action_url" method="POST" class="p-5 sm:p-6 space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="cancelled">

                {{-- Ringkasan Pemesan --}}
                <div class="p-3 sm:p-3.5 rounded-xl border flex items-center justify-between text-xs"
                     style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                    <div>
                        <span class="text-[0.68rem] uppercase font-bold block" style="color: var(--text-muted);">Pemesan</span>
                        <span class="font-extrabold text-sm" style="color: var(--text-primary);" x-text="rejectingOrder.customer_name"></span>
                        <span class="font-semibold text-xs ml-1" style="color: var(--brand);" x-text="'(' + rejectingOrder.customer_class + ')'"></span>
                    </div>
                    <div class="text-right">
                        <span class="text-[0.68rem] uppercase font-bold block" style="color: var(--text-muted);">Total Belanja</span>
                        <span class="font-black text-sm" style="color: var(--text-primary);" x-text="rejectingOrder.total"></span>
                    </div>
                </div>

                {{-- Pilihan Alasan Cepat (Quick Chips) --}}
                <div>
                    <label class="block text-xs font-bold mb-2" style="color: var(--text-secondary);">
                        Pilih Alasan Cepat:
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <button
                            type="button"
                            @click="setRejectReason('Stok menu / bahan utama telah habis di kantin')"
                            class="text-left p-2.5 rounded-xl border text-xs font-semibold transition-all cursor-pointer flex items-center gap-2"
                            :class="rejectingOrder.reason === 'Stok menu / bahan utama telah habis di kantin' ? 'border-amber-600 bg-amber-500/10 font-bold' : 'hover:bg-stone-500/5'"
                            style="border-color: var(--border-default); color: var(--text-primary);"
                        >
                            <svg class="w-4 h-4 shrink-0 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            <span class="truncate">Stok bahan / menu habis</span>
                        </button>

                        <button
                            type="button"
                            @click="setRejectReason('Antrian dapur sedang overload / waktu istirahat mepet')"
                            class="text-left p-2.5 rounded-xl border text-xs font-semibold transition-all cursor-pointer flex items-center gap-2"
                            :class="rejectingOrder.reason === 'Antrian dapur sedang overload / waktu istirahat mepet' ? 'border-amber-600 bg-amber-500/10 font-bold' : 'hover:bg-stone-500/5'"
                            style="border-color: var(--border-default); color: var(--text-primary);"
                        >
                            <svg class="w-4 h-4 shrink-0 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="truncate">Dapur overload / waktu mepet</span>
                        </button>

                        <button
                            type="button"
                            @click="setRejectReason('Stand kantin tutup lebih awal karena ada keperluan darurat')"
                            class="text-left p-2.5 rounded-xl border text-xs font-semibold transition-all cursor-pointer flex items-center gap-2"
                            :class="rejectingOrder.reason === 'Stand kantin tutup lebih awal karena ada keperluan darurat' ? 'border-amber-600 bg-amber-500/10 font-bold' : 'hover:bg-stone-500/5'"
                            style="border-color: var(--border-default); color: var(--text-primary);"
                        >
                            <svg class="w-4 h-4 shrink-0 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span class="truncate">Stand tutup lebih awal</span>
                        </button>

                        <button
                            type="button"
                            @click="setRejectReason('Pesanan duplikat atau ada kesalahan jumlah pesanan')"
                            class="text-left p-2.5 rounded-xl border text-xs font-semibold transition-all cursor-pointer flex items-center gap-2"
                            :class="rejectingOrder.reason === 'Pesanan duplikat atau ada kesalahan jumlah pesanan' ? 'border-amber-600 bg-amber-500/10 font-bold' : 'hover:bg-stone-500/5'"
                            style="border-color: var(--border-default); color: var(--text-primary);"
                        >
                            <svg class="w-4 h-4 shrink-0 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span class="truncate">Pesanan duplikat / keliru</span>
                        </button>
                    </div>
                </div>

                {{-- Input Teks Alasan (Dapat Diedit Manual) --}}
                <div>
                    <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                        Teks Alasan Lengkap <span style="color: var(--status-danger);">*</span>
                    </label>
                    <textarea
                        name="cancel_reason"
                        x-model="rejectingOrder.reason"
                        rows="2"
                        required
                        maxlength="255"
                        placeholder="Tuliskan alasan penolakan secara jelas dan sopan..."
                        class="w-full rounded-xl px-3.5 py-2.5 text-xs sm:text-sm border outline-none transition-colors"
                        style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                    ></textarea>
                    <p class="text-[0.68rem] mt-1" style="color: var(--text-muted);">
                        Teks ini akan langsung terbaca oleh pelanggan di halaman Pesanan Saya dan Lonceng Notifikasi.
                    </p>
                </div>

                {{-- Alert Info Pengembalian Stok --}}
                <div class="p-3 rounded-xl border text-xs flex items-start gap-2.5"
                     style="background-color: var(--status-info-bg); border-color: var(--status-info-border); color: var(--status-info);">
                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="leading-relaxed">
                        Saat pesanan ditolak, stok produk yang sebelumnya terpesan akan <b>otomatis dikembalikan</b> ke etalase menu toko.
                    </p>
                </div>

                {{-- Tombol Aksi Modal --}}
                <div class="pt-3 border-t flex items-center justify-end gap-2.5" style="border-color: var(--border-default);">
                    <button
                        type="button"
                        @click="showRejectOrderModal = false"
                        class="btn-secondary px-4 py-2 text-xs font-semibold rounded-xl cursor-pointer"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="px-5 py-2 text-xs font-black rounded-xl shadow-md transition-all hover:scale-[1.02] active:scale-95 cursor-pointer flex items-center gap-1.5"
                        style="background-color: var(--status-danger); color: #FFFFFF;"
                    >
                        <span>✕</span>
                        <span>Konfirmasi Tolak Pesanan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function dashboardManager() {
    return {
        activeTab: '{{ request('tab', ($outOfStockCount > 0 && request('from_stock_alert')) ? 'products' : request('tab', 'orders')) }}',
        productSearch: '',
        productFilter: '{{ request('filter', 'all') }}',
        orderFilter: 'all',
        ingredientSearch: '',
        ingredientFilter: 'all',
        refreshCountdown: 15,
        showCreateModal: false,
        showQuickEditModal: false,
        showRejectOrderModal: false,
        showCreateIngredientModal: false,
        showAdjustIngredientModal: false,
        adjustIngredientType: 'in',
        rejectingOrder: {
            id: null,
            order_number: '',
            customer_name: '',
            customer_class: '',
            total: '',
            items_count: 0,
            action_url: '',
            reason: 'Stok menu / bahan utama telah habis di kantin',
        },
        openRejectModal(btnOrData) {
            if (btnOrData && btnOrData.dataset) {
                this.rejectingOrder = {
                    id: btnOrData.dataset.id,
                    order_number: btnOrData.dataset.orderNumber,
                    customer_name: btnOrData.dataset.customerName,
                    customer_class: btnOrData.dataset.customerClass,
                    total: btnOrData.dataset.total,
                    items_count: btnOrData.dataset.itemsCount,
                    action_url: btnOrData.dataset.actionUrl,
                    reason: 'Stok menu / bahan utama telah habis di kantin',
                };
            } else if (btnOrData) {
                this.rejectingOrder = {
                    id: btnOrData.id,
                    order_number: btnOrData.order_number,
                    customer_name: btnOrData.customer_name,
                    customer_class: btnOrData.customer_class,
                    total: btnOrData.total,
                    items_count: btnOrData.items_count,
                    action_url: btnOrData.action_url,
                    reason: btnOrData.reason || 'Stok menu / bahan utama telah habis di kantin',
                };
            }
            this.showRejectOrderModal = true;
        },
        setRejectReason(text) {
            this.rejectingOrder.reason = text;
        },
        adjustIngredientItem: {
            id: null,
            name: '',
            stock: 0,
            unit: 'kg'
        },
        editingProduct: {
            id: null,
            name: '',
            price: 0,
            stock: 0,
            is_available: true
        },

        soundEnabled: localStorage.getItem('ekantin_sound_enabled') !== 'false',

        toggleSound() {
            this.soundEnabled = !this.soundEnabled;
            localStorage.setItem('ekantin_sound_enabled', this.soundEnabled);
            if (this.soundEnabled) {
                this.playBellChime();
            }
        },

        playBellChime() {
            if (!this.soundEnabled) return;
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                if (ctx.state === 'suspended') {
                    ctx.resume();
                }
                const now = ctx.currentTime;
                
                // Tone 1: E5 (659.25Hz)
                const osc1 = ctx.createOscillator();
                const gain1 = ctx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(659.25, now);
                gain1.gain.setValueAtTime(0.3, now);
                gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.5);
                osc1.connect(gain1);
                gain1.connect(ctx.destination);
                osc1.start(now);
                osc1.stop(now + 0.5);

                // Tone 2: G5 (783.99Hz)
                const osc2 = ctx.createOscillator();
                const gain2 = ctx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(783.99, now + 0.15);
                gain2.gain.setValueAtTime(0.35, now + 0.15);
                gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.8);
                osc2.connect(gain2);
                gain2.connect(ctx.destination);
                osc2.start(now + 0.15);
                osc2.stop(now + 0.8);
            } catch(e) {}
        },

        filterOrder(statusGroup) {
            if (this.orderFilter === 'all') return true;
            return statusGroup === this.orderFilter;
        },

        openQuickEdit(product) {
            this.editingProduct = {
                id: product.id,
                name: product.name,
                price: product.price,
                stock: product.stock,
                is_available: Boolean(product.is_available)
            };
            this.showQuickEditModal = true;
        },

        filterProduct(item) {
            const query = this.productSearch.toLowerCase().trim();
            const matchesSearch = !query || 
                item.name.toLowerCase().includes(query) || 
                item.tenant.toLowerCase().includes(query);

            let matchesCategory = true;
            if (this.productFilter === 'out_of_stock') {
                matchesCategory = (item.stock <= 0);
            } else if (this.productFilter === 'low_stock') {
                matchesCategory = (item.stock > 0 && item.stock <= 5);
            } else if (this.productFilter !== 'all') {
                matchesCategory = (item.category === this.productFilter);
            }

            return matchesSearch && matchesCategory;
        },

        openAdjustIngredient(item, type) {
            this.adjustIngredientItem = { ...item };
            this.adjustIngredientType = type;
            this.showAdjustIngredientModal = true;
        },

        filterIngredient(item) {
            const query = this.ingredientSearch.toLowerCase().trim();
            const matchesSearch = !query || item.name.toLowerCase().includes(query);

            let matchesCategory = true;
            if (this.ingredientFilter === 'need_restock') {
                matchesCategory = (item.is_out_of_stock || item.is_low_stock);
            } else if (this.ingredientFilter === 'out_of_stock') {
                matchesCategory = item.is_out_of_stock;
            } else if (this.ingredientFilter !== 'all') {
                matchesCategory = (item.category === this.ingredientFilter);
            }

            return matchesSearch && matchesCategory;
        },

        async submitQuickUpdate(event) {
            const form = event.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerText;
            submitBtn.disabled = true;
            submitBtn.innerText = '...';

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                if (response.ok) {
                    const data = await response.json();
                    submitBtn.innerText = '✓';
                    setTimeout(() => {
                        submitBtn.innerText = originalText;
                        submitBtn.disabled = false;
                        window.location.href = '{{ route('dashboard.index', ['tab' => 'products']) }}';
                    }, 400);
                } else {
                    form.submit();
                }
            } catch (err) {
                form.submit();
            }
        },

        init() {
            setInterval(() => {
                const hasOpenModal = this.showCreateModal || this.showQuickEditModal || this.showRejectOrderModal || this.showCreateIngredientModal || this.showAdjustIngredientModal;
                const isTyping = document.querySelector('input:focus, textarea:focus, select:focus');

                if (this.activeTab === 'orders' && !hasOpenModal && !isTyping) {
                    this.refreshCountdown--;
                    if (this.refreshCountdown <= 0) {
                        window.location.reload();
                    }
                } else {
                    this.refreshCountdown = 15;
                }
            }, 1000);

            // Notifikasi Audio Synthesizer saat pesanan baru masuk
            const currentPending = {{ $pendingOrdersCount ?? 0 }};
            let previousPending = sessionStorage.getItem('ekantin_pending_orders');
            
            if (previousPending === null) {
                sessionStorage.setItem('ekantin_pending_orders', currentPending);
            } else {
                previousPending = parseInt(previousPending);
                if (currentPending > previousPending) {
                    this.playBellChime();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Pesanan Baru Masuk!',
                            text: `Ada ${currentPending - previousPending} pesanan baru yang menunggu konfirmasi dapur!`,
                            icon: 'info',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true
                        });
                    }
                }
                sessionStorage.setItem('ekantin_pending_orders', currentPending);
            }

            @if(session('order_cancelled_success'))
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Berhasil Tolak Pesanan',
                        html: '<div style="font-size:0.85rem;line-height:1.5;text-align:left;">' +
                              '<p>Pesanan <b>#{{ session('order_cancelled_success')['order_number'] ?? '' }}</b> telah berhasil ditolak.</p>' +
                              '<div style="margin-top:8px;padding:8px 12px;border-radius:8px;background:rgba(239,68,68,0.1);color:#ef4444;border:1px solid rgba(239,68,68,0.2);">' +
                              '<b>Alasan:</b> "{{ session('order_cancelled_success')['reason'] ?? '' }}"' +
                              '</div>' +
                              '<p style="margin-top:8px;font-size:0.75rem;color:#6b7280;">Porsi produk otomatis dikembalikan ke etalase kantin.</p>' +
                              '</div>',
                        icon: 'success',
                        confirmButtonText: 'Selesai',
                        confirmButtonColor: '#E11D48',
                        timer: 5000,
                        timerProgressBar: true
                    });
                }
            @endif

            @if(session('order_confirmed_success'))
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Pesanan Telah Dikonfirmasi!',
                        text: 'Pesanan #{{ session('order_confirmed_success')['order_number'] ?? '' }} milik {{ session('order_confirmed_success')['customer_name'] ?? 'Pelanggan' }} telah berhasil dikonfirmasi. Dapur dapat segera mulai memasak!',
                        icon: 'success',
                        confirmButtonText: 'Mulai Masak',
                        confirmButtonColor: '#2563EB',
                        timer: 4000,
                        timerProgressBar: true
                    });
                }
            @endif
        }
    };
}
</script>
@endpush
@endsection
