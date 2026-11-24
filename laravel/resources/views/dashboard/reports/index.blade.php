@extends('layouts.dashboard')

@section('title', 'Laporan & Keuangan Kantin')

@push('head')
<style>
    @media print {
        /* Sembunyikan elemen dashboard & antarmuka saat mencetak */
        header, footer, #dashboard-navbar, .no-print, button, form, .btn-brand, .btn-secondary {
            display: none !important;
        }
        body, main, #dashboard-main-content {
            background: #ffffff !important;
            color: #000000 !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .max-w-7xl {
            max-width: 100% !important;
            width: 100% !important;
            padding: 0 !important;
        }
        .shadow-sm, .shadow-md, .shadow-xl, .shadow-card {
            box-shadow: none !important;
        }
        .border {
            border-color: #cbd5e1 !important;
        }
        .page-break-inside-avoid {
            break-inside: avoid;
            page-break-inside: avoid;
        }
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{
    showCustomDate: '{{ $period === 'custom' ? 'true' : 'false' }}' === 'true',
    startDate: '{{ $formattedStartDate }}',
    endDate: '{{ $formattedEndDate }}'
}">

    {{-- ── 1. HEADER HALAMAN LAPORAN KEUANGAN & CETAK ────────────────────────── --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4 no-print">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold mb-2 border shadow-2xs"
                 style="background: var(--brand-subtle); border-color: var(--brand-border); color: var(--brand);">
                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <span>Pembukuan & Rekapitulasi Finansial</span>
                <span class="opacity-40">•</span>
                <span>{{ $periodLabel }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight mb-1" style="color: var(--text-primary);">
                Laporan & Keuangan Kantin
            </h1>
            <p class="text-xs sm:text-sm" style="color: var(--text-secondary);">
                Hitung total penghasilan kotor, potongan diskon, omzet bersih, serta rincian transaksi tunai dan QRIS berdasarkan tanggal.
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            {{-- Tombol Buka Halaman Cetak Khusus PDF --}}
            <a
                href="{{ route('dashboard.reports.print', request()->all()) }}"
                target="_blank"
                class="btn-brand text-xs font-bold py-2.5 px-4 rounded-xl shadow-md flex items-center gap-2 transition-all hover:scale-[1.02] cursor-pointer"
                title="Buka Lembar Cetak Resmi & Simpan sebagai PDF"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Lembar PDF</span>
            </a>

            {{-- Tombol Cetak Langsung Halaman Ini --}}
            <button
                type="button"
                onclick="window.print()"
                class="btn-secondary text-xs font-semibold py-2.5 px-3.5 rounded-xl border flex items-center gap-1.5 shadow-2xs transition-all hover:opacity-90 cursor-pointer"
                title="Cetak langsung menggunakan dialog print browser"
            >
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print Cepat</span>
            </button>

            {{-- Pintasan Kembali ke Dashboard Utama --}}
            <a href="{{ route('dashboard.index') }}"
               class="btn-secondary text-xs font-semibold py-2.5 px-3.5 rounded-xl border flex items-center gap-1.5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Dashboard Utama</span>
            </a>
        </div>
    </div>

    {{-- ── KOP SURAT KHUSUS PRINT MODE (TAMPIL HANYA SAAT CETAK) ─────────────── --}}
    <div class="hidden print:block mb-6 border-b-2 border-slate-900 pb-4 text-center">
        <h2 class="text-xl font-black uppercase tracking-wider text-slate-900">KANTINKITA — REKAPITULASI KEUANGAN & PENGHASILAN</h2>
        <p class="text-xs text-slate-600 font-semibold mt-0.5">Sistem Pengelolaan Kantin Sekolah Digital & Multi-Tenant Terpadu</p>
        <div class="flex items-center justify-between text-[11px] text-slate-600 mt-3 pt-2 border-t border-slate-300">
            <span><b>Periode Laporan:</b> {{ $periodLabel }}</span>
            <span><b>Waktu Dicetak:</b> {{ now()->translatedFormat('l, d F Y - H:i:s') }} WIB</span>
            <span><b>Pengelola:</b> {{ auth()->user()->name }} ({{ auth()->user()->isTenant() ? 'Tenant Stand' : 'Administrator' }})</span>
        </div>
    </div>

    {{-- ── 2. FILTER RENTANG TANGGAL & PARAMETER KEUANGAN ────────────────────── --}}
    <div class="rounded-2xl border p-5 sm:p-6 mb-8 shadow-sm no-print"
         style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">

        <div class="space-y-4">

            {{-- Baris 1: Tombol Pintasan Periode Cepat (Menggunakan Tautan Nyata yang Anti-Gagal) --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-2" style="color: var(--text-muted);">
                    Pilih Periode Waktu:
                </label>
                <div class="flex items-center gap-1.5 flex-wrap">
                    @php
                        $presetPeriods = [
                            'today'       => 'Hari Ini',
                            'yesterday'   => 'Kemarin',
                            'this_week'   => 'Minggu Ini',
                            'last_7_days' => '7 Hari Terakhir',
                            'this_month'  => 'Bulan Ini',
                            'last_month'  => 'Bulan Lalu',
                            'this_year'   => 'Tahun Ini',
                            'all_time'    => 'Semua Waktu',
                        ];
                    @endphp

                    @foreach($presetPeriods as $key => $label)
                        <a
                            href="{{ route('dashboard.reports.index', array_merge(request()->only(['tenant_id', 'payment_method', 'search']), ['period' => $key])) }}"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all border inline-flex items-center active:scale-95 cursor-pointer"
                            style="{{ $period === $key ? 'background-color: var(--brand); color: #ffffff; border-color: var(--brand);' : 'background-color: var(--bg-surface-2); color: var(--text-secondary); border-color: var(--border-default);' }}"
                        >
                            {{ $label }}
                        </a>
                    @endforeach

                    {{-- Tombol Toggle Rentang Kustom --}}
                    <button
                        type="button"
                        @click="showCustomDate = !showCustomDate"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all border cursor-pointer active:scale-95 flex items-center gap-1.5"
                        style="{{ $period === 'custom' ? 'background-color: var(--brand); color: #ffffff; border-color: var(--brand);' : 'background-color: var(--bg-surface-2); color: var(--text-secondary); border-color: var(--border-default);' }}"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Rentang Kustom</span>
                    </button>
                </div>
            </div>

            {{-- Baris 2: Input Rentang Tanggal Kustom --}}
            <div x-show="showCustomDate" x-cloak
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="p-4 rounded-xl border"
                 style="background-color: var(--bg-surface-2); border-color: var(--border-default);">

                <form action="{{ route('dashboard.reports.index') }}" method="GET" class="flex flex-col sm:flex-row items-end gap-3">
                    <input type="hidden" name="period" value="custom">
                    @if($selectedTenantId && $selectedTenantId !== 'all')
                        <input type="hidden" name="tenant_id" value="{{ $selectedTenantId }}">
                    @endif
                    @if($paymentMethodFilter && $paymentMethodFilter !== 'all')
                        <input type="hidden" name="payment_method" value="{{ $paymentMethodFilter }}">
                    @endif
                    @if($search)
                        <input type="hidden" name="search" value="{{ $search }}">
                    @endif

                    <div class="w-full sm:w-auto flex-1">
                        <label class="block text-xs font-semibold mb-1" style="color: var(--text-secondary);">
                            Tanggal Mulai:
                        </label>
                        <input
                            type="date"
                            name="start_date"
                            value="{{ $formattedStartDate }}"
                            required
                            class="w-full text-xs font-medium px-3 py-2 rounded-lg border focus:outline-none"
                            style="background-color: var(--bg-surface); border-color: var(--border-default); color: var(--text-primary);"
                        >
                    </div>

                    <div class="w-full sm:w-auto flex-1">
                        <label class="block text-xs font-semibold mb-1" style="color: var(--text-secondary);">
                            Tanggal Selesai:
                        </label>
                        <input
                            type="date"
                            name="end_date"
                            value="{{ $formattedEndDate }}"
                            required
                            class="w-full text-xs font-medium px-3 py-2 rounded-lg border focus:outline-none"
                            style="background-color: var(--bg-surface); border-color: var(--border-default); color: var(--text-primary);"
                        >
                    </div>

                    <button
                        type="submit"
                        class="w-full sm:w-auto btn-brand text-xs font-bold py-2 px-4 rounded-lg flex items-center justify-center gap-1.5 cursor-pointer shadow-xs"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Terapkan Rentang</span>
                    </button>
                </form>
            </div>

            {{-- Baris 3: Filter Tambahan (Tenant, Metode Pembayaran, Pencarian) --}}
            <form action="{{ route('dashboard.reports.index') }}" method="GET" id="report-filter-form">
                <input type="hidden" name="period" value="{{ $period }}">
                @if($period === 'custom')
                    <input type="hidden" name="start_date" value="{{ $formattedStartDate }}">
                    <input type="hidden" name="end_date" value="{{ $formattedEndDate }}">
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-3 border-t" style="border-color: var(--border-default);">

                    {{-- Filter Stand / Tenant (Jika Super Admin) --}}
                    @if(auth()->user()->isAdmin())
                    <div>
                        <label class="block text-[11px] font-semibold mb-1" style="color: var(--text-muted);">
                            Filter Stand Kantin:
                        </label>
                        <select
                            name="tenant_id"
                            onchange="document.getElementById('report-filter-form').submit()"
                            class="w-full text-xs font-medium px-3 py-2 rounded-lg border focus:outline-none cursor-pointer"
                            style="background-color: var(--bg-surface-2); border-color: var(--border-default); color: var(--text-primary);"
                        >
                            <option value="all" {{ $selectedTenantId === 'all' ? 'selected' : '' }}>Semua Stand Kantin</option>
                            @foreach($tenants as $t)
                                <option value="{{ $t->id }}" {{ (string)$selectedTenantId === (string)$t->id ? 'selected' : '' }}>
                                    {{ $t->name }} ({{ $t->location }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <div>
                        <label class="block text-[11px] font-semibold mb-1" style="color: var(--text-muted);">
                            Stand Kantin Anda:
                        </label>
                        <div class="text-xs font-bold px-3 py-2 rounded-lg border"
                             style="background-color: var(--bg-surface-2); border-color: var(--border-default); color: var(--text-primary);">
                            {{ auth()->user()->tenant->name ?? 'Stand Kantin' }}
                        </div>
                    </div>
                    @endif

                    {{-- Filter Metode Pembayaran --}}
                    <div>
                        <label class="block text-[11px] font-semibold mb-1" style="color: var(--text-muted);">
                            Metode Pembayaran:
                        </label>
                        <select
                            name="payment_method"
                            onchange="document.getElementById('report-filter-form').submit()"
                            class="w-full text-xs font-medium px-3 py-2 rounded-lg border focus:outline-none cursor-pointer"
                            style="background-color: var(--bg-surface-2); border-color: var(--border-default); color: var(--text-primary);"
                        >
                            <option value="all" {{ $paymentMethodFilter === 'all' ? 'selected' : '' }}>Semua Metode (Tunai & QRIS)</option>
                            <option value="cash" {{ $paymentMethodFilter === 'cash' ? 'selected' : '' }}>Hanya Tunai (Cash di Kasir)</option>
                            <option value="qris" {{ $paymentMethodFilter === 'qris' ? 'selected' : '' }}>Hanya QRIS (Digital)</option>
                        </select>
                    </div>

                    {{-- Input Cari Transaksi --}}
                    <div>
                        <label class="block text-[11px] font-semibold mb-1" style="color: var(--text-muted);">
                            Cari Nomor / Pemesan:
                        </label>
                        <div class="flex items-center gap-1.5">
                            <input
                                type="text"
                                name="search"
                                value="{{ $search }}"
                                placeholder="Cari #ORD / nama pemesan..."
                                class="w-full text-xs font-medium px-3 py-2 rounded-lg border focus:outline-none"
                                style="background-color: var(--bg-surface-2); border-color: var(--border-default); color: var(--text-primary);"
                            >
                            <button type="submit"
                                    class="btn-brand px-3 py-2 text-xs font-bold rounded-lg cursor-pointer shrink-0">
                                Cari
                            </button>
                            @if($search)
                                <a href="{{ route('dashboard.reports.index', ['period' => $period, 'start_date' => $formattedStartDate, 'end_date' => $formattedEndDate]) }}"
                                   class="px-2.5 py-2 text-xs font-bold rounded-lg border hover:bg-slate-100 transition-colors"
                                   style="border-color: var(--border-default); color: var(--text-muted);"
                                   title="Reset Pencarian">
                                    ✕
                                </a>
                            @endif
                        </div>
                    </div>

                </div>
            </form>

        </div>

    </div>

    {{-- ── 3. EMPAT KARTU RINGKASAN FINANSIAL UTAMA ───────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        {{-- KPI 1: TOTAL PENGHASILAN BERSIH --}}
        <div class="rounded-2xl p-5 border shadow-sm transition-all hover:scale-[1.01]"
             style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
            <div class="flex items-start justify-between mb-3">
                <div class="min-w-0">
                    <span class="text-[11px] font-bold uppercase tracking-wider leading-none" style="color: var(--text-muted);">
                        Total Penghasilan Bersih
                    </span>
                    <h3 class="text-2xl sm:text-3xl font-black mt-1.5 truncate" style="color: var(--brand);">
                        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 ml-2 shadow-xs"
                     style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="flex items-center justify-between text-xs pt-2 border-t" style="border-color: var(--border-default);">
                <span class="font-medium" style="color: var(--text-secondary);">Dari <b>{{ $completedCount }}</b> pesanan sukses</span>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                      style="background-color: var(--status-success-bg); color: var(--status-success);">
                    Lunas
                </span>
            </div>
        </div>

        {{-- KPI 2: OMZET KOTOR & DISKON VOUCHER --}}
        <div class="rounded-2xl p-5 border shadow-sm transition-all hover:scale-[1.01]"
             style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
            <div class="flex items-start justify-between mb-3">
                <div class="min-w-0">
                    <span class="text-[11px] font-bold uppercase tracking-wider leading-none" style="color: var(--text-muted);">
                        Omzet Kotor (Sebelum Diskon)
                    </span>
                    <h3 class="text-xl sm:text-2xl font-black mt-1.5 truncate" style="color: var(--text-primary);">
                        Rp {{ number_format($totalGross, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 ml-2 shadow-xs"
                     style="background: rgba(249, 115, 22, 0.12); color: var(--brand);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                    </svg>
                </div>
            </div>
            <div class="flex items-center justify-between text-xs pt-2 border-t" style="border-color: var(--border-default);">
                <span class="font-medium" style="color: var(--text-secondary);">Diskon Voucher:</span>
                <span class="font-mono font-bold" style="color: var(--status-danger);">
                    - Rp {{ number_format($totalDiscount, 0, ',', '.') }}
                </span>
            </div>
        </div>

        {{-- KPI 3: RATA-RATA TRANSAKSI (AOV) --}}
        <div class="rounded-2xl p-5 border shadow-sm transition-all hover:scale-[1.01]"
             style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
            <div class="flex items-start justify-between mb-3">
                <div class="min-w-0">
                    <span class="text-[11px] font-bold uppercase tracking-wider leading-none" style="color: var(--text-muted);">
                        Rata-Rata per Transaksi (AOV)
                    </span>
                    <h3 class="text-xl sm:text-2xl font-black mt-1.5 truncate" style="color: var(--text-primary);">
                        Rp {{ number_format($averageOrderValue, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 ml-2 shadow-xs"
                     style="background: rgba(59, 130, 246, 0.12); color: #2563eb;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
            <div class="flex items-center justify-between text-xs pt-2 border-t" style="border-color: var(--border-default);">
                <span class="font-medium" style="color: var(--text-secondary);">Total Pembeli:</span>
                <span class="font-bold" style="color: var(--text-primary);">
                    {{ $completedCount }} Pelanggan
                </span>
            </div>
        </div>

        {{-- KPI 4: PESANAN BATAL / REFUND --}}
        <div class="rounded-2xl p-5 border shadow-sm transition-all hover:scale-[1.01]"
             style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
            <div class="flex items-start justify-between mb-3">
                <div class="min-w-0">
                    <span class="text-[11px] font-bold uppercase tracking-wider leading-none" style="color: var(--text-muted);">
                        Pesanan Dibatalkan
                    </span>
                    <h3 class="text-xl sm:text-2xl font-black mt-1.5 truncate" style="color: {{ $cancelledCount > 0 ? 'var(--status-danger)' : 'var(--text-secondary)' }};">
                        {{ $cancelledCount }} Pesanan
                    </h3>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 ml-2 shadow-xs"
                     style="background: rgba(239, 68, 68, 0.12); color: #dc2626;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
            </div>
            <div class="flex items-center justify-between text-xs pt-2 border-t" style="border-color: var(--border-default);">
                <span class="font-medium" style="color: var(--text-secondary);">Nilai Batal:</span>
                <span class="font-mono font-bold" style="color: var(--text-muted);">
                    Rp {{ number_format($cancelledValue, 0, ',', '.') }}
                </span>
            </div>
        </div>

    </div>

    {{-- ── 4. DUA KOLOM: METODE PEMBAYARAN & SEGMEN KELAS GURU/SISWA ─────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        {{-- Kolom Kiri: Breakdown Metode Pembayaran (Tunai vs QRIS) --}}
        <div class="rounded-2xl border p-5 sm:p-6 shadow-sm"
             style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
            <div class="flex items-center justify-between mb-4 pb-3 border-b" style="border-color: var(--border-default);">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: rgba(16, 185, 129, 0.1); color: #059669;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </span>
                    <h3 class="font-bold text-sm tracking-tight" style="color: var(--text-primary);">
                        Komposisi Metode Pembayaran
                    </h3>
                </div>
                <span class="text-xs font-semibold" style="color: var(--text-muted);">
                    Selesai & Lunas
                </span>
            </div>

            <div class="space-y-4">
                {{-- Pembayaran Tunai (Cash) --}}
                <div class="p-3.5 rounded-xl border" style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-bold" style="color: var(--text-primary);">Tunai (Cash di Kasir)</span>
                            <span class="text-[11px] font-medium" style="color: var(--text-muted);">({{ $cashCount }} transaksi)</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-black font-mono" style="color: var(--text-primary);">
                                Rp {{ number_format($cashRevenue, 0, ',', '.') }}
                            </span>
                            <span class="text-[10px] font-bold ml-1.5 px-1.5 py-0.5 rounded-md" style="background: rgba(16, 185, 129, 0.15); color: #059669;">
                                {{ $cashPercent }}%
                            </span>
                        </div>
                    </div>
                    <div class="w-full h-2 rounded-full overflow-hidden" style="background-color: var(--bg-surface-3);">
                        <div class="h-full rounded-full bg-emerald-500 transition-all duration-500" style="width: {{ $cashPercent }}%;"></div>
                    </div>
                </div>

                {{-- Pembayaran QRIS (Digital) --}}
                <div class="p-3.5 rounded-xl border" style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            <span class="text-xs font-bold" style="color: var(--text-primary);">QRIS (Pembayaran Digital)</span>
                            <span class="text-[11px] font-medium" style="color: var(--text-muted);">({{ $qrisCount }} transaksi)</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-black font-mono" style="color: var(--text-primary);">
                                Rp {{ number_format($qrisRevenue, 0, ',', '.') }}
                            </span>
                            <span class="text-[10px] font-bold ml-1.5 px-1.5 py-0.5 rounded-md" style="background: rgba(59, 130, 246, 0.15); color: #2563eb;">
                                {{ $qrisPercent }}%
                            </span>
                        </div>
                    </div>
                    <div class="w-full h-2 rounded-full overflow-hidden" style="background-color: var(--bg-surface-3);">
                        <div class="h-full rounded-full bg-blue-500 transition-all duration-500" style="width: {{ $qrisPercent }}%;"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Breakdown Segmen Pembeli (Guru vs Siswa) --}}
        <div class="rounded-2xl border p-5 sm:p-6 shadow-sm"
             style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
            <div class="flex items-center justify-between mb-4 pb-3 border-b" style="border-color: var(--border-default);">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: rgba(249, 115, 22, 0.1); color: var(--brand);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </span>
                    <h3 class="font-bold text-sm tracking-tight" style="color: var(--text-primary);">
                        Kontribusi Segmen Pelanggan
                    </h3>
                </div>
                <span class="text-xs font-semibold" style="color: var(--text-muted);">
                    Guru & Siswa
                </span>
            </div>

            <div class="grid grid-cols-2 gap-3.5">
                {{-- Segmen Guru / Staf --}}
                <div class="p-3.5 rounded-xl border flex flex-col justify-between"
                     style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                    <div>
                        <div class="flex items-center gap-1.5 mb-2">
                            <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-md border"
                                  style="background-color: var(--brand-subtle); color: var(--brand); border-color: var(--brand-border);">
                                Guru & Staf
                            </span>
                        </div>
                        <p class="text-lg font-black font-mono" style="color: var(--text-primary);">
                            Rp {{ number_format($teacherRevenue, 0, ',', '.') }}
                        </p>
                    </div>
                    <p class="text-[11px] font-medium mt-3 pt-2 border-t" style="border-color: var(--border-default); color: var(--text-muted);">
                        <b>{{ $teacherCount }}</b> pesanan diantar/ambil
                    </p>
                </div>

                {{-- Segmen Siswa --}}
                <div class="p-3.5 rounded-xl border flex flex-col justify-between"
                     style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                    <div>
                        <div class="flex items-center gap-1.5 mb-2">
                            <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-md border"
                                  style="background-color: var(--bg-surface-3); color: var(--text-secondary); border-color: var(--border-default);">
                                Siswa Sekolah
                            </span>
                        </div>
                        <p class="text-lg font-black font-mono" style="color: var(--text-primary);">
                            Rp {{ number_format($studentRevenue, 0, ',', '.') }}
                        </p>
                    </div>
                    <p class="text-[11px] font-medium mt-3 pt-2 border-t" style="border-color: var(--border-default); color: var(--text-muted);">
                        <b>{{ $studentCount }}</b> pesanan siswa
                    </p>
                </div>
            </div>
        </div>

    </div>

    {{-- ── 5. GRAFIK TREN HARIAN PENDAPATAN (JIKA RENTANG <= 31 HARI) ─────────── --}}
    @if($dailyChart->isNotEmpty())
    <div class="rounded-2xl border p-5 sm:p-6 mb-8 shadow-sm no-print"
         style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
        <div class="flex items-center justify-between mb-4 pb-3 border-b" style="border-color: var(--border-default);">
            <div class="flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: rgba(249, 115, 22, 0.1); color: var(--brand);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg>
                </span>
                <div>
                    <h3 class="font-bold text-sm tracking-tight" style="color: var(--text-primary);">
                        Grafik Penghasilan Harian
                    </h3>
                    <p class="text-xs" style="color: var(--text-muted);">
                        Pantau fluktuasi omzet harian kantin selama rentang tanggal yang dipilih
                    </p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-xs font-bold" style="color: var(--text-muted);">Puncak Tertinggi:</span>
                <span class="text-xs font-black font-mono ml-1" style="color: var(--brand);">Rp {{ number_format($chartMaxRevenue, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Bar Chart Harian Menggunakan Flexbox & CSS --}}
        <div class="overflow-x-auto pb-2">
            <div class="min-w-[600px] h-48 flex items-end gap-2 pt-6 pb-2">
                @foreach($dailyChart as $bar)
                    @php
                        $heightPercent = $chartMaxRevenue > 0 ? max(round(($bar['revenue'] / $chartMaxRevenue) * 100), 4) : 4;
                    @endphp
                    <div class="flex-1 flex flex-col items-center h-full justify-end group relative cursor-pointer">
                        {{-- Tooltip Hover --}}
                        <div class="absolute -top-12 z-20 hidden group-hover:flex flex-col items-center pointer-events-none whitespace-nowrap">
                            <div class="px-2.5 py-1 rounded-lg text-[10px] font-bold shadow-lg border"
                                 style="background-color: var(--bg-surface); border-color: var(--border-default); color: var(--text-primary);">
                                <div>{{ $bar['full_date'] }}</div>
                                <div class="font-mono text-emerald-600 font-extrabold">Rp {{ number_format($bar['revenue'], 0, ',', '.') }} ({{ $bar['orders'] }} order)</div>
                            </div>
                            <div class="w-1.5 h-1.5 rotate-45 border-r border-b -mt-0.5"
                                 style="background-color: var(--bg-surface); border-color: var(--border-default);"></div>
                        </div>

                        {{-- Batang Bar --}}
                        <div class="w-full rounded-t-lg transition-all duration-300 {{ $bar['is_today'] ? 'bg-orange-500 shadow-md shadow-orange-500/20' : ($bar['revenue'] > 0 ? 'bg-emerald-500/80 hover:bg-emerald-500' : 'bg-slate-200 dark:bg-slate-800') }}"
                             style="height: {{ $heightPercent }}%;">
                        </div>

                        {{-- Tanggal Label --}}
                        <span class="text-[9.5px] font-bold mt-2 truncate w-full text-center {{ $bar['is_today'] ? 'text-orange-600 font-extrabold' : 'text-slate-500' }}">
                            {{ $bar['date'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ── 6. BREAKDOWN PER STAND / TENANT (UNTUK SUPER ADMIN) ───────────────── --}}
    @if(auth()->user()->isAdmin() && $tenantBreakdown->isNotEmpty())
    <div class="rounded-2xl border p-5 sm:p-6 mb-8 shadow-sm page-break-inside-avoid"
         style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
        <div class="flex items-center justify-between mb-4 pb-3 border-b" style="border-color: var(--border-default);">
            <div class="flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: rgba(124, 58, 237, 0.1); color: #7c3aed;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </span>
                <div>
                    <h3 class="font-bold text-sm tracking-tight" style="color: var(--text-primary);">
                        Rincian Penghasilan Per Stand Kantin (Tenant)
                    </h3>
                    <p class="text-xs" style="color: var(--text-muted);">
                        Distribusi omzet transaksi setiap tenant kantin pada rentang tanggal terpilih
                    </p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b" style="border-color: var(--border-default); color: var(--text-muted);">
                        <th class="py-2.5 px-3 font-bold uppercase tracking-wider text-[10px]">Stand / Tenant</th>
                        <th class="py-2.5 px-3 font-bold uppercase tracking-wider text-[10px]">Lokasi</th>
                        <th class="py-2.5 px-3 font-bold uppercase tracking-wider text-[10px] text-center">Pesanan Selesai</th>
                        <th class="py-2.5 px-3 font-bold uppercase tracking-wider text-[10px] text-right">Tunai (Cash)</th>
                        <th class="py-2.5 px-3 font-bold uppercase tracking-wider text-[10px] text-right">QRIS</th>
                        <th class="py-2.5 px-3 font-bold uppercase tracking-wider text-[10px] text-right">Total Bersih</th>
                        <th class="py-2.5 px-3 font-bold uppercase tracking-wider text-[10px] text-center">Pangsa Omzet</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--border-default);">
                    @foreach($tenantBreakdown as $tb)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-3 px-3 font-bold" style="color: var(--text-primary);">
                            {{ $tb['name'] }}
                        </td>
                        <td class="py-3 px-3 text-slate-500 font-medium">
                            {{ $tb['location'] }}
                        </td>
                        <td class="py-3 px-3 text-center font-bold font-mono">
                            {{ $tb['orders_count'] }}
                        </td>
                        <td class="py-3 px-3 text-right font-mono text-emerald-600 font-medium">
                            Rp {{ number_format($tb['cash'], 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-right font-mono text-blue-600 font-medium">
                            Rp {{ number_format($tb['qris'], 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-right font-mono font-black" style="color: var(--text-primary);">
                            Rp {{ number_format($tb['revenue'], 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-center">
                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold"
                                  style="background-color: var(--brand-subtle); color: var(--brand);">
                                {{ $tb['share'] }}%
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── 7. MENU TERLARIS & KONTRIBUSI PENDAPATAN ───────────────────────────── --}}
    @if($topProducts->isNotEmpty())
    <div class="rounded-2xl border p-5 sm:p-6 mb-8 shadow-sm no-print"
         style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
        <div class="flex items-center justify-between mb-4 pb-3 border-b" style="border-color: var(--border-default);">
            <div class="flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: rgba(249, 115, 22, 0.1); color: var(--brand);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </span>
                <div>
                    <h3 class="font-bold text-sm tracking-tight" style="color: var(--text-primary);">
                        Menu & Produk Terlaris (Kontribusi Omzet)
                    </h3>
                    <p class="text-xs" style="color: var(--text-muted);">
                        Menu favorit dengan kuantitas porsi terbanyak dan pemasukan tertinggi
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            @foreach($topProducts as $idx => $prod)
            <div class="p-3.5 rounded-xl border flex flex-col justify-between"
                 style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                <div>
                    <div class="flex items-center justify-between text-[10px] font-bold mb-1.5">
                        <span class="px-1.5 py-0.5 rounded-md border" style="background-color: var(--bg-surface); border-color: var(--border-default); color: var(--brand);">
                            #{{ $idx + 1 }} Terlaris
                        </span>
                        <span class="text-slate-500 font-mono">{{ $prod['qty'] }} porsi terjual</span>
                    </div>
                    <h4 class="font-bold text-xs line-clamp-1" style="color: var(--text-primary);">
                        {{ $prod['name'] }}
                    </h4>
                    <p class="text-[10px] text-slate-500 mt-0.5 truncate">{{ $prod['tenant'] }}</p>
                </div>
                <div class="mt-3 pt-2 border-t flex items-center justify-between text-xs" style="border-color: var(--border-default);">
                    <span class="text-[11px] text-slate-500">Omzet Menu:</span>
                    <span class="font-mono font-bold text-emerald-600">Rp {{ number_format($prod['revenue'], 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── 8. TABEL DETAIL TRANSAKSI FINANSIAL ─────────────────────────────────── --}}
    <div class="rounded-2xl border shadow-sm overflow-hidden"
         style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
        <div class="p-5 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
             style="border-color: var(--border-default);">
            <div>
                <h3 class="font-bold text-sm tracking-tight" style="color: var(--text-primary);">
                    Buku Jurnal Riwayat Transaksi Finansial
                </h3>
                <p class="text-xs" style="color: var(--text-muted);">
                    Menampilkan total <b>{{ $allOrders->count() }}</b> transaksi pada periode ini
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full border"
                      style="background-color: var(--status-success-bg); color: var(--status-success); border-color: var(--status-success-border);">
                    {{ $completedCount }} Selesai
                </span>
                @if($cancelledCount > 0)
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full border"
                      style="background-color: var(--status-danger-bg); color: var(--status-danger); border-color: var(--status-danger-border);">
                    {{ $cancelledCount }} Dibatalkan
                </span>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b" style="border-color: var(--border-default); background-color: var(--bg-surface-2); color: var(--text-muted);">
                        <th class="py-3 px-3.5 font-bold uppercase tracking-wider text-[10px]">Waktu Transaksi</th>
                        <th class="py-3 px-3.5 font-bold uppercase tracking-wider text-[10px]">No. Pesanan</th>
                        <th class="py-3 px-3.5 font-bold uppercase tracking-wider text-[10px]">Pemesan & Peran</th>
                        <th class="py-3 px-3.5 font-bold uppercase tracking-wider text-[10px]">Stand</th>
                        <th class="py-3 px-3.5 font-bold uppercase tracking-wider text-[10px]">Menu Dipesan</th>
                        <th class="py-3 px-3.5 font-bold uppercase tracking-wider text-[10px]">Metode</th>
                        <th class="py-3 px-3.5 font-bold uppercase tracking-wider text-[10px] text-right">Potongan</th>
                        <th class="py-3 px-3.5 font-bold uppercase tracking-wider text-[10px] text-right">Total Akhir</th>
                        <th class="py-3 px-3.5 font-bold uppercase tracking-wider text-[10px] text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--border-default);">
                    @forelse($allOrders as $ord)
                    <tr class="hover:bg-slate-50/50 transition-colors {{ $ord->status === 'cancelled' ? 'opacity-60 bg-red-50/30' : '' }}">
                        {{-- Waktu --}}
                        <td class="py-3 px-3.5 whitespace-nowrap font-medium text-slate-500">
                            <div>{{ $ord->created_at->format('d/m/Y') }}</div>
                            <div class="text-[10px] font-mono">{{ $ord->created_at->format('H:i') }} WIB</div>
                        </td>

                        {{-- No Pesanan --}}
                        <td class="py-3 px-3.5 whitespace-nowrap font-mono font-bold" style="color: var(--brand);">
                            #{{ $ord->order_number }}
                        </td>

                        {{-- Pelanggan --}}
                        <td class="py-3 px-3.5">
                            <div class="font-bold" style="color: var(--text-primary);">
                                {{ $ord->customer_name ?? 'Pelanggan' }}
                            </div>
                            <div class="text-[10.5px]">
                                @if($ord->isTeacherOrder())
                                    <span class="inline-flex items-center text-blue-600 font-semibold">
                                        Guru • {{ $ord->customer_class ?: 'Ruang Guru' }}
                                    </span>
                                @else
                                    <span class="text-slate-500">
                                        Siswa • {{ $ord->customer_class ?: 'Umum' }}
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- Stand --}}
                        <td class="py-3 px-3.5 font-medium whitespace-nowrap" style="color: var(--text-secondary);">
                            {{ $ord->tenant->name ?? 'Stand Kantin' }}
                        </td>

                        {{-- Menu Dipesan --}}
                        <td class="py-3 px-3.5 max-w-xs">
                            <div class="line-clamp-2 text-[11px] text-slate-600">
                                @foreach($ord->items as $it)
                                    <span>{{ $it->quantity }}x {{ $it->product_name ?: ($it->product->name ?? 'Menu') }}</span>@if(!$loop->last), @endif
                                @endforeach
                            </div>
                        </td>

                        {{-- Metode Pembayaran --}}
                        <td class="py-3 px-3.5 whitespace-nowrap">
                            @if($ord->payment_method === 'cash')
                                <span class="inline-flex items-center gap-1 text-[10.5px] font-bold text-emerald-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Tunai</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[10.5px] font-bold text-blue-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                    <span>QRIS</span>
                                </span>
                            @endif
                        </td>

                        {{-- Diskon --}}
                        <td class="py-3 px-3.5 whitespace-nowrap text-right font-mono text-slate-500">
                            @if($ord->discount > 0)
                                <span class="text-red-500 font-bold">-Rp {{ number_format($ord->discount, 0, ',', '.') }}</span>
                            @else
                                <span>-</span>
                            @endif
                        </td>

                        {{-- Total Akhir --}}
                        <td class="py-3 px-3.5 whitespace-nowrap text-right font-mono font-black" style="color: var(--text-primary);">
                            Rp {{ number_format($ord->total, 0, ',', '.') }}
                        </td>

                        {{-- Status --}}
                        <td class="py-3 px-3.5 whitespace-nowrap text-center">
                            @if($ord->status === 'completed')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    Selesai
                                </span>
                            @elseif($ord->status === 'cancelled')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800 border border-red-200" title="{{ $ord->cancel_reason }}">
                                    Batal
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                    {{ ucfirst($ord->status) }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-8 text-center text-slate-400">
                            <p class="font-semibold text-xs">Tidak ada data transaksi keuangan pada rentang tanggal ini.</p>
                            <p class="text-[11px] mt-1">Coba sesuaikan pilihan tanggal pada filter di atas.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── 9. AREA TANDA TANGAN KHUSUS PRINT MODE ─────────────────────────────── --}}
    <div class="hidden print:grid grid-cols-2 gap-8 mt-12 pt-8 text-center text-xs text-slate-700 page-break-inside-avoid">
        <div>
            <p>Mengetahui,</p>
            <p class="font-bold mt-1">Kepala Sekolah / Penanggung Jawab Kantin</p>
            <div class="h-20"></div>
            <p class="font-bold underline">( .................................................... )</p>
            <p class="text-[10px] text-slate-500">NIP: ...........................................</p>
        </div>
        <div>
            <p>Dibuat oleh,</p>
            <p class="font-bold mt-1">Bendahara / Pengelola Kantin Sekolah</p>
            <div class="h-20"></div>
            <p class="font-bold underline">( {{ auth()->user()->name }} )</p>
            <p class="text-[10px] text-slate-500">{{ auth()->user()->email }}</p>
        </div>
    </div>

</div>
@endsection
