<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Keuangan Kantin — {{ $periodLabel }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])

    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 12mm 12mm 12mm;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: #ffffff;
            color: #0f172a;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .page-break-inside-avoid {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="p-6 max-w-7xl mx-auto">

    {{-- ── BAR AKSI PENGATURAN CETAK (DISEMBUNYIKAN SAAT PRINT / PDF) ────────── --}}
    <div class="no-print mb-6 p-4 bg-slate-900 text-white rounded-2xl flex flex-wrap items-center justify-between gap-3 shadow-xl">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-sm font-bold">Pratinjau Lembar Cetak Laporan & Simpan PDF</h1>
                <p class="text-xs text-slate-400">Siap cetak langsung ke printer atau simpan sebagai dokumen PDF resmi (A4 Landscape).</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button
                type="button"
                onclick="window.print()"
                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl transition-all shadow-md flex items-center gap-2 cursor-pointer active:scale-95"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak / Simpan PDF Sekarang</span>
            </button>

            <a
                href="{{ route('dashboard.reports.index', request()->all()) }}"
                class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl transition-colors"
            >
                Kembali ke Dashboard
            </a>
        </div>
    </div>

    {{-- ── KOP SURAT FORMAL LAPORAN KEUANGAN KANTIN SEKOLAH ──────────────────── --}}
    <div class="border-b-2 border-slate-900 pb-4 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-orange-600 text-white flex items-center justify-center font-black text-xl shadow-sm">
                    KK
                </div>
                <div>
                    <h2 class="text-xl font-black uppercase tracking-tight text-slate-900 leading-none">
                        KANTINKITA — LAPORAN REKAPITULASI KEUANGAN & PENGHASILAN
                    </h2>
                    <p class="text-xs text-slate-600 font-semibold mt-1">
                        Sistem Kasir & Pemesanan Digital Terpadu Kantin Sekolah
                    </p>
                </div>
            </div>

            <div class="text-right text-xs text-slate-700 leading-tight">
                <p class="font-bold text-slate-900">DOKUMEN RESMI PEMBUKUAN</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Tercatat di Database Pusat e-Kantin</p>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-2 text-xs text-slate-700 mt-4 pt-3 border-t border-slate-300 bg-slate-50 p-2.5 rounded-lg">
            <div>
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Periode Laporan:</span>
                <span class="font-bold text-slate-900">{{ $periodLabel }}</span>
            </div>
            <div>
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Waktu Dokumen Dicetak:</span>
                <span class="font-bold text-slate-900">{{ now()->translatedFormat('l, d F Y - H:i:s') }} WIB</span>
            </div>
            <div>
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Petugas Pencetak:</span>
                <span class="font-bold text-slate-900">{{ auth()->user()->name }} ({{ auth()->user()->isTenant() ? (auth()->user()->tenant->name ?? 'Tenant Stand') : 'Super Admin' }})</span>
            </div>
        </div>
    </div>

    {{-- ── RINGKASAN REKAPITULASI KEUANGAN (KPI GRID) ───────────────────────── --}}
    <div class="grid grid-cols-4 gap-3 mb-6 page-break-inside-avoid">
        <div class="border border-slate-300 p-3 rounded-xl bg-slate-50">
            <span class="text-[10px] uppercase font-bold text-slate-500 block">Total Penghasilan Bersih</span>
            <p class="text-xl font-black text-emerald-700 font-mono mt-0.5">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
            </p>
            <span class="text-[10px] text-slate-600 font-medium">Dari {{ $completedCount }} pesanan selesai/lunas</span>
        </div>

        <div class="border border-slate-300 p-3 rounded-xl bg-slate-50">
            <span class="text-[10px] uppercase font-bold text-slate-500 block">Omzet Kotor & Potongan</span>
            <p class="text-lg font-bold text-slate-900 font-mono mt-0.5">
                Rp {{ number_format($totalGross, 0, ',', '.') }}
            </p>
            <span class="text-[10px] text-red-600 font-medium">Diskon: -Rp {{ number_format($totalDiscount, 0, ',', '.') }}</span>
        </div>

        <div class="border border-slate-300 p-3 rounded-xl bg-slate-50">
            <span class="text-[10px] uppercase font-bold text-slate-500 block">Penerimaan Tunai (Kasir)</span>
            <p class="text-lg font-bold text-emerald-800 font-mono mt-0.5">
                Rp {{ number_format($cashRevenue, 0, ',', '.') }}
            </p>
            <span class="text-[10px] text-slate-600 font-medium">{{ $cashCount }} transaksi ({{ $cashPercent }}%)</span>
        </div>

        <div class="border border-slate-300 p-3 rounded-xl bg-slate-50">
            <span class="text-[10px] uppercase font-bold text-slate-500 block">Penerimaan QRIS (Digital)</span>
            <p class="text-lg font-bold text-blue-800 font-mono mt-0.5">
                Rp {{ number_format($qrisRevenue, 0, ',', '.') }}
            </p>
            <span class="text-[10px] text-slate-600 font-medium">{{ $qrisCount }} transaksi ({{ $qrisPercent }}%)</span>
        </div>
    </div>

    {{-- ── TABEL RINCIAN PER STAND / TENANT (JIKA ADMIN) ─────────────────────── --}}
    @if(auth()->user()->isAdmin() && $tenantBreakdown->isNotEmpty())
    <div class="mb-6 page-break-inside-avoid">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 mb-2">
            Rincian Penerimaan Per Stand Kantin (Tenant):
        </h3>
        <table class="w-full text-left text-xs border border-slate-300 border-collapse">
            <thead>
                <tr class="bg-slate-100 text-slate-700 border-b border-slate-300 font-bold text-[10.5px]">
                    <th class="py-2 px-3">Nama Stand</th>
                    <th class="py-2 px-3">Lokasi</th>
                    <th class="py-2 px-3 text-center">Pesanan Selesai</th>
                    <th class="py-2 px-3 text-right">Penerimaan Tunai</th>
                    <th class="py-2 px-3 text-right">Penerimaan QRIS</th>
                    <th class="py-2 px-3 text-right">Total Bersih</th>
                    <th class="py-2 px-3 text-center">Pangsa</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($tenantBreakdown as $tb)
                <tr>
                    <td class="py-2 px-3 font-bold">{{ $tb['name'] }}</td>
                    <td class="py-2 px-3 text-slate-600">{{ $tb['location'] }}</td>
                    <td class="py-2 px-3 text-center font-mono font-bold">{{ $tb['orders_count'] }}</td>
                    <td class="py-2 px-3 text-right font-mono">Rp {{ number_format($tb['cash'], 0, ',', '.') }}</td>
                    <td class="py-2 px-3 text-right font-mono">Rp {{ number_format($tb['qris'], 0, ',', '.') }}</td>
                    <td class="py-2 px-3 text-right font-mono font-bold text-slate-900">Rp {{ number_format($tb['revenue'], 0, ',', '.') }}</td>
                    <td class="py-2 px-3 text-center font-bold">{{ $tb['share'] }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- ── TABEL BUKU JURNAL TRANSAKSI KEUANGAN ──────────────────────────────── --}}
    <div class="mb-8">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">
                Buku Jurnal Riwayat Transaksi Finansial ({{ $allOrders->count() }} Data):
            </h3>
            <span class="text-[11px] text-slate-500">Menampilkan pesanan selesai, aktif, dan batal</span>
        </div>

        <table class="w-full text-left text-xs border border-slate-300 border-collapse">
            <thead>
                <tr class="bg-slate-100 text-slate-800 border-b border-slate-300 font-bold text-[10px] uppercase">
                    <th class="py-2 px-2.5">No.</th>
                    <th class="py-2 px-2.5">Waktu</th>
                    <th class="py-2 px-2.5">No. Pesanan</th>
                    <th class="py-2 px-2.5">Pemesan / Kelas</th>
                    <th class="py-2 px-2.5">Stand</th>
                    <th class="py-2 px-2.5">Rincian Menu</th>
                    <th class="py-2 px-2.5">Metode</th>
                    <th class="py-2 px-2.5 text-right">Potongan</th>
                    <th class="py-2 px-2.5 text-right">Total Akhir</th>
                    <th class="py-2 px-2.5 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($allOrders as $index => $ord)
                <tr class="{{ $ord->status === 'cancelled' ? 'bg-red-50/40 text-slate-500' : '' }}">
                    <td class="py-2 px-2.5 text-slate-500 text-center font-mono">{{ $index + 1 }}</td>
                    <td class="py-2 px-2.5 whitespace-nowrap text-[11px] font-mono">
                        {{ $ord->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="py-2 px-2.5 whitespace-nowrap font-mono font-bold text-slate-900">
                        #{{ $ord->order_number }}
                    </td>
                    <td class="py-2 px-2.5">
                        <div class="font-bold text-slate-900">{{ $ord->customer_name ?? 'Pelanggan' }}</div>
                        <div class="text-[10px] text-slate-500">{{ $ord->customer_class ?: '-' }}</div>
                    </td>
                    <td class="py-2 px-2.5 whitespace-nowrap text-slate-700">
                        {{ $ord->tenant->name ?? 'Stand' }}
                    </td>
                    <td class="py-2 px-2.5 text-[11px] text-slate-600 max-w-xs">
                        @foreach($ord->items as $it)
                            <span>{{ $it->quantity }}x {{ $it->product_name ?: ($it->product->name ?? 'Menu') }}</span>@if(!$loop->last), @endif
                        @endforeach
                    </td>
                    <td class="py-2 px-2.5 whitespace-nowrap font-bold text-[10.5px]">
                        {{ strtoupper($ord->payment_method) }}
                    </td>
                    <td class="py-2 px-2.5 whitespace-nowrap text-right font-mono text-[11px]">
                        {{ $ord->discount > 0 ? '-Rp ' . number_format($ord->discount, 0, ',', '.') : '-' }}
                    </td>
                    <td class="py-2 px-2.5 whitespace-nowrap text-right font-mono font-black text-slate-900">
                        Rp {{ number_format($ord->total, 0, ',', '.') }}
                    </td>
                    <td class="py-2 px-2.5 whitespace-nowrap text-center text-[10px] font-bold">
                        @if($ord->status === 'completed')
                            <span class="text-emerald-700">Lunas / Selesai</span>
                        @elseif($ord->status === 'cancelled')
                            <span class="text-red-600">Dibatalkan</span>
                        @else
                            <span class="text-amber-600">{{ ucfirst($ord->status) }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="py-6 text-center text-slate-400">
                        Tidak ada data transaksi keuangan pada rentang tanggal ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── LEMBAR PENGESAHAN & TANDA TANGAN (FORMAL PRINT) ──────────────────── --}}
    <div class="grid grid-cols-2 gap-12 text-center text-xs text-slate-800 page-break-inside-avoid mt-8 pt-4">
        <div>
            <p>Mengetahui & Menyetujui,</p>
            <p class="font-bold mt-1 text-slate-900">Kepala Sekolah / Penanggung Jawab Kantin</p>
            <div class="h-20"></div>
            <p class="font-bold underline text-slate-900">( .................................................... )</p>
            <p class="text-[10px] text-slate-500 mt-0.5">NIP: ....................................................</p>
        </div>
        <div>
            <p>Dibuat & Diverifikasi oleh,</p>
            <p class="font-bold mt-1 text-slate-900">Bendahara / Pengelola Kantin Sekolah</p>
            <div class="h-20"></div>
            <p class="font-bold underline text-slate-900">( {{ auth()->user()->name }} )</p>
            <p class="text-[10px] text-slate-500 mt-0.5">Email: {{ auth()->user()->email }}</p>
        </div>
    </div>

    {{-- Script Otomatis Buka Dialog Cetak Browser --}}
    <script>
        window.addEventListener('load', function () {
            // Beri jeda 400ms agar styling dan font ter-render sempurna
            setTimeout(function () {
                window.print();
            }, 400);
        });
    </script>
</body>
</html>

