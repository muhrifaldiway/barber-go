@extends('layouts.app')

@section('title', 'Daftar Layanan - BarberGo')

@section('content')
{{-- Header Banner --}}
<section class="bg-gradient-to-r from-gray-900 via-gray-800 to-black text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl mb-4">
            Layanan <span class="text-amber-400">Barbershop Keliling</span>
        </h1>
        <p class="text-lg text-gray-300 max-w-2xl mx-auto">
            Pilih layanan perawatan rambut dan grooming terbaik kami. Barber profesional siap datang langsung ke tempat Anda.
        </p>
    </div>
</section>

{{-- Services Section Grouped by Category --}}
<section class="py-16 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
        @forelse($categories as $category)
            <div>
                {{-- Category Header --}}
                <div class="flex items-center gap-3 border-b border-gray-200 pb-4 mb-8">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 text-black flex items-center justify-center text-xl font-bold">
                        ✂️
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $category->name }}</h2>
                        <p class="text-sm text-gray-500">{{ $category->description }}</p>
                    </div>
                </div>

                {{-- Services Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($category->activeServices as $service)
                        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition border border-gray-100 flex flex-col justify-between overflow-hidden">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="text-lg font-bold text-gray-900">{{ $service->name }}</h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        ⏱️ {{ $service->duration_minutes }} mnt
                                    </span>
                                </div>
                                <p class="text-gray-600 text-sm mb-4 leading-relaxed">
                                    {{ $service->description ?? 'Layanan perawatan terbaik dengan peralatan higienis dan terstandar.' }}
                                </p>
                            </div>
                            
                            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between">
                                <div>
                                    <span class="text-xs text-gray-500 block">Tarif Layanan</span>
                                    <span class="text-xl font-black text-amber-600">{{ $service->formatted_price }}</span>
                                </div>
                                <a href="{{ route('customer.bookings.create') }}" 
                                   class="inline-flex items-center bg-gray-900 hover:bg-amber-500 hover:text-black text-white px-4 py-2 rounded-xl text-sm font-semibold transition shadow-sm">
                                    Pesan Layanan
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 italic col-span-3">Belum ada layanan aktif di kategori ini.</p>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="text-center py-16">
                <p class="text-gray-500 text-lg">Belum ada kategori layanan yang tersedia.</p>
            </div>
        @endforelse
    </div>
</section>

{{-- CTA Box --}}
<section class="bg-amber-500 py-12">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h2 class="text-2xl md:text-3xl font-bold text-black mb-3">Tidak yakin ingin pilih yang mana?</h2>
        <p class="text-gray-900 mb-6">Konsultasikan kebutuhan model rambut Anda langsung saat barber tiba di lokasi!</p>
        <a href="{{ route('customer.bookings.create') }}" class="bg-black hover:bg-gray-800 text-white font-bold px-8 py-3 rounded-full transition shadow-md">
            Mulai Booking Sekarang
        </a>
    </div>
</section>
@endsection