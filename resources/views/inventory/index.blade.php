<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-yellow-500 rounded-lg shadow-lg text-white">
                <i class="fa-solid fa-boxes-stacked text-xl"></i>
            </div>
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                    {{ __('Data Stok Gudang') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Monitoring posisi stok real-time di setiap rak.</p>
            </div>
        </div>
    </x-slot>

    <x-breadcrumb :links="[
        ['label' => 'Inventory', 'url' => '#'],
        ['label' => 'Monitoring Stok', 'url' => route('inventory.index')]
    ]" />

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            
            <!-- Top Actions -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                
                <!-- Search & Filter -->
                <form action="{{ route('inventory.index') }}" method="GET" class="w-full md:w-2/3 flex flex-col md:flex-row gap-2">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fa-solid fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" 
                            class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
                            placeholder="Cari Produk, Part No, atau Lokasi...">
                    </div>
                    <select name="category" onchange="this.form.submit()" class="p-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white md:w-1/3">
                        <option value="">Semua Kategori</option>
                        <option value="Fast Moving" {{ request('category') == 'Fast Moving' ? 'selected' : '' }}>Fast Moving</option>
                        <option value="Slow Moving" {{ request('category') == 'Slow Moving' ? 'selected' : '' }}>Slow Moving</option>
                        <option value="Consumable" {{ request('category') == 'Consumable' ? 'selected' : '' }}>Consumable</option>
                    </select>
                </form>

                <!-- Add Button -->
                <a href="{{ route('inventory.create') }}" class="w-full md:w-auto inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fa-solid fa-clipboard-check mr-2"></i> Stok Opname
                </a>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Part Number</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Deskripsi Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Lokasi (Bin)</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Stok Aktual</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Update Terakhir</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($inventories as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $item->product->part_number ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ $item->product->description ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <span class="px-2 py-1 rounded text-xs font-semibold bg-gray-100 dark:bg-gray-700">
                                    {{ $item->product->category ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-600 dark:text-blue-400 font-mono">
                                {{ $item->bin->bin_code ?? 'No Bin' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $qty = $item->quantity;
                                    $color = 'bg-green-100 text-green-800';
                                    if($qty < 10) $color = 'bg-red-100 text-red-800';
                                    elseif($qty < 50) $color = 'bg-yellow-100 text-yellow-800';
                                @endphp
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full {{ $color }}">
                                    {{ $qty }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($item->last_updated)->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('inventory.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3" title="Koreksi Stok">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('inventory.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus data stok ini? (Hanya jika fisik barang benar-benar 0)');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-boxes-stacked text-4xl mb-3 opacity-50"></i>
                                    <p>Tidak ada data stok ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $inventories->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>