<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\BarberProfile;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_customers' => User::role('customer')->count(),
            'total_barbers' => BarberProfile::active()->count(),
            'pending_barbers' => BarberProfile::where('status', 'pending')->count(),
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::pending()->count(),
            'completed_bookings' => Booking::completed()->count(),
            'today_bookings' => Booking::whereDate('booking_date', today())->count(),
            'this_month_revenue' => Booking::completed()
                ->whereMonth('completed_at', now()->month)
                ->whereYear('completed_at', now()->year)
                ->sum('total_price'),
            'avg_rating' => Review::avg('rating') ?? 0,
        ];

        // Revenue chart (last 7 days)
        $revenueChart = Booking::completed()
            ->where('completed_at', '>=', now()->subDays(7))
            ->select(
                DB::raw('DATE(completed_at) as date'),
                DB::raw('SUM(total_price) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Recent bookings
        $recentBookings = Booking::with(['customer', 'barber.user'])
            ->latest()
            ->take(10)
            ->get();

        // Top barbers
        $topBarbers = BarberProfile::active()
            ->with('user')
            ->orderByDesc('rating_avg')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'revenueChart', 'recentBookings', 'topBarbers'
        ));
    }
}