<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BarberProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : now()->endOfMonth();

        // Revenue Summary
        $revenue = Booking::completed()
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->select(
                DB::raw('SUM(total_price) as total_revenue'),
                DB::raw('SUM(discount) as total_discount'),
                DB::raw('COUNT(*) as total_bookings'),
                DB::raw('AVG(total_price) as avg_booking_value')
            )
            ->first();

        // Daily Revenue Chart
        $dailyRevenue = Booking::completed()
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(completed_at) as date'),
                DB::raw('SUM(total_price) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Service popularity
        $popularServices = DB::table('booking_services')
            ->join('bookings', 'booking_services.booking_id', '=', 'bookings.id')
            ->where('bookings.status', 'completed')
            ->whereBetween('bookings.completed_at', [$startDate, $endDate])
            ->select(
                'booking_services.service_name',
                DB::raw('COUNT(*) as total_ordered'),
                DB::raw('SUM(booking_services.subtotal) as total_revenue')
            )
            ->groupBy('booking_services.service_name')
            ->orderByDesc('total_ordered')
            ->take(10)
            ->get();

        // Booking status breakdown
        $statusBreakdown = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return view('admin.reports.index', compact(
            'revenue', 'dailyRevenue', 'popularServices',
            'statusBreakdown', 'startDate', 'endDate'
        ));
    }

    public function barberPerformance(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : now()->endOfMonth();

        $barbers = BarberProfile::active()
            ->with('user')
            ->withCount([
                'bookings as completed_bookings' => function ($q) use ($startDate, $endDate) {
                    $q->completed()->whereBetween('completed_at', [$startDate, $endDate]);
                },
                'bookings as cancelled_bookings' => function ($q) use ($startDate, $endDate) {
                    $q->where('status', 'cancelled')
                        ->where('cancelled_by', 'barber')
                        ->whereBetween('cancelled_at', [$startDate, $endDate]);
                },
            ])
            ->withSum([
                'bookings as total_revenue' => function ($q) use ($startDate, $endDate) {
                    $q->completed()->whereBetween('completed_at', [$startDate, $endDate]);
                },
            ], 'total_price')
            ->orderByDesc('completed_bookings')
            ->get();

        return view('admin.reports.barber-performance', compact('barbers', 'startDate', 'endDate'));
    }

    public function export(Request $request)
    {
        // Export to Excel/PDF using maatwebsite/excel or dompdf
        // Implementation here...
    }
}