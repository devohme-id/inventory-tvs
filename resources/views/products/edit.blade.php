<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-yellow-500 rounded-lg shadow-lg text-white">
                <i class="fa-solid fa-pen-to-square text-xl"></i>
            </div>
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                    {{ __('Edit Produk') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Perbarui informasi detail produk.</p>
            </div>
        </div>
    </x-slot>

    <x-breadcrumb :links="[
        ['label' => 'Master Data', 'url' => '#'],
        ['label' => 'Data Produk', 'url' => route('products.index')],
        ['label' => 'Edit: ' . $product->part_number, 'url' => '#']
    ]" />

    <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <form action="{{ route('products.update', $product->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <x-input-label for="part_number" :value="__('Part Number')" />
                    <x-text-input id="part_number" class="block mt-1 w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed" type="text" name="part_number" :value="old('part_number', $product->part_number)" readonly />
                    <p class="text-xs text-gray-500 mt-1">Part Number tidak dapat diubah untuk menjaga integritas data.</p>
                    <x-input-error :messages="$errors->get('part_number')" class="mt-2" />
                </div>

                <div class="mb-4">
                    <x-input-label for="description" :value="__('Deskripsi Produk')" />
                    <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" rows="3">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <x-input-label for="category" :value="__('Kategori')" />
                        <select id="category" name="category" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option value="Fast Moving" {{ $product->category == 'Fast Moving' ? 'selected' : '' }}>Fast Moving</option>
                            <option value="Slow Moving" {{ $product->category == 'Slow Moving' ? 'selected' : '' }}>Slow Moving</option>
                            <option value="Consumable" {{ $product->category == 'Consumable' ? 'selected' : '' }}>Consumable</option>
                            <option value="Sparepart" {{ $product->category == 'Sparepart' ? 'selected' : '' }}>Sparepart</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="weight_kg" :value="__('Berat (Kg)')" />
                        <x-text-input id="weight_kg" class="block mt-1 w-full" type="number" step="0.01" name="weight_kg" :value="old('weight_kg', $product->weight_kg)" />
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <a href="{{ route('products.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 underline mr-4">Batal</a>
                    <x-primary-button>
                        {{ __('Update Data') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>