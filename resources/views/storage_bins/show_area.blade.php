<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-purple-600 rounded-lg shadow-lg text-white">
                    <i class="fa-solid fa-server text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                        Detail Area: Rak {{ $rack }}
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $bins->count() }} Lokasi Bin &bull; {{ $totalProducts }} Total Item &bull; {{ $productTypes }} SKU Berbeda
                    </p>
                </div>
            </div>
            <a href="{{ route('storage-bins.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-bold">
                &laquo; Kembali
            </a>
        </div>
    </x-slot>

    <x-breadcrumb :links="[
        ['label' => 'Master Data', 'url' => '#'],
        ['label' => 'Area Gudang', 'url' => route('storage-bins.index')],
        ['label' => 'Rak ' . $rack, 'url' => '#']
    ]" />

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            
            <div class="mb-6 flex justify-between items-center">
                <h3 class="text-lg font-bold">Daftar Bin & Inventory</h3>
                <!-- Legend -->
                <div class="flex gap-3 text-xs">
                    <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-green-500 mr-1"></span> Kosong</span>
                    <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-red-500 mr-1"></span> Terisi</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Kode Bin</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Posisi (L-S)</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Isi Produk</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($bins as $bin)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-blue-600 dark:text-blue-400 font-mono">
                                    {{ $bin->bin_code }}
                                </div>
                                <div class="text-xs text-gray-500">{{ $bin->bin_type }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600 dark:text-gray-300">
                                Level {{ $bin->level }} - Slot {{ $bin->slot }}
                            </td>
                            <td class="px-6 py-4">
                                @if($bin->inventories->count() > 0)
                                    <div class="space-y-1">
                                        @foreach($bin->inventories as $inv)
                                            <div class="flex items-center justify-between p-1.5 bg-blue-50 dark:bg-blue-900/30 rounded border border-blue-100 dark:border-blue-800">
                                                <div class="flex items-center">
                                                    <i class="fa-solid fa-box-open text-blue-500 mr-2 text-xs"></i>
                                                    <div>
                                                        <a href="{{ route('products.show', $inv->product_id) }}" class="text-xs font-bold text-gray-800 dark:text-white hover:underline">
                                                            {{ $inv->product->part_number }}
                                                        </a>
                                                        <p class="text-[10px] text-gray-500 truncate w-32">{{ $inv->product->description }}</p>
                                                    </div>
                                                </div>
                                                <span class="text-xs font-bold bg-white dark:bg-gray-800 px-2 py-0.5 rounded border shadow-sm">
                                                    {{ $inv->quantity }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs italic">- Kosong -</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $bin->is_empty ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $bin->is_empty ? 'Empty' : 'Filled' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('storage-bins.edit', $bin->id) }}" class="text-gray-400 hover:text-indigo-600 mr-2" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('storage-bins.destroy', $bin->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus bin ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600" {{ !$bin->is_empty ? 'disabled' : '' }} title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>