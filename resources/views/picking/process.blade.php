<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-yellow-500 rounded-lg shadow-lg text-white">
                    <i class="fa-solid fa-barcode text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                        Proses Picking: {{ $salesOrder->so_number }}
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Scan produk yang diambil dari rak.</p>
                </div>
            </div>
            <!-- LINK KEMBALI DIPERBAIKI -->
            <a href="{{ route('picking.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm">
                &laquo; Kembali
            </a>
        </div>
    </x-slot>

    <!-- BREADCRUMB DIPERBAIKI -->
    <x-breadcrumb :links="[
        ['label' => 'Outbound', 'url' => '#'],
        ['label' => 'Antrian Picking', 'url' => route('picking.index')],
        ['label' => 'Proses ' . $salesOrder->so_number, 'url' => '#']
    ]" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- ... Content Scanner & List Tetap Sama ... -->
        
        <!-- KOLOM KIRI: SCANNER AREA -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Card Input Scan -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 border-t-4 border-yellow-500">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">
                    <i class="fa-solid fa-barcode mr-2"></i> Scan Barang
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Arahkan cursor ke kolom di bawah dan scan barcode produk yang diambil.
                </p>

                <form action="{{ route('picking.scan', $salesOrder->id) }}" method="POST">
                    @csrf
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-qrcode text-gray-400 text-lg"></i>
                        </div>
                        <input type="text" name="barcode" autofocus autocomplete="off"
                            class="block w-full pl-10 pr-4 py-3 border-2 border-yellow-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-600 text-lg font-bold text-gray-900 placeholder-gray-400"
                            placeholder="Scan Part Number..." />
                    </div>
                    <button type="submit" class="mt-3 w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-4 rounded-lg shadow transition">
                        SUBMIT SCAN
                    </button>
                </form>

                <!-- Progress Bar -->
                <div class="mt-6">
                    <div class="flex justify-between text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <span>Progress Picking</span>
                        <span>{{ $progress }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-700">
                        <div class="bg-yellow-500 h-4 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Info Sales Order -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
                <p class="text-xs text-gray-500 uppercase font-bold">Customer</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ $salesOrder->customer }}</p>
                
                <p class="text-xs text-gray-500 uppercase font-bold">Tanggal Order</p>
                <p class="text-sm text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($salesOrder->order_date)->format('d M Y') }}</p>
            </div>
        </div>

        <!-- KOLOM KANAN: DAFTAR BARANG (Checklist) -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex justify-between items-center">
                <h3 class="font-bold text-gray-700 dark:text-gray-300">Daftar Barang ({{ $tasks->count() }} Item)</h3>
                
                @if($progress == 100)
                    <span class="bg-green-100 text-green-800 text-sm font-bold px-3 py-1 rounded-full animate-pulse">
                        <i class="fa-solid fa-check-circle mr-1"></i> SELESAI
                    </span>
                @else
                    <span class="bg-yellow-100 text-yellow-800 text-sm font-bold px-3 py-1 rounded-full">
                        PENDING
                    </span>
                @endif
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($tasks as $task)
                <div class="p-4 flex justify-between items-center transition {{ $task->status == 'Picked' ? 'bg-green-50 dark:bg-green-900/20' : 'hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    <!-- Info Barang -->
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            @if($task->status == 'Picked')
                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                    <i class="fa-solid fa-check text-xl"></i>
                                </div>
                            @else
                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                                    <i class="fa-solid fa-dolly text-xl"></i>
                                </div>
                            @endif
                        </div>
                        <div>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $task->soitems->product->part_number }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $task->soitems->product->description }}</p>
                            <div class="mt-1 flex items-center gap-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fa-solid fa-location-dot mr-1"></i> {{ $task->storagebins->bin_code ?? 'N/A' }}
                                </span>
                                @if($task->status == 'Picked')
                                    <span class="text-xs text-green-600 font-bold">
                                        Diambil: {{ \Carbon\Carbon::parse($task->picked_at)->format('H:i') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Qty & Status -->
                    <div class="text-right">
                        <p class="text-2xl font-bold {{ $task->status == 'Picked' ? 'text-green-600' : 'text-gray-800 dark:text-gray-200' }}">
                            {{ $task->quantity_to_pick }}
                        </p>
                        <p class="text-xs uppercase font-bold text-gray-400">Pcs</p>
                    </div>
                </div>
                @endforeach
            </div>
            
            @if($progress == 100)
            <div class="p-6 bg-green-50 dark:bg-green-900/30 text-center border-t border-green-100 dark:border-green-800">
                <h4 class="text-xl font-bold text-green-700 dark:text-green-400 mb-2">Semua Barang Telah Diambil!</h4>
                <p class="text-green-600 dark:text-green-300 mb-4">Pesanan ini akan dipindahkan ke antrian Packing.</p>
                <!-- UPDATE LINK TOMBOL KEMBALI -->
                <a href="{{ route('picking.index') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg shadow transition">
                    Kembali ke Antrian
                </a>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>