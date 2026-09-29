<?php

namespace App\Http\Controllers\Barber;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    private function getBarber()
    {
        return auth()->user()->barberProfile;
    }

    public function index(Request $request)
    {
        $barber = $this->getBarber();

        $query = Booking::where('barber_id', $barber->id)
            ->with(['customer', 'services']);

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->date) {
            $query->where('booking_date', $request->date);
        }

        $bookings = $query->latest()->paginate(10);

        return view('barber.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $barber = $this->getBarber();
        if ($booking->barber_id !== $barber->id) abort(403);

        $booking->load(['customer', 'services', 'review']);

        return view('barber.bookings.show', compact('booking'));
    }

    public function confirm(Booking $booking)
    {
        $barber = $this->getBarber();
        if ($booking->barber_id !== $barber->id) abort(403);
        if (!$booking->isPending()) return back()->with('error', 'Status booking tidak valid.');

        $booking->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);

        // TODO: Notify customer

        return back()->with('success', 'Booking berhasil dikonfirmasi.');
    }

    public function reject(Booking $booking, Request $request)
    {
        $barber = $this->getBarber();
        if ($booking->barber_id !== $barber->id) abort(403);

        $request->validate(['cancel_reason' => 'required|string|max:255']);

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancel_reason' => $request->cancel_reason,
            'cancelled_by' => 'barber',
        ]);

        return back()->with('success', 'Booking berhasil ditolak.');
    }

    public function onTheWay(Booking $booking)
    {
        $barber = $this->getBarber();
        if ($booking->barber_id !== $barber->id) abort(403);
        if (!$booking->isConfirmed()) return back()->with('error', 'Status tidak valid.');

        $booking->update(['status' => 'on_the_way']);

        return back()->with('success', 'Status diupdate: Dalam Perjalanan.');
    }

    public function arrived(Booking $booking)
    {
        $barber = $this->getBarber();
        if ($booking->barber_id !== $barber->id) abort(403);
        if (!$booking->isOnTheWay()) return back()->with('error', 'Status tidak valid.');

        $booking->update(['status' => 'arrived']);

        return back()->with('success', 'Status diupdate: Sudah Sampai.');
    }

    public function start(Booking $booking)
    {
        $barber = $this->getBarber();
        if ($booking->barber_id !== $barber->id) abort(403);
        if (!$booking->isArrived()) return back()->with('error', 'Status tidak valid.');

        $booking->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return back()->with('success', 'Status diupdate: Sedang Dikerjakan.');
    }

    public function complete(Booking $booking)
    {
        $barber = $this->getBarber();
        if ($booking->barber_id !== $barber->id) abort(403);
        if (!$booking->isInProgress()) return back()->with('error', 'Status tidak valid.');

        $booking->update([
            'status' => 'completed',
            'completed_at' => now(),
            'payment_status' => $booking->payment_method === 'cod' ? 'paid' : $booking->payment_status,
        ]);

        // Update barber stats
        $barber->increment('total_completed_bookings');

        return back()->with('success', 'Booking selesai! Terima kasih.');
    }
}