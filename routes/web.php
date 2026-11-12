<?php

use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\PlanController as AdminPlanController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Member\AttendanceController as MemberAttendanceController;
use App\Http\Controllers\Member\CheckoutController as MemberCheckoutController;
use App\Http\Controllers\Member\PaymentController as MemberPaymentController;
use App\Http\Controllers\Member\PlanController as MemberPlanController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // ==========================================
    // MEMBER ROUTES
    // ==========================================
    Route::middleware('role:member')->prefix('member')->name('member.')->group(function () {
        // Pilihan Paket Membership
        Route::get('plans', [MemberPlanController::class, 'index'])->name('plans.index');

        // Checkout & Pembayaran Paket
        Route::get('checkout/{plan}', [MemberCheckoutController::class, 'show'])->name('checkout.show');
        Route::post('checkout/{plan}/voucher', [MemberCheckoutController::class, 'checkVoucher'])->name('checkout.voucher');
        Route::post('checkout/{plan}', [MemberCheckoutController::class, 'process'])->name('checkout.process');

        // Riwayat Pembayaran Member
        Route::get('payments', [MemberPaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/{payment}', [MemberPaymentController::class, 'show'])->name('payments.show');

        // Riwayat Kunjungan / Presensi Member
        Route::get('attendances', [MemberAttendanceController::class, 'index'])->name('attendances.index');
    });

    // ==========================================
    // ADMIN ROUTES
    // ==========================================
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // Master Data CRUD
        Route::resource('plans', AdminPlanController::class)->except('show');
        Route::resource('vouchers', AdminVoucherController::class)->except('show');
        Route::resource('users', AdminUserController::class)->except('show');

        // Validasi Pembayaran
        Route::get('payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');
        Route::post('payments/{payment}/approve', [AdminPaymentController::class, 'approve'])->name('payments.approve');
        Route::post('payments/{payment}/reject', [AdminPaymentController::class, 'reject'])->name('payments.reject');

        // Presensi & Check-in
        Route::get('attendances', [AdminAttendanceController::class, 'index'])->name('attendances.index');
        Route::post('attendances', [AdminAttendanceController::class, 'store'])->name('attendances.store');
        Route::delete('attendances/{attendance}', [AdminAttendanceController::class, 'destroy'])->name('attendances.destroy');

        // Laporan Keuangan
        Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');
    });
});
