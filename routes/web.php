<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;
use App\Http\Controllers\Customer\ReviewController;
use App\Http\Controllers\Barber\DashboardController as BarberDashboardController;
use App\Http\Controllers\Barber\BookingController as BarberBookingController;
use App\Http\Controllers\Barber\ScheduleController;
use App\Http\Controllers\Barber\PortfolioController;
use App\Http\Controllers\Barber\EarningController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;

// ============ PUBLIC ROUTES ============
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/services', [HomeController::class, 'services'])->name('services');
Route::get('/barbers', [HomeController::class, 'barbers'])->name('barbers');
Route::get('/barbers/{barber}', [HomeController::class, 'barberDetail'])->name('barbers.detail');

// ============ AUTH ROUTES (dari Breeze) ============
require __DIR__.'/auth.php';

// ============ REDIRECT AFTER LOGIN ============
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->hasRole('admin')) return redirect()->route('admin.dashboard');
    if ($user->hasRole('barber')) return redirect()->route('barber.dashboard');
    return redirect()->route('customer.bookings.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// ============ CUSTOMER ROUTES ============
Route::middleware(['auth', 'verified', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    // Profile
    Route::get('/profile', [CustomerProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [CustomerProfileController::class, 'update'])->name('profile.update');

    // Addresses
    Route::resource('addresses', AddressController::class)->except(['show']);

    // Bookings
    Route::get('/booking/create', [CustomerBookingController::class, 'create'])->name('bookings.create');
    Route::get('/booking/select-barber', [CustomerBookingController::class, 'selectBarber'])->name('bookings.select-barber');
    Route::get('/booking/select-schedule/{barber}', [CustomerBookingController::class, 'selectSchedule'])->name('bookings.select-schedule');
    Route::post('/booking/store', [CustomerBookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings', [CustomerBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [CustomerBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/cancel', [CustomerBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/payment', [CustomerBookingController::class, 'processPayment'])->name('bookings.payment');

    // Voucher
    Route::post('/voucher/check', [CustomerBookingController::class, 'checkVoucher'])->name('voucher.check');

    // Reviews
    Route::post('/bookings/{booking}/review', [ReviewController::class, 'store'])->name('reviews.store');
});

// ============ BARBER ROUTES ============
Route::middleware(['auth', 'verified', 'role:barber'])->prefix('barber')->name('barber.')->group(function () {
    Route::get('/dashboard', [BarberDashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [BarberDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [BarberDashboardController::class, 'updateProfile'])->name('profile.update');

    // Schedule
    Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');
    Route::post('/schedule', [ScheduleController::class, 'update'])->name('schedule.update');
    Route::post('/toggle-availability', [BarberDashboardController::class, 'toggleAvailability'])->name('toggle-availability');

    // Bookings
    Route::get('/bookings', [BarberBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [BarberBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/confirm', [BarberBookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings/{booking}/reject', [BarberBookingController::class, 'reject'])->name('bookings.reject');
    Route::post('/bookings/{booking}/on-the-way', [BarberBookingController::class, 'onTheWay'])->name('bookings.on-the-way');
    Route::post('/bookings/{booking}/arrived', [BarberBookingController::class, 'arrived'])->name('bookings.arrived');
    Route::post('/bookings/{booking}/start', [BarberBookingController::class, 'start'])->name('bookings.start');
    Route::post('/bookings/{booking}/complete', [BarberBookingController::class, 'complete'])->name('bookings.complete');

    // Portfolio
    Route::resource('portfolio', PortfolioController::class)->except(['show']);

    // Earnings
    Route::get('/earnings', [EarningController::class, 'index'])->name('earnings.index');

    // Reviews
    Route::post('/reviews/{review}/reply', [BarberDashboardController::class, 'replyReview'])->name('reviews.reply');
});

// ============ ADMIN ROUTES ============
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users Management
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Barber Management
    Route::get('/barbers', [UserController::class, 'barbers'])->name('barbers.index');
    Route::post('/barbers/{barber}/approve', [UserController::class, 'approveBarber'])->name('barbers.approve');
    Route::post('/barbers/{barber}/suspend', [UserController::class, 'suspendBarber'])->name('barbers.suspend');

    // Service Categories
    Route::resource('service-categories', ServiceCategoryController::class);

    // Services
    Route::resource('services', ServiceController::class);

    // Bookings
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/update-status', [AdminBookingController::class, 'updateStatus'])->name('bookings.update-status');

    // Vouchers
    Route::resource('vouchers', VoucherController::class);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('/reports/barber-performance', [ReportController::class, 'barberPerformance'])->name('reports.barber-performance');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});
