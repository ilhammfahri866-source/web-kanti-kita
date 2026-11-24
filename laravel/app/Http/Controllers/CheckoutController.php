<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\CanteenSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        // Validasi jam operasional kantin (Istirahat 1: 09:30 - 10:00 WIB & Istirahat 2: 12:00 - 13:00 WIB)
        if (!CanteenSchedule::isOpen()) {
            return response()->json([
                'success' => false,
                'message' => 'Kantin sedang tutup. Pemesanan hanya dibuka saat jam istirahat: Sesi 1 (09:30 - 10:00 WIB) dan Sesi 2 (12:00 - 13:00 WIB).'
            ], 400);
        }

        $request->validate([
            'items'            => 'required|array|min:1',
            'items.*.id'       => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes'            => 'nullable|string',
            'customer_name'    => 'nullable|string|max:255',
            'customer_class'   => 'nullable|string|max:50',
            'payment_method'   => 'required|in:cash,qris',
            'latitude'         => 'nullable|numeric',
            'longitude'        => 'nullable|numeric',
        ]);

        // Siswa wajib masuk akun (login) untuk melakukan pemesanan
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'require_login' => true,
                'login_url' => route('login.siswa'),
                'register_url' => route('register'),
                'message' => 'Silakan masuk ke akun siswa terlebih dahulu untuk melakukan pemesanan. Jika belum punya akun, silakan daftar akun siswa baru.'
            ], 401);
        }

        $userId = auth()->id();
        $user = auth()->user();

        // Nama & Kelas selalu mengutamakan data autentik akun yang login (Siswa atau Guru)
        $customerName = !empty($user->name) ? $user->name : ($request->customer_name ?: 'Pemesan');
        $customerClass = !empty($user->classroom) ? $user->classroom : ($request->customer_class ?: '-');

        // Perbarui data kelas siswa jika belum tersimpan di akunnya
        if (empty($user->classroom) && !empty($request->customer_class)) {
            $user->update(['classroom' => $request->customer_class]);
        }

        // Verifikasi Lokasi Geofencing Sekolah
        $schoolLat = config('canteen.school_latitude', -6.2088);
        $schoolLng = config('canteen.school_longitude', 106.8456);
        $schoolRadius = config('canteen.school_radius_meters', 500);

        $lat = $request->input('latitude');
        $lng = $request->input('longitude');
        $distanceMeters = null;
        $locationStatus = 'in_school';

        if ($lat && $lng) {
            $distanceMeters = $this->calculateDistanceMeters((float)$lat, (float)$lng, (float)$schoolLat, (float)$schoolLng);
            if ($distanceMeters <= $schoolRadius) {
                $locationStatus = 'in_school';
            } else {
                $locationStatus = 'outside';
                if (config('canteen.require_school_location', false)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Pemesanan ditolak: Anda terdeteksi berada di luar area sekolah (jarak {$distanceMeters}m dari sekolah). Pemesanan hanya dapat dilakukan saat Anda berada di lingkungan sekolah."
                    ], 403);
                }
            }
        }

        DB::beginTransaction();
        try {
            // Group request items by tenant_id
            $itemsByTenant = [];
            foreach ($request->items as $itemData) {
                $product = Product::findOrFail($itemData['id']);
                
                if ($product->stock < $itemData['quantity']) {
                    return response()->json([
                        'success' => false, 
                        'message' => "Stok produk {$product->name} tidak mencukupi. Sisa stok: {$product->stock}"
                    ], 400);
                }
                
                // Reduce stock
                $product->decrement('stock', $itemData['quantity']);

                $tenantId = $product->tenant_id;
                if (!isset($itemsByTenant[$tenantId])) {
                    $itemsByTenant[$tenantId] = [
                        'subtotal' => 0,
                        'orderItems' => []
                    ];
                }

                $itemSubtotal = $product->price * $itemData['quantity'];
                $itemsByTenant[$tenantId]['subtotal'] += $itemSubtotal;

                $itemsByTenant[$tenantId]['orderItems'][] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $itemData['quantity'],
                    'subtotal' => $itemSubtotal,
                ];
            }

            // Hitung diskon voucher jika ada
            $totalSubtotal = array_sum(array_column($itemsByTenant, 'subtotal'));
            $discount = 0;
            $voucherCode = null;
            $voucherClaim = null;

            $activeVoucherData = session('active_voucher');
            $candidateCode = $request->input('voucher_code', $activeVoucherData['code'] ?? null);

            if ($candidateCode) {
                $voucher = \App\Models\Voucher::where('code', $candidateCode)->first();
                if ($voucher && $voucher->isValid()) {
                    if ($userId) {
                        $voucherClaim = \App\Models\VoucherClaim::where('voucher_id', $voucher->id)
                            ->where('user_id', $userId)
                            ->where('is_used', false)
                            ->first();

                        if ($voucherClaim) {
                            $discount = round(($totalSubtotal * $voucher->discount_percent) / 100);
                            $voucherCode = $voucher->code;
                        }
                    } else {
                        // Untuk pemesan tanpa login (siswa/guest)
                        $discount = round(($totalSubtotal * $voucher->discount_percent) / 100);
                        $voucherCode = $voucher->code;
                    }
                }
            }

            // Create an order for each tenant
            $createdOrders = [];
            foreach ($itemsByTenant as $tenantId => $tenantData) {
                $tenantTotal = max(0, $tenantData['subtotal'] - $discount);
                $order = Order::create([
                    'user_id' => $userId,
                    'tenant_id' => $tenantId,
                    'customer_name' => $customerName,
                    'customer_class' => $customerClass,
                    'latitude' => $lat ? (float)$lat : null,
                    'longitude' => $lng ? (float)$lng : null,
                    'distance_meters' => $distanceMeters,
                    'location_status' => $locationStatus,
                    'order_number' => 'ORD-' . date('Ymd') . '-' . strtoupper(uniqid()),
                    'status' => 'pending',
                    'payment_status' => 'unpaid',
                    'payment_method' => $request->payment_method,
                    'subtotal' => $tenantData['subtotal'],
                    'discount' => $discount,
                    'total' => $tenantTotal,
                    'voucher_code' => $voucherCode,
                    'notes' => $request->notes,
                ]);

                foreach ($tenantData['orderItems'] as $orderItem) {
                    $orderItem['order_id'] = $order->id;
                    OrderItem::create($orderItem);
                }
                
                $createdOrders[] = $order;
            }

            // Tandai voucher claim sebagai sudah digunakan & hubungkan ke order
            if ($voucherClaim) {
                $voucherClaim->update([
                    'is_used' => true,
                    'order_id' => $order->id,
                ]);
                session()->forget('active_voucher');
            }

            // Simpan ID order ke session agar siswa tanpa login bisa melacak pesanan mereka di "Pesanan Saya"
            $guestOrderIds = session('guest_order_ids', []);
            foreach ($createdOrders as $createdOrder) {
                $guestOrderIds[] = $createdOrder->id;
            }
            session(['guest_order_ids' => array_values(array_unique($guestOrderIds))]);

            DB::commit();
            
            // If only 1 order, redirect to success page. If multiple, redirect to orders list.
            $redirectUrl = count($createdOrders) === 1 
                ? route('checkout.success', $createdOrders[0]->id)
                : route('orders.index');

            return response()->json([
                'success' => true, 
                'message' => 'Pesanan berhasil dibuat!', 
                'redirect_url' => $redirectUrl
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'Terjadi kesalahan saat memproses pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function success(Order $order)
    {
        // Pastikan relasi order item ter-load
        $order->load(['items', 'tenant']);
        return view('cart.success', compact('order'));
    }

    /**
     * Hitung jarak dua titik koordinat bumi dalam satuan meter (rumus Haversine).
     */
    private function calculateDistanceMeters(float $lat1, float $lon1, float $lat2, float $lon2): int
    {
        $earthRadius = 6371000; // Radius bumi dalam meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return (int) round($earthRadius * $c);
    }
}
