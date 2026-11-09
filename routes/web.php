<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminPasswordResetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CourierPasswordResetController;
use App\Http\Controllers\KurirController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ReturnController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.process');
Route::get('/kurir/register', [AuthController::class, 'showRegisterKurir'])->name('kurir.register');
Route::post('/kurir/register', [AuthController::class, 'registerKurir'])->name('kurir.register.process');

Route::get('/password/lupa', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/lupa', [PasswordResetController::class, 'verifyRecoveryCode'])->name('password.verify');
Route::get('/email/pelanggan/{id}/{hash}', [PelangganController::class, 'verifyEmail'])
    ->middleware('signed')
    ->name('pelanggan.email.verify');
Route::get('/admin/password/request', [AdminPasswordResetController::class, 'requestForm'])->name('admin.password.request');
Route::post('/admin/password/request', [AdminPasswordResetController::class, 'submitRequest'])->name('admin.password.verify');
Route::get('/kurir/password-reset', [CourierPasswordResetController::class, 'requestForm'])->name('kurir.password-reset.request');
Route::post('/kurir/password-reset', [CourierPasswordResetController::class, 'submitRequest'])->name('kurir.password-reset.submit');

Route::view('/onboarding', 'onboarding')->middleware('role:Pelanggan')->name('pelanggan.onboarding');
Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
Route::get('/produk/{id}', [ProdukController::class, 'show'])->name('produk.show');
Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
Route::get('/pesanan/{id}', [PesananController::class, 'show'])->middleware('role:Pelanggan')->name('pesanan.show');
Route::get('/keranjang', [ProdukController::class, 'keranjang'])->name('keranjang.index');
Route::post('/keranjang/tambah/{id}', [ProdukController::class, 'tambahKeranjang'])->name('keranjang.tambah');
Route::put('/keranjang/{id}', [ProdukController::class, 'updateKeranjang'])->name('keranjang.update');
Route::delete('/keranjang/{id}', [ProdukController::class, 'hapusKeranjang'])->name('keranjang.hapus');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

Route::get('/pesanan/{id}/pembayaran', [PembayaranController::class, 'show'])->middleware('role:Pelanggan')->name('pembayaran.show');
Route::get('/pesanan/{id}/pembayaran/struk', [PembayaranController::class, 'receipt'])->name('pembayaran.receipt');
Route::get('/pesanan/{id}/pembayaran/unggah', [PembayaranController::class, 'uploadForm'])->middleware('role:Pelanggan')->name('pembayaran.upload.form');
Route::post('/pesanan/{id}/pembayaran/metode', [PembayaranController::class, 'pilihMetode'])->middleware('role:Pelanggan')->name('pembayaran.method');
Route::post('/pesanan/{id}/pembayaran/unggah', [PembayaranController::class, 'uploadBukti'])->middleware('role:Pelanggan')->name('pembayaran.upload');
Route::post('/pesanan/{id}/return', [ReturnController::class, 'store'])->middleware('role:Pelanggan')->name('pesanan.return.store');
Route::get('/pesanan/{orderId}/return/{returnId}/bukti', [ReturnController::class, 'proof'])->name('pesanan.return.proof');

Route::get('/admin-login', fn () => redirect()->route('login'))->name('admin.login');
Route::post('/admin-login', [AuthController::class, 'login'])->name('admin.login.process');
Route::get('/kurir-login', fn () => redirect()->route('login'))->name('kurir.login');
Route::post('/kurir-login', [AuthController::class, 'login'])->name('kurir.login.process');

Route::middleware('role:Admin')->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

    Route::get('/products', [AdminController::class, 'products'])->name('products.index');
    Route::get('/products/best-sellers', [AdminController::class, 'bestSellers'])->name('products.best-sellers');
    Route::get('/products/create', [AdminController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{id}/edit', [AdminController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{id}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{id}', [AdminController::class, 'deleteProduct'])->name('products.destroy');

    Route::get('/inventory', [AdminController::class, 'inventory'])->name('inventory');
    Route::put('/inventory/{id}', [AdminController::class, 'updateInventory'])->name('inventory.update');
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/shipping-rates', [AdminController::class, 'shippingRates'])->name('shipping-rates');
    Route::put('/shipping-rates', [AdminController::class, 'updateShippingRates'])->name('shipping-rates.update');
    Route::get('/orders/{id}', [AdminController::class, 'orderDetail'])->name('orders.show');
    Route::put('/orders/{id}', [AdminController::class, 'updateOrder'])->name('orders.update');
    Route::put('/orders/{id}/ongkir', [AdminController::class, 'updateShippingCost'])->name('orders.ongkir.update');
    Route::get('/payments/{id}', [AdminController::class, 'paymentReviewPage'])->name('payments.show');
    Route::put('/payments/{id}', [AdminController::class, 'reviewPayment'])->name('payments.review');
    Route::get('/payments/{id}/proof', [AdminController::class, 'paymentProof'])->name('payments.proof');
    Route::get('/payments/{id}/receipt', [PembayaranController::class, 'receipt'])->name('payments.receipt');
    Route::get('/shipments', [AdminController::class, 'shipments'])->name('shipments');
    Route::put('/shipments/{id}/assign', [AdminController::class, 'assignCourier'])->name('shipments.assign');
    Route::put('/shipments/{id}', [AdminController::class, 'updateShipment'])->name('shipments.update');

    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/reports/export', [AdminController::class, 'exportReports'])->name('reports.export');
    Route::get('/staff', [AdminController::class, 'staff'])->name('staff');
    Route::post('/staff', [AdminController::class, 'storeStaff'])->name('staff.store');
    Route::put('/password-recovery/{id}/approve', [AdminPasswordResetController::class, 'approve'])->name('password-recovery.approve');
    Route::put('/password-recovery/{id}/reject', [AdminPasswordResetController::class, 'reject'])->name('password-recovery.reject');
    Route::put('/staff/{id}/approve', [AdminController::class, 'approveCourier'])->name('staff.approve');
    Route::put('/staff/{id}/password-reset/approve', [AdminController::class, 'approveCourierPasswordReset'])->name('staff.password-reset.approve');
    Route::put('/staff/{id}/password-reset/reject', [AdminController::class, 'rejectCourierPasswordReset'])->name('staff.password-reset.reject');
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
    Route::get('/returns', [ReturnController::class, 'adminIndex'])->name('returns');
    Route::get('/orders/{orderId}/returns/{returnId}/proof', [ReturnController::class, 'proof'])->name('returns.proof');
    Route::put('/orders/{orderId}/returns/{returnId}/review', [ReturnController::class, 'review'])->name('returns.review');
    Route::put('/orders/{orderId}/returns/{returnId}/status', [ReturnController::class, 'updateStatus'])->name('returns.status');
    Route::put('/orders/{orderId}/returns/{returnId}/refund', [ReturnController::class, 'updateRefund'])->name('returns.refund');
});

