@extends('layouts.app')

@section('title', 'Profil ' . $barber->user->name . ' - BarberGo')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    {{-- Breadcrumb --}}
    <div class="mb-6">
        <a href="{{ route('barbers') }}" class="text-amber-600 hover:text-amber-700 text-sm font-medium">
            ← Kembali ke Semua Barber
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Profile Card --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
                <img src="{{ $barber->user->avatar_url }}" 
                     alt="{{ $barber->user->name }}" 
                     class="w-32 h-32 rounded-full object-cover mx-auto border-4 border-amber-500 mb-4">
                <h1 class="text-2xl font-bold text-gray-900">{{ $barber->user->name }}</h1>
                <p class="text-amber-600 font-medium text-sm mb-3">{{ $barber->speciality ?? 'Hair Stylist & Groomer' }}</p>

                <div class="flex items-center justify-center gap-1 text-amber-500 mb-4">
                    <span class="text-xl font-bold">★ {{ number_format($barber->rating_avg, 1) }}</span>
                    <span class="text-gray-400 text-sm">({{ $barber->total_reviews }} review)</span>
                </div>

                <div class="border-t border-gray-100 pt-4 text-left space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>Pengalaman:</span>
                        <span class="font-semibold text-gray-900">{{ $barber->experience_years }} Tahun</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Radius Layanan:</span>
                        <span class="font-semibold text-gray-900">{{ $barber->service_radius_km }} Km</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Order Selesai:</span>
                        <span class="font-semibold text-gray-900">{{ $barber->total_completed_bookings }} Pelanggan</span>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('customer.bookings.create') }}" 
                       class="block w-full bg-amber-500 hover:bg-amber-600 text-black font-bold py-3 rounded-xl transition shadow-md">
                        ✂️ Booking Barber Ini
                    </a>
                </div>
            </div>

            {{-- Jadwal Operasional --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-900 mb-4">📅 Jam Operasional</h3>
                <div class="space-y-2 text-sm">
                    @forelse($barber->schedules as $schedule)
                        <div class="flex justify-between py-1 border-b border-gray-50 last:border-0">
                            <span class="text-gray-600">{{ $schedule->day_name }}</span>
                            <span class="font-semibold {{ $schedule->is_available ? 'text-gray-900' : 'text-red-500' }}">
                                {{ $schedule->is_available ? date('H:i', strtotime($schedule->start_time)) . ' - ' . date('H:i', strtotime($schedule->end_time)) : 'Libur' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-500 italic">Jadwal belum dikonfigurasi.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Detail, Portofolio & Review --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Bio --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-3">Tentang Barber</h3>
                <p class="text-gray-600 leading-relaxed text-sm">
                    {{ $barber->bio ?? 'Belum ada deskripsi profil untuk barber ini.' }}
                </p>
            </div>

            {{-- Portofolio --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">📸 Portofolio Hasil Cukur</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @forelse($barber->portfolios as $portfolio)
                        <div class="group relative rounded-xl overflow-hidden aspect-square bg-gray-100">
                            <img src="{{ $portfolio->image_url }}" alt="Portfolio" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @if($portfolio->caption)
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-end p-2">
                                    <p class="text-white text-xs">{{ $portfolio->caption }}</p>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-400 italic col-span-3 text-sm">Belum ada foto portofolio yang diunggah.</p>
                    @endforelse
                </div>
            </div>

            {{-- Customer Reviews --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">💬 Ulasan Pelanggan</h3>
                <div class="space-y-4">
                    @forelse($barber->reviews as $review)
                        <div class="border-b border-gray-100 pb-4 last:border-0">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $review->customer->avatar_url }}" class="w-8 h-8 rounded-full object-cover">
                                    <span class="font-semibold text-sm text-gray-900">{{ $review->customer->name }}</span>
                                </div>
                                <div class="text-amber-400 text-sm">
                                    @for($i = 1; $i <= 5; $i++)
                                        {{ $i <= $review->rating ? '★' : '☆' }}
                                    @endfor
                                </div>
                            </div>
                            <p class="text-gray-600 text-sm">{{ $review->comment }}</p>
                            @if($review->barber_reply)
                                <div class="mt-2 bg-amber-50/60 p-3 rounded-lg text-xs text-gray-700">
                                    <span class="font-bold text-amber-800">Balasan Barber:</span> {{ $review->barber_reply }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-400 italic text-sm">Belum ada ulasan untuk barber ini.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection