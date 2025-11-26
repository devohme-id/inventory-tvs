<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-red-500 rounded-lg shadow-lg text-white">
                <i class="fa-solid fa-clipboard-check text-xl"></i>
            </div>
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                    {{ __('Stok Opname (Adjustment)') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Koreksi manual jumlah stok fisik vs sistem.</p>
            </div>
        </div>
    </x-slot>

    <x-breadcrumb :links="[
        ['label' => 'Inventory', 'url' => '#'],
        ['label' => 'Monitoring Stok', 'url' => route('inventory.index')],
        ['label' => 'Stok Opname', 'url' => '#']
    ]" />

    <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900 border-l-4 border-blue-500 rounded text-sm">
                <p class="font-bold text-blue-700 dark:text-blue-200">Info:</p>
                <p class="text-blue-600 dark:text-blue-300">
                    Gunakan form ini untuk memasukkan stok awal atau mengoreksi stok fisik (Stock Opname).
                    Jika kombinasi Produk dan Rak sudah ada, jumlah stok akan di-update (ditimpa) dengan nilai baru ini.
                </p>
            </div>

            <form action="{{ route('inventory.store') }}" method="POST">
                @csrf

                <!-- Pilih Produk -->
                <div class="mb-4">
                    <x-input-label for="product_id" :value="__('Pilih Produk')" />
                    <select id="product_id" name="product_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 select2" required>
                        <option value="">-- Cari Part Number / Nama --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->part_number }} - {{ $product->description }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
                </div>

                <!-- Pilih Lokasi Rak -->
                <div class="mb-4">
                    <x-input-label for="bind_id" :value="__('Lokasi Penyimpanan (Bin)')" />
                    <select id="bind_id" name="bind_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">-- Pilih Rak --</option>
                        @foreach($bins as $bin)
                            <option value="{{ $bin->id }}" {{ old('bind_id') == $bin->id ? 'selected' : '' }}>
                                {{ $bin->bin_code }} ({{ $bin->bin_type }}) - {{ $bin->is_empty ? 'Kosong' : 'Terisi' }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('bind_id')" class="mt-2" />
                </div>

                <!-- Jumlah Stok -->
                <div class="mb-6">
                    <x-input-label for="quantity" :value="__('Jumlah Stok Fisik (Aktual)')" />
                    <x-text-input id="quantity" class="block mt-1 w-full" type="number" name="quantity" min="0" :value="old('quantity')" required placeholder="0" />
                    <p class="text-xs text-gray-500 mt-1">Masukkan jumlah total yang ada di rak tersebut saat ini.</p>
                    <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end border-t pt-4 dark:border-gray-700">
                    <a href="{{ route('inventory.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 underline mr-4">Batal</a>
                    <x-primary-button class="bg-blue-600 hover:bg-blue-700">
                        {{ __('Simpan Penyesuaian') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>