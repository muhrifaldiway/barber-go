@extends('layouts.app')

@section('title', 'Cari Barber Profesional - BarberGo')

@section('content')
{{-- Header --}}
<section class="bg-gray-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl sm:text-4xl font-black mb-3">
            Mitra Barber <span class="text-amber-400">Pilihan Kami</span>
        </h1>
        <p class="text-gray-300 max-w-xl mx-auto text-sm sm:text-base">
            Pilih barber favorit Anda berdasarkan ulasan pelanggan, keahlian gaya rambut, dan radius lokasi.
        </p>

        {{-- Search & Filter Bar --}}
        <form method="GET" action="{{ route('barbers') }}" class="mt-8 max-w-2xl mx-auto flex flex-col sm:flex-row gap-3">
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}" 
                   placeholder="Cari nama barber atau keahlian (contoh: Fade, Undercut)..." 
                   class="flex-1 rounded-xl border-gray-300 text-gray-900 px-4 py-3 focus:ring-amber-500 focus:border-amber-500">
            <select name="sort" class="rounded-xl border-gray-300 text-gray-900 px-4 py-3 focus:ring-amber-500 focus:border-amber-500">
                <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Rating Tertinggi</option>
                <option value="experience" {{ request('sort') == 'experience' ? 'selected' : '' }}>Pengalaman Terlama</option>
            </select>
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-black font-bold px-6 py-3 rounded-xl transition">
                Cari
            </button>
        </form>
    </div>
</section>

{{-- Barber Listing --}}
<section class="py-16 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($barbers as $barber)
                <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition border border-gray-100 overflow-hidden flex flex-col justify-between">
                    <div class="p-6">
                        <div class="flex items-center gap-4 mb-4">
                            <img src="{{ $barber->user->avatar_url }}" 
                                 alt="{{ $barber->user->name }}" 
                                 class="w-16 h-16 rounded-full object-cover border-2 border-amber-500">
                            <div>
                                <h3 class="font-bold text-lg text-gray-900">{{ $barber->user->name }}</h3>
                                <p class="text-amber-600 font-medium text-xs">{{ $barber->speciality ?? 'Spesialis Classic & Modern Cut' }}</p>
                                <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $barber->is_available ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $barber->is_available ? '🟢 Siap Melayani' : '🔴 Sedang Libur' }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs text-gray-600 bg-gray-50 p-3 rounded-xl mb-4">
                            <div>
                                <span class="font-bold text-gray-900">⭐ {{ number_format($barber->rating_avg, 1) }}</span>
                                <span class="text-gray-400">({{ $barber->total_reviews }} ulasan)</span>
                            </div>
                            <div>
                                ✂️ <span class="font-semibold">{{ $barber->experience_years }} Thn Pengalaman</span>
                            </div>
                            <div>
                                📍 <span class="font-semibold">{{ $barber->service_radius_km }} Km Radius</span>
                            </div>
                        </div>

                        <p class="text-gray-600 text-sm line-clamp-3">
                            {{ $barber->bio ?? 'Barber ramah dan berpengalaman dengan dedikasi memberikan hasil potongan terbaik langsung ke rumah Anda.' }}
                        </p>
                    </div>

                    <div class="p-6 pt-0">
                        <a href="{{ route('barbers.detail', $barber) }}" 
                           class="block text-center w-full bg-gray-900 hover:bg-amber-500 hover:text-black text-white font-semibold py-2.5 rounded-xl transition text-sm">
                            Lihat Profil & Jadwal
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12">
                    <p class="text-gray-500 text-lg">Tidak ada data barber yang ditemukan.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-10">
            {{ $barbers->links() }}
        </div>
    </div>
</section>
@endsection