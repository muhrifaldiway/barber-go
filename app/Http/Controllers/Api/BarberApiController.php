<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarberProfile;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BarberApiController extends Controller
{
    public function availableSlots(BarberProfile $barber, Request $request)
    {
        $request->validate(['date' => 'required|date|after_or_equal:today']);

        $bookingService = new BookingService();
        $slots = $bookingService->getAvailableSlots($barber, $request->date);

        return response()->json(['slots' => $slots]);
    }

    public function nearby(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|integer|max:50',
        ]);

        $bookingService = new BookingService();
        $barbers = $bookingService->getNearbyBarbers(
            $request->latitude,
            $request->longitude,
            $request->radius ?? 15
        );

        return response()->json(['barbers' => $barbers->values()]);
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $barber = auth()->user()->barberProfile;
        $barber->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json(['success' => true]);
    }
}