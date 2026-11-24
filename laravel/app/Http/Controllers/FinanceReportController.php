<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class FinanceReportController extends Controller
{
    /**
     * Tampilkan halaman laporan dan pembukuan keuangan kantin.
     */
    public function index(Request $request)
    {
        $data = $this->calculateReportData($request);
        return view('dashboard.reports.index', $data);
    }

    /**
     * Tampilkan halaman khusus cetak laporan / PDF yang bersih tanpa navigasi dashboard.
     */
    public function print(Request $request)
    {
        $data = $this->calculateReportData($request);
        return view('dashboard.reports.print', $data);
    }

    /**
     * Hitung semua parameter dan metrik keuangan berdasarkan tanggal & filter.
     */
    private function calculateReportData(Request $request): array
    {
        $user = auth()->user();

        // 1. Tentukan Periode Tanggal
        $period = $request->input('period', 'this_month');
        $today = Carbon::today();

        switch ($period) {
            case 'today':
                $startDate = $today->copy()->startOfDay();
                $endDate = $today->copy()->endOfDay();
                $periodLabel = 'Hari Ini (' . $today->translatedFormat('d F Y') . ')';
                break;

            case 'yesterday':
                $yesterday = $today->copy()->subDay();
                $startDate = $yesterday->copy()->startOfDay();
                $endDate = $yesterday->copy()->endOfDay();
                $periodLabel = 'Kemarin (' . $yesterday->translatedFormat('d F Y') . ')';
                break;

            case 'last_7_days':
                $startDate = $today->copy()->subDays(6)->startOfDay();
                $endDate = $today->copy()->endOfDay();
                $periodLabel = '7 Hari Terakhir (' . $startDate->format('d M') . ' - ' . $endDate->format('d M Y') . ')';
                break;

            case 'this_week':
                $startDate = Carbon::now()->startOfWeek()->startOfDay();
                $endDate = Carbon::now()->endOfWeek()->endOfDay();
                $periodLabel = 'Minggu Ini (' . $startDate->format('d M') . ' - ' . $endDate->format('d M Y') . ')';
                break;

            case 'last_month':
                $lastMonth = Carbon::now()->subMonth();
                $startDate = $lastMonth->copy()->startOfMonth()->startOfDay();
                $endDate = $lastMonth->copy()->endOfMonth()->endOfDay();
                $periodLabel = 'Bulan Lalu (' . $lastMonth->translatedFormat('F Y') . ')';
                break;

            case 'this_year':
                $startDate = Carbon::now()->startOfYear()->startOfDay();
                $endDate = Carbon::now()->endOfYear()->endOfDay();
                $periodLabel = 'Tahun Ini (' . Carbon::now()->year . ')';
                break;

            case 'all_time':
                $startDate = Carbon::create(2020, 1, 1)->startOfDay();
                $endDate = Carbon::tomorrow()->endOfDay();
                $periodLabel = 'Semua Waktu';
                break;

            case 'custom':
            default:
                if ($request->filled('start_date') && $request->filled('end_date')) {
                    try {
                        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
                        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
                        $period = 'custom';
                        $periodLabel = $startDate->translatedFormat('d M Y') . ' s/d ' . $endDate->translatedFormat('d M Y');
                    } catch (\Exception $e) {
                        $startDate = Carbon::now()->startOfMonth()->startOfDay();
                        $endDate = Carbon::now()->endOfDay();
                        $period = 'this_month';
                        $periodLabel = 'Bulan Ini (' . Carbon::now()->translatedFormat('F Y') . ')';
                    }
                } else {
                    $period = 'this_month';
                    $startDate = Carbon::now()->startOfMonth()->startOfDay();
                    $endDate = Carbon::now()->endOfDay();
                    $periodLabel = 'Bulan Ini (' . Carbon::now()->translatedFormat('F Y') . ')';
                }
                break;
        }

        // 2. Siapkan Query Dasar Pesanan
        $query = Order::with(['items.product', 'tenant', 'user'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc');

        // Filter Hak Akses Tenant
        if ($user && $user->isTenant() && $user->tenant) {
            $selectedTenantId = $user->tenant->id;
            $query->where('tenant_id', $selectedTenantId);
        } else {
            $selectedTenantId = $request->input('tenant_id', 'all');
            if ($selectedTenantId && $selectedTenantId !== 'all') {
                $query->where('tenant_id', $selectedTenantId);
            }
        }

        // Filter Metode Pembayaran (Semua, Tunai/Cash, QRIS)
        $paymentMethodFilter = $request->input('payment_method', 'all');
        if ($paymentMethodFilter && $paymentMethodFilter !== 'all') {
            $query->where('payment_method', $paymentMethodFilter);
        }

        // Filter Pencarian (No Pesanan, Nama Pelanggan)
        $search = $request->input('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_class', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Ambil semua pesanan sesuai rentang tanggal & filter
        $allOrders = $query->get();

        // 3. Kalkulasi Finansial (Hanya dari pesanan completed / lunas)
        $completedOrders = $allOrders->where('status', 'completed');
        $cancelledOrders = $allOrders->where('status', 'cancelled');
        $activeOrders    = $allOrders->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready']);

        $totalRevenue  = (float) $completedOrders->sum('total');
        $totalGross    = (float) $completedOrders->sum('subtotal');
        $totalDiscount = (float) $completedOrders->sum('discount');

        $completedCount = $completedOrders->count();
        $cancelledCount = $cancelledOrders->count();
        $activeCount    = $activeOrders->count();
        $totalOrdersCount = $allOrders->count();

        $averageOrderValue = $completedCount > 0 ? round($totalRevenue / $completedCount) : 0;
        $cancelledValue    = (float) $cancelledOrders->sum('total');

        // 4. Breakdown Metode Pembayaran
        $cashOrders   = $completedOrders->where('payment_method', 'cash');
        $qrisOrders   = $completedOrders->where('payment_method', 'qris');

        $cashRevenue  = (float) $cashOrders->sum('total');
        $cashCount    = $cashOrders->count();
        $qrisRevenue  = (float) $qrisOrders->sum('total');
        $qrisCount    = $qrisOrders->count();

        $cashPercent  = $totalRevenue > 0 ? round(($cashRevenue / $totalRevenue) * 100, 1) : 0;
        $qrisPercent  = $totalRevenue > 0 ? round(($qrisRevenue / $totalRevenue) * 100, 1) : 0;

        // 5. Breakdown Segmen Pelanggan (Guru vs Siswa)
        $teacherOrders = $completedOrders->filter(function ($order) {
            $class = strtolower((string)$order->customer_class);
            return str_contains($class, 'guru') || ($order->user && $order->user->isTeacher());
        });
        $teacherRevenue = (float) $teacherOrders->sum('total');
        $teacherCount   = $teacherOrders->count();

        $studentOrders  = $completedOrders->reject(function ($order) {
            $class = strtolower((string)$order->customer_class);
            return str_contains($class, 'guru') || ($order->user && $order->user->isTeacher());
        });
        $studentRevenue = (float) $studentOrders->sum('total');
        $studentCount   = $studentOrders->count();

        // 6. Breakdown Per Stand / Tenant (Untuk Tampilan Admin)
        $tenants = Tenant::all();
        $tenantBreakdown = collect();

        if ($user && $user->isAdmin()) {
            foreach ($tenants as $t) {
                $tOrders = $completedOrders->where('tenant_id', $t->id);
                $tRev    = (float) $tOrders->sum('total');
                $tCash   = (float) $tOrders->where('payment_method', 'cash')->sum('total');
                $tQris   = (float) $tOrders->where('payment_method', 'qris')->sum('total');
                $tShare  = $totalRevenue > 0 ? round(($tRev / $totalRevenue) * 100, 1) : 0;

                $tenantBreakdown->push([
                    'id'          => $t->id,
                    'name'        => $t->name,
                    'location'    => $t->location,
                    'orders_count'=> $tOrders->count(),
                    'revenue'     => $tRev,
                    'cash'        => $tCash,
                    'qris'        => $tQris,
                    'share'       => $tShare,
                ]);
            }
            $tenantBreakdown = $tenantBreakdown->sortByDesc('revenue')->values();
        }

        // 7. Breakdown Menu / Produk Terlaris di Periode Ini (Gunakan native PHP array agar aman)
        $productSales = [];
        foreach ($completedOrders as $order) {
            foreach ($order->items as $item) {
                $name = $item->product_name ?: ($item->product->name ?? 'Menu Kantin');
                $qty  = (int) $item->quantity;
                $sub  = (float) $item->subtotal;

                if (!isset($productSales[$name])) {
                    $productSales[$name] = [
                        'name'     => $name,
                        'category' => $item->product->category ?? 'Umum',
                        'tenant'   => $item->order->tenant->name ?? 'Stand Kantin',
                        'qty'      => 0,
                        'revenue'  => 0,
                    ];
                }
                $productSales[$name]['qty'] += $qty;
                $productSales[$name]['revenue'] += $sub;
            }
        }
        $topProducts = collect($productSales)->sortByDesc('revenue')->take(8)->values();

        // 8. Grafik Tren Pendapatan Harian
        $dailyChart = collect();
        $daysDiff = $startDate->diffInDays($endDate);

        if ($daysDiff <= 31) {
            $curr = $startDate->copy();
            while ($curr->lte($endDate)) {
                $dateStr = $curr->format('Y-m-d');
                $dayCompleted = $completedOrders->filter(fn($o) => $o->created_at->format('Y-m-d') === $dateStr);
                $dayRev   = (float) $dayCompleted->sum('total');
                $dayCount = $dayCompleted->count();

                $dailyChart->push([
                    'date'     => $curr->format('d M'),
                    'full_date'=> $curr->translatedFormat('l, d F Y'),
                    'revenue'  => (int) $dayRev,
                    'orders'   => $dayCount,
                    'is_today' => $curr->isToday(),
                ]);
                $curr->addDay();
            }
        }

        $chartMaxRevenue = max($dailyChart->max('revenue') ?? 0, 1);

        // 9. Format Tanggal Input
        $formattedStartDate = $startDate->format('Y-m-d');
        $formattedEndDate   = $endDate->format('Y-m-d');

        return compact(
            'allOrders',
            'completedOrders',
            'cancelledOrders',
            'activeOrders',
            'totalRevenue',
            'totalGross',
            'totalDiscount',
            'completedCount',
            'cancelledCount',
            'activeCount',
            'totalOrdersCount',
            'averageOrderValue',
            'cancelledValue',
            'cashRevenue',
            'cashCount',
            'cashPercent',
            'qrisRevenue',
            'qrisCount',
            'qrisPercent',
            'teacherRevenue',
            'teacherCount',
            'studentRevenue',
            'studentCount',
            'tenants',
            'tenantBreakdown',
            'topProducts',
            'dailyChart',
            'chartMaxRevenue',
            'period',
            'periodLabel',
            'formattedStartDate',
            'formattedEndDate',
            'selectedTenantId',
            'paymentMethodFilter',
            'search'
        );
    }
}
