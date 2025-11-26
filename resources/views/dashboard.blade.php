<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-blue-600 rounded-lg shadow-lg text-white">
                <i class="fa-solid fa-chart-pie text-xl"></i>
            </div>
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                    {{ __('Dashboard Operasional') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Ringkasan aktivitas dan performa gudang secara real-time.</p>
            </div>
        </div>
    </x-slot>

    <!-- Baris 1: KPI Utama -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md border-l-4 border-red-500 flex items-center space-x-4 transform hover:scale-105 transition duration-300">
            <div class="p-3 rounded-full bg-red-100 dark:bg-red-900">
                <i class="fa-solid fa-file-invoice w-6 h-6 text-red-600 dark:text-red-300 flex justify-center items-center text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">SO Pending</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $kpi['pending'] }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md border-l-4 border-yellow-500 flex items-center space-x-4 transform hover:scale-105 transition duration-300">
            <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                <i class="fa-solid fa-dolly w-6 h-6 text-yellow-600 dark:text-yellow-300 flex justify-center items-center text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Proses Picking</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $kpi['picking'] }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md border-l-4 border-blue-500 flex items-center space-x-4 transform hover:scale-105 transition duration-300">
            <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                <i class="fa-solid fa-box-open w-6 h-6 text-blue-600 dark:text-blue-300 flex justify-center items-center text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Siap Kirim</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $kpi['packing'] }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md border-l-4 border-green-500 flex items-center space-x-4 transform hover:scale-105 transition duration-300">
            <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                <i class="fa-solid fa-truck-fast w-6 h-6 text-green-600 dark:text-green-300 flex justify-center items-center text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Terkirim (Hari Ini)</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $kpi['shipped'] }}</p>
            </div>
        </div>
    </div>

    <!-- Baris 2: Grafik -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                <i class="fa-solid fa-chart-simple mr-2 text-blue-600"></i> Progres Harian
            </h3>
            <div class="relative h-64 w-full">
                <canvas id="orderProgressChart"></canvas>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                <i class="fa-solid fa-warehouse mr-2 text-blue-600"></i> Kapasitas Rak
            </h3>
            <div class="h-48 mb-4 flex justify-center">
                <canvas id="storageDonutChart"></canvas>
            </div>
            <div class="grid grid-cols-2 gap-2 text-center text-sm">
                <div class="bg-blue-50 dark:bg-blue-900 p-2 rounded">
                    <span class="block text-gray-500 dark:text-gray-300 text-xs">Terisi</span>
                    <span class="font-bold text-blue-700 dark:text-blue-200 text-lg">{{ number_format($storage['used']) }}</span>
                </div>
                <div class="bg-gray-100 dark:bg-gray-700 p-2 rounded">
                    <span class="block text-gray-500 dark:text-gray-300 text-xs">Kosong</span>
                    <span class="font-bold text-gray-700 dark:text-white text-lg">{{ number_format($storage['empty']) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Baris 3: INFO KRITIS & URGENSI (New Section) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- 1. Stok Menipis (Product Urgency) -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md border-t-4 border-red-500">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                    <i class="fa-solid fa-triangle-exclamation mr-2 text-red-500"></i> Stok Menipis
                </h3>
                <a href="{{ route('products.index') }}" class="text-xs text-blue-600 hover:underline">Lihat Semua</a>
            </div>
            
            <div class="overflow-y-auto max-h-60">
                <table class="min-w-full">
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($lowStockProducts as $product)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="py-3">
                                <p class="text-sm font-bold text-gray-800 dark:text-white">{{ $product->part_number }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate w-40">{{ $product->description }}</p>
                            </td>
                            <td class="py-3 text-right">
                                <span class="px-2 py-1 text-xs font-bold rounded-full 
                                    {{ $product->inventories_sum_quantity == 0 ? 'bg-red-200 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $product->inventories_sum_quantity ?? 0 }} Pcs
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="py-4 text-center text-sm text-gray-500">Stok aman.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. Sales Order Prioritas (Aging Order) -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md border-t-4 border-yellow-500">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                    <i class="fa-solid fa-clock mr-2 text-yellow-500"></i> Order Prioritas
                </h3>
                <a href="{{ route('sales-orders.index') }}" class="text-xs text-blue-600 hover:underline">Lihat Semua</a>
            </div>

            <div class="overflow-y-auto max-h-60">
                <div class="space-y-3">
                    @forelse($urgentOrders as $order)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700 hover:shadow-sm transition">
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ $order['id'] }}</p>
                                @if($order['is_late'])
                                    <span class="text-[10px] bg-red-100 text-red-600 px-1.5 rounded font-bold animate-pulse">TELAT</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-300">{{ $order['customer'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-400">Pending selama</p>
                            <p class="text-sm font-bold {{ $order['is_late'] ? 'text-red-600' : 'text-gray-700 dark:text-gray-200' }}">
                                {{ $order['days_pending'] }} Hari
                            </p>
                        </div>
                        <!-- Pastikan id_raw ada di controller untuk link ini -->
                        <a href="{{ route('sales-orders.show', ['sales_order' => $order['id_raw'] ?? 1]) }}" class="ml-2 text-gray-400 hover:text-blue-600">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    </div>
                    @empty
                    <div class="text-center py-4 text-sm text-gray-500">Tidak ada order pending.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- 3. Aktivitas Terkini (Log) -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md border-t-4 border-blue-500">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                <i class="fa-solid fa-list-ul mr-2 text-blue-500"></i> Aktivitas Terkini
            </h3>
            <ul class="space-y-4 overflow-y-auto max-h-60 pr-2">
                @foreach($logs as $log)
                <li class="flex items-start space-x-3 pb-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                    <div class="flex-shrink-0 p-2 rounded-full {{ $log['bg'] }} dark:bg-opacity-20">
                        <i class="{{ $log['icon'] }} w-3 h-3 {{ $log['color'] }} text-center"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $log['detail'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $log['user'] }} <span class="mx-1">•</span> {{ $log['time'] }}
                        </p>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Chart Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const kpiData = @json($kpi);
            const storageData = @json($storage);
            const isDarkMode = document.documentElement.classList.contains('dark');
            const textColor = isDarkMode ? '#cbd5e1' : '#6b7280';
            
            // Chart Progres
            new Chart(document.getElementById('orderProgressChart'), {
                type: 'bar',
                data: {
                    labels: ['Pending', 'Picking', 'Packing', 'Shipped'],
                    datasets: [{
                        label: 'Total SO',
                        data: [kpiData.pending, kpiData.picking, kpiData.packing, kpiData.shipped],
                        backgroundColor: ['#ef4444', '#eab308', '#3b82f6', '#22c55e'],
                        borderRadius: 4,
                        barThickness: 20
                    }]
                },
                options: {
                    indexAxis: 'y',
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: textColor } },
                        y: { grid: { display: false }, ticks: { color: textColor } }
                    }
                }
            });

            // Chart Storage
            new Chart(document.getElementById('storageDonutChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Terisi', 'Kosong'],
                    datasets: [{
                        data: [storageData.used, storageData.empty],
                        backgroundColor: ['#1e3a8a', '#e5e7eb'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: { legend: { display: false } }
                }
            });
        });
    </script>
</x-app-layout>