@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}" class="p-2 -ml-2 rounded-lg transition-colors hover:opacity-80" style="color: var(--text-secondary); background: var(--bg-surface-2);" aria-label="Kembali ke Beranda">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold tracking-tight" style="color: var(--text-primary);">Pesanan Saya</h1>
        </div>
        <a href="{{ route('home') }}" class="btn-brand text-xs">
            + Pesan Lagi
        </a>
    </div>

    @if($orders->isEmpty())
        <div class="text-center py-16 rounded-2xl shadow-sm border" style="background-color: var(--bg-surface); border-color: var(--border-default);">
            <div class="w-14 h-14 mx-auto rounded-full flex items-center justify-center mb-4" style="background: var(--bg-surface-2); color: var(--text-muted);">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <h2 class="text-lg font-semibold mb-2" style="color: var(--text-primary);">Belum ada pesanan</h2>
            <p class="text-sm mb-6" style="color: var(--text-muted);">Yuk mulai pesan makanan favoritmu dari kantin!</p>
            <a href="{{ route('home') }}" class="btn-brand px-6 py-2.5 rounded-lg text-xs font-semibold">Lihat Menu</a>
        </div>
    @else
        <div x-data="{
            filter: 'all',
            receiptModalOpen: false,
            receipt: null,
            openReceipt(data) {
                this.receipt = data;
                this.receiptModalOpen = true;
            },
            closeReceipt() {
                this.receiptModalOpen = false;
            },
            printReceipt() {
                window.print();
            },
            matches(status) {
                if (this.filter === 'all') return true;
                if (this.filter === 'active') return ['pending', 'confirmed', 'preparing', 'processing', 'ready'].includes(status);
                if (this.filter === 'history') return ['completed', 'cancelled'].includes(status);
                return true;
            }
        }">
            {{-- Filter Tabs (Semua / Sedang Dimasak / Riwayat Selesai) --}}
            <div class="flex items-center gap-2 overflow-x-auto scrollbar-none mb-5 pb-1">
                <button
                    type="button"
                    @click="filter = 'all'"
                    :class="filter === 'all' ? 'bg-orange-600 text-white font-bold shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800'"
                    class="px-3.5 py-1.5 text-xs rounded-xl transition-all cursor-pointer whitespace-nowrap active:scale-95"
                >
                    Semua ({{ $orders->count() }})
                </button>
                <button
                    type="button"
                    @click="filter = 'active'"
                    :class="filter === 'active' ? 'bg-orange-600 text-white font-bold shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800'"
                    class="px-3.5 py-1.5 text-xs rounded-xl transition-all cursor-pointer whitespace-nowrap flex items-center gap-1.5 active:scale-95"
                >
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Sedang Berjalan ({{ $orders->whereIn('status', ['pending', 'confirmed', 'preparing', 'processing', 'ready'])->count() }})</span>
                </button>
                <button
                    type="button"
                    @click="filter = 'history'"
                    :class="filter === 'history' ? 'bg-orange-600 text-white font-bold shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800'"
                    class="px-3.5 py-1.5 text-xs rounded-xl transition-all cursor-pointer whitespace-nowrap active:scale-95"
                >
                    Riwayat Selesai ({{ $orders->whereIn('status', ['completed', 'cancelled'])->count() }})
                </button>
            </div>

            <div class="space-y-4">
                @foreach($orders as $order)
                    <div
                        x-show="matches('{{ $order->status }}')"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-98"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="rounded-2xl shadow-sm border p-4 sm:p-5 transition-all duration-200"
                        style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);"
                    >
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 pb-4 border-b"
                         style="border-color: var(--border-default);">
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs font-mono font-semibold px-2.5 py-1 rounded-md border"
                                      style="background-color: var(--bg-surface-2); color: var(--text-secondary); border-color: var(--border-default);">
                                    {{ $order->order_number }}
                                </span>
                                @if($order->isTeacherOrder())
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded border"
                                          style="background-color: rgba(59, 130, 246, 0.12); color: #1d4ed8; border-color: rgba(59, 130, 246, 0.3);"
                                          title="Pesanan dari Guru / Tenaga Pendidik">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
                                        <span>GURU</span>
                                    </span>
                                @endif
                                @if($order->isInSchool())
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded border"
                                          style="background-color: rgba(16, 185, 129, 0.1); color: #059669; border-color: rgba(16, 185, 129, 0.25);"
                                          title="Pesanan dibuat dari area sekolah">
                                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span>Area Sekolah</span>
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm font-semibold mt-2.5" style="color: var(--text-primary);">Stand: {{ $order->tenant->name }}</p>
                            <p class="text-xs mt-0.5" style="color: var(--text-muted);">{{ $order->created_at->format('d M Y, H:i') }} WIB</p>
                        </div>
                        <div class="flex flex-col items-start sm:items-end gap-2">
                            @if($order->status === 'pending')
                                <span class="status-pill status-pill-pending">Menunggu Diproses</span>
                            @elseif($order->status === 'confirmed')
                                <span class="status-pill status-pill-ready" style="background-color: rgba(59, 130, 246, 0.12); color: #2563EB; border-color: rgba(59, 130, 246, 0.3);">
                                    ✓ Pesanan Telah Dikonfirmasi
                                </span>
                            @elseif($order->status === 'processing' || $order->status === 'preparing')
                                <span class="status-pill status-pill-processing">Sedang Dimasak</span>
                            @elseif($order->status === 'ready')
                                <span class="status-pill status-pill-ready">Siap Diambil</span>
                            @elseif($order->status === 'cancelled')
                                <span class="status-pill status-pill-cancelled">Dibatalkan</span>
                            @else
                                <span class="status-pill status-pill-completed">Selesai</span>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-2 mb-4">
                        @foreach($order->items as $item)
                            <div class="flex justify-between items-start text-xs sm:text-sm gap-2">
                                <span class="flex-1 min-w-0 pr-2" style="color: var(--text-secondary);">{{ $item->quantity }}x {{ $item->product_name }}</span>
                                <span class="font-medium shrink-0" style="color: var(--text-primary);">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Informasi Pesanan Telah Dikonfirmasi --}}
                    @if($order->status === 'confirmed')
                        <div class="mb-4 p-3.5 sm:p-4 rounded-xl border flex items-center gap-3 shadow-xs"
                             style="background-color: rgba(59, 130, 246, 0.08); border-color: rgba(59, 130, 246, 0.25);">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-base shrink-0 shadow-2xs"
                                 style="background: #FFFFFF; color: #2563EB; border: 1px solid rgba(59, 130, 246, 0.25);">
                                ✓
                            </div>
                            <div>
                                <span class="text-xs font-black uppercase tracking-wider" style="color: #2563EB;">
                                    Pesanan Telah Dikonfirmasi Penjual
                                </span>
                                <p class="text-xs mt-0.5" style="color: var(--text-secondary);">
                                    Pesanan Anda telah diterima oleh <b>{{ $order->tenant->name ?? 'Kantin' }}</b> dan segera masuk ke antrian dapur untuk disiapkan.
                                </p>
                            </div>
                        </div>
                    @endif

                    {{-- Informasi Alasan Penolakan / Pembatalan yang Menarik --}}
                    @if($order->status === 'cancelled')
                        <div class="mb-4 p-3.5 sm:p-4 rounded-xl border flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs"
                             style="background-color: var(--status-danger-bg); border-color: var(--status-danger-border);">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 shadow-2xs mt-0.5"
                                     style="background: #FFFFFF; color: var(--status-danger); border: 1px solid var(--status-danger-border);">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                </div>
                                <div>
                                    <span class="text-xs font-black uppercase tracking-wider" style="color: var(--status-danger);">
                                        Pesanan Ditolak / Dibatalkan
                                    </span>
                                    <p class="text-xs sm:text-sm mt-1 font-semibold" style="color: var(--text-primary);">
                                        Alasan: <span class="italic font-bold" style="color: var(--status-danger);">"{{ $order->cancel_reason ?: 'Stok menu / bahan utama sedang tidak tersedia.' }}"</span>
                                    </p>
                                    <p class="text-[0.7rem] sm:text-xs mt-1" style="color: var(--text-secondary);">
                                        Mohon maaf atas ketidaknyamanannya. Anda dapat memilih sajian lezat lainnya dari stand kantin yang sedang buka!
                                    </p>
                                </div>
                            </div>

                            <a href="{{ route('home') }}"
                               class="btn-brand text-xs px-3.5 py-1.5 rounded-lg font-bold shrink-0 self-start sm:self-auto shadow-xs flex items-center gap-1.5">
                                <span>Pilih Menu Lain</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    @endif

                    <div class="flex items-center justify-between pt-3 border-t" style="border-color: var(--border-default);">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium" style="color: var(--text-secondary);">Total:</span>
                            <span class="text-base font-bold" style="color: var(--brand);">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                @click="openReceipt({
                                    orderNumber: '{{ $order->order_number }}',
                                    date: '{{ $order->created_at->format('d M Y, H:i') }} WIB',
                                    customerName: '{{ addslashes($order->customer_name ?: 'Siswa') }}',
                                    customerClass: '{{ addslashes($order->customer_class ?: '-') }}',
                                    tenantName: '{{ addslashes($order->tenant->name ?? 'Kantin Sekolah') }}',
                                    paymentMethod: '{{ strtoupper($order->payment_method) }}',
                                    statusText: '{{ $order->status === 'completed' ? 'SELESAI' : ($order->status === 'cancelled' ? 'BATAL' : 'AKTIF') }}',
                                    locationStatus: '{{ $order->location_status }}',
                                    isInSchool: {{ $order->isInSchool() ? 'true' : 'false' }},
                                    isTeacherOrder: {{ $order->isTeacherOrder() ? 'true' : 'false' }},
                                    items: [
                                        @foreach($order->items as $it)
                                        { name: '{{ addslashes($it->product_name) }}', qty: {{ $it->quantity }}, subtotal: {{ (int)$it->subtotal }} },
                                        @endforeach
                                    ],
                                    subtotal: {{ (int)$order->subtotal }},
                                    discount: {{ (int)$order->discount }},
                                    voucherCode: '{{ $order->voucher_code }}',
                                    total: {{ (int)$order->total }}
                                })"
                                class="px-2.5 py-1.5 text-xs font-semibold rounded-lg border flex items-center gap-1.5 transition-all hover:opacity-80 active:scale-95 cursor-pointer shadow-2xs"
                                style="background-color: var(--bg-surface-2); border-color: var(--border-default); color: var(--text-primary);"
                                title="Lihat / Cetak Struk Bukti Pesanan"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Lihat Struk</span>
                            </button>

                            @if($order->status === 'pending')
                                <form action="{{ route('orders.cancel', $order->id) }}" method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');">
                                    @csrf
                                    <button type="submit" class="text-xs font-bold transition-opacity hover:opacity-80 underline cursor-pointer"
                                            style="color: var(--status-danger);">
                                        Batal
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── MODAL STRUK DIGITAL / THERMAL RECEIPT REALISTIS ───────────────── --}}
        <div
            x-show="receiptModalOpen"
            x-cloak
            @keydown.escape.window="closeReceipt()"
            class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/60 backdrop-blur-xs"
        >
            <div
                @click.outside="closeReceipt()"
                x-show="receiptModalOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="w-full max-w-sm max-h-[92vh] flex flex-col rounded-2xl p-4 sm:p-5 shadow-2xl relative overflow-y-auto font-mono border"
                style="background-color: var(--bg-surface); border-color: var(--border-default); color: var(--text-primary);"
                id="printable-receipt"
            >
                {{-- Header Struk --}}
                <div class="text-center pb-3 border-b-2 border-dashed" style="border-color: var(--border-default);">
                    <p class="text-base font-black tracking-wider uppercase">KANTINKITA</p>
                    <p class="text-[11px] opacity-75">Kantin Sekolah Digital</p>
                    <p class="text-[10px] opacity-60 mt-1" x-text="receipt?.date"></p>
                </div>

                {{-- Metadata Pesanan --}}
                <div class="py-2.5 border-b border-dashed text-xs space-y-1" style="border-color: var(--border-default);">
                    <div class="flex justify-between">
                        <span class="opacity-70">No. Order:</span>
                        <span class="font-bold" x-text="receipt?.orderNumber"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="opacity-70">Pemesan:</span>
                        <span class="font-bold flex items-center gap-1.5 text-right">
                            <span x-text="receipt?.customerName"></span>
                            <template x-if="receipt?.isTeacherOrder">
                                <span class="text-[9px] px-1 py-0.5 rounded bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-300 font-black border border-blue-200">GURU</span>
                            </template>
                            <span class="opacity-80" x-text="'(' + receipt?.customerClass + ')'"></span>
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="opacity-70">Stand Kantin:</span>
                        <span class="font-bold" x-text="receipt?.tenantName"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="opacity-70">Metode Bayar:</span>
                        <span class="font-bold uppercase" x-text="receipt?.paymentMethod"></span>
                    </div>
                    <template x-if="receipt?.isInSchool">
                        <div class="flex justify-between text-emerald-600 dark:text-emerald-400 font-bold">
                            <span class="opacity-70">Lokasi:</span>
                            <span class="flex items-center gap-1">
                                <span>✓</span>
                                <span>Area Sekolah (Valid)</span>
                            </span>
                        </div>
                    </template>
                </div>

                {{-- Daftar Menu --}}
                <div class="py-3 border-b-2 border-dashed text-xs space-y-1.5" style="border-color: var(--border-default);">
                    <template x-for="item in (receipt?.items || [])" :key="item.name">
                        <div class="flex justify-between items-start">
                            <span class="flex-1 truncate pr-2" x-text="item.qty + 'x ' + item.name"></span>
                            <span class="shrink-0 font-bold" x-text="'Rp ' + item.subtotal.toLocaleString('id-ID')"></span>
                        </div>
                    </template>
                </div>

                {{-- Rincian Biaya --}}
                <div class="py-2.5 border-b border-dashed text-xs space-y-1" style="border-color: var(--border-default);">
                    <div class="flex justify-between">
                        <span class="opacity-70">Subtotal:</span>
                        <span x-text="'Rp ' + (receipt?.subtotal || 0).toLocaleString('id-ID')"></span>
                    </div>
                    <template x-if="receipt?.discount > 0">
                        <div class="flex justify-between text-emerald-600 dark:text-emerald-400 font-bold">
                            <span>Diskon (<span x-text="receipt?.voucherCode"></span>):</span>
                            <span x-text="'-Rp ' + (receipt?.discount || 0).toLocaleString('id-ID')"></span>
                        </div>
                    </template>
                    <div class="flex justify-between text-sm font-black pt-1">
                        <span>TOTAL BAYAR:</span>
                        <span style="color: var(--brand);" x-text="'Rp ' + (receipt?.total || 0).toLocaleString('id-ID')"></span>
                    </div>
                </div>

                {{-- Barcode Simulasi & Footer Struk --}}
                <div class="text-center pt-3 text-[10px] opacity-75">
                    <p class="font-bold tracking-widest uppercase mb-1">|||| | ||||| || |||||| | |||||</p>
                    <p>Terima kasih sudah jajan di KantinKita!</p>
                    <p class="mt-0.5">Tunjukkan struk ini ke stand kantin saat mengambil.</p>
                </div>

                {{-- Tombol Aksi Modal (Disembunyikan saat cetak) --}}
                <div class="mt-4 pt-3 border-t flex items-center justify-end gap-2 print:hidden" style="border-color: var(--border-default);">
                    <button
                        type="button"
                        @click="closeReceipt()"
                        class="px-3 py-1.5 rounded-lg border text-xs font-semibold hover:opacity-80 cursor-pointer"
                        style="border-color: var(--border-default);"
                    >
                        Tutup
                    </button>
                    <button
                        type="button"
                        @click="printReceipt()"
                        class="btn-brand px-3.5 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 cursor-pointer shadow-xs"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Struk</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
