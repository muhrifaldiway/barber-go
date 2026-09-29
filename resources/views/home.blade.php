<!-- resources/views/home.blade.php -->
@extends('layouts.app')

@section('title', 'BarberGo - Barbershop Keliling Profesional')

@section('content')
{{-- Hero Section --}}
<section class="relative bg-gradient-to-br from-gray-900 via-gray-800 to-black text-white">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                Barbershop <span class="text-amber-400">Keliling</span>
                <br>Profesional
            </h1>
            <p class="text-xl md:text-2xl text-gray-300 mb-8 max-w-3xl mx-auto">
                Potong rambut tanpa keluar rumah. Barber profesional datang ke lokasi Anda.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('customer.bookings.create') }}"
                   class="bg-amber-500 hover:bg-amber-600 text-black font-bold py-4 px-8 rounded-full text-lg transition transform hover:scale-105 shadow-lg">
                    ✂️ Booking Sekarang
                </a>
                <a href="{{ route('services') }}"
                   class="border-2 border-white hover:bg-white hover:text-black font-bold py-4 px-8 rounded-full text-lg transition">
                    Lihat Layanan
                </a>
            </div>
        </div>

        {{-- Stats --}}
        <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-amber-400">{{ $totalBarbers }}+</div>
                <div class="text-gray-400">Barber Profesional</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-amber-400">{{ $totalBookings }}+</div>
                <div class="text-gray-400">Booking Selesai</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-amber-400">4.8</div>
                <div class="text-gray-400">Rating Rata-rata</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-amber-400">24/7</div>
                <div class="text-gray-400">Booking Online</div>
            </div>
        </div>
    </div>
</section>

{{-- How It Works --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Cara Kerjanya</h2>
            <p class="text-gray-600 mt-4 text-lg">Mudah, cepat, dan profesional</p>
        </div>
        <div class="grid md:grid-cols-4 gap-8">
            @php
                $steps = [
                    ['icon' => '📱', 'title' => 'Pilih Layanan', 'desc' => 'Pilih layanan potong rambut yang Anda inginkan'],
                    ['icon' => '💈', 'title' => 'Pilih Barber', 'desc' => 'Pilih barber berdasarkan rating dan keahlian'],
                    ['icon' => '📍', 'title' => 'Tentukan Lokasi', 'desc' => 'Masukkan alamat dan waktu yang diinginkan'],
                    ['icon' => '✅', 'title' => 'Barber Datang', 'desc' => 'Barber datang ke lokasi Anda tepat waktu'],
                ];
            @endphp
            @foreach($steps as $index => $step)
            <div class="text-center">
                <div class="w-20 h-20 mx-auto bg-amber-100 rounded-full flex items-center justify-center text-4xl mb-4">
                    {{ $step['icon'] }}
                </div>
                <div class="bg-amber-500 text-white w-8 h-8 rounded-full flex items-center justify-center mx-auto mb-4 font-bold">
                    {{ $index + 1 }}
                </div>
                <h3 class="text-xl font-semibold mb-2">{{ $step['title'] }}</h3>
                <p class="text-gray-600">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Services --}}
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Layanan Kami</h2>
            <p class="text-gray-600 mt-4 text-lg">Pilih layanan terbaik untuk Anda</p>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($services as $service)
            <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition p-6">
                <div class="w-16 h-16 bg-amber-100 rounded-xl flex items-center justify-center text-2xl mb-4">
                    ✂️
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $service->name }}</h3>
                <p class="text-gray-600 text-sm mb-4">{{ $service->description }}</p>
                <div class="flex items-center justify-between">
                    <span class="text-2xl font-bold text-amber-600">{{ $service->formatted_price }}</span>
                    <span class="text-sm text-gray-500">{{ $service->duration_minutes }} menit</span>
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-10">
            <a href="{{ route('services') }}" class="text-amber-600 hover:text-amber-700 font-semibold text-lg">
                Lihat Semua Layanan →
            </a>
        </div>
    </div>
</section>

{{-- Top Barbers --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Barber Terbaik Kami</h2>
            <p class="text-gray-600 mt-4 text-lg">Profesional berpengalaman siap melayani Anda</p>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($barbers as $barber)
            <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition overflow-hidden border">
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <img src="{{ $barber->user->avatar_url }}"
                             alt="{{ $barber->user->name }}"
                             class="w-16 h-16 rounded-full object-cover">
                        <div>
                            <h3 class="font-semibold text-lg">{{ $barber->user->name }}</h3>
                            <p class="text-gray-500 text-sm">{{ $barber->speciality ?? 'Barber' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 text-sm text-gray-600 mb-4">
                        <span class="flex items-center gap-1">
                            ⭐ {{ number_format($barber->rating_avg, 1) }}
                        </span>
                        <span>{{ $barber->total_reviews }} review</span>
                        <span>{{ $barber->experience_years }} thn pengalaman</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">{{ Str::limit($barber->bio, 100) }}</p>
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                            {{ $barber->is_available ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $barber->is_available ? '🟢 Tersedia' : '🔴 Tidak Tersedia' }}
                        </span>
                        <a href="{{ route('barbers.detail', $barber) }}"
                           class="text-amber-600 hover:text-amber-700 font-medium text-sm">
                            Lihat Profil →
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Reviews --}}
@if($reviews->count())
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Apa Kata Mereka</h2>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($reviews as $review)
            <div class="bg-white rounded-2xl shadow-md p-6">
                <div class="flex items-center gap-1 mb-3">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="{{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-300' }}">★</span>
                    @endfor
                </div>
                <p class="text-gray-700 mb-4">"{{ Str::limit($review->comment, 150) }}"</p>
                <div class="flex items-center gap-3">
                    <img src="{{ $review->customer->avatar_url }}" class="w-10 h-10 rounded-full">
                    <div>
                        <p class="font-medium text-sm">{{ $review->customer->name }}</p>
                        <p class="text-gray-500 text-xs">Barber: {{ $review->barber->user->name }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA --}}
<section class="py-20 bg-gradient-to-r from-amber-500 to-amber-600">
    <div class="max-w-4xl mx-auto text-center px-4">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
            Siap Tampil Rapi Tanpa Ribet?
        </h2>
        <p class="text-amber-100 text-xl mb-8">
            Booking sekarang dan barber profesional akan datang ke lokasi Anda
        </p>
        <a href="{{ route('customer.bookings.create') }}"
           class="bg-black hover:bg-gray-900 text-white font-bold py-4 px-10 rounded-full text-lg transition transform hover:scale-105 inline-block shadow-lg">
            ✂️ Booking Sekarang - Gratis Registrasi
        </a>
    </div>
</section>
@endsection