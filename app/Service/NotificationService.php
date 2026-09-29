<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\BookingCreated;
use App\Notifications\BookingStatusChanged;
use App\Notifications\BookingConfirmed;

class NotificationService
{
    public function notifyNewBooking($booking): void
    {
        // Notify barber
        $barberUser = $booking->barber->user;
        $barberUser->notify(new BookingCreated($booking));

        // Store in database
        \DB::table('notifications')->insert([
            'id' => \Str::uuid(),
            'type' => 'App\Notifications\BookingCreated',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $barberUser->id,
            'data' => json_encode([
                'title' => 'Booking Baru!',
                'message' => "Ada booking baru dari {$booking->customer->name}",
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function notifyStatusChange($booking, $oldStatus): void
    {
        $customer = $booking->customer;
        $customer->notify(new BookingStatusChanged($booking, $oldStatus));
    }
}