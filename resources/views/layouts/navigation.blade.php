<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 sticky top-0 z-40 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <span class="text-2xl">✂️</span>
                        <span class="font-black text-xl tracking-tight text-gray-900">Barber<span class="text-amber-500">Go</span></span>
                    </a>
                </div>

                <!-- Navigation Links (Public) -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('home') }}" 
                       class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('home') ? 'border-amber-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Beranda
                    </a>
                    <a href="{{ route('services') }}" 
                       class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('services') ? 'border-amber-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Layanan
                    </a>
                    <a href="{{ route('barbers') }}" 
                       class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('barbers*') ? 'border-amber-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Cari Barber
                    </a>
                </div>
            </div>

            <!-- Settings / User Dropdown or Login Button -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <!-- Dropdown for Authenticated User -->
                    <div class="relative" x-data="{ dropdownOpen: false }">
                        <button @click="dropdownOpen = !dropdownOpen" 
                                class="inline-flex items-center gap-2 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-600 bg-white hover:text-gray-800 focus:outline-none transition">
                            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full object-cover border border-gray-200">
                            <div>{{ Auth::user()->name }}</div>
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="dropdownOpen" 
                             @click.outside="dropdownOpen = false" 
                             x-transition 
                             class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg py-2 border border-gray-100 z-50">
                            
                            <!-- Role Specific Dashboard Link -->
                            @if(Auth::user()->hasRole('admin'))
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-600 font-medium">
                                    ⚙️ Dashboard Admin
                                </a>
                            @elseif(Auth::user()->hasRole('barber'))
                                <a href="{{ route('barber.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-600 font-medium">
                                    💈 Dashboard Barber
                                </a>
                            @else
                                <a href="{{ route('customer.bookings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-600 font-medium">
                                    📋 Riwayat Booking
                                </a>
                                <a href="{{ route('customer.profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-600">
                                    👤 Profil Saya
                                </a>
                            @endif

                            <div class="border-t border-gray-100 my-1"></div>

                            <!-- Logout Form -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-medium">
                                    🚪 Keluar (Logout)
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Guest Action Buttons -->
                    <div class="flex items-center gap-3">
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-700 hover:text-amber-600 px-3 py-2 transition">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="text-sm font-semibold bg-amber-500 hover:bg-amber-600 text-black px-4 py-2 rounded-full shadow-sm transition">
                            Daftar Sekarang
                        </a>
                    </div>
                @endauth
            </div>

            <!-- Hamburger Button for Mobile -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Mobile Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-b border-gray-200">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('home') }}" class="block ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('home') ? 'border-amber-500 text-amber-700 bg-amber-50 font-semibold' : 'border-transparent text-gray-600' }}">
                Beranda
            </a>
            <a href="{{ route('services') }}" class="block ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('services') ? 'border-amber-500 text-amber-700 bg-amber-50 font-semibold' : 'border-transparent text-gray-600' }}">
                Layanan
            </a>
            <a href="{{ route('barbers') }}" class="block ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('barbers*') ? 'border-amber-500 text-amber-700 bg-amber-50 font-semibold' : 'border-transparent text-gray-600' }}">
                Cari Barber
            </a>
        </div>

        <!-- Responsive Auth Settings -->
        <div class="pt-4 pb-3 border-t border-gray-200">
            @auth
                <div class="flex items-center px-4 mb-3">
                    <img src="{{ Auth::user()->avatar_url }}" class="h-10 w-10 rounded-full object-cover">
                    <div class="ms-3">
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="space-y-1">
                    @if(Auth::user()->hasRole('admin'))
                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-base font-medium text-gray-600 hover:bg-gray-100">Dashboard Admin</a>
                    @elseif(Auth::user()->hasRole('barber'))
                        <a href="{{ route('barber.dashboard') }}" class="block px-4 py-2 text-base font-medium text-gray-600 hover:bg-gray-100">Dashboard Barber</a>
                    @else
                        <a href="{{ route('customer.bookings.index') }}" class="block px-4 py-2 text-base font-medium text-gray-600 hover:bg-gray-100">Riwayat Booking</a>
                        <a href="{{ route('customer.profile.edit') }}" class="block px-4 py-2 text-base font-medium text-gray-600 hover:bg-gray-100">Profil Saya</a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-base font-medium text-red-600 hover:bg-gray-100">
                            Keluar (Logout)
                        </button>
                    </form>
                </div>
            @else
                <div class="px-4 py-2 space-y-2">
                    <a href="{{ route('login') }}" class="block w-full text-center py-2 text-sm font-semibold text-gray-700 border border-gray-300 rounded-lg">Masuk</a>
                    <a href="{{ route('register') }}" class="block w-full text-center py-2 text-sm font-semibold bg-amber-500 text-black rounded-lg">Daftar Sekarang</a>
                </div>
            @endauth
        </div>
    </div>
</nav>