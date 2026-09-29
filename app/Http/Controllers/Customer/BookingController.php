<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingService;
use App\Models\BarberProfile;
use App\Models\Service;
use App\Models\Voucher;
use App\Services\BookingService as BookingServiceHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::where('customer_id', auth()->id())
            ->with(['barber.user', 'services'])
            ->latest();

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $bookings = $query->paginate(10);

        return view('customer.bookings.index', compact('bookings'));
    }

    public function create()
    {
        $services = Service::active()
            ->with('category')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category.name');

        return view('customer.bookings.create', compact('services'));
    }

    public function selectBarber(Request $request)
    {
        $request->validate([
            'services' => 'required|array|min:1',
            'services.*' => 'exists:services,id',
        ]);

        $selectedServices = Service::whereIn('id', $request->services)->get();

        $barbers = BarberProfile::available()
            ->with('user')
            ->orderByDesc('rating_avg')
            ->get();

        $totalPrice = $selectedServices->sum('price');
        $totalDuration = $selectedServices->sum('duration_minutes');

        return view('customer.bookings.select-barber', compact(
            'selectedServices', 'barbers', 'totalPrice', 'totalDuration'
        ));
    }

    public function selectSchedule(BarberProfile $barber, Request $request)
    {
        $barber->load(['user', 'schedules']);

        $selectedServiceIds = $request->services;
        $selectedServices = Service::whereIn('id', $selectedServiceIds)->get();

        $addresses = auth()->user()->addresses;

        return view('customer.bookings.select-schedule', compact(
            'barber', 'selectedServices', 'addresses'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barber_id' => 'required|exists:barber_profiles,id',
            'services' => 'required|array|min:1',
            'services.*' => 'exists:services,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'payment_method' => 'required|in:cod,bank_transfer,ewallet',
            'notes' => 'nullable|string|max:500',
            'voucher_code' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $services = Service::whereIn('id', $request->services)->get();
            $subtotal = $services->sum('price');
            $totalDuration = $services->sum('duration_minutes');

            // Calculate discount
            $discount = 0;
            if ($request->voucher_code) {
                $voucher = Voucher::where('code', $request->voucher_code)->first();
                if ($voucher && $voucher->isValid()) {
                    $discount = $voucher->calculateDiscount($subtotal);
                    $voucher->increment('used_count');
                }
            }

            // Calculate travel fee (bisa disesuaikan)
            $travelFee = 0; // Atau hitung berdasarkan jarak

            $totalPrice = $subtotal - $discount + $travelFee;

            // Calculate estimated end time
            $startTime = \Carbon\Carbon::parse($request->booking_time);
            $estimatedEndTime = $startTime->copy()->addMinutes($totalDuration);

            // Check jika barber sudah ada booking di waktu yang sama
            $conflictingBooking = Booking::where('barber_id', $request->barber_id)
                ->where('booking_date', $request->booking_date)
                ->whereIn('status', ['pending', 'confirmed', 'on_the_way', 'arrived', 'in_progress'])
                ->where(function ($q) use ($request, $estimatedEndTime) {
                    $q->whereBetween('booking_time', [$request->booking_time, $estimatedEndTime->format('H:i')])
                        ->orWhereBetween('estimated_end_time', [$request->booking_time, $estimatedEndTime->format('H:i')]);
                })
                ->exists();

            if ($conflictingBooking) {
                return back()->with('error', 'Barber sudah memiliki booking di waktu tersebut. Silakan pilih waktu lain.');
            }

            // Create booking
            $booking = Booking::create([
                'customer_id' => auth()->id(),
                'barber_id' => $request->barber_id,
                'booking_date' => $request->booking_date,
                'booking_time' => $request->booking_time,
                'estimated_end_time' => $estimatedEndTime->format('H:i'),
                'address' => $request->address,
                'address_detail' => $request->address_detail,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'notes' => $request->notes,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'voucher_code' => $request->voucher_code,
                'travel_fee' => $travelFee,
                'total_price' => $totalPrice,
                'payment_method' => $request->payment_method,
            ]);

            // Create booking services
            foreach ($services as $service) {
                BookingService::create([
                    'booking_id' => $booking->id,
                    'service_id' => $service->id,
                    'service_name' => $service->name,
                    'price' => $service->price,
                    'quantity' => 1,
                    'subtotal' => $service->price,
                ]);
            }

            DB::commit();

            // TODO: Send notification to barber

            return redirect()->route('customer.bookings.show', $booking)
                ->with('success', 'Booking berhasil dibuat! Menunggu konfirmasi barber.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Booking $booking)
    {
        // Ensure customer can only view their own booking
        if ($booking->customer_id !== auth()->id()) {
            abort(403);
        }

        $booking->load(['barber.user', 'services', 'payment', 'review']);

        return view('customer.bookings.show', compact('booking'));
    }

    public function cancel(Booking $booking, Request $request)
    {
        if ($booking->customer_id !== auth()->id()) {
            abort(403);
        }

        if (!$booking->canBeCancelled()) {
            return back()->with('error', 'Booking tidak dapat dibatalkan.');
        }

        $request->validate([
            'cancel_reason' => 'required|string|max:255',
        ]);

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancel_reason' => $request->cancel_reason,
            'cancelled_by' => 'customer',
        ]);

        // TODO: Send notification to barber
        // TODO: Process refund if already paid

        return redirect()->route('customer.bookings.index')
            ->with('success', 'Booking berhasil dibatalkan.');
    }

    public function checkVoucher(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric',
        ]);

        $voucher = Voucher::where('code', strtoupper($request->code))->first();

        if (!$voucher) {
            return response()->json(['valid' => false, 'message' => 'Kode voucher tidak ditemukan.']);
        }

        if (!$voucher->isValid()) {
            return response()->json(['valid' => false, 'message' => 'Voucher sudah tidak berlaku.']);
        }

        if ($request->subtotal < $voucher->min_order) {
            return response()->json([
                'valid' => false,
                'message' => 'Minimal order Rp ' . number_format($voucher->min_order, 0, ',', '.'),
            ]);
        }

        $discount = $voucher->calculateDiscount($request->subtotal);

        return response()->json([
            'valid' => true,
            'discount' => $discount,
            'formatted_discount' => 'Rp ' . number_format($discount, 0, ',', '.'),
            'message' => 'Voucher berhasil diterapkan!',
        ]);
    }
}