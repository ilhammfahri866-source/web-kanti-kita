@extends('layouts.app')

@section('title', 'Menu Kantin')
@section('meta_description', 'Jelajahi menu makanan dan minuman dari berbagai tenant kantin. Pesan sekarang tanpa antri!')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-5 pb-8">

    {{-- ── 1. CLEAN MODERN FOOD DELIVERY HERO BANNER ─────────────────────── --}}
    {{-- ── 1. AUTHENTIC SCHOOL CANTEEN HERO BANNER ─────────────────────── --}}
    {{-- ── 1. COMPACT RESPONSIVE HERO BANNER ─────────────────────── --}}
    <div class="mb-4 sm:mb-6 p-3.5 sm:p-5 md:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-gradient-to-br from-white via-orange-50/20 to-amber-50/30 dark:from-slate-900 dark:via-slate-900/90 dark:to-orange-950/20 shadow-xs">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3.5 lg:gap-6 items-center">
            
            {{-- Kolom Kiri: Teks Sambutan & Voucher Ringkas --}}
            <div class="lg:col-span-7 space-y-2.5 sm:space-y-3">
                
                {{-- Baris Status & Sapaan --}}
                <div class="flex items-center justify-between gap-2 flex-wrap">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        @auth
                            <h1 class="text-base sm:text-xl lg:text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                                Halo, <span class="text-orange-600 dark:text-orange-400">{{ auth()->user()->name }}</span>
                            </h1>
                            @if(auth()->user()->isStudent())
                                <span class="text-[10px] sm:text-xs font-semibold px-2 py-0.5 rounded-md bg-orange-100 dark:bg-orange-950/60 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-900">
                                    {{ auth()->user()->display_classroom ?? auth()->user()->classroom }}
                                </span>
                            @endif
                        @else
                            <h1 class="text-base sm:text-xl lg:text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                                Kantin Sekolah Digital
                            </h1>
                        @endauth
                    </div>

                    {{-- Status Buka / Tutup Kantin (Sleek Pill) --}}
                    @if($isOpen)
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Buka • {{ $schedule['currentSession'] }}</span>
                        </div>
                    @else
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            <span>Tutup • Buka {{ $schedule['nextSession'] }}</span>
                        </div>
                    @endif
                </div>

                {{-- Tagline Ringkas --}}
                <p class="text-xs text-slate-600 dark:text-slate-300 leading-normal max-w-lg hidden sm:block">
                    Pesan makanan dan minuman hangat langsung dari kelas sebelum bel istirahat berbunyi.
                </p>

                @guest
                    <div class="flex items-center gap-2 pt-0.5">
                        <a href="{{ route('login.siswa') }}" class="btn-brand text-xs py-1.5 px-3 rounded-lg font-bold">
                            Masuk Siswa
                        </a>
                        <a href="{{ route('register') }}" class="btn-secondary text-xs py-1.5 px-3 rounded-lg font-bold">
                            Daftar Akun
                        </a>
                    </div>
                @endguest

                {{-- Voucher Promo Strip Ringkas (1 Baris, Responsif, Tanpa Emoji) --}}
                @php
                    $isClaimed = false;
                    $isUsed = false;
                    if (auth()->check() && isset($voucher) && isset($userVoucherClaim) && $userVoucherClaim) {
                        $isClaimed = true;
                        $isUsed = (bool)$userVoucherClaim->is_used;
                    } elseif (session()->has('active_voucher') && session('active_voucher.code') === 'KANTINHEMAT') {
                        $isClaimed = true;
                    }
                    $isExpired = isset($voucher) && $voucher ? $voucher->isExpired() : false;
                    $discountPercent = isset($voucher) && $voucher ? $voucher->discount_percent : 20;
                @endphp

                <div x-data="voucherClaimWidget({
                        initialClaimed: {{ $isClaimed ? 'true' : 'false' }},
                        isUsed: {{ $isUsed ? 'true' : 'false' }},
                        isExpired: {{ $isExpired ? 'true' : 'false' }},
                        code: 'KANTINHEMAT',
                        discountPercent: {{ $discountPercent }}
                     })">
                    <div class="p-2 sm:p-2.5 rounded-xl border border-orange-200 dark:border-orange-900/60 bg-white/80 dark:bg-slate-900/80 flex items-center justify-between gap-2 shadow-2xs">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="w-6 h-6 rounded-md flex items-center justify-center shrink-0 bg-orange-500 text-white font-black text-[11px]">
                                %
                            </span>
                            <div class="min-w-0 flex items-center gap-1.5 text-xs">
                                <span class="font-bold text-slate-900 dark:text-white truncate">Diskon {{ $discountPercent }}%</span>
                                <span class="font-mono font-bold text-[10px] px-1.5 py-0.2 rounded bg-orange-50 dark:bg-slate-800 text-orange-600 dark:text-orange-400 border border-orange-200 dark:border-orange-800" x-text="code"></span>
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 hidden md:inline">• Otomatis potong di keranjang</span>
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="claimVoucher()"
                            :disabled="loading || isUsed"
                            id="btn-claim-voucher"
                            class="btn-brand text-[11px] font-bold py-1 px-3 rounded-lg shadow-2xs active:scale-95 transition-all cursor-pointer whitespace-nowrap shrink-0"
                            :title="claimed ? 'Voucher diskon aktif' : (isExpired ? 'Voucher telah kedaluwarsa' : 'Klik untuk klaim diskon')"
                        >
                            <template x-if="loading"><span>Memproses...</span></template>
                            <template x-if="!loading && !claimed && !isUsed && !isExpired">
                                <span>Klaim Kupon</span>
                            </template>
                            <template x-if="!loading && claimed && !isUsed">
                                <span>Diskon Aktif</span>
                            </template>
                            <template x-if="!loading && isUsed">
                                <span>Sudah Digunakan</span>
                            </template>
                            <template x-if="!loading && isExpired">
                                <span>Kedaluwarsa</span>
                            </template>
                        </button>
                    </div>
                </div>

            </div>

            {{-- Kolom Kanan: Foto & Suasana Kantin Hero Slider (Desktop Only) --}}
            <div class="lg:col-span-5 relative hidden lg:block"
                 x-data="{
                    slides: [
                        {
                            image: '{{ asset('images/canteen/suasana-kantin.jpg') }}',
                            tag: 'Suasana Kantin Sekolah',
                            title: 'Kantin Sekolah Modern & Bersih',
                            subtitle: 'Area makan nyaman dan tertata rapi'
                        },
                        {
                            image: '{{ asset('images/canteen/stand-makanan.jpg') }}',
                            tag: 'Stand & Gerai Tenant',
                            title: 'Aneka Menu Pilihan',
                            subtitle: 'Pelayanan ramah, higienis, dan cepat'
                        },
                        {
                            image: '{{ asset('images/hero/hero-banner.jpg') }}',
                            tag: 'Rating 4.9 Siswa',
                            title: 'Menu Segar Setiap Hari',
                            subtitle: 'Disiapkan hangat sebelum jam istirahat'
                        }
                    ],
                    active: 0,
                    timer: null,
                    startAutoSlide() {
                        this.timer = setInterval(() => {
                            this.next();
                        }, 5000);
                    },
                    stopAutoSlide() {
                        if (this.timer) clearInterval(this.timer);
                    },
                    next() {
                        this.active = (this.active + 1) % this.slides.length;
                    },
                    prev() {
                        this.active = (this.active - 1 + this.slides.length) % this.slides.length;
                    }
                 }"
                 x-init="startAutoSlide()"
                 @mouseenter="stopAutoSlide()"
                 @mouseleave="startAutoSlide()">

                <div class="relative rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-md group aspect-[16/9] bg-slate-100 dark:bg-slate-800">
                    
                    {{-- Slides Container --}}
                    <template x-for="(slide, index) in slides" :key="index">
                        <div x-show="active === index"
                             x-transition:enter="transition ease-out duration-500"
                             x-transition:enter-start="opacity-0 scale-[1.02]"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-300"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-[0.98]"
                             class="absolute inset-0">
                            <img :src="slide.image"
                                 :alt="slide.title"
                                 class="w-full h-full object-cover"
                                 loading="eager">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-black/30 pointer-events-none"></div>
                        </div>
                    </template>

                    {{-- Chip Atas Kiri (Dynamic Tag) --}}
                    <div class="absolute top-2.5 left-2.5 z-10 px-2.5 py-0.5 rounded-lg bg-black/60 backdrop-blur-md border border-white/20 text-white flex items-center gap-1.5 text-[11px] font-semibold shadow">
                        <span class="w-1.5 h-1.5 rounded-full bg-orange-400 animate-pulse"></span>
                        <span x-text="slides[active].tag"></span>
                    </div>

                    {{-- Navigasi Panah Kiri / Kanan --}}
                    <button type="button"
                            @click="prev()"
                            class="absolute left-2 top-1/2 -translate-y-1/2 z-10 w-7 h-7 rounded-full bg-black/50 hover:bg-black/80 backdrop-blur-md text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all cursor-pointer shadow-xs"
                            title="Foto sebelumnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button type="button"
                            @click="next()"
                            class="absolute right-2 top-1/2 -translate-y-1/2 z-10 w-7 h-7 rounded-full bg-black/50 hover:bg-black/80 backdrop-blur-md text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all cursor-pointer shadow-xs"
                            title="Foto selanjutnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>

                    {{-- Bar CTA Bawah Gambar & Dots --}}
                    <div class="absolute bottom-2 inset-x-2 z-10 p-2.5 rounded-xl bg-slate-900/85 backdrop-blur-md border border-white/10 text-white flex items-center justify-between shadow">
                        <div class="min-w-0 pr-2">
                            <div class="flex items-center gap-2 mb-0.5">
                                <p class="text-[0.65rem] uppercase font-bold text-orange-400 tracking-wider truncate" x-text="slides[active].title"></p>
                                {{-- Dots Indicator --}}
                                <div class="flex items-center gap-1">
                                    <template x-for="(slide, i) in slides" :key="i">
                                        <button type="button"
                                                @click="active = i"
                                                class="h-1.5 rounded-full transition-all cursor-pointer"
                                                :class="active === i ? 'bg-orange-400 w-3' : 'bg-white/40 hover:bg-white/70 w-1.5'">
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <p class="text-[11px] font-medium text-slate-200 truncate" x-text="slides[active].subtitle"></p>
                        </div>
                        <a href="#product-grid" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-orange-500 hover:bg-orange-600 text-white transition-colors flex items-center gap-1 shadow-xs shrink-0">
                            <span>Pilih Menu</span>
                            <span>&darr;</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ── 2. SEARCH & FILTER SECTION (STAND KANTIN & KATEGORI) ───────────── --}}
    <div x-data="catalogFilter()" class="mb-8">

        <div class="space-y-3 mb-4">
            {{-- Search Bar Bersih --}}
            <div class="relative w-full">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input
                    type="search"
                    x-model="search"
                    @keydown.window.prevent.slash="if($event.target.tagName !== 'INPUT' && $event.target.tagName !== 'TEXTAREA') { $refs.searchInput.focus(); }"
                    x-ref="searchInput"
                    placeholder="Cari makanan, minuman segar, camilan, atau dessert... (Tekan '/' untuk cari)"
                    class="w-full pl-10 pr-16 py-2.5 sm:py-3 text-xs sm:text-sm rounded-xl border outline-none transition-all bg-white dark:bg-slate-900 text-slate-900 dark:text-white border-slate-200 dark:border-slate-800 focus:border-orange-500 shadow-2xs"
                    id="menu-search"
                    aria-label="Cari menu makanan"
                >
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center gap-1.5">
                    <button
                        x-show="search.length > 0"
                        @click="search = ''"
                        type="button"
                        class="text-xs px-2 py-0.5 rounded text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer"
                        title="Hapus pencarian"
                    >
                        ✕
                    </button>
                    <span class="hidden sm:inline-block text-[0.65rem] font-bold px-1.5 py-0.5 rounded border border-slate-200 dark:border-slate-800 text-slate-400 bg-slate-50 dark:bg-slate-800">
                        /
                    </span>
                </div>
            </div>

            {{-- Kategori Menu Tabs (Makanan, Minuman, Camilan, Dessert) --}}
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none -mx-4 px-4 sm:mx-0 sm:px-0 pt-0.5" role="tablist" aria-label="Kategori Menu Makanan">
                <template x-for="cat in categories" :key="cat.value">
                    <button
                        type="button"
                        @click="activeCategory = cat.value"
                        :class="activeCategory === cat.value ? 'category-tab-active shadow-xs' : 'category-tab-inactive'"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold rounded-xl border transition-all cursor-pointer whitespace-nowrap shrink-0 active:scale-95"
                        :aria-selected="activeCategory === cat.value"
                    >
                        <span x-text="cat.label"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Section Subheading --}}
        <div class="flex items-center justify-between mb-3.5 pt-1">
            <div class="flex items-center gap-2">
                <h2 class="font-bold text-sm sm:text-base text-slate-900 dark:text-white">
                    Daftar Menu Kantin
                </h2>
                <span class="text-[11px] px-2 py-0.2 rounded-full font-bold bg-slate-100 dark:bg-slate-800 text-orange-600 dark:text-orange-400 border border-slate-200 dark:border-slate-700"
                      x-text="totalFilteredText()">
                </span>
            </div>
            <span class="text-xs hidden sm:inline text-slate-500 dark:text-slate-400">
                Pesan sebelum jam istirahat dimulai
            </span>
        </div>

        {{-- ── 3. GRID PRODUK (4 KOLOM GRID RESPONSIF) ─────────────────────── --}}
        @if($products->isEmpty())
            <div class="text-center py-16 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-1">Belum ada menu tersedia</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Silakan hubungi pengelola kantin atau coba beberapa saat lagi.</p>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-5" id="product-grid">
                @foreach($products as $product)
                    <div
                        class="product-card group flex flex-col justify-between"
                        x-show="matchesFilter('{{ strtolower($product->name) }} {{ strtolower($product->description ?? '') }}', '{{ $product->category }}', {{ $product->is_featured ? 'true' : 'false' }})"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        id="product-{{ $product->id }}"
                    >
                        <div>
                            {{-- Foto Menu Produk --}}
                            <div class="product-card__img aspect-[4/3] relative overflow-hidden rounded-t-xl bg-slate-100 dark:bg-slate-800">
                                @if($product->image)
                                    <img
                                        src="{{ $product->image_url }}"
                                        alt="{{ $product->name }}"
                                        loading="lazy"
                                        decoding="async"
                                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                    >
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-400 text-xs">
                                        Foto Menu
                                    </div>
                                @endif

                                {{-- Subtle Bottom Shadow Overlay --}}
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent pointer-events-none"></div>

                                {{-- Badge Kategori & Status Stok (Pojok Kiri Atas) --}}
                                <div class="absolute top-2 left-2 z-10 flex flex-col gap-1">
                                    @if($product->stock <= 0 || !$product->is_available)
                                        <span class="badge-stock-out text-[10px] px-2 py-0.5">Habis</span>
                                    @elseif($product->stock <= 5)
                                        <span class="badge-stock-low text-[10px] px-2 py-0.5">Sisa {{ $product->stock }}</span>
                                    @else
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-black/60 text-white backdrop-blur-xs">
                                            @if($product->category == 'minuman') Minuman
                                            @elseif($product->category == 'makanan_berat') Makanan
                                            @elseif($product->category == 'dessert') Dessert
                                            @else Camilan @endif
                                        </span>
                                    @endif
                                </div>

                                {{-- Badge Favorit (Pojok Kanan Atas) --}}
                                @if($product->is_featured)
                                    <div class="absolute top-2 right-2 z-10">
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-amber-500 text-white shadow-xs">
                                            Favorit
                                        </span>
                                    </div>
                                @endif

                                {{-- Tag Estimasi Waktu (Pojok Kanan Bawah) --}}
                                <div class="absolute bottom-1.5 right-1.5 z-10">
                                    <span class="text-[10px] font-medium px-1.5 py-0.2 rounded bg-black/60 text-slate-200 backdrop-blur-xs">
                                        5–10 mnt
                                    </span>
                                </div>
                            </div>

                            {{-- Info Produk --}}
                            <div class="p-2.5 sm:p-3">
                                {{-- Nama Produk --}}
                                <h3 class="font-bold text-xs sm:text-sm leading-snug line-clamp-1 text-slate-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors"
                                    title="{{ $product->name }}">
                                    {{ $product->name }}
                                </h3>

                                {{-- Deskripsi Singkat --}}
                                <p class="text-[11px] sm:text-xs mt-0.5 line-clamp-1 leading-normal text-slate-500 dark:text-slate-400">
                                    {{ $product->description ?: 'Menu lezat siap saji hangat dari dapur kantin.' }}
                                </p>
                            </div>
                        </div>

                        {{-- Footer Kartu: Harga & Tombol Pesan --}}
                        <div class="p-2.5 sm:p-3 pt-1 border-t border-slate-100 dark:border-slate-800/60 mt-auto flex flex-col gap-1.5">
                            {{-- Baris Harga Jelas (Tanpa Terpotong) --}}
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-black text-xs sm:text-sm text-slate-900 dark:text-white shrink-0 whitespace-nowrap"
                                      style="color: var(--brand);">
                                    {{ $product->formatted_price }}
                                </span>
                                @if($product->stock > 0 && $product->stock <= 5)
                                    <span class="text-[9.5px] font-bold px-1.5 py-0.2 rounded bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200/60 shrink-0">
                                        Sisa {{ $product->stock }}
                                    </span>
                                @endif
                            </div>

                            @if(!$isOpen)
                                <span class="text-[10.5px] py-1 px-2 rounded-lg font-bold uppercase text-center bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900 w-full block">
                                    Tutup
                                </span>
                            @elseif($product->stock > 0 && $product->is_available)
                                <div class="grid grid-cols-2 gap-1.5 w-full">
                                    <button
                                        type="button"
                                        onclick="handleAddToCart({
                                            productId: {{ $product->id }},
                                            name: {{ json_encode($product->name) }},
                                            price: {{ $product->price }},
                                            tenantId: {{ $product->tenant_id }},
                                            tenantName: {{ json_encode($product->tenant->name) }},
                                            image: {{ json_encode($product->image_url ?? '') }}
                                        }, this)"
                                        class="btn-cart text-[11px] py-1 px-1 rounded-lg font-bold active:scale-95 transition-transform flex items-center justify-center text-center w-full"
                                        id="add-to-cart-{{ $product->id }}"
                                        aria-label="Tambah {{ $product->name }} ke keranjang"
                                        title="Simpan ke keranjang belanja"
                                    >
                                        + Pesan
                                    </button>
                                    <button
                                        type="button"
                                        onclick="openDirectBuyModal({
                                            id: {{ $product->id }},
                                            name: {{ json_encode($product->name) }},
                                            price: {{ $product->price }},
                                            stock: {{ $product->stock }},
                                            tenantId: {{ $product->tenant_id }},
                                            tenantName: {{ json_encode($product->tenant->name) }},
                                            image: {{ json_encode($product->image_url ?? '') }}
                                        })"
                                        class="btn-buy-now text-[11px] py-1 px-1 rounded-lg font-bold active:scale-95 transition-transform flex items-center justify-center text-center w-full"
                                        id="buy-direct-{{ $product->id }}"
                                        aria-label="Beli {{ $product->name }}"
                                        title="Beli langsung"
                                    >
                                        Beli
                                    </button>
                                </div>
                            @else
                                <span class="text-[10.5px] py-1 px-2 rounded-lg font-medium text-center bg-slate-100 dark:bg-slate-800 text-slate-400 w-full block">
                                    Habis
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pesan "Tidak ada hasil filter" --}}
            <div
                x-show="filteredCount === 0"
                class="text-center py-12 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800"
            >
                <div class="w-10 h-10 mx-auto mb-2 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Tidak ada menu yang sesuai dengan pencarian Anda.</p>
                <button @click="search = ''; activeCategory = 'all'"
                        class="mt-3 text-xs hover:underline font-bold text-orange-600 dark:text-orange-400 cursor-pointer">
                    Reset pencarian & filter
                </button>
            </div>
        @endif

    </div>{{-- end x-data catalogFilter --}}

    {{-- ── 5. NOTIFIKASI TOAST TAMBAH KE KERANJANG (HILANG OTOMATIS) ──────── --}}
    <div
        x-data="{
            count: 0,
            visible: false,
            timer: null,
            latestName: '',
            showToast(name = '') {
                if (name) this.latestName = name;
                this.count = window.getCartCount ? window.getCartCount() : this.count;
                this.visible = true;
                if (this.timer) clearTimeout(this.timer);
                // Hilang otomatis setelah 3.5 detik
                this.timer = setTimeout(() => {
                    this.visible = false;
                }, 3500);
            },
            close() {
                this.visible = false;
                if (this.timer) clearTimeout(this.timer);
            }
        }"
        x-init="
            window.addEventListener('item-added-to-cart', (e) => {
                showToast(e.detail?.name || '');
            });
        "
        x-show="visible"
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="opacity-0 translate-y-8 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-8 scale-95"
        class="floating-cart-dock p-3 sm:p-3.5 px-4 flex items-center justify-between gap-3 shadow-2xl"
        x-cloak
    >
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 bg-emerald-500 text-white font-bold text-sm shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white truncate">
                    <span x-text="latestName ? latestName + ' berhasil ditambah' : 'Menu berhasil ditambah'"></span>
                </p>
                <p class="text-[0.6875rem] text-slate-500 dark:text-slate-400 truncate">
                    Total <b class="text-orange-600 dark:text-orange-400" x-text="count"></b> menu di keranjang belanja
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('cart.index') }}"
               class="btn-buy-now text-xs px-3.5 py-1.5 rounded-lg font-bold flex items-center gap-1.5 shadow-sm">
                <span>Lihat Keranjang</span>
                <span>&rarr;</span>
            </a>
            <button
                type="button"
                @click="close()"
                class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors text-xs font-bold cursor-pointer"
                title="Tutup notifikasi"
                aria-label="Tutup notifikasi"
            >
                ✕
            </button>
        </div>
    {{-- ── 5. FLOATING MOBILE CART BAR (NATIVE APP FEEL) ───────────────── --}}
    <div
        x-data="{
            count: 0,
            init() {
                this.count = window.getCartCount ? window.getCartCount() : 0;
            }
        }"
        @cart-updated.window="count = $event.detail.count"
        x-show="count > 0"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-12"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-12"
        class="fixed bottom-4 inset-x-4 z-40 sm:hidden"
    >
        <a href="{{ route('cart.index') }}"
           class="flex items-center justify-between p-3.5 px-4 rounded-2xl bg-orange-600 text-white font-bold shadow-xl shadow-orange-600/30 active:scale-95 transition-all border border-orange-500/50">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-white/20 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <div class="text-left leading-tight">
                    <p class="text-xs font-black" x-text="count + ' Menu di Keranjang'"></p>
                    <p class="text-[10.5px] font-medium text-orange-100">Siap dipesan dan diambil di kantin</p>
                </div>
            </div>
            <span class="text-xs font-bold bg-white/20 px-3 py-1.5 rounded-xl flex items-center gap-1">
                <span>Lihat Keranjang</span>
                <span>&rarr;</span>
            </span>
        </a>
    </div>

