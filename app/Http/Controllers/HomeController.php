<?php

namespace App\Http\Controllers;

use App\Models\BarberProfile;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Review;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $services = Service::active()
            ->with('category')
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        $barbers = BarberProfile::active()
            ->available()
            ->with('user')
            ->orderByDesc('rating_avg')
            ->take(6)
            ->get();

        $reviews = Review::with(['customer', 'barber.user'])
            ->where('rating', '>=', 4)
            ->latest()
            ->take(6)
            ->get();

        $totalBarbers = BarberProfile::active()->count();
        $totalBookings = \App\Models\Booking::completed()->count();

        return view('home', compact(
            'services', 'barbers', 'reviews',
            'totalBarbers', 'totalBookings'
        ));
    }

    public function services()
    {
        $categories = ServiceCategory::active()
            ->with('activeServices')
            ->orderBy('sort_order')
            ->get();

        return view('services', compact('categories'));
    }

    public function barbers(Request $request)
    {
        $query = BarberProfile::active()
            ->with(['user', 'schedules']);

        if ($request->search) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })->orWhere('speciality', 'like', '%' . $request->search . '%');
        }

        if ($request->sort === 'rating') {
            $query->orderByDesc('rating_avg');
        } elseif ($request->sort === 'experience') {
            $query->orderByDesc('experience_years');
        } else {
            $query->orderByDesc('rating_avg');
        }

        $barbers = $query->paginate(12);

        return view('barbers.index', compact('barbers'));
    }

    public function barberDetail(BarberProfile $barber)
    {
        $barber->load(['user', 'schedules', 'portfolios', 'reviews.customer']);

        return view('barbers.detail', compact('barber'));
    }
}