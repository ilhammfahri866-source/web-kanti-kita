<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            $orders = Order::where('user_id', auth()->id())
                ->with(['tenant', 'items'])
                ->latest()
                ->paginate(10);
        } else {
            $guestOrderIds = session('guest_order_ids', []);
            $orders = Order::whereIn('id', $guestOrderIds)
                ->with(['tenant', 'items'])
                ->latest()
                ->paginate(10);
        }

        return view('orders.index', compact('orders'));
    }

    /**
     * Batalkan pesanan oleh pelanggan (hanya jika masih pending).
     */
    public function cancel(Request $request, Order $order)
    {
        $guestOrderIds = session('guest_order_ids', []);
        $isOwner = auth()->check() ? ($order->user_id === auth()->id()) : in_array($order->id, $guestOrderIds);

        if (!$isOwner) {
            abort(403, 'Akses ditolak.');
        }

        // Hanya bisa batalkan jika masih pending
        if ($order->status !== 'pending') {
            return back()->with('error', 'Pesanan tidak bisa dibatalkan karena sudah diproses oleh tenant.');
        }

        // Kembalikan stok produk
        foreach ($order->items as $item) {
            $product = $item->product;
            if ($product) {
                $product->increment('stock', $item->quantity);
                if (!$product->is_available && $product->stock > 0) {
                    $product->update(['is_available' => true]);
                }
            }
        }

        $order->update([
            'status'         => 'cancelled',
            'payment_status' => 'cancelled',
            'cancel_reason'  => $request->cancel_reason ?: 'Dibatalkan oleh pemesan.',
        ]);

        return back()->with('success', "Pesanan #{$order->order_number} berhasil dibatalkan. Stok produk telah dikembalikan.");
    }
}
