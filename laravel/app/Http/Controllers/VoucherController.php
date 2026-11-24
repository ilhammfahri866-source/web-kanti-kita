<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\VoucherClaim;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VoucherController extends Controller
{
    // ─── Dashboard Admin: CRUD Voucher ────────────────────────────────────────

    /**
     * Tampilkan daftar voucher di dashboard admin.
     */
    public function index()
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isTenant()) {
            abort(403);
        }

        $vouchers = Voucher::withCount('claims')->latest()->get();
        return view('dashboard.vouchers.index', compact('vouchers'));
    }

    /**
     * Buat voucher baru.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isTenant()) {
            abort(403);
        }

        $request->validate([
            'code'             => ['required', 'string', 'max:50', 'unique:vouchers,code'],
            'name'             => ['required', 'string', 'max:255'],
            'discount_percent' => ['required', 'integer', 'min:1', 'max:100'],
            'expires_at'       => ['required', 'date', 'after:now'],
        ]);

        Voucher::create([
            'code'             => strtoupper($request->code),
            'name'             => $request->name,
            'discount_percent' => $request->discount_percent,
            'expires_at'       => $request->expires_at,
            'is_active'        => true,
        ]);

        return redirect()->route('dashboard.vouchers.index')
            ->with('success', "Voucher '{$request->code}' berhasil dibuat!");
    }

    /**
     * Perbarui voucher yang sudah ada.
     */
    public function update(Request $request, Voucher $voucher)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isTenant()) {
            abort(403);
        }

        $request->validate([
            'code'             => ['required', 'string', 'max:50', 'unique:vouchers,code,' . $voucher->id],
            'name'             => ['required', 'string', 'max:255'],
            'discount_percent' => ['required', 'integer', 'min:1', 'max:100'],
            'expires_at'       => ['required', 'date'],
        ]);

        $voucher->update([
            'code'             => strtoupper($request->code),
            'name'             => $request->name,
            'discount_percent' => $request->discount_percent,
            'expires_at'       => $request->expires_at,
        ]);

        return redirect()->route('dashboard.vouchers.index')
            ->with('success', "Voucher '{$voucher->code}' berhasil diperbarui!");
    }

    /**
     * Toggle aktif/nonaktif voucher.
     */
    public function toggle(Voucher $voucher)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isTenant()) {
            abort(403);
        }

        $voucher->update(['is_active' => !$voucher->is_active]);

        $status = $voucher->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Voucher '{$voucher->code}' berhasil {$status}.");
    }

    /**
     * Hapus voucher.
     */
    public function destroy(Voucher $voucher)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isTenant()) {
            abort(403);
        }

        $code = $voucher->code;
        // Hapus riwayat klaim terkait terlebih dahulu untuk memastikan integritas DB
        $voucher->claims()->delete();
        $voucher->delete();

        return redirect()->route('dashboard.vouchers.index')
            ->with('success', "Voucher '{$code}' berhasil dihapus secara permanen.");
    }

    /**
     * Bersihkan / hapus semua voucher yang telah kedaluwarsa.
     */
    public function destroyExpired()
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isTenant()) {
            abort(403);
        }

        $expiredVouchers = Voucher::where('expires_at', '<', now())->get();
        $count = $expiredVouchers->count();

        if ($count === 0) {
            return redirect()->route('dashboard.vouchers.index')
                ->with('info', 'Tidak ada voucher kedaluwarsa yang perlu dibersihkan.');
        }

        foreach ($expiredVouchers as $voucher) {
            $voucher->claims()->delete();
            $voucher->delete();
        }

        return redirect()->route('dashboard.vouchers.index')
            ->with('success', "Sebanyak {$count} voucher kedaluwarsa berhasil dibersihkan.");
    }

    // ─── Pelanggan: Klaim Voucher ──────────────────────────────────────────────

    /**
     * Klaim voucher promo via AJAX / Fetch API.
     */
    public function claim(Request $request): JsonResponse
    {
        $code = strtoupper(trim($request->input('code', 'KANTINHEMAT')));

        $voucher = Voucher::where('code', $code)->first();

        if (!$voucher || !$voucher->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Kode voucher promo tidak ditemukan atau sedang tidak aktif.',
            ], 404);
        }

        if ($voucher->isExpired()) {
            return response()->json([
                'success' => false,
                'expired' => true,
                'message' => 'Maaf, voucher ' . $voucher->code . ' telah kedaluwarsa pada ' . $voucher->expires_at->format('d/m/Y H:i') . ' WIB.',
            ], 422);
        }

        // Jika user belum login, langsung aktifkan voucher di session untuk transaksi pesanan ini
        if (!auth()->check()) {
            session(['active_voucher' => [
                'id'               => $voucher->id,
                'code'             => $voucher->code,
                'name'             => $voucher->name,
                'discount_percent' => $voucher->discount_percent,
            ]]);

            return response()->json([
                'success' => true,
                'message' => 'Selamat! Diskon ' . $voucher->discount_percent . '% dari voucher ' . $voucher->code . ' berhasil diaktifkan!',
                'voucher' => [
                    'code'             => $voucher->code,
                    'discount_percent' => $voucher->discount_percent,
                ],
            ]);
        }

        $existingClaim = VoucherClaim::where('voucher_id', $voucher->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existingClaim) {
            if ($existingClaim->is_used) {
                return response()->json([
                    'success'         => false,
                    'already_claimed' => true,
                    'is_used'         => true,
                    'message'         => 'Anda sudah pernah mengklaim dan menggunakan voucher ' . $voucher->code . '. Promo ini dibatasi 1 kali klaim per akun.',
                ], 422);
            }

            session(['active_voucher' => [
                'id'               => $voucher->id,
                'code'             => $voucher->code,
                'name'             => $voucher->name,
                'discount_percent' => $voucher->discount_percent,
            ]]);

            return response()->json([
                'success'         => false,
                'already_claimed' => true,
                'is_used'         => false,
                'message'         => 'Anda sudah mengklaim voucher ' . $voucher->code . ' sebelumnya. Diskon ' . $voucher->discount_percent . '% sudah aktif untuk pesanan Anda.',
                'voucher'         => [
                    'code'             => $voucher->code,
                    'discount_percent' => $voucher->discount_percent,
                ],
            ], 422);
        }

        VoucherClaim::create([
            'voucher_id' => $voucher->id,
            'user_id'    => auth()->id(),
            'claimed_at' => now(),
            'is_used'    => false,
        ]);

        session(['active_voucher' => [
            'id'               => $voucher->id,
            'code'             => $voucher->code,
            'name'             => $voucher->name,
            'discount_percent' => $voucher->discount_percent,
        ]]);

        return response()->json([
            'success' => true,
            'message' => 'Selamat! Diskon ' . $voucher->discount_percent . '% dari voucher ' . $voucher->code . ' berhasil diaktifkan!',
            'voucher' => [
                'id'               => $voucher->id,
                'code'             => $voucher->code,
                'name'             => $voucher->name,
                'discount_percent' => $voucher->discount_percent,
                'expires_at'       => $voucher->expires_at->format('d/m/Y'),
            ],
        ]);
    }

    /**
     * Dapatkan informasi voucher yang sedang aktif di sesi pengguna.
     */
    public function active(): JsonResponse
    {
        $activeVoucher = session('active_voucher');

        if (!$activeVoucher && auth()->check()) {
            $claim = VoucherClaim::with('voucher')
                ->where('user_id', auth()->id())
                ->where('is_used', false)
                ->latest()
                ->first();

            if ($claim && $claim->voucher && $claim->voucher->isValid()) {
                $activeVoucher = [
                    'id'               => $claim->voucher->id,
                    'code'             => $claim->voucher->code,
                    'name'             => $claim->voucher->name,
                    'discount_percent' => $claim->voucher->discount_percent,
                ];
                session(['active_voucher' => $activeVoucher]);
            }
        }

        return response()->json([
            'active_voucher' => $activeVoucher,
        ]);
    }
}
