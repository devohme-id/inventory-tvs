<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-purple-600 rounded-lg shadow-lg text-white">
                    <i class="fa-solid fa-server text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                        Detail Rak: {{ $storageBin->bin_code }}
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Informasi lokasi dan daftar isi barang.</p>
                </div>
            </div>
            <a href="{{ route('storage-bins.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-bold">
                &laquo; Kembali
            </a>
        </div>
    </x-slot>

    <x-breadcrumb :links="[
        ['label' => 'Master Data', 'url' => '#'],
        ['label' => 'Lokasi Rak', 'url' => route('storage-bins.index')],
        ['label' => $storageBin->bin_code, 'url' => '#']
    ]" />

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- INFO RAK -->
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">
                    Spesifikasi Lokasi
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Area / Rak</p>
                        <p class="text-lg font-medium text-gray-900 dark:text-white">Area {{ $storageBin->rack }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-bold">Level</p>
                            <p class="font-bold text-gray-900 dark:text-white">{{ $storageBin->level }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-bold">Slot</p>
                            <p class="font-bold text-gray-900 dark:text-white">{{ $storageBin->slot }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Tipe Penyimpanan</p>
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 mt-1">
                            {{ $storageBin->bin_type }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Status Saat Ini</p>
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded {{ $storageBin->is_empty ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} mt-1">
                            {{ $storageBin->is_empty ? 'KOSONG' : 'TERISI' }}
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Quick Action -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow text-center">
                <a href="{{ route('storage-bins.edit', $storageBin->id) }}" class="block w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded mb-2">
                    <i class="fa-solid fa-pen-to-square mr-1"></i> Edit Lokasi
                </a>
            </div>
        </div>

        <!-- ISI RAK (INVENTORY) -->
        <div class="md:col-span-2">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow h-full">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex justify-between items-center border-b pb-2 dark:border-gray-700">
                    <span>Isi Rak (Inventory)</span>
                    <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded">{{ $storageBin->inventories->count() }} Item</span>
                </h3>

                @if($storageBin->inventories->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Part Number</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Deskripsi</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Qty</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($storageBin->inventories as $inventory)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3">
                                        <a href="{{ route('products.show', $inventory->product_id) }}" class="font-bold text-blue-600 hover:underline">
                                            {{ $inventory->product->part_number }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $inventory->product->description }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $inventory->quantity }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('inventory.edit', $inventory->id) }}" class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded hover:bg-indigo-200">
                                            Koreksi Stok
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                        <i class="fa-solid fa-box-open text-5xl mb-3 opacity-30"></i>
                        <p class="text-lg">Rak ini saat ini kosong.</p>
                        <p class="text-sm">Gunakan menu Put-away untuk mengisi rak ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>