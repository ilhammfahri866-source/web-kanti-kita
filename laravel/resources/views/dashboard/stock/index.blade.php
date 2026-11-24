@extends('layouts.dashboard')

@section('title', 'Manajemen Stok Bahan & Produk')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="stockManager()">

    {{-- ── HEADER & AKSI UTAMA ───────────────────────────────────────────── --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold mb-2 border shadow-sm"
                 style="background: var(--brand-subtle); border-color: var(--brand-border); color: var(--brand);">
                <span>Inventaris & Dapur Kantin</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight mb-1" style="color: var(--text-primary);">
                Manajemen Stok Bahan & Produk
            </h1>
            <p class="text-xs sm:text-sm" style="color: var(--text-secondary);">
                Pantau ketersediaan bahan baku dapur kantin, catat belanjaan masuk/pemakaian, dan kelola stok porsi produk jadi.
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            {{-- Tombol Tambah Bahan Baku Baru --}}
            <button
                type="button"
                @click="showCreateIngredientModal = true"
                class="btn-secondary text-xs font-bold py-2.5 px-4 rounded-xl border shadow-sm transition-all hover:scale-[1.02] flex items-center gap-2 cursor-pointer"
            >
                <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Bahan Baku</span>
            </button>

            {{-- Tombol Tambah Produk / Menu Baru (10% CTA Accent) --}}
            <button
                type="button"
                @click="showCreateProductModal = true"
                class="btn-brand text-xs font-bold py-2.5 px-4 rounded-xl shadow-md transition-all hover:scale-[1.02] active:scale-95 flex items-center gap-2 cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Menu Baru</span>
            </button>
        </div>
    </div>

    {{-- ── FLASH ALERTS ─────────────────────────────────────────────────── --}}
    @if(session('success'))
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

    {{-- ── 4 KPI STATISTIK INVENTARIS ────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Card 1: Total Bahan Baku --}}
        <div class="rounded-2xl p-5 border shadow-sm transition-all cursor-pointer"
             @click="activeTab = 'ingredients'; ingredientFilter = 'all'"
             style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs font-bold uppercase tracking-wider" style="color: var(--text-muted);">Total Bahan Baku</h3>
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" style="background: rgba(249, 115, 22, 0.12); color: #ea580c;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
            <p class="text-3xl font-black" style="color: var(--text-primary);">{{ $totalIngredientsCount }}</p>
            <p class="text-xs mt-1 font-medium hover:underline" style="color: var(--brand);">
                Item bahan di gudang & dapur &rarr;
            </p>
        </div>

        {{-- Card 2: Bahan Perlu Belanja (Menipis & Habis) --}}
        <div class="rounded-2xl p-5 border shadow-sm transition-all cursor-pointer"
             @click="activeTab = 'ingredients'; ingredientFilter = 'need_restock'"
             :style="'{{ $outOfStockIngredientsCount + $lowStockIngredientsCount }}' > 0 
                ? 'background-color: var(--status-warning-bg); border-color: var(--status-warning-border);' 
                : 'background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);'">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs font-bold uppercase tracking-wider"
                    style="color: {{ ($outOfStockIngredientsCount + $lowStockIngredientsCount) > 0 ? 'var(--status-warning)' : 'var(--text-muted)' }};">
                    Bahan Perlu Belanja
                </h3>
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                     style="background: {{ ($outOfStockIngredientsCount + $lowStockIngredientsCount) > 0 ? 'rgba(245, 158, 11, 0.15)' : 'rgba(16, 185, 129, 0.12)' }}; color: {{ ($outOfStockIngredientsCount + $lowStockIngredientsCount) > 0 ? '#d97706' : '#059669' }};">
                    @if(($outOfStockIngredientsCount + $lowStockIngredientsCount) > 0)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @endif
                </div>
            </div>
            <p class="text-3xl font-black"
               style="color: {{ ($outOfStockIngredientsCount + $lowStockIngredientsCount) > 0 ? 'var(--status-warning)' : 'var(--status-success)' }};">
                {{ $outOfStockIngredientsCount + $lowStockIngredientsCount }}
            </p>
            <p class="text-xs mt-1 font-bold"
               style="color: {{ ($outOfStockIngredientsCount + $lowStockIngredientsCount) > 0 ? 'var(--status-warning)' : 'var(--status-success)' }};">
                @if($outOfStockIngredientsCount > 0)
                    {{ $outOfStockIngredientsCount }} bahan habis, {{ $lowStockIngredientsCount }} menipis &rarr;
                @elseif($lowStockIngredientsCount > 0)
                    {{ $lowStockIngredientsCount }} bahan menipis &rarr;
                @else
                    Semua stok bahan aman
                @endif
            </p>
        </div>

        {{-- Card 3: Total Menu Produk --}}
        <div class="rounded-2xl p-5 border shadow-sm transition-all cursor-pointer"
             @click="activeTab = 'products'; productFilter = 'all'"
             style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs font-bold uppercase tracking-wider" style="color: var(--text-muted);">Total Menu Produk</h3>
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" style="background: rgba(59, 130, 246, 0.12); color: #2563eb;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
            </div>
            <p class="text-3xl font-black" style="color: var(--brand);">{{ $totalProductsCount }}</p>
            <p class="text-xs mt-1 font-medium hover:underline" style="color: var(--brand);">
                Pilihan menu di etalase &rarr;
            </p>
        </div>

        {{-- Card 4: Menu Porsi Kosong --}}
        <div class="rounded-2xl p-5 border shadow-sm transition-all cursor-pointer"
             @click="activeTab = 'products'; productFilter = 'out_of_stock'"
             :style="'{{ $outOfStockProductsCount }}' > 0 
                ? 'background-color: var(--status-danger-bg); border-color: var(--status-danger-border);' 
                : 'background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);'">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs font-bold uppercase tracking-wider"
                    style="color: {{ $outOfStockProductsCount > 0 ? 'var(--status-danger)' : 'var(--text-muted)' }};">
                    Menu Porsi Kosong
                </h3>
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                     style="background: {{ $outOfStockProductsCount > 0 ? 'rgba(239, 68, 68, 0.15)' : 'rgba(16, 185, 129, 0.12)' }}; color: {{ $outOfStockProductsCount > 0 ? '#dc2626' : '#059669' }};">
                    @if($outOfStockProductsCount > 0)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @endif
                </div>
            </div>
            <p class="text-3xl font-black"
               style="color: {{ $outOfStockProductsCount > 0 ? 'var(--status-danger)' : 'var(--status-success)' }};">
                {{ $outOfStockProductsCount }}
            </p>
            <p class="text-xs mt-1 font-bold"
               style="color: {{ $outOfStockProductsCount > 0 ? 'var(--status-danger)' : 'var(--status-success)' }};">
                @if($outOfStockProductsCount > 0)
                    Menu kehabisan stok! Klik untuk isi porsi &rarr;
                @else
                    Semua menu siap disajikan
                @endif
            </p>
        </div>
    </div>

    {{-- ── TAB NAVIGASI: BAHAN BAKU VS PRODUK ─────────────────────────────── --}}
    <div class="flex items-center gap-2 mb-6 border-b pb-3 overflow-x-auto scrollbar-none" style="border-color: var(--border-default);">
        {{-- Tab Bahan Baku --}}
        <button
            type="button"
            @click="activeTab = 'ingredients'"
            class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm flex items-center gap-2 transition-all cursor-pointer whitespace-nowrap"
            :class="activeTab === 'ingredients' ? 'category-tab-active shadow-sm' : 'category-tab-inactive'"
            id="tab-btn-ingredients"
        >
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <span>Stok Bahan Baku Dapur</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-extrabold"
                  :style="activeTab === 'ingredients' ? 'background: rgba(255,255,255,0.25); color: #ffffff;' : 'background: var(--bg-surface-3); color: var(--text-secondary);'">
                {{ $ingredients->count() }}
            </span>

            @if(($outOfStockIngredientsCount + $lowStockIngredientsCount) > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase animate-pulse shadow-sm"
                      style="background-color: var(--status-warning); color: #ffffff;">
                    {{ $outOfStockIngredientsCount + $lowStockIngredientsCount }} Perlu Belanja
                </span>
            @endif
        </button>

        {{-- Tab Produk Jadi --}}
        <button
            type="button"
            @click="activeTab = 'products'"
            class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm flex items-center gap-2 transition-all cursor-pointer whitespace-nowrap"
            :class="activeTab === 'products' ? 'category-tab-active shadow-sm' : 'category-tab-inactive'"
            id="tab-btn-stock-products"
        >
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <span>Stok Menu Produk Siap Jual</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-extrabold"
                  :style="activeTab === 'products' ? 'background: rgba(255,255,255,0.25); color: #ffffff;' : 'background: var(--bg-surface-3); color: var(--text-secondary);'">
                {{ $products->count() }}
            </span>

            @if($outOfStockProductsCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase animate-pulse shadow-sm"
                      style="background-color: var(--status-danger); color: #ffffff;">
                    {{ $outOfStockProductsCount }} Habis
                </span>
            @endif
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         TAB 1: MANAJEMEN STOK BAHAN BAKU
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'ingredients'" x-transition:enter="transition ease-out duration-150">
        
        {{-- Filter & Search Bar Bahan --}}
        <div class="mb-5 flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
            <div class="relative flex-1 max-w-md">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none"
                     style="color: var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    type="search"
                    x-model="ingredientSearch"
                    placeholder="Cari bahan baku (beras, ayam, minyak, cup...)..."
                    class="w-full pl-10 pr-4 py-2 text-xs sm:text-sm rounded-xl border outline-none transition-colors"
                    style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);"
                >
            </div>

            <div class="flex items-center gap-1.5 flex-wrap">
                <button
                    type="button"
                    @click="ingredientFilter = 'all'"
                    class="px-3 py-1.5 text-xs font-bold rounded-lg border transition-all"
                    :class="ingredientFilter === 'all' ? 'category-tab-active' : 'btn-secondary'"
                >
                    Semua ({{ $ingredients->count() }})
                </button>

                <button
                    type="button"
                    @click="ingredientFilter = 'need_restock'"
                    class="px-3 py-1.5 text-xs font-bold rounded-lg border transition-all flex items-center gap-1.5"
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
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all"
                    :class="ingredientFilter === 'bahan_pokok' ? 'category-tab-active' : 'btn-secondary'"
                >
                    Bahan Pokok
                </button>

                <button
                    type="button"
                    @click="ingredientFilter = 'daging_telur'"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all"
                    :class="ingredientFilter === 'daging_telur' ? 'category-tab-active' : 'btn-secondary'"
                >
                    Daging & Telur
                </button>

                <button
                    type="button"
                    @click="ingredientFilter = 'bumbu_dapur'"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all"
                    :class="ingredientFilter === 'bumbu_dapur' ? 'category-tab-active' : 'btn-secondary'"
                >
                    Bumbu Dapur
                </button>

                <button
                    type="button"
                    @click="ingredientFilter = 'sayuran'"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all"
                    :class="ingredientFilter === 'sayuran' ? 'category-tab-active' : 'btn-secondary'"
                >
                    Sayuran Segar
                </button>

                <button
                    type="button"
                    @click="ingredientFilter = 'minuman'"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all"
                    :class="ingredientFilter === 'minuman' ? 'category-tab-active' : 'btn-secondary'"
                >
                    Bahan Minuman
                </button>

                <button
                    type="button"
                    @click="ingredientFilter = 'kemasan'"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all"
                    :class="ingredientFilter === 'kemasan' ? 'category-tab-active' : 'btn-secondary'"
                >
                    Kemasan & Cup
                </button>

                <button
                    type="button"
                    @click="ingredientFilter = 'lainnya'"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all"
                    :class="ingredientFilter === 'lainnya' ? 'category-tab-active' : 'btn-secondary'"
                >
                    Persediaan Dapur & Gas
                </button>
            </div>
        </div>

        {{-- Tabel Bahan Baku --}}
        <div class="rounded-2xl border shadow-sm overflow-hidden"
             style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
            
            <div class="px-6 py-4 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
                 style="border-color: var(--border-default);">
                <div>
                    <h2 class="font-bold text-base tracking-tight" style="color: var(--text-primary);">
                        Daftar Bahan Baku & Persediaan Dapur
                    </h2>
                    <p class="text-xs" style="color: var(--text-muted);">
                        Catat barang belanjaan yang baru masuk atau pemakaian harian untuk memasak.
                    </p>
                </div>

                <button
                    type="button"
                    @click="showCreateIngredientModal = true"
                    class="btn-brand text-xs font-bold py-2 px-3.5 rounded-xl self-start sm:self-auto flex items-center gap-1.5"
                >
                    <span>+ Tambah Bahan Baru</span>
                </button>
            </div>

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
                            <th class="px-6 py-3.5 text-right">Aksi Restock / Pakai</th>
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
                                id="row-ingredient-{{ $ing->id }}"
                            >
                                {{-- Nama Bahan & Catatan --}}
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
                                    @if($ing->cost_per_unit)
                                        <div class="text-[11px] mt-0.5" style="color: var(--text-muted);">
                                            Rp {{ number_format($ing->cost_per_unit, 0, ',', '.') }} / {{ $ing->unit }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Batas Minimum --}}
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-medium" style="color: var(--text-secondary);">
                                    <span>{{ $ing->min_stock }} {{ $ing->unit }}</span>
                                </td>

                                {{-- Status Bahan --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($ing->is_out_of_stock)
                                        <span class="status-pill status-pill-cancelled animate-pulse">Habis (0)</span>
                                    @elseif($ing->is_low_stock)
                                        <span class="status-pill status-pill-pending">Menipis!</span>
                                    @else
                                        <span class="status-pill status-pill-completed">Aman</span>
                                    @endif
                                </td>

                                {{-- Aksi Cepat Restock / Pemakaian --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-1.5 text-xs font-bold">
                                        {{-- Tombol + Masuk --}}
                                        <button
                                            type="button"
                                            @click="openAdjustModal({
                                                id: {{ $ing->id }},
                                                name: '{{ addslashes($ing->name) }}',
                                                stock: {{ $ing->stock }},
                                                unit: '{{ $ing->unit }}'
                                            }, 'in')"
                                            class="btn-brand text-[11px] py-1 px-2.5 rounded-lg shadow-sm"
                                            title="Tambah stok belanjaan yang baru masuk"
                                        >
                                            + Restock
                                        </button>

                                        {{-- Tombol - Pakai --}}
                                        <button
                                            type="button"
                                            @click="openAdjustModal({
                                                id: {{ $ing->id }},
                                                name: '{{ addslashes($ing->name) }}',
                                                stock: {{ $ing->stock }},
                                                unit: '{{ $ing->unit }}'
                                            }, 'out')"
                                            class="btn-secondary text-[11px] py-1 px-2.5 rounded-lg border transition-opacity hover:opacity-80"
                                            title="Catat pemakaian bahan untuk memasak"
                                        >
                                            - Pakai
                                        </button>

                                        {{-- Tombol Edit --}}
                                        <button
                                            type="button"
                                            @click="openEditIngredientModal({
                                                id: {{ $ing->id }},
                                                name: '{{ addslashes($ing->name) }}',
                                                category: '{{ $ing->category }}',
                                                stock: {{ $ing->stock }},
                                                unit: '{{ $ing->unit }}',
                                                min_stock: {{ $ing->min_stock }},
                                                cost_per_unit: '{{ $ing->cost_per_unit }}',
                                                supplier: '{{ addslashes($ing->supplier ?? '') }}',
                                                notes: '{{ addslashes($ing->notes ?? '') }}'
                                            })"
                                            class="p-1.5 rounded-lg border btn-secondary text-stone-500 hover:text-stone-800"
                                            title="Ubah rincian bahan"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </button>

                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('dashboard.ingredients.destroy', $ing->id) }}" method="POST"
                                              class="inline-block"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus bahan {{ addslashes($ing->name) }} dari gudang?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-red-500 hover:opacity-80" title="Hapus">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    </div>
                                    <h3 class="text-sm font-bold" style="color: var(--text-primary);">Belum Ada Bahan Baku Tercatat</h3>
                                    <p class="text-xs mt-1 mb-4" style="color: var(--text-muted);">Mulai catat bahan baku masakan dan persediaan kantin Anda.</p>
                                    <button
                                        type="button"
                                        @click="showCreateIngredientModal = true"
                                        class="btn-brand text-xs font-bold px-4 py-2 rounded-xl"
                                    >
                                        + Tambah Bahan Baku Sekarang
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-3.5 border-t flex items-center justify-between text-xs"
                 style="border-color: var(--border-default); color: var(--text-muted);">
                <span>Total <b>{{ $ingredients->count() }}</b> jenis bahan baku tercatat.</span>
                <span>Gunakan tombol <b>+ Restock</b> saat belanjaan baru datang.</span>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         TAB 2: STOK MENU PRODUK JADI (SIAP JUAL)
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'products'" x-transition:enter="transition ease-out duration-150">
        
        <div class="mb-5 flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
            <div class="relative flex-1 max-w-md">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none"
                     style="color: var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    type="search"
                    x-model="productSearch"
                    placeholder="Cari menu makanan, minuman..."
                    class="w-full pl-10 pr-4 py-2 text-xs sm:text-sm rounded-xl border outline-none"
                    style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);"
                >
            </div>

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
                    :style="productFilter !== 'out_of_stock' && {{ $outOfStockProductsCount }} > 0 ? 'border-color: var(--status-danger); color: var(--status-danger);' : ''"
                >
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                    <span>Stok Porsi Habis</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black"
                          :style="productFilter === 'out_of_stock' ? 'background: #fff; color: #000;' : 'background: var(--status-danger); color: #fff;'">
                        {{ $outOfStockProductsCount }}
                    </span>
                </button>
            </div>
        </div>

        {{-- Tabel Produk --}}
        <div class="rounded-2xl border shadow-sm overflow-hidden"
             style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
            
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
                                    stock: {{ $product->stock }}
                                })"
                                class="transition-colors hover:opacity-95"
                                style="border-color: var(--border-default);"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($product->image)
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                                 class="w-10 h-10 rounded-lg object-cover border shrink-0"
                                                 style="border-color: var(--border-default);">
                                        @else
                                            <div class="w-10 h-10 rounded-lg border flex items-center justify-center shrink-0"
                                                 style="background-color: var(--bg-surface-2); border-color: var(--border-default); color: var(--text-muted);">
                                                <svg class="w-5 h-5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <div class="font-extrabold text-sm truncate" style="color: var(--text-primary);">
                                                {{ $product->name }}
                                            </div>
                                            <span class="text-xs font-semibold" style="color: var(--brand);">
                                                {{ $product->tenant->name ?? 'Tenant' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-xs font-semibold" style="color: var(--text-secondary);">
                                    {{ $product->category_label }}
                                </td>

                                {{-- Inline Quick Price --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('dashboard.products.quick-update', $product->id) }}" method="POST" class="flex items-center gap-1.5">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="redirect_to" value="dashboard.stock.index">
                                        <input
                                            type="number"
                                            name="price"
                                            value="{{ (int) $product->price }}"
                                            min="0"
                                            step="500"
                                            class="w-24 px-2 py-1 text-xs font-extrabold rounded-lg border outline-none"
                                            style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                                        >
                                        <button type="submit" class="btn-secondary text-[11px] font-bold px-2 py-1 rounded-lg border">
                                            Ubah
                                        </button>
                                    </form>
                                </td>

                                {{-- Inline Quick Stock --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('dashboard.products.quick-update', $product->id) }}" method="POST" class="flex items-center gap-1.5">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="redirect_to" value="dashboard.stock.index">
                                        <input
                                            type="number"
                                            name="stock"
                                            value="{{ $product->stock }}"
                                            min="0"
                                            class="w-16 px-2 py-1 text-xs font-black text-center rounded-lg border outline-none"
                                            :style="'{{ $product->stock }}' <= 0 ? 'background: #FEF2F2; color: #DC2626; border-color: #FECACA;' : 'background: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);'"
                                        >
                                        <button type="submit" class="text-[11px] font-bold px-2.5 py-1 rounded-lg border {{ $product->stock <= 0 ? 'btn-brand shadow-sm' : 'btn-secondary' }}">
                                            {{ $product->stock <= 0 ? 'Isi!' : 'Simpan' }}
                                        </button>
                                    </form>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($product->stock <= 0)
                                        <span class="status-pill status-pill-cancelled">Habis (0)</span>
                                    @elseif($product->is_available)
                                        <span class="status-pill status-pill-ready">Tersedia</span>
                                    @else
                                        <span class="status-pill status-pill-completed">Nonaktif</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <a href="{{ route('products.edit', $product->id) }}" class="font-bold text-xs hover:underline" style="color: var(--brand);">
                                        Edit Lengkap &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-xs" style="color: var(--text-muted);">
                                    Belum ada menu produk terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL 1: TAMBAH BAHAN BAKU BARU
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
                    <p class="text-xs" style="color: var(--text-muted);">Catat bahan persediaan kantin untuk memudahkan kontrol stok.</p>
                </div>
                <button type="button" @click="showCreateIngredientModal = false" class="text-lg font-bold">&times;</button>
            </div>

            <form action="{{ route('dashboard.ingredients.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="redirect_to" value="dashboard.stock.index">

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
                            <option value="lainnya">Lainnya</option>
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
                            Estimasi Modal per Satuan (Rp)
                        </label>
                        <input type="number" name="cost_per_unit" step="100" min="0" placeholder="15000"
                               class="w-full rounded-xl px-3 py-2 text-xs border outline-none"
                               style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                            Supplier / Toko Langganan
                        </label>
                        <input type="text" name="supplier" placeholder="Pasar Induk / Toko Sembako"
                               class="w-full rounded-xl px-3 py-2 text-xs border outline-none"
                               style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                        Catatan Penggunaan (Opsional)
                    </label>
                    <input type="text" name="notes" placeholder="Misal: untuk bumbu ayam & rica-rica"
                           class="w-full rounded-xl px-3 py-2 text-xs border outline-none"
                           style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                </div>

                <div class="pt-4 border-t flex justify-end gap-2.5" style="border-color: var(--border-default);">
                    <button type="button" @click="showCreateIngredientModal = false" class="btn-secondary px-4 py-2 text-xs font-semibold rounded-xl">
                        Batal
                    </button>
                    <button type="submit" class="btn-brand px-5 py-2 text-xs font-extrabold rounded-xl shadow-md">
                        Simpan Bahan Baku
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL 2: PENYESUAIAN STOK BAHAN CEPAT (+ RESTOCK / - PAKAI)
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div
        x-show="showAdjustModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
        role="dialog"
    >
        <div x-show="showAdjustModal" @click="showAdjustModal = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div
            x-show="showAdjustModal"
            class="relative w-full max-w-sm rounded-2xl border p-6 shadow-2xl z-10"
            style="background-color: var(--bg-surface); border-color: var(--border-default);"
        >
            <div class="flex items-center justify-between pb-3 border-b mb-4" style="border-color: var(--border-default);">
                <div>
                    <h3 class="text-base font-bold" style="color: var(--text-primary);"
                        x-text="adjustType === 'in' ? '+ Restock Belanja Masuk' : '- Catat Pemakaian Bahan'"></h3>
                    <p class="text-xs truncate font-bold" style="color: var(--brand);" x-text="adjustItem.name"></p>
                </div>
                <button type="button" @click="showAdjustModal = false" class="text-lg font-bold">&times;</button>
            </div>

            <form :action="'{{ url('/dashboard/ingredients') }}/' + adjustItem.id + '/adjust'" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="redirect_to" value="dashboard.stock.index">
                <input type="hidden" name="type" :value="adjustType">

                <div class="p-3 rounded-xl border text-xs" style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                    <div class="flex justify-between">
                        <span style="color: var(--text-muted);">Stok Saat Ini:</span>
                        <span class="font-extrabold" style="color: var(--text-primary);" x-text="adjustItem.stock + ' ' + adjustItem.unit"></span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);"
                           x-text="adjustType === 'in' ? 'Jumlah Belanjaan Masuk (' + adjustItem.unit + ')' : 'Jumlah Terpakai Memasak (' + adjustItem.unit + ')'">
                    </label>
                    <input
                        type="number"
                        name="amount"
                        step="0.1"
                        min="0.1"
                        required
                        autofocus
                        placeholder="Contoh: 5 atau 2.5"
                        class="w-full rounded-xl px-3.5 py-2.5 text-sm font-black border outline-none"
                        style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                    >
                </div>

                <div class="pt-4 border-t flex justify-end gap-2.5" style="border-color: var(--border-default);">
                    <button type="button" @click="showAdjustModal = false" class="btn-secondary px-4 py-2 text-xs font-medium rounded-xl">
                        Batal
                    </button>
                    <button type="submit" class="btn-brand px-5 py-2 text-xs font-extrabold rounded-xl shadow-md">
                        Simpan Penyesuaian
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL 3: EDIT DETAIL BAHAN BAKU
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div
        x-show="showEditIngredientModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
        role="dialog"
    >
        <div x-show="showEditIngredientModal" @click="showEditIngredientModal = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div
            x-show="showEditIngredientModal"
            class="relative w-full max-w-lg rounded-2xl border p-6 shadow-2xl z-10"
            style="background-color: var(--bg-surface); border-color: var(--border-default);"
        >
            <div class="flex items-center justify-between pb-3 border-b mb-4" style="border-color: var(--border-default);">
                <div>
                    <h3 class="text-base font-bold" style="color: var(--text-primary);">Edit Rincian Bahan Baku</h3>
                    <p class="text-xs truncate font-bold" style="color: var(--brand);" x-text="editIngredientItem.name"></p>
                </div>
                <button type="button" @click="showEditIngredientModal = false" class="text-lg font-bold">&times;</button>
            </div>

            <form :action="'{{ url('/dashboard/ingredients') }}/' + editIngredientItem.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="redirect_to" value="dashboard.stock.index">

                <div>
                    <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">Nama Bahan</label>
                    <input type="text" name="name" x-model="editIngredientItem.name" required
                           class="w-full rounded-xl px-3 py-2 text-xs sm:text-sm border outline-none"
                           style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">Kategori</label>
                        <select name="category" x-model="editIngredientItem.category" required class="w-full rounded-xl px-3 py-2 text-xs border outline-none"
                                style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                            <option value="bahan_pokok">Bahan Pokok</option>
                            <option value="daging_telur">Daging & Telur</option>
                            <option value="bumbu_dapur">Bumbu Dapur</option>
                            <option value="sayuran">Sayuran Segar</option>
                            <option value="minuman">Bahan Minuman</option>
                            <option value="kemasan">Kemasan & Cup</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">Satuan</label>
                        <input type="text" name="unit" x-model="editIngredientItem.unit" required
                               class="w-full rounded-xl px-3 py-2 text-xs border outline-none"
                               style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">Stok Saat Ini</label>
                        <input type="number" name="stock" step="0.1" x-model="editIngredientItem.stock" required
                               class="w-full rounded-xl px-3 py-2 text-xs font-black border outline-none"
                               style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">Batas Minimum</label>
                        <input type="number" name="min_stock" step="0.1" x-model="editIngredientItem.min_stock" required
                               class="w-full rounded-xl px-3 py-2 text-xs font-bold border outline-none"
                               style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                    </div>
                </div>

                <div class="pt-4 border-t flex justify-end gap-2.5" style="border-color: var(--border-default);">
                    <button type="button" @click="showEditIngredientModal = false" class="btn-secondary px-4 py-2 text-xs font-semibold rounded-xl">
                        Batal
                    </button>
                    <button type="submit" class="btn-brand px-5 py-2 text-xs font-extrabold rounded-xl shadow-md">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL 4: TAMBAH MENU PRODUK BARU
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div
        x-show="showCreateProductModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
        role="dialog"
    >
        <div x-show="showCreateProductModal" @click="showCreateProductModal = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div
            x-show="showCreateProductModal"
            class="relative w-full max-w-lg rounded-2xl border p-6 shadow-2xl z-10"
            style="background-color: var(--bg-surface); border-color: var(--border-default);"
        >
            <div class="flex items-center justify-between pb-3 border-b mb-4" style="border-color: var(--border-default);">
                <div>
                    <h3 class="text-base font-bold" style="color: var(--text-primary);">Tambah Menu Produk Baru</h3>
                    <p class="text-xs" style="color: var(--text-muted);">Menu makanan/minuman siap jual di etalase kantin.</p>
                </div>
                <button type="button" @click="showCreateProductModal = false" class="text-lg font-bold">&times;</button>
            </div>

            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="from_dashboard" value="1">
                <input type="hidden" name="redirect_to" value="dashboard.stock.index">

                <div>
                    <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">Nama Menu</label>
                    <input type="text" name="name" required placeholder="Contoh: Nasi Bakar Cumi Pedas"
                           class="w-full rounded-xl px-3 py-2 text-xs sm:text-sm border outline-none"
                           style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">Kategori</label>
                        <select name="category" required class="w-full rounded-xl px-3 py-2 text-xs border outline-none"
                                style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                            <option value="makanan_berat">Makanan Berat</option>
                            <option value="makanan_ringan">Makanan Ringan</option>
                            <option value="minuman">Minuman Segar</option>
                            <option value="dessert">Dessert</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">Harga Jual (Rp)</label>
                        <input type="number" name="price" step="500" min="0" required placeholder="15000"
                               class="w-full rounded-xl px-3 py-2 text-xs font-bold border outline-none"
                               style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">Stok Porsi Awal</label>
                    <input type="number" name="stock" min="0" required value="20"
                           class="w-full rounded-xl px-3 py-2 text-xs font-bold border outline-none"
                           style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                </div>

                <div>
                    <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">Foto Menu (Opsional)</label>
                    <input type="file" name="image" accept="image/*" class="w-full rounded-xl px-3 py-1.5 text-xs border outline-none"
                           style="background-color: var(--bg-surface-2); color: var(--text-secondary); border-color: var(--border-default);">
                </div>

                <div class="pt-4 border-t flex justify-end gap-2.5" style="border-color: var(--border-default);">
                    <button type="button" @click="showCreateProductModal = false" class="btn-secondary px-4 py-2 text-xs font-semibold rounded-xl">
                        Batal
                    </button>
                    <button type="submit" class="btn-brand px-5 py-2 text-xs font-extrabold rounded-xl shadow-md">
                        Simpan Menu Produk
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function stockManager() {
    return {
        activeTab: '{{ request('tab', 'ingredients') }}',
        ingredientSearch: '',
        ingredientFilter: 'all',
        productSearch: '',
        productFilter: 'all',

        showCreateIngredientModal: false,
        showAdjustModal: false,
        showEditIngredientModal: false,
        showCreateProductModal: false,

        adjustType: 'in', // 'in' atau 'out'
        adjustItem: {
            id: null,
            name: '',
            stock: 0,
            unit: 'kg'
        },

        editIngredientItem: {
            id: null,
            name: '',
            category: 'bahan_pokok',
            stock: 0,
            unit: 'kg',
            min_stock: 5,
            cost_per_unit: '',
            supplier: '',
            notes: ''
        },

        openAdjustModal(item, type) {
            this.adjustItem = { ...item };
            this.adjustType = type;
            this.showAdjustModal = true;
        },

        openEditIngredientModal(item) {
            this.editIngredientItem = { ...item };
            this.showEditIngredientModal = true;
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

        filterProduct(item) {
            const query = this.productSearch.toLowerCase().trim();
            const matchesSearch = !query || 
                item.name.toLowerCase().includes(query) || 
                item.tenant.toLowerCase().includes(query);

            let matchesCategory = true;
            if (this.productFilter === 'out_of_stock') {
                matchesCategory = (item.stock <= 0);
            }

            return matchesSearch && matchesCategory;
        }
    };
}
</script>
@endpush
@endsection
