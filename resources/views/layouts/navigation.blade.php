<header class="h-16 flex items-center justify-between px-6 bg-white dark:bg-gray-800 shadow-sm z-20 transition-colors duration-300 border-b border-gray-200 dark:border-gray-700">
    
    <!-- KIRI: Mobile Toggle & Search -->
    <div class="flex items-center gap-4 flex-1">
        <!-- Mobile Sidebar Toggle -->
        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none transition-colors">
            <i class="fa-solid fa-bars text-xl"></i>
        </button>

        <!-- Search Bar (Responsive) -->
        <div class="relative w-full max-w-md hidden md:block">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <i class="fa-solid fa-search text-gray-400 dark:text-gray-500"></i>
            </span>
            <input class="w-full pl-10 pr-4 py-2 rounded-lg text-sm border-none ring-1 ring-gray-200 dark:ring-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:outline-none transition-colors"
                type="text" placeholder="Cari Part Number, SO, atau Menu...">
        </div>
    </div>

    <!-- KANAN: Actions -->
    <div class="flex items-center gap-3 sm:gap-4">
        
        <!-- Dark Mode Toggle -->
        <button @click="toggleDarkMode()" 
            class="w-9 h-9 flex items-center justify-center rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors focus:outline-none"
            title="Ganti Tema Gelap/Terang">
            <!-- Icon Sun (Tampil saat Dark Mode aktif) -->
            <i x-show="darkMode" class="fa-solid fa-sun text-yellow-400 transition-transform duration-500 rotate-0"></i>
            <!-- Icon Moon (Tampil saat Light Mode aktif) -->
            <i x-show="!darkMode" class="fa-solid fa-moon text-gray-600 transition-transform duration-500 rotate-0"></i>
        </button>

        <!-- Notifications -->
        <div class="relative">
            <button class="w-9 h-9 flex items-center justify-center rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors focus:outline-none">
                <i class="fa-solid fa-bell"></i>
                <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border border-white dark:border-gray-800"></span>
            </button>
        </div>

        <!-- User Dropdown -->
        <div class="relative ml-2" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-2 focus:outline-none group">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 leading-tight group-hover:text-blue-600 transition">{{ Auth::user()->name ?? 'User' }}</p>
                    <p class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ Auth::user()->role ?? 'Operator' }}</p>
                </div>
                <div class="w-9 h-9 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 flex items-center justify-center font-bold border-2 border-white dark:border-gray-700 shadow-sm">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
                <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{'rotate-180': open}"></i>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" 
                 @click.away="open = false"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 style="display: none;"
                 class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-1 z-50 border border-gray-100 dark:border-gray-700">
                
                <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700 sm:hidden">
                    <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ Auth::user()->name ?? 'User' }}</p>
                </div>

                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <i class="fa-solid fa-user-gear mr-2 text-gray-400"></i> {{ __('Profil Saya') }}
                </a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition">
                        <i class="fa-solid fa-right-from-bracket mr-2"></i> {{ __('Keluar') }}
                    </a>
                </form>
            </div>
        </div>
    </div>
</header>