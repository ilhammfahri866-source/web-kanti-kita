<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// ─── Halaman Publik (Tanpa Login) ─────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

// ─── Keranjang Belanja, Checkout & Tracking (Tanpa Login) ───────────────────
Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
Route::post('/checkout', [\App\Http\Controllers\CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success/{order}', [\App\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success');

// Pesanan Saya & Voucher (Dapat diakses tanpa login)
Route::get('/pesanan-saya', [\App\Http\Controllers\OrderTrackingController::class, 'index'])->name('orders.index');
Route::post('/pesanan-saya/{order}/batal', [\App\Http\Controllers\OrderTrackingController::class, 'cancel'])->name('orders.cancel');
Route::post('/voucher/claim', [\App\Http\Controllers\VoucherController::class, 'claim'])->name('voucher.claim');
Route::get('/voucher/active', [\App\Http\Controllers\VoucherController::class, 'active'])->name('voucher.active');

// ─── Fitur Profil Pengguna (Butuh Login) ───────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profil', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profil/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
});

// ─── Dashboard Tenant / Admin (Memerlukan Login & Hak Akses Pengelola) ──────
Route::middleware(['auth', 'admin_or_tenant'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard.index');
    Route::patch('/dashboard/orders/{order}/status', [\App\Http\Controllers\DashboardController::class, 'updateStatus'])->name('dashboard.orders.status');
    Route::patch('/dashboard/products/{product}/quick-update', [\App\Http\Controllers\ProductController::class, 'quickUpdate'])->name('dashboard.products.quick-update');
    Route::resource('/dashboard/products', \App\Http\Controllers\ProductController::class);

    // ─── Manajemen Stok Bahan & Produk ─────────────────────────────────────────
    Route::get('/dashboard/stok', [\App\Http\Controllers\IngredientController::class, 'index'])->name('dashboard.stock.index');
    Route::post('/dashboard/ingredients', [\App\Http\Controllers\IngredientController::class, 'store'])->name('dashboard.ingredients.store');
    Route::patch('/dashboard/ingredients/{ingredient}/adjust', [\App\Http\Controllers\IngredientController::class, 'adjust'])->name('dashboard.ingredients.adjust');
    Route::put('/dashboard/ingredients/{ingredient}', [\App\Http\Controllers\IngredientController::class, 'update'])->name('dashboard.ingredients.update');
    Route::delete('/dashboard/ingredients/{ingredient}', [\App\Http\Controllers\IngredientController::class, 'destroy'])->name('dashboard.ingredients.destroy');

    // ─── Laporan & Keuangan (Penghasilan & Tanggal) ───────────────────────────
    Route::get('/dashboard/laporan', [\App\Http\Controllers\FinanceReportController::class, 'index'])->name('dashboard.reports.index');
    Route::get('/dashboard/laporan/cetak', [\App\Http\Controllers\FinanceReportController::class, 'print'])->name('dashboard.reports.print');
    Route::get('/dashboard/keuangan', [\App\Http\Controllers\FinanceReportController::class, 'index'])->name('dashboard.finance.index');

    // ─── Manajemen Voucher ────────────────────────────────────────────────────
    Route::get('/dashboard/vouchers', [\App\Http\Controllers\VoucherController::class, 'index'])->name('dashboard.vouchers.index');
    Route::post('/dashboard/vouchers', [\App\Http\Controllers\VoucherController::class, 'store'])->name('dashboard.vouchers.store');
    Route::put('/dashboard/vouchers/{voucher}', [\App\Http\Controllers\VoucherController::class, 'update'])->name('dashboard.vouchers.update');
    Route::patch('/dashboard/vouchers/{voucher}/toggle', [\App\Http\Controllers\VoucherController::class, 'toggle'])->name('dashboard.vouchers.toggle');
    Route::delete('/dashboard/vouchers/{voucher}', [\App\Http\Controllers\VoucherController::class, 'destroy'])->name('dashboard.vouchers.destroy');
    Route::delete('/dashboard/vouchers-expired', [\App\Http\Controllers\VoucherController::class, 'destroyExpired'])->name('dashboard.vouchers.destroy-expired');
});

// ─── Autentikasi ──────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    // 1. Halaman Login Pembeli (Siswa & Guru)
    Route::get('/masuk', [\App\Http\Controllers\AuthController::class, 'showCustomerLogin'])->name('login');
    Route::post('/masuk', [\App\Http\Controllers\AuthController::class, 'loginCustomer']);
    Route::get('/masuk-pembeli', [\App\Http\Controllers\AuthController::class, 'showCustomerLogin'])->name('login.pembeli');
    Route::get('/masuk-siswa', [\App\Http\Controllers\AuthController::class, 'showCustomerLogin'])->name('login.siswa');
    Route::redirect('/login-siswa', '/masuk');
    Route::redirect('/login/siswa', '/masuk');
    Route::redirect('/login', '/masuk');

    // 2. Halaman Login Khusus Admin & Tenant (Pengelola Kantin)
    Route::get('/masuk-admin', [\App\Http\Controllers\AuthController::class, 'showAdminLogin'])->name('login.admin');
    Route::post('/masuk-admin', [\App\Http\Controllers\AuthController::class, 'loginAdmin'])->name('login.admin.post');
    Route::redirect('/admin/login', '/masuk-admin');
    Route::redirect('/admin', '/masuk-admin');
    Route::redirect('/login-admin', '/masuk-admin');
    Route::redirect('/tenant/login', '/masuk-admin');
    Route::redirect('/login-tenant', '/masuk-admin');

    // 3. Pendaftaran Akun Pembeli (Siswa & Guru)
    Route::get('/daftar', [\App\Http\Controllers\AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [\App\Http\Controllers\AuthController::class, 'register']);
    Route::redirect('/daftar-siswa', '/daftar');
    Route::redirect('/daftar/siswa', '/daftar');
    Route::redirect('/register-siswa', '/daftar');
    Route::redirect('/register', '/daftar');
});

Route::post('/keluar', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
