<?php

namespace App\Http\Controllers\Barber;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $barber = auth()->user()->barberProfile;

        if (!$barber) {
            return redirect()->route('barber.profile')->with('info', 'Lengkapi profil terlebih dahulu.');
        }

        $todayBookings = Booking::where('barber_id', $barber->id)
            ->where('booking_date', today())
            ->whereNotIn('status', ['cancelled'])
            ->with('customer')
            ->orderBy('booking_time')
            ->get();

        $pendingBookings = Booking::where('barber_id', $barber->id)
            ->where('status', 'pending')
            ->with('customer')
            ->latest()
            ->get();

        $stats = [
            'total_completed' => Booking::where('barber_id', $barber->id)->completed()->count(),
            'this_month_completed' => Booking::where('barber_id', $barber->id)
                ->completed()
                ->whereMonth('completed_at', now()->month)
                ->count(),
            'this_month_earning' => Booking::where('barber_id', $barber->id)
                ->completed()
                ->whereMonth('completed_at', now()->month)
                ->sum('total_price') * ($barber->commission_rate / 100),
            'rating' => $barber->rating_avg,
            'total_reviews' => $barber->total_reviews,
        ];

        $recentReviews = Review::where('barber_id', $barber->id)
            ->with('customer')
            ->latest()
            ->take(5)
            ->get();

        return view('barber.dashboard', compact(
            'barber', 'todayBookings', 'pendingBookings', 'stats', 'recentReviews'
        ));
    }

    public function profile()
    {
        $barber = auth()->user()->barberProfile;
        return view('barber.profile', compact('barber'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'experience_years' => 'required|integer|min:0',
            'speciality' => 'nullable|string|max:255',
            'service_radius_km' => 'required|integer|min:1|max:50',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = auth()->user();
        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $path]);
        }

        $barberData = $request->only(['bio', 'experience_years', 'speciality', 'service_radius_km']);

        if ($user->barberProfile) {
            $user->barberProfile->update($barberData);
        } else {
            $user->barberProfile()->create(array_merge($barberData, [
                'status' => 'pending',
            ]));
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function toggleAvailability()
    {
        $barber = auth()->user()->barberProfile;
        $barber->update(['is_available' => !$barber->is_available]);

        $status = $barber->is_available ? 'tersedia' : 'tidak tersedia';

        return back()->with('success', "Status berhasil diubah menjadi {$status}.");
    }

    public function replyReview(Review $review, Request $request)
    {
        $request->validate(['barber_reply' => 'required|string|max:500']);

        if ($review->barber_id !== auth()->user()->barberProfile->id) {
            abort(403);
        }

        $review->update([
            'barber_reply' => $request->barber_reply,
            'replied_at' => now(),
        ]);

        return back()->with('success', 'Balasan berhasil disimpan.');
    }
}