Route::middleware('role:Kurir')->prefix('kurir')->name('kurir.')->group(function (): void {
    Route::post('/logout', [KurirController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [KurirController::class, 'dashboard'])->name('dashboard');
    Route::get('/shipments', [KurirController::class, 'shipments'])->name('shipments');
    Route::get('/shipments/{id}', [KurirController::class, 'show'])->name('shipments.show');
    Route::put('/shipments/{id}', [KurirController::class, 'updateStatus'])->name('shipments.update');
    Route::get('/shipments/{id}/confirmation', [KurirController::class, 'confirmation'])->name('confirmation');
    Route::get('/profile', [KurirController::class, 'profile'])->name('profile');
    Route::put('/profile', [KurirController::class, 'updateProfile'])->name('profile.update');
    Route::get('/password/change', [KurirController::class, 'forcePasswordChangeForm'])->name('password.change');
    Route::post('/password/change', [KurirController::class, 'updateTemporaryPassword'])->name('password.update-temporary');
});

Route::put('/profil/update', [PelangganController::class, 'updateProfile'])->middleware('role:Pelanggan')->name('pelanggan.profile.update');
Route::get('/profil/pemulihan-kode', [PelangganController::class, 'recoveryCodePage'])->middleware('role:Pelanggan')->name('pelanggan.recovery-code');
Route::post('/profil/pemulihan-kode', [PelangganController::class, 'issueRecoveryCode'])->middleware('role:Pelanggan')->name('pelanggan.recovery-code.issue');
Route::post('/profil/pemulihan-kode/lanjut', [PelangganController::class, 'continuePasswordRecovery'])->middleware('role:Pelanggan')->name('pelanggan.recovery-code.continue');
Route::post('/profil/verifikasi-email', [PelangganController::class, 'sendEmailVerification'])->middleware('role:Pelanggan')->name('pelanggan.email.verify.send');
Route::post('/logout', [PelangganController::class, 'logout'])->name('pelanggan.logout');

// Contoh rute autentikasi bawaan (sesuaikan jika ada di project kamu)
// Auth::routes();

// Rute Utama Pelanggan & Profil
Route::get('/profil', [PelangganController::class, 'profile'])->middleware('role:Pelanggan')->name('pelanggan.profile');
Route::put('/profil/update-lama', [PelangganController::class, 'updateProfile'])->middleware('role:Pelanggan')->name('pelanggan.update-profile');

// ==========================================
// RUTE PEMULIHAN AKUN & LUPA PASSWORD (BARU)
// ==========================================

// 1. Halaman Lupa Password (Publik - Sesuai Desain Foto 1)
// Halaman & Proses Pembuatan Password Baru
Route::get('/password/baru', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/baru', [PasswordResetController::class, 'updatePassword'])->name('password.update');

Route::get('/admin/password/reset', [AdminPasswordResetController::class, 'resetForm'])->name('admin.password.reset');
Route::post('/admin/password/reset', [AdminPasswordResetController::class, 'reset'])->name('admin.password.update');

// 2. Tombol Terbitkan Kode Pemulihan di Menu Profil (Sesuai Desain Foto 2 - Tanpa Password Saat Ini)