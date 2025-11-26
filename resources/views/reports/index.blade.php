<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-purple-600 rounded-lg shadow-lg text-white">
                <i class="fa-solid fa-file-contract text-xl"></i>
            </div>
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-white">Laporan Stok</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Rekapitulasi barang masuk dan keluar periode tertentu.</p>
            </div>
        </div>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
        
        <!-- FILTER FORM -->
        <form action="{{ route('reports.index') }}" method="GET" class="mb-8 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <x-input-label for="type" :value="__('Jenis Laporan')" />
                    <select name="type" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                        <option value="inbound" {{ $type == 'inbound' ? 'selected' : '' }}>Barang Masuk (Inbound)</option>
                        <option value="outbound" {{ $type == 'outbound' ? 'selected' : '' }}>Barang Keluar (Outbound)</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="start_date" :value="__('Dari Tanggal')" />
                    <x-text-input id="start_date" type="date" name="start_date" :value="$startDate" class="block mt-1 w-full" required />
                </div>
                <div>
                    <x-input-label for="end_date" :value="__('Sampai Tanggal')" />
                    <x-text-input id="end_date" type="date" name="end_date" :value="$endDate" class="block mt-1 w-full" required />
                </div>
                <div class="flex gap-2">
                    <x-primary-button class="w-full justify-center h-[42px] bg-purple-600 hover:bg-purple-700">
                        <i class="fa-solid fa-filter mr-2"></i> Tampilkan
                    </x-primary-button>
                    <!-- Tombol Print Membuka Tab Baru -->
                    <a href="{{ route('reports.print', request()->all()) }}" target="_blank" class="inline-flex items-center justify-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white h-[42px]">
                        <i class="fa-solid fa-print"></i>
                    </a>
                </div>
            </div>
        </form>

        <!-- TABEL DATA -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-purple-50 dark:bg-purple-900/20">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Dokumen (Ref)</th>
                        @if($type == 'inbound')
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Supplier</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Produk</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Qty Masuk</th>
                        @else
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Total Item</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Berat Total</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($data as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            @if($type == 'inbound')
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($item->invoice->received_at)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-purple-600">
                                    {{ $item->invoice->invoice_number }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $item->invoice->supplier }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="font-bold text-gray-900 dark:text-white">{{ $item->product->part_number }}</div>
                                    <div class="text-xs text-gray-500">{{ Str::limit($item->product->description, 30) }}</div>
                                </td>
                                <td class="px-6 py-4 text-center text-sm font-bold text-green-600">
                                    +{{ $item->quantity_received }}
                                </td>
                            @else
                                <!-- Outbound (Shipment) -->
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($item->shipped_at)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-blue-600">
                                    {{ $item->box_id }} <br>
                                    <span class="text-xs text-gray-400 font-normal">{{ $item->salesorders->so_number }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $item->salesorders->customer }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                    {{ $item->salesorders->items->count() }} SKU
                                </td>
                                <td class="px-6 py-4 text-center text-sm font-bold text-red-600">
                                    {{ $item->total_weight_kg }} Kg
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500">Tidak ada data pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>