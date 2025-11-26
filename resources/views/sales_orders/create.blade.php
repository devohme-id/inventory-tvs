<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-blue-600 rounded-lg shadow-lg text-white">
                <i class="fa-solid fa-file-circle-plus text-xl"></i>
            </div>
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                    {{ __('Buat Sales Order Baru') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Dokumen pesanan penjualan otomatis.</p>
            </div>
        </div>
    </x-slot>

    <x-breadcrumb :links="[
        ['label' => 'Outbound', 'url' => '#'],
        ['label' => 'Sales Order', 'url' => route('sales-orders.index')],
        ['label' => 'Buat Order', 'url' => '#']
    ]" />

    <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/30 border-l-4 border-blue-500 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-circle-info text-blue-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            Nomor Sales Order digenerate otomatis oleh sistem. Silakan isi nama customer dan tanggal.
                        </p>
                    </div>
                </div>
            </div>

            <form action="{{ route('sales-orders.store') }}" method="POST">
                @csrf

                <!-- SO Number (Auto) -->
                <div class="mb-4">
                    <x-input-label for="so_number" :value="__('Nomor Sales Order (Otomatis)')" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-hashtag text-gray-400"></i>
                        </div>
                        <x-text-input id="so_number" class="block w-full pl-10 bg-gray-100 dark:bg-gray-700 cursor-not-allowed font-mono font-bold text-gray-600 dark:text-gray-300" 
                            type="text" 
                            name="so_number" 
                            :value="$autoSoNumber" 
                            readonly />
                    </div>
                    <x-input-error :messages="$errors->get('so_number')" class="mt-2" />
                </div>

                <!-- Customer -->
                <div class="mb-4">
                    <x-input-label for="customer" :value="__('Nama Customer / Dealer')" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-user-tie text-gray-400"></i>
                        </div>
                        <x-text-input id="customer" class="block w-full pl-10" 
                            type="text" 
                            name="customer" 
                            :value="old('customer')" 
                            required autofocus 
                            placeholder="Contoh: TVS Dealer Jakarta" />
                    </div>
                    <x-input-error :messages="$errors->get('customer')" class="mt-2" />
                </div>

                <!-- Order Date -->
                <div class="mb-6">
                    <x-input-label for="order_date" :value="__('Tanggal Order')" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-regular fa-calendar text-gray-400"></i>
                        </div>
                        <x-text-input id="order_date" class="block w-full pl-10" 
                            type="date" 
                            name="order_date" 
                            :value="old('order_date', date('Y-m-d'))" 
                            required />
                    </div>
                    <x-input-error :messages="$errors->get('order_date')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end border-t pt-4 dark:border-gray-700">
                    <a href="{{ route('sales-orders.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 underline mr-4">
                        Batal
                    </a>
                    <x-primary-button>
                        <i class="fa-solid fa-check mr-2"></i> {{ __('Simpan & Lanjut') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>