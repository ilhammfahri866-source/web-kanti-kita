<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $orderQuery      = Order::with(['items', 'tenant'])->orderBy('created_at', 'desc');
        $productQuery    = Product::with('tenant')->latest();
        $ingredientQuery = Ingredient::with('tenant')->latest();

        if ($user && $user->isTenant() && $user->tenant) {
            $orderQuery->where('tenant_id', $user->tenant->id);
            $productQuery->where('tenant_id', $user->tenant->id);
            $ingredientQuery->where('tenant_id', $user->tenant->id);
        }

        $orders      = $orderQuery->get();
        $products    = $productQuery->get();
        $ingredients = $ingredientQuery->get();

        $totalRevenue         = $orders->where('status', 'completed')->sum('total');
        $activeOrdersCount    = $orders->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])->count();
        $completedOrdersCount = $orders->where('status', 'completed')->count();
        $pendingOrdersCount   = $orders->where('status', 'pending')->count();

        $todayRevenue     = $orders->where('status', 'completed')->filter(fn($o) => $o->created_at->isToday())->sum('total');
        $yesterdayRevenue = $orders->where('status', 'completed')->filter(fn($o) => $o->created_at->isYesterday())->sum('total');
        $todayOrders      = $orders->filter(fn($o) => $o->created_at->isToday())->count();
        $yesterdayOrders  = $orders->filter(fn($o) => $o->created_at->isYesterday())->count();

        $revenueChange = $yesterdayRevenue > 0 ? round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 1) : ($todayRevenue > 0 ? 100 : 0);
        $ordersChange  = $yesterdayOrders > 0  ? round((($todayOrders  - $yesterdayOrders)  / $yesterdayOrders)  * 100, 1) : ($todayOrders  > 0 ? 100 : 0);

        // ── Perhitungan Finansial Berdasarkan Filter Tanggal di Halaman Dashboard ──
        $calcDateFrom = $request->input('calc_date_from', Carbon::today()->format('Y-m-d'));
        $calcDateTo   = $request->input('calc_date_to', Carbon::today()->format('Y-m-d'));

        try {
            $fromCarbon = Carbon::parse($calcDateFrom)->startOfDay();
            $toCarbon   = Carbon::parse($calcDateTo)->endOfDay();
        } catch (\Exception $e) {
            $fromCarbon = Carbon::today()->startOfDay();
            $toCarbon   = Carbon::today()->endOfDay();
            $calcDateFrom = Carbon::today()->format('Y-m-d');
            $calcDateTo   = Carbon::today()->format('Y-m-d');
        }

        $filteredDateOrders = $orders->filter(function ($o) use ($fromCarbon, $toCarbon) {
            return $o->created_at->between($fromCarbon, $toCarbon);
        });
        $filteredDateCompleted = $filteredDateOrders->where('status', 'completed');
        $calcRevenue           = (float) $filteredDateCompleted->sum('total');
        $calcOrdersCount       = $filteredDateCompleted->count();
        $calcCashRevenue       = (float) $filteredDateCompleted->where('payment_method', 'cash')->sum('total');
        $calcQrisRevenue       = (float) $filteredDateCompleted->where('payment_method', 'qris')->sum('total');

        $chartDays = collect();
        for ($i = 6; $i >= 0; $i--) {
            $day        = Carbon::today()->subDays($i);
            $dayRevenue = $orders->where('status', 'completed')->filter(fn($o) => $o->created_at->format('Y-m-d') === $day->format('Y-m-d'))->sum('total');
            $dayOrders  = $orders->filter(fn($o) => $o->created_at->format('Y-m-d') === $day->format('Y-m-d'))->count();
            $chartDays->push(['date' => $day->format('d M'), 'label' => $day->isoFormat('ddd'), 'revenue' => (int) $dayRevenue, 'orders' => $dayOrders, 'isToday' => $day->isToday()]);
        }
        $chartMaxRevenue = max($chartDays->max('revenue'), 1);

        $totalProductsCount = $products->count();
        $outOfStockCount    = $products->where('stock', '<=', 0)->count();
        $lowStockCount      = $products->where('stock', '>', 0)->where('stock', '<=', 5)->count();

        $totalIngredientsCount      = $ingredients->count();
        $outOfStockIngredientsCount = $ingredients->filter(fn($i) => (bool) ($i->is_out_of_stock ?? ($i->stock <= 0)))->count();
        $lowStockIngredientsCount   = $ingredients->filter(fn($i) => (bool) ($i->is_low_stock  ?? ($i->stock > 0 && $i->stock <= 5)))->count();

        $tenants = Tenant::all();

        return view('dashboard.index', compact(
            'orders', 'products', 'ingredients',
            'totalRevenue', 'activeOrdersCount', 'completedOrdersCount', 'pendingOrdersCount',
            'totalProductsCount', 'outOfStockCount', 'lowStockCount',
            'totalIngredientsCount', 'outOfStockIngredientsCount', 'lowStockIngredientsCount',
            'tenants',
            'todayRevenue', 'yesterdayRevenue', 'todayOrders', 'yesterdayOrders',
            'revenueChange', 'ordersChange', 'chartDays', 'chartMaxRevenue',
            'calcDateFrom', 'calcDateTo', 'calcRevenue', 'calcOrdersCount', 'calcCashRevenue', 'calcQrisRevenue'
        ));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status'        => 'required|in:pending,confirmed,preparing,ready,completed,cancelled',
            'cancel_reason' => 'nullable|string|max:255',
        ]);

        $order->status = $request->status;

        if ($request->status === 'completed') {
            $order->completed_at = now();
            if ($order->payment_method === 'cash') {
                $order->payment_status = 'paid';
            }
        } elseif ($request->status === 'cancelled') {
            $order->cancel_reason = $request->cancel_reason ?: 'Pesanan ditolak oleh pihak tenant kantin.';
            $order->payment_status = 'cancelled';

            // Kembalikan stok produk jika pesanan ditolak
            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product) {
                    $product->increment('stock', $item->quantity);
                    if (!$product->is_available && $product->stock > 0) {
                        $product->update(['is_available' => true]);
                    }
                }
            }
        }

        $order->save();

        if ($request->status === 'cancelled') {
            $reason = $order->cancel_reason ?: 'Stok menu / bahan utama telah habis';
            return back()
                ->with('order_cancelled_success', [
                    'order_number'  => $order->order_number,
                    'customer_name' => $order->customer_name ?? 'Pelanggan',
                    'reason'        => $reason,
                ])
                ->with('success', 'Berhasil tolak pesanan #' . $order->order_number . '! Alasan: "' . $reason . '". Stok produk otomatis dikembalikan.');
        }

        if ($request->status === 'confirmed') {
            return back()
                ->with('order_confirmed_success', [
                    'order_number'  => $order->order_number,
                    'customer_name' => $order->customer_name ?? 'Pelanggan',
                ])
                ->with('success', 'Pesanan #' . $order->order_number . ' telah dikonfirmasi! Dapur dapat segera mulai menyiapkan pesanan.');
        }

        return back()->with('success', 'Status pesanan ' . $order->order_number . ' berhasil diperbarui menjadi ' . $order->status_label . '.');
    }
}
