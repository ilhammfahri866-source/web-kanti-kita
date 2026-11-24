@extends('layouts.app')

@section('title', 'Keranjang Belanja')
@section('meta_description', 'Tinjau dan konfirmasi pesananmu sebelum checkout.')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-8" x-data="cartPage()">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('home') }}" class="p-2 -ml-2 rounded-lg hover:bg-slate-200/50 transition-colors" style="color: var(--text-secondary);" aria-label="Kembali ke Menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <h1 class="text-2xl font-bold tracking-tight" style="color: var(--text-primary);">
            Keranjang Belanja
        </h1>
    </div>

    {{-- State: Keranjang Kosong --}}
    <div x-show="items.length === 0" class="text-center py-16">
        <p class="text-5xl mb-4">🛒</p>
        <h2 class="text-lg font-semibold mb-1" style="color: var(--text-primary);">Keranjangmu masih kosong</h2>
        <p class="text-sm mb-6" style="color: var(--text-muted);">Yuk, pilih menu dari katalog kantin.</p>
        <a href="{{ route('home') }}" class="btn-brand">Lihat Menu</a>
    </div>

    {{-- State: Ada Item --}}
    <div x-show="items.length > 0">

        {{-- Status Kantin Tutup --}}
        @if(!$isOpen)
        <div class="mb-5 rounded-2xl p-4 sm:p-5 flex items-start gap-3.5 border shadow-sm"
             style="background-color: var(--status-danger-bg); border-color: var(--status-danger-border);">
            <span class="text-2xl mt-0.5">🛑</span>
            <div>
                <h2 class="font-bold text-sm sm:text-base" style="color: var(--status-danger);">Kantin Sedang Tutup</h2>
                <p class="text-xs sm:text-sm mt-1 leading-relaxed" style="color: var(--text-secondary);">
                    Pemesanan hanya dibuka saat jam istirahat:
                    <b>Istirahat 1 (09:30 - 10:00 WIB)</b> & <b>Istirahat 2 (12:00 - 13:00 WIB)</b>.
                </p>
                <p class="text-xs mt-1.5 font-medium" style="color: var(--text-muted);">
                    Saat ini pukul {{ $schedule['currentTime'] }} WIB. Pemesanan dibuka kembali: <b style="color: var(--brand);">{{ $schedule['nextSession'] }}</b>.
                </p>
            </div>
        </div>
        @endif

        {{-- Info Kantin --}}
        <div class="mb-4 px-4 py-2.5 rounded-xl text-xs sm:text-sm flex items-center justify-between border" style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
            <div class="flex items-center gap-2">
                <span class="text-base">🍱</span>
                <span style="color: var(--text-secondary);">Pesanan Kantin:</span>
                <span class="font-bold" style="color: var(--brand);">KantinKita</span>
            </div>
            <span class="text-xs font-semibold px-2 py-0.5 rounded-md" style="background: var(--brand-subtle); color: var(--brand);">
                Ambil di Kantin
            </span>
        </div>

        {{-- Daftar Item --}}
        <div class="rounded-xl overflow-hidden border mb-4" style="border-color: var(--border-default);">
            <template x-for="(item, id) in itemsMap" :key="id">
                <div class="p-3.5 sm:px-4 sm:py-3.5 border-b last:border-b-0 flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                     style="border-color: var(--border-default); background-color: var(--bg-surface);">
                    {{-- Info Produk: Gambar, Nama, Harga Satuan --}}
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <template x-if="item.image">
                            <img :src="item.image" :alt="item.name" class="w-12 h-12 sm:w-11 sm:h-11 rounded-xl object-cover border shrink-0" style="border-color: var(--border-default);">
                        </template>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold truncate text-gray-900 dark:text-white" x-text="item.name"></p>
                            <p class="text-xs mt-0.5 text-gray-500 dark:text-gray-400"
                               x-text="'Rp ' + item.price.toLocaleString('id-ID') + ' / porsi'"></p>
                        </div>
                    </div>

                    {{-- Kontrol Jumlah & Subtotal & Hapus --}}
                    <div class="flex items-center justify-between sm:justify-end gap-3 sm:gap-4 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100 dark:border-slate-800/60">
                        {{-- Tombol Kurang / Tambah --}}
                        <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                            <button @click="decrease(id)"
                                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg border text-base font-bold flex items-center justify-center leading-none transition-all duration-100 hover:opacity-75 active:scale-95 text-gray-900 dark:text-white"
                                    style="border-color: var(--border-default); background: var(--bg-surface-2);"
                                    :aria-label="'Kurangi ' + item.name">−</button>
                            <span class="w-7 text-center text-sm font-bold text-gray-900 dark:text-white"
                                  x-text="item.quantity"></span>
                            <button @click="increase(id)"
                                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg border text-base font-bold flex items-center justify-center leading-none transition-all duration-100 hover:opacity-75 active:scale-95 text-gray-900 dark:text-white"
                                    style="border-color: var(--border-default); background: var(--bg-surface-2);"
                                    :aria-label="'Tambah ' + item.name">+</button>
                        </div>

                        {{-- Subtotal & Hapus --}}
                        <div class="flex items-center sm:flex-col sm:items-end gap-2.5 sm:gap-1 shrink-0">
                            <span class="text-sm font-bold sm:w-24 text-right shrink-0 text-gray-900 dark:text-white"
                                  x-text="'Rp ' + (item.price * item.quantity).toLocaleString('id-ID')"></span>
                            <button @click="removeItem(id)" 
                                    class="text-xs flex items-center gap-1 transition-opacity hover:opacity-80 cursor-pointer"
                                    style="color: var(--status-danger);"
                                    aria-label="Batalkan Pesanan">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span>Hapus</span>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Ringkasan & Voucher Promo --}}
        <div class="rounded-xl border p-4 mb-6 shadow-sm" style="border-color: var(--border-default); background-color: var(--bg-surface);">
            {{-- Kartu Status Voucher Promo Aktif --}}
            <template x-if="activeVoucher">
                <div class="mb-3.5 p-3 rounded-xl border border-dashed border-emerald-600/50 bg-emerald-50/70 dark:bg-emerald-950/30 flex items-center justify-between text-xs sm:text-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        <div>
                            <span class="font-bold text-emerald-800 dark:text-emerald-300" x-text="'Voucher ' + activeVoucher.code + ' Aktif'"></span>
                            <span class="block text-[0.7rem] text-emerald-700/80 dark:text-emerald-400" x-text="'Diskon ' + activeVoucher.discount_percent + '% diterapkan pada pesanan ini'"></span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-full text-xs font-black bg-emerald-600 text-white" x-text="'-' + activeVoucher.discount_percent + '%'"></span>
                        <button type="button" @click="removeVoucher()" class="text-xs text-rose-500 hover:underline font-semibold cursor-pointer">Batal</button>
                    </div>
                </div>
            </template>

            {{-- Opsi Input Kode Voucher & Klaim Kupon Cepat --}}
            <template x-if="!activeVoucher">
                <div class="mb-3.5 p-3 rounded-xl border border-dashed text-xs space-y-2.5"
                     style="border-color: var(--brand-border); background-color: var(--bg-surface-2);">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5 font-bold" style="color: var(--text-primary);">
                            <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                            <span>Punya Kode Voucher Promo?</span>
                        </div>
                        <button type="button" @click="claimCartVoucher('KANTINHEMAT')" :disabled="claimingVoucher" class="text-[11px] font-bold text-orange-600 hover:underline cursor-pointer">
                            Klaim Cepat 20% &rarr;
                        </button>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="text"
                               x-model="inputVoucherCode"
                               @keydown.enter.prevent="claimCartVoucher(inputVoucherCode)"
                               placeholder="Ketik kode voucher (misal: KANTINHEMAT)"
                               class="flex-1 px-3 py-1.5 text-xs rounded-lg border outline-none uppercase font-mono bg-white dark:bg-slate-800"
                               style="border-color: var(--border-default); color: var(--text-primary);">
                        <button type="button"
                                @click="claimCartVoucher(inputVoucherCode)"
                                :disabled="claimingVoucher || !inputVoucherCode.trim()"
                                class="btn-brand text-xs px-3.5 py-1.5 rounded-lg font-bold shadow-xs disabled:opacity-50 cursor-pointer">
                            <span x-text="claimingVoucher ? '...' : 'Terapkan'"></span>
                        </button>
                    </div>
                </div>
            </template>

            <div class="flex justify-between text-sm mb-2" style="color: var(--text-secondary);">
                <span>Subtotal (<span x-text="totalItems"></span> item)</span>
                <span x-text="'Rp ' + subtotal.toLocaleString('id-ID')"></span>
            </div>

            {{-- Baris Diskon Voucher --}}
            <template x-if="activeVoucher && discount > 0">
                <div class="flex justify-between text-sm mb-2 font-semibold" style="color: var(--status-success);">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        <span>Diskon Voucher (<span x-text="activeVoucher.code"></span>)</span>
                    </span>
                    <span x-text="'-Rp ' + discount.toLocaleString('id-ID')"></span>
                </div>
            </template>

            <div class="border-t pt-3 mt-2 flex justify-between font-bold" style="border-color: var(--border-default);">
                <span style="color: var(--text-primary);">Total Bayar</span>
                <span style="color: var(--brand);" x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
            </div>
        </div>

        {{-- Banner Wajib Login untuk Siswa --}}
        @guest
        <div class="mb-6 p-4 sm:p-5 rounded-2xl border shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
             style="background: linear-gradient(135deg, rgba(234, 88, 12, 0.08) 0%, rgba(245, 158, 11, 0.05) 100%); border-color: rgba(234, 88, 12, 0.3);">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl flex items-center justify-center shrink-0 bg-orange-100 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400 font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                </div>
                <div>
                    <h2 class="font-extrabold text-sm sm:text-base text-slate-900 dark:text-white">
                        Siswa Wajib Masuk Akun untuk Memesan
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 mt-0.5 leading-relaxed">
                        Silakan masuk ke akun siswa sebelum checkout. Belum punya akun? Pendaftaran akun baru cepat dan gratis.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0 w-full sm:w-auto">
                <a href="{{ route('login') }}?redirect=cart"
                   class="btn-brand text-xs sm:text-sm px-4 py-2.5 rounded-xl font-bold flex-1 sm:flex-initial text-center shadow-xs">
                    Masuk Akun
                </a>
                <a href="{{ route('register') }}?redirect=cart"
                   class="text-xs sm:text-sm px-4 py-2.5 rounded-xl font-bold border border-orange-500/40 text-orange-600 dark:text-orange-400 bg-white dark:bg-slate-800 hover:bg-orange-50 dark:hover:bg-slate-700 flex-1 sm:flex-initial text-center transition-colors">
                    Daftar Akun
                </a>
            </div>
        </div>
        @else
        <div class="mb-5 px-4 py-3 rounded-xl border flex items-center justify-between gap-2 text-xs"
             style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center border text-xs"
                     style="background-color: var(--bg-surface); border-color: var(--border-default); color: var(--brand);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    @if(auth()->user()->isTeacher())
                        <span style="color: var(--text-muted);">Masuk sebagai:</span>
                        <span class="font-bold ml-1" style="color: var(--text-primary);">{{ auth()->user()->name }}</span>
                        <span class="ml-1 px-2 py-0.5 rounded-md font-bold text-[10.5px] bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                            Guru • {{ auth()->user()->classroom ?? 'Ruang Guru' }}
                        </span>
                    @else
                        <span style="color: var(--text-muted);">Masuk sebagai Siswa:</span>
                        <span class="font-bold ml-1" style="color: var(--text-primary);">{{ auth()->user()->name }}</span>
                        <span class="ml-1 px-2 py-0.5 rounded-md font-semibold text-[10px] bg-orange-100 dark:bg-orange-950/50 text-orange-600 dark:text-orange-400">
                            {{ auth()->user()->classroom ?? 'Siswa' }}
                        </span>
                    @endif
                </div>
            </div>
            <span class="text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1">
                <span>✓</span>
                <span class="hidden sm:inline">Terverifikasi</span>
            </span>
        </div>
        @endguest

        {{-- Data Pembeli (Terkunci Otomatis dari Akun) --}}
        <div class="mb-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="customer-name" class="block text-sm font-medium" style="color: var(--text-secondary);">
                        Nama Pemesan <span style="color: var(--status-danger);">*</span>
                    </label>
                    @auth
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Terkunci dari Akun</span>
                    </span>
                    @endauth
                </div>
                <input
                    type="text"
                    id="customer-name"
                    name="customer_name"
                    x-model="customerName"
                    @auth readonly @endauth
                    required
                    placeholder="Contoh: Budi Santoso"
                    class="w-full px-3 py-2.5 text-sm rounded-lg border outline-none transition-colors duration-150 @auth opacity-85 cursor-not-allowed font-semibold @endauth"
                    style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);"
                >
            </div>
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="customer-class" class="block text-sm font-medium" style="color: var(--text-secondary);">
                        {{ auth()->check() && auth()->user()->isTeacher() ? 'Ruangan / Posisi Guru' : 'Kelas & Jurusan' }} <span style="color: var(--status-danger);">*</span>
                    </label>
                    @if(auth()->check() && auth()->user()->isTeacher())
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-950/60 px-2 py-0.5 rounded-full border border-blue-200 dark:border-blue-800">
                        <span>Bisa ubah lokasi saat ini</span>
                    </span>
                    @elseif(auth()->check() && !empty(auth()->user()->classroom))
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Terkunci dari Akun</span>
                    </span>
                    @endif
                </div>
                <div class="relative">
                    <select
                        id="customer-class"
                        name="customer_class"
                        x-model="customerClass"
                        @if(auth()->check() && !auth()->user()->isTeacher() && !empty(auth()->user()->classroom)) disabled @endif
                        required
                        class="w-full pl-3 pr-8 py-2.5 text-sm rounded-lg border outline-none transition-colors duration-150 cursor-pointer appearance-none @if(auth()->check() && !auth()->user()->isTeacher() && !empty(auth()->user()->classroom)) opacity-85 cursor-not-allowed font-semibold @endif"
                        style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);"
                    >
                        <option value="" disabled :selected="!customerClass">-- {{ auth()->check() && auth()->user()->isTeacher() ? 'Pilih Ruangan / Posisi' : 'Pilih Kelas / Ruangan' }} --</option>

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
                        <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-xs" style="color: var(--text-muted);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </span>
                </div>
            </div>
        </div>

        {{-- ── WIDGET VERIFIKASI LOKASI SEKOLAH (GEOFENCE RADAR) ───────────── --}}
        <div class="mb-6 p-4 rounded-xl border transition-all shadow-xs"
             :style="locationStatus === 'in_school' 
                ? 'background-color: rgba(16, 185, 129, 0.08); border-color: rgba(16, 185, 129, 0.35);' 
                : (locationStatus === 'outside' 
                    ? 'background-color: rgba(239, 68, 68, 0.08); border-color: rgba(239, 68, 68, 0.35);' 
                    : 'background-color: var(--bg-surface-2); border-color: var(--border-default);')">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-2xs mt-0.5"
                         :style="locationStatus === 'in_school' 
                            ? 'background: #10B981; color: #FFFFFF;' 
                            : (locationStatus === 'outside' ? 'background: #EF4444; color: #FFFFFF;' : 'background: var(--brand-subtle); color: var(--brand);')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h4 class="text-xs sm:text-sm font-extrabold" style="color: var(--text-primary);">
                                Verifikasi Lokasi Kantin
                            </h4>
                            <template x-if="locationStatus === 'in_school'">
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-black px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                    <span>Terverifikasi di Area Sekolah</span>
                                </span>
                            </template>
                            <template x-if="locationStatus === 'outside'">
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-black px-2.5 py-0.5 rounded-full bg-red-100 text-red-800 border border-red-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                                    <span>Di Luar Lingkungan Sekolah</span>
                                </span>
                            </template>
                            <template x-if="locationStatus === 'checking'">
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-stone-100 text-stone-600 border border-stone-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                    <span>Membaca GPS Lokasi...</span>
                                </span>
                            </template>
                        </div>
                        <p class="text-xs mt-1 font-medium" style="color: var(--text-secondary);" x-text="locationMessage"></p>
                    </div>
                </div>

                <button
                    type="button"
                    @click="detectLocation()"
                    class="btn-secondary text-xs font-bold py-2 px-3 rounded-lg border self-start sm:self-center shrink-0 flex items-center gap-1.5 transition-all cursor-pointer shadow-2xs hover:scale-102"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Cek Ulang Lokasi</span>
                </button>
            </div>
        </div>

        {{-- Catatan --}}
        <div class="mb-6">
            <label for="cart-notes" class="block text-sm font-medium mb-1.5" style="color: var(--text-secondary);">
                Catatan Pesanan (opsional)
            </label>
            <textarea
                id="cart-notes"
                x-model="notes"
                rows="2"
                placeholder="Contoh: tidak pedas, porsi banyak, dll."
                class="w-full px-3 py-2.5 text-sm rounded-lg border outline-none resize-none transition-colors duration-150"
                style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);"
            ></textarea>
        </div>

        {{-- Metode Pembayaran --}}
        <div class="mb-8">
            <label class="block text-sm font-medium mb-3" style="color: var(--text-secondary);">
                Metode Pembayaran <span style="color: var(--status-danger);">*</span>
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label class="flex items-center gap-3 p-3.5 border rounded-xl cursor-pointer transition-all duration-150"
                       :style="paymentMethod === 'cash' ? 'border-color: var(--brand); background-color: var(--brand-subtle);' : 'border-color: var(--border-default); background-color: var(--bg-surface);'">
                    <input type="radio" x-model="paymentMethod" value="cash" style="accent-color: var(--brand);" class="w-4 h-4">
                    <div>
                        <span class="block text-sm font-bold" style="color: var(--text-primary);">Uang Tunai (Cash)</span>
                        <span class="block text-xs" style="color: var(--text-muted);">Bayar di kasir kantin</span>
                    </div>
                </label>
                <label class="flex items-center gap-3 p-3.5 border rounded-xl cursor-pointer transition-all duration-150"
                       :style="paymentMethod === 'qris' ? 'border-color: var(--brand); background-color: var(--brand-subtle);' : 'border-color: var(--border-default); background-color: var(--bg-surface);'">
                    <input type="radio" x-model="paymentMethod" value="qris" style="accent-color: var(--brand);" class="w-4 h-4">
                    <div>
                        <span class="block text-sm font-bold text-gray-900 dark:text-white">QRIS</span>
                        <span class="block text-xs" style="color: var(--text-muted);">Pembayaran Digital</span>
                    </div>
                </label>
            </div>
            
            {{-- Muncul foto QRIS jika dipilih --}}
            <div x-show="paymentMethod === 'qris'" x-transition class="mt-4">
                <div class="p-5 rounded-xl border flex flex-col items-center justify-center shadow-sm"
                     style="background-color: var(--bg-surface); border-color: var(--border-default);">
                    <span class="font-bold mb-3 text-sm tracking-wide" style="color: var(--brand);">Scan QRIS di Bawah Ini</span>
                    <div class="p-3 bg-white rounded-xl flex justify-center items-center shadow-sm mb-3 border border-gray-100 mx-auto" style="width: 280px; max-width: 100%;">
                        <img src="{{ asset('images/qris.jpg') }}?t={{ time() }}" alt="Foto QRIS" class="w-full h-auto object-contain">
                    </div>
                    <span class="text-xs text-center leading-relaxed" style="color: var(--text-secondary);">
                        Pastikan untuk mentransfer sesuai <b>Total</b>, lalu tekan <b>Pesan Sekarang</b>.
                    </span>
                </div>
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex flex-col sm:flex-row gap-3">
            <button @click="clearCart()"
                    class="flex-1 py-2.5 text-sm font-medium rounded-lg border transition-colors duration-150 hover:opacity-70"
                    style="border-color: var(--border-default); color: var(--text-secondary); background: transparent;">
                Kosongkan
            </button>
            <a href="{{ route('home') }}"
               class="flex-1 py-2.5 text-sm font-medium rounded-lg border text-center transition-colors duration-150 hover:opacity-70"
               style="border-color: var(--border-default); color: var(--text-secondary); background: transparent;">
                + Tambah Menu
            </a>
            @if($isOpen)
                <button
                    class="flex-2 py-3 px-6 btn-brand rounded-xl text-sm font-extrabold justify-center shadow-md transition-all hover:scale-[1.02] active:scale-95"
                    @click="checkout()"
                    id="btn-checkout"
                >
                    Pesan Sekarang
                </button>
            @else
                <button
                    type="button"
                    disabled
                    class="flex-2 py-2.5 px-6 rounded-lg text-sm justify-center font-bold opacity-60 cursor-not-allowed border"
                    style="background-color: var(--bg-surface-2); color: var(--text-muted); border-color: var(--border-default);"
                    title="Kantin sedang tutup di luar jam istirahat"
                >
                    Kantin Tutup
                </button>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function cartPage() {
        return {
            itemsMap: {},
            notes: '',
            isLoggedIn: @json(auth()->check()),
            customerName: @json(auth()->check() ? auth()->user()->name : ''),
            customerClass: @json(auth()->check() ? (auth()->user()->display_classroom ?? auth()->user()->classroom ?? '') : ''),
            paymentMethod: 'cash',
            activeVoucher: @json($activeVoucher ?? null),
            inputVoucherCode: '',

            get items() {
                return Object.entries(this.itemsMap);
            },
            get tenantNames() {
                const names = Object.values(this.itemsMap).map(i => i.tenantName);
                return [...new Set(names)].filter(Boolean);
            },
            get totalItems() {
                return Object.values(this.itemsMap).reduce((s, i) => s + i.quantity, 0);
            },
            get subtotal() {
                return Object.values(this.itemsMap).reduce((s, i) => s + (i.price * i.quantity), 0);
            },
            get discount() {
                if (!this.activeVoucher || !this.activeVoucher.discount_percent) return 0;
                return Math.round((this.subtotal * this.activeVoucher.discount_percent) / 100);
            },
            get total() {
                return Math.max(0, this.subtotal - this.discount);
            },

            init() {
                this.itemsMap = window.getCart ? window.getCart() : {};
                if (!this.isLoggedIn) {
                    const savedName = localStorage.getItem('ekantin_customer_name') || '';
                    const savedClass = localStorage.getItem('ekantin_customer_class') || '';
                    if (savedName) this.customerName = savedName;
                    if (savedClass) this.customerClass = savedClass;
                }
                if (!this.activeVoucher) {
                    const saved = sessionStorage.getItem('ekantin_active_voucher');
                    if (saved) {
                        try {
                            this.activeVoucher = JSON.parse(saved);
                        } catch(e) {}
                    }
                }
            },

            claimingVoucher: false,

            async claimCartVoucher(customCode = null) {
                const codeToClaim = (customCode || this.inputVoucherCode || 'KANTINHEMAT').trim().toUpperCase();
                if (!codeToClaim) {
                    Swal.fire({
                        title: 'Kode Kosong',
                        text: 'Silakan ketik kode voucher promo terlebih dahulu.',
                        icon: 'info',
                        confirmButtonColor: '#ea580c'
                    });
                    return;
                }

                if (this.claimingVoucher) return;
                this.claimingVoucher = true;

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                    const res = await fetch('{{ route("voucher.claim") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ code: codeToClaim })
                    });
                    const data = await res.json();

                    if (res.status === 419 || (data.message && data.message.toLowerCase().includes('csrf'))) {
                        Swal.fire({
                            title: 'Sesi Kedaluwarsa',
                            text: 'Sesi Anda telah berakhir demi keamanan. Halaman akan dimuat ulang.',
                            icon: 'warning',
                            confirmButtonColor: '#ea580c',
                            confirmButtonText: 'Muat Ulang'
                        }).then(() => {
                            window.location.reload();
                        });
                        return;
                    }

                    if (res.ok && data.success) {
                        this.activeVoucher = data.voucher;
                        this.inputVoucherCode = '';
                        sessionStorage.setItem('ekantin_active_voucher', JSON.stringify(data.voucher));
                        Swal.fire({
                            title: 'Voucher Aktif',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#ea580c',
                            confirmButtonText: 'Gunakan'
                        });
                    } else if (data.require_login) {
                        Swal.fire({
                            title: 'Perlu Masuk Akun',
                            text: data.message,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#ea580c',
                            cancelButtonColor: '#78716C',
                            confirmButtonText: 'Masuk Sekarang',
                            cancelButtonText: 'Nanti'
                        }).then((r) => {
                            if (r.isConfirmed) {
                                window.location.href = data.login_url || '{{ route("login") }}';
                            }
                        });
                    } else {
                        Swal.fire({
                            title: data.expired ? 'Voucher Kedaluwarsa' : (data.already_claimed ? 'Klaim Ditolak' : 'Gagal Klaim'),
                            text: data.message,
                            icon: data.already_claimed ? 'info' : 'error',
                            confirmButtonColor: '#ea580c',
                            confirmButtonText: 'Tutup'
                        });
                        if (data.already_claimed && data.voucher) {
                            this.activeVoucher = data.voucher;
                        }
                    }
                } catch (e) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Gagal menghubungi server.',
                        icon: 'error',
                        confirmButtonColor: '#ea580c'
                    });
                } finally {
                    this.claimingVoucher = false;
                }
            },

            removeVoucher() {
                this.activeVoucher = null;
                sessionStorage.removeItem('ekantin_active_voucher');
                Swal.fire({
                    title: 'Voucher Dibatalkan',
                    text: 'Potongan voucher telah dilepas dari pesanan ini.',
                    icon: 'info',
                    timer: 1500,
                    showConfirmButton: false
                });
            },

            save() {
                sessionStorage.setItem('ekantin_cart', JSON.stringify(this.itemsMap));
                window.refreshCartBadge && window.refreshCartBadge();
            },

            increase(id) {
                this.itemsMap[id].quantity++;
                this.itemsMap = { ...this.itemsMap }; // trigger Alpine reactivity
                this.save();
            },

            decrease(id) {
                if (this.itemsMap[id].quantity > 1) {
                    this.itemsMap[id].quantity--;
                } else {
                    delete this.itemsMap[id];
                }
                this.itemsMap = { ...this.itemsMap }; // trigger Alpine reactivity
                this.save();
            },

            removeItem(id) {
                Swal.fire({
                    title: 'Batalkan pesanan?',
                    text: 'Yakin ingin membatalkan pesanan item ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, batalkan!',
                    cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        delete this.itemsMap[id];
                        this.itemsMap = { ...this.itemsMap }; // trigger Alpine reactivity
                        this.save();
                        Swal.fire({
                            title: 'Dibatalkan!',
                            text: 'Pesanan berhasil dibatalkan.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            },

            clearCart() {
                Swal.fire({
                    title: 'Kosongkan keranjang?',
                    text: 'Yakin ingin mengosongkan keranjang belanja Anda?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, kosongkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.itemsMap = {};
                        sessionStorage.removeItem('ekantin_cart');
                        window.refreshCartBadge && window.refreshCartBadge();
                        Swal.fire({
                            title: 'Dikosongkan!',
                            text: 'Keranjang telah dikosongkan.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            },

            checkout() {
                if (Object.keys(this.itemsMap).length === 0) return;
                
                // Cek apakah pengguna sudah login sebelum checkout
                if (!this.isLoggedIn) {
                    Swal.fire({
                        title: 'Masuk ke Akun Anda',
                        html: `
                            <div class="text-left text-xs sm:text-sm text-slate-600 dark:text-slate-300 space-y-2.5 mt-2">
                                <p>Untuk menyelesaikan pesanan di kantin, silakan masuk ke akun Siswa atau Guru terlebih dahulu.</p>
                                <div class="p-3 bg-amber-50 dark:bg-slate-800 rounded-xl border border-amber-200 dark:border-slate-700 text-xs text-amber-900 dark:text-amber-200">
                                    <p class="font-bold mb-0.5">Belum punya akun pemesan?</p>
                                    <p class="text-amber-800/80 dark:text-amber-300/80">Tersedia untuk Siswa dan Guru. Semua menu di keranjang belanja tetap tersimpan.</p>
                                </div>
                            </div>
                        `,
                        icon: 'info',
                        showCancelButton: true,
                        showDenyButton: true,
                        confirmButtonText: 'Masuk Akun',
                        denyButtonText: 'Daftar Akun Baru',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#ea580c',
                        denyButtonColor: '#059669',
                        cancelButtonColor: '#64748b'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{{ route("login") }}?redirect=cart';
                        } else if (result.isDenied) {
                            window.location.href = '{{ route("register") }}?redirect=cart';
                        }
                    });
                    return;
                }

                const items = Object.entries(this.itemsMap).map(([id, item]) => ({
                    id: id,
                    quantity: item.quantity
                }));
                const tenantId = Object.values(this.itemsMap)[0].tenantId;

                if (!this.customerName.trim() || !this.customerClass.trim()) {
                    Swal.fire({
                        title: 'Data Belum Lengkap',
                        text: 'Mohon isi Nama Pemesan dan Kelas terlebih dahulu.',
                        icon: 'warning'
                    });
                    return;
                }

                const btn = event.target;
                const originalText = btn.textContent;
                btn.textContent = 'Memproses...';
                btn.disabled = true;

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                fetch('{{ route("checkout.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        items: items,
                        notes: this.notes,
                        customer_name: this.customerName,
                        customer_class: this.customerClass,
                        payment_method: this.paymentMethod,
                        voucher_code: this.activeVoucher ? this.activeVoucher.code : null
                    })
                })
                .then(res => res.json().then(data => ({ status: res.status, data })))
                .then(({ status, data }) => {
                    if (status === 419 || (data.message && data.message.toLowerCase().includes('csrf'))) {
                        Swal.fire({
                            title: 'Sesi Kedaluwarsa',
                            text: 'Sesi pemesanan Anda telah berakhir demi keamanan. Memuat ulang halaman...',
                            icon: 'warning',
                            confirmButtonColor: '#E05326'
                        }).then(() => {
                            window.location.reload();
                        });
                        btn.textContent = originalText;
                        btn.disabled = false;
                        return;
                    }

                    if (data.success) {
                        this.itemsMap = {};
                        sessionStorage.removeItem('ekantin_cart');
                        sessionStorage.removeItem('ekantin_active_voucher');
                        if(window.refreshCartBadge) window.refreshCartBadge();
                        window.location.href = data.redirect_url;
                    } else if (data.require_login) {
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
                                window.location.href = data.login_url || '{{ route("login.siswa") }}';
                            } else if (result.isDenied) {
                                window.location.href = data.register_url || '{{ route("register") }}';
                            }
                        });
                        btn.textContent = originalText;
                        btn.disabled = false;
                    } else {
                        Swal.fire({
                            title: 'Terjadi Kesalahan!',
                            text: data.message || 'Terjadi kesalahan saat checkout.',
                            icon: 'error'
                        });
                        btn.textContent = originalText;
                        btn.disabled = false;
                    }
                })
                .catch(err => {
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Gagal memproses pesanan. Periksa koneksi internet Anda.',
                        icon: 'error'
                    });
                    btn.textContent = originalText;
                    btn.disabled = false;
                });
            },
        };
    }
</script>
@endpush
