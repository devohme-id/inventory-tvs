<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-yellow-500 rounded-lg shadow-lg text-white">
                <i class="fa-solid fa-pen-to-square text-xl"></i>
            </div>
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                    {{ __('Koreksi Stok Manual') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Perbarui jumlah stok fisik di lokasi tertentu.</p>
            </div>
        </div>
    </x-slot>

    <x-breadcrumb :links="[
        ['label' => 'Inventory', 'url' => '#'],
        ['label' => 'Monitoring Stok', 'url' => route('inventory.index')],
        ['label' => 'Koreksi: ' . $inventory->product->part_number, 'url' => '#']
    ]" />

    <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            
            <!-- Info Item (Read-only) -->
            <div class="mb-6 grid grid-cols-2 gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Part Number</p>
                    <p class="font-bold text-lg text-gray-900 dark:text-white">{{ $inventory->product->part_number }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Lokasi Rak</p>
                    <p class="font-bold text-lg text-blue-600">{{ $inventory->bin->bin_code }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-gray-500 uppercase font-bold">Deskripsi</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $inventory->product->description }}</p>
                </div>
            </div>

            <form action="{{ route('inventory.update', $inventory->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Jumlah Stok -->
                <div class="mb-6">
                    <x-input-label for="quantity" :value="__('Jumlah Stok Baru')" />
                    <x-text-input id="quantity" class="block mt-1 w-full text-lg font-bold" type="number" name="quantity" min="0" :value="old('quantity', $inventory->quantity)" required />
                    <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end border-t pt-4 dark:border-gray-700">
                    <a href="{{ route('inventory.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 underline mr-4">Batal</a>
                    <x-primary-button class="bg-yellow-600 hover:bg-yellow-700">
                        {{ __('Update Stok') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>