</div>
@endsection

@push('modals')
{{-- ══════════════════════════════════════════════════════════════════
     MODAL: BELI LANGSUNG (INSTANT FAST CHECKOUT POPUP)
══════════════════════════════════════════════════════════════════════ --}}
<div
    x-data="directBuyModal()"
    x-show="isOpen"
    x-cloak
    @keydown.escape.window="close()"
    class="relative z-[999]"
    role="dialog"
    aria-modal="true"
    aria-labelledby="direct-buy-title"
>
    {{-- 1. Backdrop Gelap dengan Blur --}}
    <div
        x-show="isOpen"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="close()"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
        aria-hidden="true"
    ></div>

    {{-- 2. Container Panel Modal (Fixed di atas backdrop) --}}
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end sm:items-center justify-center p-0 sm:p-4 text-center">
            <div
                x-show="isOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
                @click.outside="close()"
                class="relative w-full max-w-lg transform overflow-hidden rounded-t-3xl sm:rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-left shadow-2xl transition-all"
            >
                {{-- Handle Bar Khusus Mobile --}}
                <div class="w-12 h-1 rounded-full bg-slate-300 dark:bg-slate-700 mx-auto mt-2.5 mb-1 sm:hidden"></div>

                {{-- Header Modal --}}
                <div class="px-5 py-3.5 sm:py-4 border-b flex items-center justify-between border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/60">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange-100 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <div>
                            <h3 id="direct-buy-title" class="font-bold text-base text-slate-900 dark:text-white">
                                Pesan Langsung
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Pesan cepat tanpa perlu masuk ke keranjang
                            </p>
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="close()"
                        class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer"
                        aria-label="Tutup modal"
                    >
                        ✕
                    </button>
                </div>

                <div class="p-5 max-h-[75vh] overflow-y-auto space-y-4">
                    {{-- Detail Produk Yang Dipilih --}}
                    <div class="flex items-center gap-3.5 p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40">
                        <template x-if="product?.image">
                            <img :src="product.image" :alt="product.name" class="w-16 h-16 rounded-lg object-cover shrink-0 border border-slate-200 dark:border-slate-700">
                        </template>
                        <template x-if="!product?.image">
                            <div class="w-16 h-16 rounded-lg flex items-center justify-center shrink-0 border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 text-slate-400">
                                <svg class="w-6 h-6 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                        </template>
                        <div class="flex-1 min-w-0">
                            <span class="text-[0.6875rem] font-semibold text-orange-600 dark:text-orange-400 block truncate"
                                  x-text="product?.tenantName">
                            </span>
                            <h4 class="font-bold text-sm leading-snug truncate text-slate-900 dark:text-white" x-text="product?.name"></h4>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="font-extrabold text-sm text-orange-600 dark:text-orange-400" x-text="'Rp ' + (product ? product.price.toLocaleString('id-ID') : '0')"></span>
                                <span class="text-[0.6875rem] px-2 py-0.5 rounded-full font-medium bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300"
                                      x-text="'Stok: ' + (product ? product.stock : 0)">
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Stepper Jumlah Porsi --}}
                    <div class="flex items-center justify-between p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                        <div>
                            <span class="block text-xs font-bold text-slate-900 dark:text-white">Jumlah Porsi</span>
                            <span class="block text-[0.6875rem] text-slate-500 dark:text-slate-400">Maksimal sesuai stok tersedia</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                @click="decreaseQty()"
                                :disabled="quantity <= 1"
                                class="w-8 h-8 rounded-lg border border-slate-200 dark:border-slate-700 font-bold text-base flex items-center justify-center transition-all disabled:opacity-30 disabled:cursor-not-allowed hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-800 dark:text-slate-200 cursor-pointer"
                                aria-label="Kurangi porsi"
                            >
                                −
                            </button>
                            <span class="w-8 text-center text-base font-bold text-slate-900 dark:text-white" x-text="quantity"></span>
                            <button
                                type="button"
                                @click="increaseQty()"
                                :disabled="product && quantity >= product.stock"
                                class="w-8 h-8 rounded-lg border border-slate-200 dark:border-slate-700 font-bold text-base flex items-center justify-center transition-all disabled:opacity-30 disabled:cursor-not-allowed hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-800 dark:text-slate-200 cursor-pointer"
                                aria-label="Tambah porsi"
                            >
                                +
                            </button>
                        </div>
                    </div>

                    @auth
                    <div class="p-2.5 px-3 rounded-xl border flex items-center justify-between text-xs"
                         style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            @if(auth()->user()->isTeacher())
                                <span class="text-slate-600 dark:text-slate-300">
                                    Akun Guru: <b class="text-slate-900 dark:text-white">{{ auth()->user()->name }}</b>
                                    <span class="ml-1.5 px-2 py-0.5 rounded-md font-bold text-[10px] bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                        GURU
                                    </span>
                                </span>
                            @else
                                <span class="text-slate-600 dark:text-slate-300">Akun Siswa: <b class="text-slate-900 dark:text-white">{{ auth()->user()->name }}</b></span>
                            @endif
                        </div>
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold text-[11px] flex items-center gap-1">
                            <span>✓</span> Terverifikasi
                        </span>
                    </div>
                    @endauth

                    {{-- Form Identitas Pembeli (Nama & Kelas) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="direct-buy-name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                                    {{ auth()->check() && auth()->user()->isTeacher() ? 'Nama Guru / Pemesan' : 'Nama Pemesan' }} <span class="text-rose-500">*</span>
                                </label>
                                @auth
                                <span class="text-[9.5px] font-bold text-emerald-600 dark:text-emerald-400">Terkunci</span>
                                @endauth
                            </div>
                            <input
                                type="text"
                                id="direct-buy-name"
                                name="customer_name"
                                x-model="customerName"
                                placeholder="{{ auth()->check() && auth()->user()->isTeacher() ? 'Contoh: Bpk. Ahmad Fauzi' : 'Contoh: Budi Santoso' }}"
                                required
                                @auth readonly @endauth
                                class="w-full px-3 py-2 text-xs rounded-lg border outline-none bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:border-orange-500 @auth opacity-85 cursor-not-allowed font-medium @endauth"
                            >
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="direct-buy-class" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                                    {{ auth()->check() && auth()->user()->isTeacher() ? 'Ruangan / Posisi Guru' : 'Kelas & Jurusan' }} <span class="text-rose-500">*</span>
                                </label>
                                @if(auth()->check() && auth()->user()->isTeacher())
                                    <span class="text-[9.5px] font-semibold text-blue-600 dark:text-blue-400">Bisa ubah lokasi</span>
                                @elseif(auth()->check() && !empty(auth()->user()->classroom))
                                    <span class="text-[9.5px] font-bold text-emerald-600 dark:text-emerald-400">Terkunci</span>
                                @endif
                            </div>
                            <div class="relative">
                                <select
                                    id="direct-buy-class"
                                    name="customer_class"
                                    x-model="customerClass"
                                    @if(auth()->check() && !auth()->user()->isTeacher() && !empty(auth()->user()->classroom)) disabled @endif
                                    required
                                    class="w-full pl-3 pr-8 py-2 text-xs rounded-lg border outline-none bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:border-orange-500 cursor-pointer appearance-none @if(auth()->check() && !auth()->user()->isTeacher() && !empty(auth()->user()->classroom)) opacity-85 cursor-not-allowed font-medium @endif"
                                >
                                    <option value="" disabled :selected="!customerClass">-- {{ auth()->check() && auth()->user()->isTeacher() ? 'Pilih Ruangan / Posisi' : 'Pilih Kelas & Jurusan' }} --</option>

                                    {{-- Opsi Khusus Guru & Tenaga Pendidik --}}
                                    <optgroup label="Ruangan Guru & Staf (Pilih Lokasi Pemesan)">
                                        <option value="Guru (Ruang Guru)">Guru (Ruang Guru)</option>
                                        <option value="Guru (Perpustakaan)">Guru (Perpustakaan)</option>
                                        <option value="Guru (Lab Komputer)">Guru (Lab Komputer)</option>
                                        <option value="Guru (Ruang Piket)">Guru (Ruang Piket)</option>
                                        <option value="Guru (Ruang TU / Tata Usaha)">Guru (Ruang TU / Tata Usaha)</option>
                                        <option value="Guru (Ruang Kepala Sekolah)">Guru (Ruang Kepala Sekolah)</option>
                                        <option value="Guru (Bengkel Praktek)">Guru (Bengkel Praktek)</option>
                                        <option value="Guru (Ruang Kelas / Mengajar)">Guru (Ruang Kelas / Mengajar)</option>
                                        <option value="Guru / Staf Karyawan">Guru / Staf Karyawan</option>
                                    </optgroup>

                                    <optgroup label="TO (Teknik Otomotif)">
                                        <option value="10 TO 1">10 TO 1</option>
                                        <option value="10 TO 2">10 TO 2</option>
                                        <option value="10 TO 3">10 TO 3</option>
                                        <option value="11 TO 1">11 TO 1</option>
                                        <option value="11 TO 2">11 TO 2</option>
                                        <option value="11 TO 3">11 TO 3</option>
                                        <option value="12 TO 1">12 TO 1</option>
                                        <option value="12 TO 2">12 TO 2</option>
                                        <option value="12 TO 3">12 TO 3</option>
                                    </optgroup>

                                    <optgroup label="PPLG (Pengembangan Perangkat Lunak & Gim)">
                                        <option value="10 PPLG 1">10 PPLG 1</option>
                                        <option value="10 PPLG 2">10 PPLG 2</option>
                                        <option value="10 PPLG 3">10 PPLG 3</option>
                                        <option value="11 PPLG 1">11 PPLG 1</option>
                                        <option value="11 PPLG 2">11 PPLG 2</option>
                                        <option value="11 PPLG 3">11 PPLG 3</option>
                                        <option value="12 PPLG 1">12 PPLG 1</option>
                                        <option value="12 PPLG 2">12 PPLG 2</option>
                                        <option value="12 PPLG 3">12 PPLG 3</option>
                                    </optgroup>

                                    <optgroup label="BCF (Broadcasting & Perfilman)">
                                        <option value="10 BCF 1">10 BCF 1</option>
                                        <option value="10 BCF 2">10 BCF 2</option>
                                        <option value="10 BCF 3">10 BCF 3</option>
                                        <option value="11 BCF 1">11 BCF 1</option>
                                        <option value="11 BCF 2">11 BCF 2</option>
                                        <option value="11 BCF 3">11 BCF 3</option>
                                        <option value="12 BCF 1">12 BCF 1</option>
                                        <option value="12 BCF 2">12 BCF 2</option>
                                        <option value="12 BCF 3">12 BCF 3</option>
                                    </optgroup>

                                    <optgroup label="TPFL (Teknik Pengelasan & Fabrikasi Logam)">
                                        <option value="10 TPFL 1">10 TPFL 1</option>
                                        <option value="10 TPFL 2">10 TPFL 2</option>
                                        <option value="10 TPFL 3">10 TPFL 3</option>
                                        <option value="11 TPFL 1">11 TPFL 1</option>
                                        <option value="11 TPFL 2">11 TPFL 2</option>
                                        <option value="11 TPFL 3">11 TPFL 3</option>
                                        <option value="12 TPFL 1">12 TPFL 1</option>
                                        <option value="12 TPFL 2">12 TPFL 2</option>
                                        <option value="12 TPFL 3">12 TPFL 3</option>
                                    </optgroup>

                                    <optgroup label="Animasi">
                                        <option value="10 Animasi 1">10 Animasi 1</option>
                                        <option value="10 Animasi 2">10 Animasi 2</option>
                                        <option value="10 Animasi 3">10 Animasi 3</option>
                                        <option value="11 Animasi 1">11 Animasi 1</option>
                                        <option value="11 Animasi 2">11 Animasi 2</option>
                                        <option value="11 Animasi 3">11 Animasi 3</option>
                                        <option value="12 Animasi 1">12 Animasi 1</option>
                                        <option value="12 Animasi 2">12 Animasi 2</option>
                                        <option value="12 Animasi 3">12 Animasi 3</option>
                                    </optgroup>
                                </select>
                                <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-xs text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </span>
                            </div>
                            @if(auth()->check() && auth()->user()->isTeacher())
                                <p class="text-[10px] text-blue-600 dark:text-blue-400 mt-1">Pilih lokasi Anda saat ini (Ruang Guru, Perpustakaan, Lab, dll.)</p>
                            @endif
                        </div>
                    </div>

                    {{-- Catatan Pesanan --}}
                    <div>
                        <label for="direct-buy-notes" class="block text-xs font-semibold mb-1 text-slate-700 dark:text-slate-300">
                            Catatan Pesanan <span class="font-normal text-slate-400">(opsional)</span>
                        </label>
                        <input
                            type="text"
                            id="direct-buy-notes"
                            x-model="notes"
                            placeholder="Contoh: pedas sedang, tanpa sayur, es terpisah, dll."
                            class="w-full px-3 py-2 text-xs rounded-lg border outline-none bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:border-orange-500"
                        >
                    </div>

                    {{-- Pilihan Metode Pembayaran --}}
                    <div>
                        <label class="block text-xs font-semibold mb-2 text-slate-700 dark:text-slate-300">
                            Metode Pembayaran <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <label
                                class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition-colors"
                                :class="paymentMethod === 'cash' ? 'border-orange-500 bg-orange-50/60 dark:bg-orange-950/20' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800'"
                            >
                                <input type="radio" x-model="paymentMethod" value="cash" class="w-3.5 h-3.5 accent-orange-600">
                                <div>
                                    <span class="block text-xs font-bold text-slate-900 dark:text-white">Tunai (Cash)</span>
                                    <span class="block text-[0.6875rem] text-slate-500 dark:text-slate-400">Bayar di kasir tenant</span>
                                </div>
                            </label>
                            <label
                                class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition-colors"
                                :class="paymentMethod === 'qris' ? 'border-orange-500 bg-orange-50/60 dark:bg-orange-950/20' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800'"
                            >
                                <input type="radio" x-model="paymentMethod" value="qris" class="w-3.5 h-3.5 accent-orange-600">
                                <div>
                                    <span class="block text-xs font-bold text-slate-900 dark:text-white">QRIS</span>
                                    <span class="block text-[0.6875rem] text-slate-500 dark:text-slate-400">Scan QR pembayaran</span>
                                </div>
                            </label>
                        </div>

                        {{-- Panel Preview QRIS --}}
                        <div x-show="paymentMethod === 'qris'" x-transition class="mt-3">
                            <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 flex flex-col items-center justify-center text-center">
                                <span class="font-bold text-xs mb-2 text-slate-900 dark:text-white">Scan QRIS Pembayaran</span>
                                <div class="p-2 bg-white rounded-lg shadow-sm mb-2 border border-slate-200 mx-auto" style="width: 200px; max-width: 100%;">
                                    <img src="{{ asset('images/qris.jpg') }}?t={{ time() }}" alt="Barcode QRIS" class="w-full h-auto object-contain">
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-xs leading-relaxed">
                                    Silakan scan dan bayar total <b class="text-orange-600 dark:text-orange-400" x-text="'Rp ' + total.toLocaleString('id-ID')"></b>, lalu klik tombol pesan di bawah.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Informasi Diskon Voucher Promo (Jika Ada Voucher Aktif) --}}
                    <template x-if="activeVoucher">
                        <div class="p-2.5 rounded-lg border border-dashed border-emerald-300 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-1.5">
                                <span>Voucher <b><span x-text="activeVoucher.code"></span></b> Aktif (<span x-text="activeVoucher.discount_percent"></span>% OFF)</span>
                            </div>
                            <span class="font-bold" x-text="'-Rp ' + discount.toLocaleString('id-ID')"></span>
                        </div>
                    </template>

                    {{-- Ringkasan Pembayaran & Total --}}
                    <div class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40 space-y-1.5 text-xs">
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>Subtotal (<span x-text="quantity"></span> porsi)</span>
                            <span x-text="'Rp ' + subtotal.toLocaleString('id-ID')"></span>
                        </div>
                        <template x-if="activeVoucher && discount > 0">
                            <div class="flex justify-between font-semibold text-emerald-600 dark:text-emerald-400">
                                <span>Potongan Voucher Promo</span>
                                <span x-text="'-Rp ' + discount.toLocaleString('id-ID')"></span>
                            </div>
                        </template>
                        <div class="border-t border-slate-200 dark:border-slate-700 pt-2 flex items-center justify-between font-bold text-sm">
                            <span class="text-slate-900 dark:text-white">Total Pembayaran</span>
                            <span class="text-orange-600 dark:text-orange-400 text-base font-extrabold" x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
                        </div>
                    </div>

                    {{-- Alert Pesan Error --}}
                    <div x-show="errorMessage" x-transition class="p-2.5 rounded-lg text-xs font-medium border border-rose-200 dark:border-rose-900 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400"
                         x-text="errorMessage">
                    </div>
                </div>

                {{-- Footer Aksi Modal --}}
                <div class="px-5 py-3.5 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/60 flex items-center justify-end gap-2.5">
                    <button
                        type="button"
                        @click="close()"
                        :disabled="loading"
                        class="px-4 py-2 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 cursor-pointer"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        @click="submitOrder()"
                        :disabled="loading"
                        id="btn-confirm-direct-buy"
                        class="btn-brand text-xs px-5 py-2 rounded-lg font-bold shadow-sm flex items-center gap-1.5 cursor-pointer"
                    >
                        <template x-if="loading">
                            <span>Memproses...</span>
                        </template>
                        <template x-if="!loading">
                            <span>Pesan Sekarang (<span x-text="'Rp ' + total.toLocaleString('id-ID')"></span>)</span>
                        </template>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    // ─── Alpine Component: Catalog Filter ────────────────────────────────────
    function catalogFilter() {
        return {
            search: '',
            activeCategory: 'all',
            filteredCount: {{ $products->count() }},

            categories: [
                { value: 'all',           label: 'Semua Menu' },
                { value: 'makanan_berat', label: 'Makanan Utama' },
                { value: 'minuman',       label: 'Minuman Segar' },
                { value: 'makanan_ringan',label: 'Camilan & Snack' },
                { value: 'dessert',       label: 'Dessert Manis' },
            ],

            matchesFilter(searchStr, category, isFeatured) {
                const q = this.search.trim().toLowerCase();
                const searchMatch = !q || searchStr.toLowerCase().includes(q) || (category && category.toLowerCase().includes(q));
                const catMatch = this.activeCategory === 'all' || category === this.activeCategory;
                return searchMatch && catMatch;
            },

            totalFilteredText() {
                if (this.search.trim().length > 0 || this.activeCategory !== 'all') {
                    return this.filteredCount + ' hasil';
                }
                return this.filteredCount + ' menu';
            },

            init() {
                this.$watch('search', () => this.updateCount());
                this.$watch('activeCategory', () => this.updateCount());
                this.updateCount();
            },

            updateCount() {
                this.$nextTick(() => {
                    const cards = this.$el.querySelectorAll('#product-grid > .product-card');
                    let visible = 0;
                    cards.forEach(card => {
                        if (card.style.display !== 'none') {
                            visible++;
                        }
                    });
                    this.filteredCount = visible;
                });
            },
        };
    }

    // ─── Alpine Component: Product Card Stepper ──────────────────────────────
    function productCardItem(productData) {
        return {
            qty: 0,
            product: productData,

            init() {
                this.syncQty();
                window.addEventListener('cart-updated', () => this.syncQty());
            },

            syncQty() {
                const cart = window.getCart ? window.getCart() : {};
                this.qty = cart[this.product.id] ? cart[this.product.id].quantity : 0;
            },

            add() {
                const item = {
                    productId: this.product.id,
                    name: this.product.name,
                    price: this.product.price,
                    tenantId: this.product.tenantId,
                    tenantName: this.product.tenantName,
                    image: this.product.image
                };
                window.addToCart(item);
                this.syncQty();
                window.refreshCartBadge();
            },

            inc() {
                if (this.qty < this.product.stock) {
                    this.add();
                }
            },

            dec() {
                if (this.qty > 0) {
                    const cart = window.getCart ? window.getCart() : {};
                    if (cart[this.product.id]) {
                        cart[this.product.id].quantity--;
                        if (cart[this.product.id].quantity <= 0) {
                            delete cart[this.product.id];
                        }
                        sessionStorage.setItem('ekantin_cart', JSON.stringify(cart));
                        window.refreshCartBadge();
                    }
                    this.syncQty();
                }
            }
        };
    }

    // ─── Add to Cart Handler ─────────────────────────────────────────────────
    function handleAddToCart(item, btn) {
        const result = window.addToCart(item);

        if (!result.success) {
            // Konfirmasi dari user untuk kosongkan cart
            if (confirm(result.message + '\n\nKosongkan keranjang dan pesan dari ' + item.tenantName + '?')) {
                sessionStorage.removeItem('ekantin_cart');
                window.addToCart(item);
                window.refreshCartBadge();
                showAddedFeedback(btn);
            }
            return;
        }

        window.refreshCartBadge();
        showAddedFeedback(btn);
    }

    // Feedback visual saat item ditambah
    function showAddedFeedback(btn) {
        const original = btn.textContent;
        btn.textContent = '✓ Ditambah';
        btn.disabled = true;
        btn.style.opacity = '0.7';

        setTimeout(() => {
            btn.textContent = original;
            btn.disabled = false;
            btn.style.opacity = '';
        }, 1500);
    }

    // ─── Alpine Component: Voucher Claim Widget ──────────────────────────────
    function voucherClaimWidget(config) {
        return {
            claimed: config.initialClaimed || false,
            isUsed: config.isUsed || false,
            isExpired: config.isExpired || false,
            code: config.code || 'KANTINHEMAT',
            discountPercent: config.discountPercent || 20,
            loading: false,

            async claimVoucher() {
                if (this.loading || this.isUsed) return;

                // 1. Cek masa kedaluwarsa di sisi client
                if (this.isExpired) {
                    Swal.fire({
                        title: 'Voucher Kedaluwarsa',
                        text: 'Maaf, masa aktif voucher promo ' + this.code + ' telah berakhir.',
                        icon: 'error',
                        confirmButtonColor: '#D97706',
                        confirmButtonText: 'Tutup'
                    });
                    return;
                }

                // 2. Jika sudah pernah diklaim sebelumnya dan belum digunakan
                if (this.claimed) {
                    Swal.fire({
                        title: 'Diskon 20% Sudah Aktif',
                        text: 'Voucher ' + this.code + ' sudah tersimpan untuk akun Anda. Potongan 20% otomatis diaplikasikan di keranjang belanja!',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#D97706',
                        cancelButtonColor: '#78716C',
                        confirmButtonText: 'Buka Keranjang',
                        cancelButtonText: 'Lanjut Pilih Menu'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{{ route("cart.index") }}';
                        }
                    });
                    return;
                }

                // 3. Jalankan aksi klaim langsung via AJAX / Fetch API tanpa reload halaman
                this.loading = true;

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                    const response = await fetch('{{ route("voucher.claim") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ code: this.code })
                    });

                    const data = await response.json();

                    if (response.status === 419 || (data.message && data.message.toLowerCase().includes('csrf'))) {
                        Swal.fire({
                            title: 'Sesi Kedaluwarsa',
                            text: 'Sesi keamanan Anda telah berakhir. Halaman akan diperbarui otomatis.',
                            icon: 'warning',
                            confirmButtonColor: '#D97706',
                            confirmButtonText: 'Muat Ulang Halaman'
                        }).then(() => {
                            window.location.reload();
                        });
                        return;
                    }

                    if (response.ok && data.success) {
                        this.claimed = true;
                        if (data.voucher) {
                            sessionStorage.setItem('ekantin_active_voucher', JSON.stringify(data.voucher));
                        }

                        // Notifikasi diskon 20% langsung muncul aktif di layar tanpa reload halaman
                        Swal.fire({
                            title: 'Diskon 20% Aktif',
                            html: `
                                <div class="text-sm mt-1 text-stone-600 dark:text-stone-300">
                                    <p class="font-medium mb-3">${data.message}</p>
                                    <div class="p-3 bg-amber-50 dark:bg-stone-800 rounded-xl border border-amber-200/80 dark:border-stone-700 text-amber-900 dark:text-amber-200 text-xs text-left">
                                        <div class="flex items-center gap-2 font-bold mb-1">
                                            <span>Kupon: ${data.voucher.code} (Diskon ${data.voucher.discount_percent}%)</span>
                                        </div>
                                        <p class="opacity-80">Potongan harga 20% langsung aktif dan otomatis memotong total belanja di keranjang Anda!</p>
                                    </div>
                                </div>
                            `,
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonColor: '#D97706',
                            cancelButtonColor: '#78716C',
                            confirmButtonText: 'Lihat Keranjang',
                            cancelButtonText: 'Lanjut Belanja'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '{{ route("cart.index") }}';
                            }
                        });
                    } else if (data.require_login) {
                        Swal.fire({
                            title: 'Perlu Masuk Akun',
                            text: data.message || 'Silakan masuk ke akun Anda terlebih dahulu untuk mengklaim voucher ini (1 akun hanya bisa klaim 1 kali).',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#D97706',
                            cancelButtonColor: '#78716C',
                            confirmButtonText: 'Masuk Sekarang',
                            cancelButtonText: 'Nanti Saja'
                        }).then((res) => {
                            if (res.isConfirmed) {
                                window.location.href = data.login_url || '{{ route("login") }}';
                            }
                        });
                    } else {
                        // Aturan Bisnis & Validasi Backend: Kedaluwarsa atau Satu Akun Satu Kali
                        if (data.expired) {
                            this.isExpired = true;
                        }
                        if (data.already_claimed) {
                            this.claimed = true;
                            if (data.is_used) {
                                this.isUsed = true;
                            }
                        }

                        Swal.fire({
                            title: data.expired ? 'Voucher Kedaluwarsa' : (data.already_claimed ? 'Klaim Ditolak' : 'Gagal Klaim Voucher'),
                            text: data.message || 'Tidak dapat mengklaim voucher saat ini.',
                            icon: data.already_claimed ? 'warning' : 'error',
                            confirmButtonColor: '#D97706',
                            confirmButtonText: 'Mengerti'
                        });
                    }
                } catch (error) {
                    console.error('Gagal klaim voucher:', error);
                    Swal.fire({
                        title: 'Koneksi Terputus',
                        text: 'Terjadi gangguan saat memproses voucher. Silakan periksa jaringan Anda.',
                        icon: 'error',
                        confirmButtonColor: '#D97706',
                        confirmButtonText: 'OK'
                    });
                } finally {
                    this.loading = false;
                }
            }
        };
    }

    // ─── Alpine Component: Beli Langsung (Direct Instant Buy Modal) ──────────
    function directBuyModal() {
        return {
            isOpen: false,
            product: null,
            quantity: 1,
            isLoggedIn: @json(auth()->check()),
            customerName: @json(auth()->check() ? auth()->user()->name : ''),
            customerClass: @json(auth()->check() ? (auth()->user()->display_classroom ?? auth()->user()->classroom ?? '') : ''),
            notes: '',
            paymentMethod: 'cash',
            activeVoucher: null,
            loading: false,
            errorMessage: '',

            init() {
                // Jika belum login, gunakan data tersimpan di browser jika ada
                if (!this.isLoggedIn) {
                    const savedName = localStorage.getItem('ekantin_customer_name') || '';
                    const savedClass = localStorage.getItem('ekantin_customer_class') || '';
                    if (savedName) this.customerName = savedName;
                    if (savedClass) this.customerClass = savedClass;
                }

                this.checkVoucher();

                // Listen trigger event buka modal dari tombol kartu menu
                window.addEventListener('open-direct-buy', (e) => {
                    this.open(e.detail);
                });
            },

            checkVoucher() {
                const saved = sessionStorage.getItem('ekantin_active_voucher');
                if (saved) {
                    try {
                        this.activeVoucher = JSON.parse(saved);
                    } catch(e) {
                        this.activeVoucher = null;
                    }
                } else {
                    this.activeVoucher = null;
                }
            },

            open(productData) {
                this.product = productData;
                this.quantity = 1;
                this.notes = '';
                this.paymentMethod = 'cash';
                this.errorMessage = '';
                this.checkVoucher();

                if (!this.customerName) {
                    this.customerName = localStorage.getItem('ekantin_customer_name') || '{{ auth()->check() ? addslashes(auth()->user()->name) : '' }}';
                }
                if (!this.customerClass) {
                    this.customerClass = localStorage.getItem('ekantin_customer_class') || '';
                }

                this.isOpen = true;
                document.body.style.overflow = 'hidden';
            },

            close() {
                this.isOpen = false;
                document.body.style.overflow = '';
            },

            increaseQty() {
                if (this.product && this.quantity < this.product.stock) {
                    this.quantity++;
                }
            },

            decreaseQty() {
                if (this.quantity > 1) {
                    this.quantity--;
                }
            },

            get subtotal() {
                if (!this.product) return 0;
                return this.product.price * this.quantity;
            },

            get discount() {
                if (!this.activeVoucher || !this.activeVoucher.discount_percent) return 0;
                return Math.round((this.subtotal * this.activeVoucher.discount_percent) / 100);
            },

            get total() {
                return Math.max(0, this.subtotal - this.discount);
            },

            async submitOrder() {
                if (!this.product) return;

                const name = this.customerName.trim();
                const cls = this.customerClass.trim();

                if (!name || !cls) {
                    this.errorMessage = 'Mohon isi Nama Pemesan dan Kelas terlebih dahulu.';
                    return;
                }

                this.errorMessage = '';
                this.loading = true;

                // Simpan agar pemesanan berikutnya otomatis terisi
                localStorage.setItem('ekantin_customer_name', name);
                localStorage.setItem('ekantin_customer_class', cls);

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                    const response = await fetch('{{ route("checkout.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            items: [{
                                id: this.product.id,
                                quantity: this.quantity
                            }],
                            tenant_id: this.product.tenantId,
                            customer_name: name,
                            customer_class: cls,
                            payment_method: this.paymentMethod,
                            notes: this.notes.trim() || null,
                            voucher_code: this.activeVoucher ? this.activeVoucher.code : null
                        })
                    });

                    const data = await response.json();

                    if (response.status === 419 || (data.message && data.message.toLowerCase().includes('csrf'))) {
                        this.errorMessage = 'Sesi keamanan telah kedaluwarsa. Memuat ulang halaman...';
                        Swal.fire({
                            title: 'Sesi Kedaluwarsa',
                            text: 'Sesi pemesanan Anda telah berakhir demi keamanan. Halaman akan dimuat ulang otomatis.',
                            icon: 'warning',
                            confirmButtonColor: '#E05326',
                            confirmButtonText: 'Muat Ulang Halaman'
                        }).then(() => {
                            window.location.reload();
                        });
                        return;
                    }

                    if (response.ok && data.success) {
                        if (this.activeVoucher) {
                            sessionStorage.removeItem('ekantin_active_voucher');
                        }
                        this.close();

                        Swal.fire({
                            title: 'Pesanan Diterima',
                            text: 'Pesanan langsung diteruskan ke ' + this.product.tenantName,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = data.redirect_url;
                        });
                    } else if (data.require_login) {
                        this.close();
                        Swal.fire({
                            title: 'Perlu Masuk Akun',
                            text: data.message,
                            icon: 'warning',
                            showCancelButton: true,
                            showDenyButton: true,
                            confirmButtonText: 'Masuk Siswa',
                            denyButtonText: 'Daftar Baru',
                            cancelButtonText: 'Batal',
                            confirmButtonColor: '#ea580c',
                            denyButtonColor: '#059669',
                            cancelButtonColor: '#64748b'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = data.login_url || '{{ route("login") }}';
                            } else if (result.isDenied) {
                                window.location.href = data.register_url || '{{ route("register") }}';
                            }
                        });
                    } else {
                        this.errorMessage = data.message || 'Gagal memproses pesanan.';
                        Swal.fire({
                            title: 'Pesanan Gagal',
                            text: data.message || 'Terjadi kesalahan saat memproses pesanan.',
                            icon: 'error',
                            confirmButtonColor: '#E05326'
                        });
                    }
                } catch (err) {
                    console.error('Direct buy error:', err);
                    this.errorMessage = 'Terjadi gangguan jaringan. Silakan coba lagi.';
                    Swal.fire({
                        title: 'Gangguan Koneksi',
                        text: 'Tidak dapat menghubungi server. Periksa jaringan Anda.',
                        icon: 'error',
                        confirmButtonColor: '#E05326'
                    });
                } finally {
                    this.loading = false;
                }
            }
        };
    }

    // Helper pemicu modal beli langsung dari tombol kartu
    function openDirectBuyModal(productData) {
        if (!@json(auth()->check())) {
            Swal.fire({
                title: 'Silakan Masuk ke Akun Anda',
                html: `
                    <div class="text-left text-xs sm:text-sm text-slate-600 dark:text-slate-300 space-y-2.5 mt-2">
                        <p>Untuk memesan menu <b>${productData.name}</b>, silakan masuk ke akun Anda terlebih dahulu.</p>
                        <div class="p-3 bg-amber-50 dark:bg-slate-800 rounded-xl border border-amber-200 dark:border-slate-700 text-xs text-amber-900 dark:text-amber-200">
                            <p class="font-bold mb-0.5">Belum punya akun?</p>
                            <p class="text-amber-800/80 dark:text-amber-300/80">Daftar sekarang gratis! Tersedia pilihan pendaftaran untuk Siswa maupun Bapak/Ibu Guru & Staf.</p>
                        </div>
                    </div>
                `,
                icon: 'info',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: 'Masuk (Login)',
                denyButtonText: 'Daftar Akun Baru',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ea580c',
                denyButtonColor: '#059669',
                cancelButtonColor: '#64748b'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route("login") }}';
                } else if (result.isDenied) {
                    window.location.href = '{{ route("register") }}';
                }
            });
            return;
        }
        window.dispatchEvent(new CustomEvent('open-direct-buy', { detail: productData }));
    }
</script>
@endpush
