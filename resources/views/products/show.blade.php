<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-500 rounded-lg shadow-lg text-white">
                    <i class="fa-solid fa-box text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                        {{ $product->part_number }}
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Detail informasi dan lokasi stok produk.</p>
                </div>
            </div>
            <a href="{{ route('products.edit', $product->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md text-sm font-bold shadow transition">
                <i class="fa-solid fa-pen mr-1"></i> Edit Produk
            </a>
        </div>
    </x-slot>

    <x-breadcrumb :links="[
        ['label' => 'Master Data', 'url' => '#'],
        ['label' => 'Data Produk', 'url' => route('products.index')],
        ['label' => 'Detail: ' . $product->part_number, 'url' => '#']
    ]" />

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Info Produk -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow md:col-span-1 h-fit border-t-4 border-blue-500">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">
                Informasi Dasar
            </h3>
            
            <div class="space-y-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Deskripsi</p>
                    <p class="text-md font-medium text-gray-900 dark:text-white">{{ $product->description ?? '-' }}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Kategori</p>
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 mt-1">
                            {{ $product->category }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Berat</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $product->weight_kg }} Kg</p>
                    </div>
                </div>
                <div class="pt-4 border-t dark:border-gray-700">
                    <p class="text-xs text-gray-500 uppercase font-bold">Total Stok Global</p>
                    <p class="text-4xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($totalStock) }} <span class="text-sm text-gray-500 font-normal">Pcs</span></p>
                </div>
            </div>
        </div>

        <!-- Lokasi Penyimpanan (Inventory) -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow md:col-span-2 border-t-4 border-green-500">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700 flex justify-between items-center">
                <span>Lokasi Penyimpanan</span>
                <span class="text-xs font-normal bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $product->inventories->count() }} Lokasi</span>
            </h3>
            
            @if($product->inventories->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Kode Rak (Bin)</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Tipe Rak</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Qty</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Last Updated</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($product->inventories as $inventory)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td class="px-4 py-3 text-sm font-bold text-blue-600 dark:text-blue-400 font-mono">
                                    {{ $inventory->bin->bin_code }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $inventory->bin->bin_type }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right font-bold text-gray-900 dark:text-white">
                                    {{ $inventory->quantity }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right text-gray-500 text-xs">
                                    {{ $inventory->last_updated ?? '-' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-12 text-gray-500 border-2 border-dashed rounded-lg dark:border-gray-700">
                    <i class="fa-solid fa-box-open text-4xl mb-3 opacity-50"></i>
                    <p>Produk ini belum memiliki stok di rak manapun.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>