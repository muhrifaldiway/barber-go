<!-- resources/views/customer/bookings/show.blade.php -->
@extends('layouts.app')

@section('title', 'Detail Booking #' . $booking->booking_code)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('customer.bookings.index') }}" class="text-amber-600 hover:text-amber-700 mb-4 inline-block">
            ← Kembali ke Daftar Booking
        </a>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Booking #{{ $booking->booking_code }}</h1>
            <span class="px-4 py-2 rounded-full text-sm font-medium
                bg-{{ $booking->status_color }}-100 text-{{ $booking->status_color }}-700">
                {{ $booking->status_label }}
            </span>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Status Tracker --}}
            <div class="bg-white rounded-2xl shadow-md p-6">
                <h3 class="font-semibold text-lg mb-6">Status Booking</h3>
                <div class="relative">
                    @php
                        $statuses = ['pending', 'confirmed', 'on_the_way', 'arrived', 'in_progress', 'completed'];
                        $currentIndex = array_search($booking->status, $statuses);
                        if ($booking->isCancelled()) $currentIndex = -1;
                    @endphp
                    <div class="flex items-center justify-between">
                        @foreach($statuses as $index => $status)
                        <div class="flex flex-col items-center {{ $index < count($statuses) - 1 ? 'flex-1' : '' }}">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold
                                {{ $index <= $currentIndex ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500' }}">
                                @if($index <= $currentIndex) ✓ @else {{ $index + 1 }} @endif
                            </div>
                            <span class="text-xs mt-2 text-center">
                                {{ \App\Models\Booking::make(['status' => $status])->status_label }}
                            </span>
                        </div>
                        @if($index < count($statuses) - 1)
                        <div class="flex-1 h-1 mx-2 {{ $index < $currentIndex ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                        @endif
                        @endforeach
                    </div>
                </div>

                @if($booking->isCancelled())
                <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-red-700 font-medium">Booking Dibatalkan</p>
                    <p class="text-red-600 text-sm">Alasan: {{ $booking->cancel_reason }}</p>
                </div>
                @endif
            </div>

            {{-- Barber Info --}}
            <div class="bg-white rounded-2xl shadow-md p-6">
                <h3 class="font-semibold text-lg mb-4">Informasi Barber</h3>
                <div class="flex items-center gap-4">
                    <img src="{{ $booking->barber->user->avatar_url }}"
                         class="w-16 h-16 rounded-full object-cover">
                    <div>
                        <h4 class="font-semibold text-lg">{{ $booking->barber->user->name }}</h4>
                        <p class="text-gray-500">⭐ {{ $booking->barber->rating_avg }} ({{ $booking->barber->total_reviews }} review)</p>
                        <p class="text-gray-500">📞 {{ $booking->barber->user->phone }}</p>
                    </div>
                </div>

                @if($booking->isOnTheWay() || $booking->isArrived())
                <div class="mt-4">
                    <a href="https://www.google.com/maps/dir/?api=1&destination={{ $booking->latitude }},{{ $booking->longitude }}"
                       target="_blank"
                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg inline-block">
                        📍 Lihat Lokasi di Maps
                    </a>
                </div>
                @endif
            </div>

            {{-- Services --}}
            <div class="bg-white rounded-2xl shadow-md p-6">
                <h3 class="font-semibold text-lg mb-4">Layanan yang Dipesan</h3>
                <div class="space-y-3">
                    @foreach($booking->services as $service)
                    <div class="flex items-center justify-between py-2 border-b last:border-0">
                        <div>
                            <p class="font-medium">{{ $service->service_name }}</p>
                            <p class="text-gray-500 text-sm">{{ $service->quantity }}x</p>
                        </div>
                        <p class="font-medium">Rp {{ number_format($service->subtotal, 0, ',', '.') }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Lokasi --}}
            <div class="bg-white rounded-2xl shadow-md p-6">
                <h3 class="font-semibold text-lg mb-4">Lokasi</h3>
                <p class="text-gray-700 mb-2">{{ $booking->address }}</p>
                @if($booking->address_detail)
                <p class="text-gray-500 text-sm mb-4">Detail: {{ $booking->address_detail }}</p>
                @endif
                <div id="map" class="w-full h-64 rounded-lg bg-gray-200"></div>
            </div>

            {{-- Review --}}
            @if($booking->isCompleted() && !$booking->review)
            <div class="bg-white rounded-2xl shadow-md p-6">
                <h3 class="font-semibold text-lg mb-4">Beri Review</h3>
                <form action="{{ route('customer.reviews.store', $booking) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                        <div class="flex gap-2" x-data="{ rating: 5 }">
                            @for($i = 1; $i <= 5; $i++)
                            <button type="button"
                                    @click="rating = {{ $i }}"
                                    :class="rating >= {{ $i }} ? 'text-amber-400' : 'text-gray-300'"
                                    class="text-3xl hover:text-amber-400 transition">★</button>
                            @endfor
                            <input type="hidden" name="rating" :value="rating">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Komentar</label>
                        <textarea name="comment" rows="3"
                                  class="w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500"
                                  placeholder="Bagaimana pengalaman Anda?"></textarea>
                    </div>
                    <button type="submit"
                            class="bg-amber-500 hover:bg-amber-600 text-white font-medium py-2 px-6 rounded-lg">
                        Kirim Review
                    </button>
                </form>
            </div>
            @endif

            @if($booking->review)
            <div class="bg-white rounded-2xl shadow-md p-6">
                <h3 class="font-semibold text-lg mb-4">Review Anda</h3>
                <div class="flex items-center gap-1 mb-2">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="{{ $i <= $booking->review->rating ? 'text-amber-400' : 'text-gray-300' }}">★</span>
                    @endfor
                </div>
                <p class="text-gray-700">{{ $booking->review->comment }}</p>
                @if($booking->review->barber_reply)
                <div class="mt-4 bg-gray-50 rounded-lg p-4">
                    <p class="text-sm font-medium text-gray-500 mb-1">Balasan Barber:</p>
                    <p class="text-gray-700">{{ $booking->review->barber_reply }}</p>
                </div>
                @endif
            </div>
            @endif
        </div>

        {{-- Sidebar: Payment Summary --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-md p-6 sticky top-4">
                <h3 class="font-semibold text-lg mb-4">Ringkasan Pembayaran</h3>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tanggal</span>
                        <span>{{ $booking->booking_date->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Jam</span>
                        <span>{{ \Carbon\Carbon::parse($booking->booking_time)->format('H:i') }} WIB</span>
                    </div>
                    <hr>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span>Rp {{ number_format($booking->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if($booking->discount > 0)
                    <div class="flex justify-between text-green-600">
                        <span>Diskon</span>
                        <span>- Rp {{ number_format($booking->discount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($booking->travel_fee > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Biaya Perjalanan</span>
                        <span>Rp {{ number_format($booking->travel_fee, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <hr>
                    <div class="flex justify-between font-bold text-lg">
                        <span>Total</span>
                        <span class="text-amber-600">{{ $booking->formatted_total }}</span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Metode Pembayaran</span>
                        <span class="font-medium">
                            {{ $booking->payment_method === 'cod' ? 'Bayar di Tempat' :
                               ($booking->payment_method === 'bank_transfer' ? 'Transfer Bank' : 'E-Wallet') }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm mt-2">
                        <span class="text-gray-600">Status Bayar</span>
                        <span class="font-medium {{ $booking->payment_status === 'paid' ? 'text-green-600' : 'text-orange-600' }}">
                            {{ $booking->payment_status === 'paid' ? 'Sudah Dibayar' : 'Belum Dibayar' }}
                        </span>
                    </div>
                </div>

                @if($booking->canBeCancelled())
                <div class="mt-6">
                    <button onclick="document.getElementById('cancelModal').classList.remove('hidden')"
                            class="w-full bg-red-50 hover:bg-red-100 text-red-600 font-medium py-2 rounded-lg transition">
                        Batalkan Booking
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Cancel Modal --}}
<div id="cancelModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold mb-4">Batalkan Booking?</h3>
        <form action="{{ route('customer.bookings.cancel', $booking) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Pembatalan</label>
                <textarea name="cancel_reason" rows="3" required
                          class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500"
                          placeholder="Mengapa Anda membatalkan?"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button"
                        onclick="document.getElementById('cancelModal').classList.add('hidden')"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 rounded-lg">
                    Tidak Jadi
                </button>
                <button type="submit"
                        class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2 rounded-lg">
                    Ya, Batalkan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}"></script>
<script>
    function initMap() {
        const position = { lat: {{ $booking->latitude }}, lng: {{ $booking->longitude }} };
        const map = new google.maps.Map(document.getElementById('map'), {
            center: position,
            zoom: 15,
        });
        new google.maps.Marker({ position, map, title: 'Lokasi Booking' });
    }
    initMap();
</script>
@endpush
@endsection