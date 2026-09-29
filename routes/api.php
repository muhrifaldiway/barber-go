<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookingApiController;
use App\Http\Controllers\Api\BarberApiController;

Route::middleware('auth:sanctum')->group(function () {
    // Get available time slots
    Route::get('/barbers/{barber}/available-slots', [BarberApiController::class, 'availableSlots']);

    // Get nearby barbers
    Route::get('/barbers/nearby', [BarberApiController::class, 'nearby']);

    // Check voucher
    Route::post('/voucher/check', [BookingApiController::class, 'checkVoucher']);

    // Booking status
    Route::get('/bookings/{booking}/status', [BookingApiController::class, 'status']);

    // Update barber location (for real-time tracking)
    Route::post('/barber/update-location', [BarberApiController::class, 'updateLocation']);
});