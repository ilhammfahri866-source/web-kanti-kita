@extends('layouts.dashboard')

@section('title', 'Kelola Voucher')

@push('head')
<script>
    // Pastikan voucherManager tersedia secara global sebelum Alpine menginisialisasi komponen
    function voucherManager() {
        return {
            showCreateForm: false,
            showEditModal: false,
            showDeleteModal: false,
            filterStatus: 'all',

            editingVoucher: {
                id: null,
                code: '',
                name: '',
                discount_percent: 20,
                expires_at: ''
            },

            deletingVoucher: {
                id: null,
                code: '',
                name: '',
                discount: 0,
                claims: 0
            },

            openEditModal(voucher) {
                this.editingVoucher = {
                    id: voucher.id,
                    code: voucher.code,
                    name: voucher.name,
                    discount_percent: voucher.discount_percent,
                    expires_at: voucher.expires_at_input
                };
                this.showEditModal = true;
            },

            confirmDeleteVoucher(voucher) {
                this.deletingVoucher = { ...voucher };
                this.showDeleteModal = true;
            }
        };
    }
</script>
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8" x-data="voucherManager()">

    {{-- ── 1. HEADER & AKSI UTAMA ─────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold mb-2 border shadow-2xs"
                 style="background: var(--brand-subtle); border-color: var(--brand-border); color: var(--brand);">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
                <span>Promo & Diskon Kantin</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight" style="color: var(--text-primary);">
                Kelola Voucher Promo
            </h1>
            <p class="text-xs sm:text-sm mt-1" style="color: var(--text-muted);">
                Buat kode voucher baru, ubah besaran diskon, atur masa berlaku, dan pantau penggunaan voucher.
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <button
                type="button"
                @click="showCreateForm = !showCreateForm"
                class="btn-brand text-xs font-bold py-2.5 px-4 rounded-xl shadow-md transition-all hover:scale-[1.02] flex items-center gap-1.5 cursor-pointer"
            >
                <svg x-show="!showCreateForm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <svg x-show="showCreateForm" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span x-text="showCreateForm ? 'Tutup Form' : '+ Buat Voucher Baru'"></span>
            </button>

            @php
                $expiredCount = $vouchers->filter->isExpired()->count();
            @endphp
            @if($expiredCount > 0)
                <form action="{{ route('dashboard.vouchers.destroy-expired') }}" method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin membersihkan semua {{ $expiredCount }} voucher yang sudah kedaluwarsa?');">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="btn-secondary text-xs font-bold py-2.5 px-3.5 rounded-xl border flex items-center gap-1.5 transition-colors cursor-pointer"
                        style="color: var(--status-danger); border-color: var(--status-danger-border);"
                        title="Hapus massal voucher yang sudah lewat tanggal berlakunya"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Bersihkan {{ $expiredCount }} Kedaluwarsa</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- ── 2. FLASH NOTIFICATIONS ─────────────────────────────────────────── --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition
             class="mb-6 p-4 rounded-xl border text-sm font-semibold flex items-center justify-between backdrop-blur-md shadow-sm"
             style="background-color: var(--status-success-bg); color: var(--status-success); border-color: var(--status-success-border);">
            <div class="flex items-center gap-2.5">
                <span class="text-base font-black">✓</span>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="text-sm font-bold opacity-70 hover:opacity-100 cursor-pointer">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div x-data="{ show: true }" x-show="show" x-transition
             class="mb-6 p-4 rounded-xl border text-xs font-medium backdrop-blur-md shadow-sm"
             style="background-color: var(--status-danger-bg); color: var(--status-danger); border-color: var(--status-danger-border);">
            <div class="font-bold mb-1 flex items-center gap-1.5">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <span>Terjadi kendala pada formulir voucher:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 ml-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ── 3. METRIK RINGKASAN VOUCHER ─────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="rounded-2xl p-4 border shadow-2xs"
             style="background-color: var(--bg-surface); border-color: var(--border-default);">
            <div class="flex items-center justify-between mb-1">
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-wider" style="color: var(--text-muted);">Total Voucher</p>
                <div class="w-6 h-6 rounded-lg flex items-center justify-center text-xs" style="background-color: var(--bg-surface-2); color: var(--text-muted);">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-black" style="color: var(--text-primary);">{{ $vouchers->count() }}</p>
        </div>

        <div class="rounded-2xl p-4 border shadow-2xs"
             style="background-color: var(--bg-surface); border-color: var(--border-default);">
            <div class="flex items-center justify-between mb-1">
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-wider" style="color: var(--status-success);">Voucher Aktif</p>
                <div class="w-6 h-6 rounded-lg flex items-center justify-center text-xs" style="background-color: var(--status-success-bg); color: var(--status-success);">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-black" style="color: var(--status-success);">
                {{ $vouchers->filter(fn($v) => $v->is_active && !$v->isExpired())->count() }}
            </p>
        </div>

        <div class="rounded-2xl p-4 border shadow-2xs"
             style="background-color: var(--bg-surface); border-color: var(--border-default);">
            <div class="flex items-center justify-between mb-1">
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-wider" style="color: var(--status-danger);">Kedaluwarsa</p>
                <div class="w-6 h-6 rounded-lg flex items-center justify-center text-xs" style="background-color: var(--status-danger-bg); color: var(--status-danger);">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-black" style="color: var(--status-danger);">
                {{ $expiredCount }}
            </p>
        </div>

        <div class="rounded-2xl p-4 border shadow-2xs"
             style="background-color: var(--bg-surface); border-color: var(--border-default);">
            <div class="flex items-center justify-between mb-1">
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-wider" style="color: var(--brand);">Total Klaim</p>
                <div class="w-6 h-6 rounded-lg flex items-center justify-center text-xs" style="background-color: var(--brand-subtle); color: var(--brand);">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-black" style="color: var(--brand);">
                {{ $vouchers->sum('claims_count') }}
            </p>
        </div>
    </div>

    {{-- ── 4. FORM BUAT VOUCHER BARU (COLLAPSIBLE) ─────────────────────────── --}}
    <div x-show="showCreateForm" x-cloak x-transition
         class="rounded-2xl border p-5 sm:p-6 mb-6 shadow-md"
         style="background-color: var(--bg-surface); border-color: var(--border-default);">
        <div class="flex items-center justify-between pb-3 border-b mb-4" style="border-color: var(--border-default);">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-md flex items-center justify-center text-xs" style="background-color: var(--brand-subtle); color: var(--brand);">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold" style="color: var(--text-primary);">Form Tambah Voucher Baru</h3>
            </div>
            <button type="button" @click="showCreateForm = false" class="text-xs font-bold opacity-70 hover:opacity-100 cursor-pointer">
                Tutup ✕
            </button>
        </div>

        <form action="{{ route('dashboard.vouchers.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold mb-1.5" style="color: var(--text-secondary);">
                        Kode Voucher <span style="color: var(--status-danger);">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code') }}" required
                           placeholder="Contoh: KANTINHEMAT, JUMATBERKAH"
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border outline-none uppercase font-mono tracking-widest"
                           style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                </div>

                <div>
                    <label class="block text-xs font-bold mb-1.5" style="color: var(--text-secondary);">
                        Nama / Judul Promo <span style="color: var(--status-danger);">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="Contoh: Diskon Makan Siang Pelajar 20%"
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border outline-none"
                           style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                </div>

                <div>
                    <label class="block text-xs font-bold mb-1.5" style="color: var(--text-secondary);">
                        Besaran Diskon (%) <span style="color: var(--status-danger);">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="discount_percent" value="{{ old('discount_percent', 20) }}"
                               min="1" max="100" required placeholder="20"
                               class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-black rounded-xl border outline-none pr-8"
                               style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-sm font-bold" style="color: var(--text-muted);">%</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold mb-1.5" style="color: var(--text-secondary);">
                        Masa Berlaku Sampai <span style="color: var(--status-danger);">*</span>
                    </label>
                    <input type="datetime-local" name="expires_at"
                           value="{{ old('expires_at', now()->addMonths(1)->format('Y-m-d\TH:i')) }}" required
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border outline-none"
                           style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t" style="border-color: var(--border-default);">
                <button type="button" @click="showCreateForm = false"
                        class="btn-secondary px-4 py-2 text-xs font-semibold rounded-xl cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="btn-brand px-5 py-2 text-xs font-extrabold rounded-xl shadow-md cursor-pointer flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Simpan & Aktifkan Voucher</span>
                </button>
            </div>
        </form>
    </div>

    {{-- ── 5. DAFTAR VOUCHER LENGKAP ───────────────────────────────────────── --}}
    <div class="rounded-2xl border overflow-hidden shadow-sm"
         style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">

        {{-- Bar Header & Filter Tab --}}
        <div class="px-5 py-4 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
             style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
            <div>
                <h2 class="text-sm font-bold tracking-tight" style="color: var(--text-primary);">Daftar Voucher Promo Tersedia</h2>
                <p class="text-[11px]" style="color: var(--text-muted);">Kelola status voucher, edit detail promo, atau hapus voucher.</p>
            </div>

            {{-- Filter Status Chips --}}
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none">
                <button
                    type="button"
                    @click="filterStatus = 'all'"
                    class="px-3 py-1 text-xs font-bold rounded-lg border transition-all cursor-pointer whitespace-nowrap"
                    :class="filterStatus === 'all' ? 'category-tab-active' : 'btn-secondary'"
                >
                    Semua ({{ $vouchers->count() }})
                </button>

                <button
                    type="button"
                    @click="filterStatus = 'active'"
                    class="px-3 py-1 text-xs font-bold rounded-lg border transition-all cursor-pointer whitespace-nowrap flex items-center gap-1"
                    :class="filterStatus === 'active' ? 'category-tab-active' : 'btn-secondary'"
                >
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>Aktif</span>
                </button>

                <button
                    type="button"
                    @click="filterStatus = 'expired'"
                    class="px-3 py-1 text-xs font-bold rounded-lg border transition-all cursor-pointer whitespace-nowrap flex items-center gap-1"
                    :class="filterStatus === 'expired' ? 'category-tab-active' : 'btn-secondary'"
                >
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                    <span>Kedaluwarsa</span>
                </button>

                <button
                    type="button"
                    @click="filterStatus = 'inactive'"
                    class="px-3 py-1 text-xs font-bold rounded-lg border transition-all cursor-pointer whitespace-nowrap flex items-center gap-1"
                    :class="filterStatus === 'inactive' ? 'category-tab-active' : 'btn-secondary'"
                >
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                    <span>Nonaktif</span>
                </button>
            </div>
        </div>

        {{-- List Items --}}
        @if($vouchers->isEmpty())
            <div class="py-16 text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-2xl flex items-center justify-center border"
                     style="background-color: var(--bg-surface-2); border-color: var(--border-default); color: var(--text-muted);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                </div>
                <h3 class="font-bold text-sm" style="color: var(--text-primary);">Belum Ada Voucher Promo</h3>
                <p class="text-xs mt-1 mb-4" style="color: var(--text-muted);">Buat voucher diskon pertama Anda untuk memikat lebih banyak pembeli.</p>
                <button
                    type="button"
                    @click="showCreateForm = true"
                    class="btn-brand text-xs font-bold py-2.5 px-4 rounded-xl shadow-md cursor-pointer"
                >
                    + Buat Voucher Sekarang
                </button>
            </div>
        @else
            <div class="divide-y" style="border-color: var(--border-default);">
                @foreach($vouchers as $voucher)
                    @php
                        $isExpired = $voucher->isExpired();
                        $statusCategory = $isExpired ? 'expired' : ($voucher->is_active ? 'active' : 'inactive');
                        $expiresInput = $voucher->expires_at ? $voucher->expires_at->format('Y-m-d\TH:i') : '';
                    @endphp
                    <div
                        x-show="filterStatus === 'all' || filterStatus === '{{ $statusCategory }}'"
                        class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-5 py-4 transition-colors hover:opacity-95"
                        style="border-color: var(--border-default);"
                        id="voucher-card-{{ $voucher->id }}"
                    >
                        {{-- Kiri: Detail Voucher --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                                {{-- Kode Badge --}}
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border font-mono font-black text-xs sm:text-sm tracking-wider shadow-2xs"
                                     style="background-color: var(--bg-surface-2); color: var(--brand); border-color: var(--border-default);">
                                    <span>{{ $voucher->code }}</span>
                                </div>

                                {{-- Status Pill --}}
                                @if($voucher->is_active && !$isExpired)
                                    <span class="text-[11px] font-extrabold px-2.5 py-0.5 rounded-full border shadow-2xs inline-flex items-center gap-1"
                                          style="background-color: var(--status-success-bg); color: var(--status-success); border-color: var(--status-success-border);">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Aktif</span>
                                    </span>
                                @elseif($isExpired)
                                    <span class="text-[11px] font-extrabold px-2.5 py-0.5 rounded-full border shadow-2xs inline-flex items-center gap-1"
                                          style="background-color: var(--status-danger-bg); color: var(--status-danger); border-color: var(--status-danger-border);">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        <span>Kedaluwarsa</span>
                                    </span>
                                @else
                                    <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full border inline-flex items-center gap-1"
                                          style="background-color: var(--bg-surface-2); color: var(--text-muted); border-color: var(--border-default);">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        <span>Nonaktif</span>
                                    </span>
                                @endif
                            </div>

                            {{-- Nama Voucher --}}
                            <p class="text-xs sm:text-sm font-bold" style="color: var(--text-primary);">
                                {{ $voucher->name }}
                            </p>

                            {{-- Info Meta --}}
                            <div class="flex items-center gap-3 mt-2 text-xs flex-wrap" style="color: var(--text-muted);">
                                <span class="font-semibold inline-flex items-center gap-1">
                                    <span>Diskon:</span>
                                    <b class="text-sm font-black" style="color: var(--brand);">{{ $voucher->discount_percent }}%</b>
                                </span>
                                <span>•</span>
                                <span class="inline-flex items-center gap-1">
                                    <span>Berlaku s/d:</span>
                                    <b style="color: var(--text-secondary);">{{ $voucher->expires_at ? $voucher->expires_at->format('d M Y, H:i') : '-' }} WIB</b>
                                    @if(!$isExpired && $voucher->expires_at)
                                        <span class="text-[11px] opacity-75">({{ $voucher->expires_at->diffForHumans() }})</span>
                                    @endif
                                </span>
                                <span>•</span>
                                <span class="inline-flex items-center gap-1">
                                    <span>Total Klaim:</span>
                                    <b style="color: var(--text-secondary);">{{ $voucher->claims_count }} klaim</b>
                                </span>
                            </div>
                        </div>

                        {{-- Kanan: Aksi (Toggle, Edit & Hapus) --}}
                        <div class="flex items-center gap-2 self-start sm:self-auto shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0"
                             style="border-color: var(--border-default);">

                            {{-- 1. Tombol Toggle Aktif/Nonaktif --}}
                            <form action="{{ route('dashboard.vouchers.toggle', $voucher) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button
                                    type="submit"
                                    class="text-xs font-bold px-3 py-2 rounded-xl border transition-all hover:scale-105 cursor-pointer flex items-center gap-1.5 shadow-2xs"
                                    style="{{ $voucher->is_active ? 'background-color: var(--bg-surface-2); color: var(--text-secondary); border-color: var(--border-default);' : 'background-color: var(--status-success-bg); color: var(--status-success); border-color: var(--status-success-border);' }}"
                                    title="{{ $voucher->is_active ? 'Klik untuk nonaktifkan voucher sementara' : 'Klik untuk mengaktifkan voucher' }}"
                                >
                                    @if($voucher->is_active)
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Nonaktifkan</span>
                                    @else
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Aktifkan</span>
                                    @endif
                                </button>
                            </form>

                            {{-- 2. Tombol Edit Voucher --}}
                            <button
                                type="button"
                                @click="openEditModal({
                                    id: {{ $voucher->id }},
                                    code: '{{ addslashes($voucher->code) }}',
                                    name: '{{ addslashes($voucher->name) }}',
                                    discount_percent: {{ $voucher->discount_percent }},
                                    expires_at_input: '{{ $expiresInput }}'
                                })"
                                class="text-xs font-bold px-3 py-2 rounded-xl border transition-all hover:scale-105 cursor-pointer flex items-center gap-1.5 shadow-2xs"
                                style="background-color: var(--bg-surface-2); color: var(--brand); border-color: var(--border-default);"
                                title="Ubah data voucher ini"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>Edit</span>
                            </button>

                            {{-- 3. Tombol Hapus Voucher --}}
                            <button
                                type="button"
                                @click="confirmDeleteVoucher({
                                    id: {{ $voucher->id }},
                                    code: '{{ addslashes($voucher->code) }}',
                                    name: '{{ addslashes($voucher->name) }}',
                                    discount: {{ $voucher->discount_percent }},
                                    claims: {{ $voucher->claims_count }}
                                })"
                                class="text-xs font-bold px-3 py-2 rounded-xl border transition-all hover:scale-105 cursor-pointer flex items-center gap-1.5 shadow-2xs"
                                style="background-color: var(--status-danger-bg); color: var(--status-danger); border-color: var(--status-danger-border);"
                                title="Hapus voucher ini secara permanen"
                                id="btn-delete-voucher-{{ $voucher->id }}"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span>Hapus</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ── 6. MODAL EDIT VOUCHER ───────────────────────────────────────────── --}}
    <div
        x-show="showEditModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
    >
        <div x-show="showEditModal" @click="showEditModal = false"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

        <div
            x-show="showEditModal"
            class="relative w-full max-w-lg rounded-2xl border p-6 shadow-2xl z-10"
            style="background-color: var(--bg-surface); border-color: var(--border-default);"
        >
            <div class="flex items-center justify-between pb-3 border-b mb-4" style="border-color: var(--border-default);">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs" style="background-color: var(--brand-subtle); color: var(--brand);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-extrabold" style="color: var(--text-primary);">Edit Voucher Promo</h3>
                </div>
                <button type="button" @click="showEditModal = false" class="text-xs font-bold opacity-70 hover:opacity-100 cursor-pointer">
                    ✕
                </button>
            </div>

            <form :action="'{{ url('/dashboard/vouchers') }}/' + editingVoucher.id" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-3.5 mb-5">
                    <div>
                        <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                            Kode Voucher <span style="color: var(--status-danger);">*</span>
                        </label>
                        <input type="text" name="code" x-model="editingVoucher.code" required
                               class="w-full px-3.5 py-2 text-xs sm:text-sm rounded-xl border outline-none uppercase font-mono tracking-widest"
                               style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                            Nama / Judul Promo <span style="color: var(--status-danger);">*</span>
                        </label>
                        <input type="text" name="name" x-model="editingVoucher.name" required
                               class="w-full px-3.5 py-2 text-xs sm:text-sm rounded-xl border outline-none"
                               style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                                Diskon (%) <span style="color: var(--status-danger);">*</span>
                            </label>
                            <input type="number" name="discount_percent" x-model="editingVoucher.discount_percent"
                                   min="1" max="100" required
                                   class="w-full px-3.5 py-2 text-xs sm:text-sm font-black rounded-xl border outline-none"
                                   style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                                Berlaku Sampai <span style="color: var(--status-danger);">*</span>
                            </label>
                            <input type="datetime-local" name="expires_at" x-model="editingVoucher.expires_at" required
                                   class="w-full px-3.5 py-2 text-xs sm:text-sm rounded-xl border outline-none"
                                   style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2.5 pt-3 border-t" style="border-color: var(--border-default);">
                    <button type="button" @click="showEditModal = false"
                            class="btn-secondary px-4 py-2 text-xs font-semibold rounded-xl cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="btn-brand px-5 py-2 text-xs font-extrabold rounded-xl shadow-md cursor-pointer flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── 7. MODAL KONFIRMASI HAPUS VOUCHER ──────────────────────────────── --}}
    <div
        x-show="showDeleteModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
    >
        <div x-show="showDeleteModal" @click="showDeleteModal = false"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

        <div
            x-show="showDeleteModal"
            class="relative w-full max-w-md rounded-2xl border p-6 shadow-2xl z-10"
            style="background-color: var(--bg-surface); border-color: var(--border-default);"
        >
            <div class="flex items-start gap-3.5 mb-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                     style="background-color: var(--status-danger-bg); color: var(--status-danger); border: 1px solid var(--status-danger-border);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-extrabold tracking-tight" style="color: var(--text-primary);">
                        Hapus Voucher Promo?
                    </h3>
                    <p class="text-xs mt-0.5" style="color: var(--text-muted);">
                        Voucher yang dihapus tidak akan bisa diklaim lagi oleh siswa.
                    </p>
                </div>
            </div>

            {{-- Ringkasan Voucher yang Akan Dihapus --}}
            <div class="p-3.5 rounded-xl border mb-4 space-y-1 text-xs"
                 style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                <div class="flex justify-between items-center">
                    <span style="color: var(--text-muted);">Kode Voucher:</span>
                    <span class="font-mono font-black text-sm" style="color: var(--brand);" x-text="deletingVoucher.code"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span style="color: var(--text-muted);">Nama Promo:</span>
                    <span class="font-bold truncate max-w-[200px]" style="color: var(--text-primary);" x-text="deletingVoucher.name"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span style="color: var(--text-muted);">Diskon:</span>
                    <span class="font-black" style="color: var(--text-primary);" x-text="deletingVoucher.discount + '%'"></span>
                </div>
                <div class="flex justify-between items-center pt-1 border-t" style="border-color: var(--border-default);">
                    <span style="color: var(--text-muted);">Riwayat Klaim:</span>
                    <span class="font-bold" style="color: var(--status-danger);" x-text="deletingVoucher.claims + ' klaim akan ikut terhapus'"></span>
                </div>
            </div>

            {{-- Form Delete Action --}}
            <form :action="'{{ url('/dashboard/vouchers') }}/' + deletingVoucher.id" method="POST" class="flex justify-end gap-2.5">
                @csrf
                @method('DELETE')
                <button
                    type="button"
                    @click="showDeleteModal = false"
                    class="btn-secondary px-4 py-2 text-xs font-semibold rounded-xl cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="px-5 py-2 text-xs font-extrabold rounded-xl shadow-md transition-all hover:scale-105 cursor-pointer flex items-center gap-1.5"
                    style="background-color: var(--status-danger); color: #ffffff;"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Ya, Hapus Voucher</span>
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
