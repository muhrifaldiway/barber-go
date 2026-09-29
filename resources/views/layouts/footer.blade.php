<footer class="bg-gray-900 text-gray-400 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="space-y-4 md:col-span-2">
                <div class="flex items-center gap-2">
                    <span class="text-3xl">✂️</span>
                    <span class="font-black text-2xl tracking-tight text-white">Barber<span class="text-amber-500">Go</span></span>
                </div>
                <p class="text-sm text-gray-400 max-w-sm">
                    Layanan barbershop panggilan profesional langsung ke depan pintu rumah atau kantor Anda. Rapi, nyaman, dan higienis.
                </p>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-3">Tautan Cepat</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-amber-400">Beranda</a></li>
                    <li><a href="{{ route('services') }}" class="hover:text-amber-400">Daftar Layanan</a></li>
                    <li><a href="{{ route('barbers') }}" class="hover:text-amber-400">Daftar Barber</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-3">Bantuan & Kontak</h4>
                <ul class="space-y-2 text-sm">
                    <li>WhatsApp: +62 812-3456-7890</li>
                    <li>Email: support@barbergo.com</li>
                    <li>Jam Operasional: 08:00 - 21:00 WIB</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-xs text-gray-500">
            &copy; {{ date('Y') }} BarberGo. Hak Cipta Dilindungi.
        </div>
    </div>
</footer>