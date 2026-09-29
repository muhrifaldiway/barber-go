<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BarberProfile;
use App\Models\BarberSchedule;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BookingService
{
    /**
     * Get available time slots for a barber on a specific date
     */
    public function getAvailableSlots(BarberProfile $barber, string $date): array
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        $schedule = BarberSchedule::where('barber_id', $barber->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->first();

        if (!$schedule) return [];

        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);

        // Get existing bookings for this barber on this date
        $existingBookings = Booking::where('barber_id', $barber->id)
            ->where('booking_date', $date)
            ->whereNotIn('status', ['cancelled'])
            ->get(['booking_time', 'estimated_end_time']);

        $slots = [];
        $slotDuration = 30; // 30 minute slots

        $current = $startTime->copy();
        while ($current->lt($endTime)) {
            $slotStart = $current->format('H:i');
            $slotEnd = $current->copy()->addMinutes($slotDuration)->format('H:i');

            // Check if this slot conflicts with existing bookings
            $isAvailable = true;
            foreach ($existingBookings as $booking) {
                $bookingStart = Carbon::parse($booking->booking_time);
                $bookingEnd = Carbon::parse($booking->estimated_end_time);

                if ($current->between($bookingStart, $bookingEnd->subMinute()) ||
                    $current->copy()->addMinutes($slotDuration)->between($bookingStart->addMinute(), $bookingEnd)) {
                    $isAvailable = false;
                    break;
                }
            }

            // Don't show past time slots for today
            if (Carbon::parse($date)->isToday() && $current->lt(now())) {
                $isAvailable = false;
            }

            $slots[] = [
                'time' => $slotStart,
                'end' => $slotEnd,
                'available' => $isAvailable,
            ];

            $current->addMinutes($slotDuration);
        }

        return $slots;
    }

    /**
     * Calculate travel fee based on distance
     */
    public function calculateTravelFee(float $distance): float
    {
        if ($distance <= 5) return 0;
        if ($distance <= 10) return 10000;
        if ($distance <= 20) return 20000;
        return 30000;
    }

    /**
     * Get nearby barbers based on customer location
     */
    public function getNearbyBarbers(float $lat, float $lng, int $radiusKm = 15): Collection
    {
        return BarberProfile::available()
            ->with('user')
            ->get()
            ->filter(function ($barber) use ($lat, $lng, $radiusKm) {
                $distance = $barber->distanceTo($lat, $lng);
                $barber->distance = $distance;
                return $distance <= $radiusKm;
            })
            ->sortBy('distance');
    }
}