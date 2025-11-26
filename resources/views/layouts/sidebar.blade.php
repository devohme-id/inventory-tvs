<aside 
    x-show="sidebarOpen"
    x-transition:enter="transition ease-in-out duration-300 transform"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in-out duration-300 transform"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    class="fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-gray-800 shadow-lg overflow-y-auto md:relative md:translate-x-0 flex flex-col justify-between"
    style="display: none;" x-show.important="sidebarOpen">
    
    <div>
        <!-- Logo Area (Diperbaiki agar sejajar Navbar) -->
        <!-- Ubah h-20 menjadi h-16 agar sama dengan navigation.blade.php -->
        <div class="flex items-center justify-center h-16 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 sticky top-0 z-10">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <!-- Ukuran padding dan icon disesuaikan sedikit agar pas di h-16 -->
                <div class="bg-blue-900 text-white p-1.5 rounded-lg shadow-lg">
                    <i class="fa-solid fa-warehouse text-lg"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-800 dark:text-white leading-none">TVS WMS</h1>
                    <p class="text-[10px] text-gray-500 dark:text-gray-400 tracking-wider uppercase">Inventory System</p>
                </div>
            </a>
        </div>

        <!-- Menu Items -->
        <nav class="px-4 py-6 space-y-1">
            
            <!-- DASHBOARD -->
            <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="fa-solid fa-chart-pie">
                {{ __('Dashboard') }}
            </x-sidebar-link>

            <!-- 1. MASTER DATA -->
            <div class="pt-6 pb-2">
                <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">1. Master Data</p>
            </div>

            <x-sidebar-link :href="route('products.index')" :active="request()->routeIs('products.*')" icon="fa-solid fa-box-archive">
                {{ __('Data Produk') }}
            </x-sidebar-link>

            <!-- Icon Rak: fa-server -->
            <x-sidebar-link :href="route('storage-bins.index')" :active="request()->routeIs('storage-bins.*')" icon="fa-solid fa-server">
                {{ __('Lokasi Rak') }}
            </x-sidebar-link>

            <!-- 2. INBOUND (BARANG MASUK) -->
            <div class="pt-6 pb-2">
                <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">2. Inbound (Masuk)</p>
            </div>

            <x-sidebar-link :href="route('inbound-invoices.index')" :active="request()->routeIs('inbound-invoices.*', 'incoming.*')" icon="fa-solid fa-truck-ramp-box">
                {{ __('Penerimaan Barang') }}
            </x-sidebar-link>

            <!-- 3. INVENTORY (PENYIMPANAN) -->
            <div class="pt-6 pb-2">
                <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">3. Inventory</p>
            </div>

            <x-sidebar-link :href="route('inventory.index')" :active="request()->routeIs('inventory.*')" icon="fa-solid fa-boxes-stacked">
                {{ __('Monitoring Stok') }}
            </x-sidebar-link>

            <!-- 4. OUTBOUND (BARANG KELUAR) -->
            <div class="pt-6 pb-2">
                <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">4. Outbound (Keluar)</p>
            </div>

            <x-sidebar-link :href="route('sales-orders.index')" :active="request()->routeIs('sales-orders.*')" icon="fa-solid fa-file-invoice-dollar">
                {{ __('Sales Order') }}
            </x-sidebar-link>

            <!-- Menu Picking Tasks -->
            <x-sidebar-link :href="route('picking.index')" :active="request()->routeIs('picking.index', 'picking.*')" icon="fa-solid fa-dolly">
                {{ __('Picking (Ambil)') }}
            </x-sidebar-link>
            
            <!-- Menu Packing -->
            <x-sidebar-link :href="route('packing.index')" :active="request()->routeIs('packing.*')" icon="fa-solid fa-box-open">
                {{ __('Packing (Kemas)') }}
            </x-sidebar-link>
            
            <x-sidebar-link :href="route('shipments.index')" :active="request()->routeIs('shipments.*')" icon="fa-solid fa-truck-fast">
                {{ __('Pengiriman') }}
            </x-sidebar-link>

            <!-- 5. LAPORAN & AUDIT -->
            <div class="pt-6 pb-2">
                <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">5. Laporan</p>
            </div>
            
            <!-- MENU BARU -->
            <x-sidebar-link :href="route('reports.index')" :active="request()->routeIs('reports.*')" icon="fa-solid fa-file-contract">
                {{ __('Laporan Stok') }}
            </x-sidebar-link>

            <x-sidebar-link :href="route('audit-logs.index')" :active="request()->routeIs('audit-logs.*')" icon="fa-solid fa-clock-rotate-left">
                {{ __('Audit Log') }}
            </x-sidebar-link>
        </nav>
    </div>

    <!-- User Profile (Bottom) -->
    <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
        <div class="flex items-center w-full">
            <div class="flex-shrink-0">
                <a href="{{ route('profile.edit') }}" class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-500 to-blue-700 flex items-center justify-center text-white font-bold shadow-md hover:opacity-90 transition">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </a>
            </div>
            <div class="ml-3 w-full min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                    {{ Auth::user()->name ?? 'Guest' }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                    {{ Auth::user()->role ?? 'User' }}
                </p>
            </div>
            
            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}" class="ml-auto">
                @csrf
                <button type="submit" class="text-gray-400 hover:text-red-500 transition p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700" title="Keluar Sistem">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </div>
</aside>