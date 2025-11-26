<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-purple-600 rounded-lg shadow-lg text-white">
                <i class="fa-solid fa-server text-xl"></i>
            </div>
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                    {{ __('Storage & Put-away') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Visualisasi rak, proses put-away barang, dan master data area.</p>
            </div>
        </div>
    </x-slot>

    <x-breadcrumb :links="[
        ['label' => 'Master Data', 'url' => '#'],
        ['label' => 'Lokasi Rak', 'url' => route('storage-bins.index')]
    ]" />

    <div class="space-y-8">
        
        <!-- BAGIAN 1: CORE MODULE - AUTO PUT-AWAY & VISUALISASI -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" 
             x-data="{ 
                selectedItem: null, 
                suggestedBin: null,
                highlightBin(binCode) {
                    this.suggestedBin = binCode;
                },
                selectItem(item) {
                    this.selectedItem = item;
                    this.suggestedBin = null;
                }
             }">
            
            <!-- Kolom Kiri: Daftar Tunggu (Received Items) -->
            <div class="lg:col-span-1 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-blue-500">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4 flex items-center text-blue-600 dark:text-blue-400">
                        <i class="fa-solid fa-clipboard-list mr-2"></i> Daftar Tunggu Put-away
                    </h3>
                    
                    @if($pendingItems->count() > 0)
                        <div class="overflow-y-auto max-h-[400px] space-y-3 pr-2">
                            @foreach($pendingItems as $item)
                                <div @click="selectItem({{ json_encode($item) }})" 
                                     class="p-3 border rounded-lg cursor-pointer transition hover:bg-blue-50 dark:hover:bg-blue-900/30"
                                     :class="selectedItem && selectedItem.id === {{ $item->id }} ? 'border-blue-500 ring-1 ring-blue-500 bg-blue-50 dark:bg-blue-900' : 'border-gray-200 dark:border-gray-700'">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-bold text-sm text-gray-900 dark:text-white">{{ $item->product->part_number }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1">{{ $item->product->description }}</p>
                                        </div>
                                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full font-bold">
                                            {{ $item->quantity_expected }} Pcs
                                        </span>
                                    </div>
                                    <div class="mt-2 flex justify-between items-center">
                                        <span class="text-xs text-gray-400 font-mono">{{ $item->invoice->invoice_number }}</span>
                                        
                                        <!-- TOMBOL AUTO SUGGEST -->
                                        <button @click.stop="
                                            let emptyBins = {{ json_encode($allBins->flatten()->where('is_empty', true)->pluck('bin_code')) }};
                                            if(emptyBins.length > 0) {
                                                let randomBin = emptyBins[Math.floor(Math.random() * emptyBins.length)];
                                                highlightBin(randomBin);
                                            } else {
                                                alert('Gudang Penuh! Tidak ada rak kosong.');
                                            }
                                        " class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded shadow transition">
                                            <i class="fa-solid fa-magnifying-glass-location mr-1"></i> Cari Lokasi
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500 border-2 border-dashed rounded-lg dark:border-gray-700">
                            <i class="fa-solid fa-check-double text-4xl mb-3 text-green-500 opacity-80"></i>
                            <p class="font-medium">Semua barang sudah masuk rak.</p>
                            <p class="text-xs mt-1">Tidak ada pending put-away.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Kolom Kanan: Visualisasi Rak (Grid) -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-purple-500">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold flex items-center text-gray-800 dark:text-white">
                            <i class="fa-solid fa-cubes-stacked mr-2 text-purple-500"></i> Peta Gudang
                        </h3>
                        <div class="flex gap-3 text-xs font-medium">
                            <div class="flex items-center"><span class="w-3 h-3 bg-green-200 border border-green-400 rounded mr-1"></span> Kosong</div>
                            <div class="flex items-center"><span class="w-3 h-3 bg-red-200 border border-red-400 rounded mr-1"></span> Terisi</div>
                            <div class="flex items-center"><span class="w-3 h-3 bg-blue-300 border border-blue-500 rounded mr-1 animate-pulse"></span> Saran</div>
                        </div>
                    </div>

                    <!-- Tabs Rak -->
                    <div x-data="{ activeTab: '{{ $allBins->keys()->first() }}' }">
                        <div class="flex space-x-1 border-b dark:border-gray-700 mb-4 overflow-x-auto pb-1">
                            @foreach($allBins->keys() as $rackName)
                                <button @click="activeTab = '{{ $rackName }}'" 
                                    :class="activeTab === '{{ $rackName }}' ? 'border-purple-500 text-purple-700 dark:text-purple-300 bg-purple-50 dark:bg-purple-900/30' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                    class="py-2 px-4 border-b-2 rounded-t-lg transition font-bold text-sm whitespace-nowrap">
                                    Rak {{ $rackName }}
                                </button>
                            @endforeach
                        </div>

                        <!-- Grid Rak -->
                        @foreach($allBins as $rackName => $binsInRack)
                            <div x-show="activeTab === '{{ $rackName }}'" class="grid grid-cols-5 md:grid-cols-8 lg:grid-cols-10 gap-2 max-h-[400px] overflow-y-auto p-1">
                                @foreach($binsInRack as $bin)
                                    <div class="relative group">
                                        <!-- Kotak Bin -->
                                        <div class="h-12 border rounded text-[10px] flex items-center justify-center font-bold cursor-pointer transition duration-300 shadow-sm"
                                            @click="suggestedBin = '{{ $bin->bin_code }}'"
                                            :class="{
                                                'bg-blue-400 text-white border-blue-600 ring-2 ring-blue-300 scale-110 z-10': suggestedBin === '{{ $bin->bin_code }}',
                                                'bg-green-100 text-green-800 border-green-300 hover:bg-green-200': {{ $bin->is_empty }} && suggestedBin !== '{{ $bin->bin_code }}',
                                                'bg-red-100 text-red-800 border-red-300 hover:bg-red-200': !{{ $bin->is_empty }} && suggestedBin !== '{{ $bin->bin_code }}',
                                            }">
                                            {{ $bin->bin_code }}
                                        </div>
                                        
                                        <!-- Tooltip (Hover) -->
                                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-40 bg-gray-900/90 text-white text-xs rounded p-2 hidden group-hover:block z-20 text-center shadow-xl pointer-events-none backdrop-blur-sm">
                                            <p class="font-bold text-yellow-300 border-b border-gray-600 pb-1 mb-1">{{ $bin->bin_code }}</p>
                                            @if($bin->inventories->count() > 0)
                                                @foreach($bin->inventories as $inv)
                                                    <div class="text-left mb-1">
                                                        <span class="block font-bold truncate">{{ $inv->product->part_number }}</span>
                                                        <span class="text-gray-300">Qty: {{ $inv->quantity }}</span>
                                                    </div>
                                                @endforeach
                                            @else
                                                <p class="text-gray-400 italic">Kosong</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>

                    <!-- PANEL KONFIRMASI PUT-AWAY -->
                    <div x-show="selectedItem && suggestedBin" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         class="mt-6 p-4 bg-blue-50 dark:bg-gray-700 border-2 border-blue-200 dark:border-blue-500 rounded-xl flex flex-col md:flex-row justify-between items-center shadow-lg">
                        <div class="mb-4 md:mb-0 text-center md:text-left">
                            <p class="text-sm text-gray-600 dark:text-gray-300 uppercase font-bold tracking-wider">Konfirmasi Penempatan</p>
                            <div class="flex items-center gap-2 text-lg">
                                <span class="font-bold text-gray-900 dark:text-white" x-text="selectedItem.product.part_number"></span>
                                <i class="fa-solid fa-arrow-right text-gray-400"></i>
                                <span class="font-bold text-blue-600 dark:text-blue-400 bg-white dark:bg-gray-800 px-2 py-0.5 rounded border border-blue-200 dark:border-blue-600 font-mono" x-text="suggestedBin"></span>
                            </div>
                        </div>
                        
                        <form action="{{ route('inventory.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" :value="selectedItem?.product_id">
                            <!-- Cari ID Bin berdasarkan Kode Bin yang dipilih -->
                            <input type="hidden" name="bind_id" :value="{{ $allBins->flatten()->toJson() }}.find(b => b.bin_code === suggestedBin)?.id">
                            <input type="hidden" name="quantity" :value="selectedItem?.quantity_expected">
                            <input type="hidden" name="inbound_item_id" :value="selectedItem?.id">
                            
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg shadow hover:shadow-lg transition transform hover:scale-105 flex items-center">
                                <i class="fa-solid fa-check-circle mr-2"></i> KONFIRMASI SIMPAN
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- BAGIAN 2: TABEL MASTER AREA (Grouping per Rak) -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium flex items-center">
                        <i class="fa-solid fa-list mr-2 text-gray-500"></i> Daftar Area Penyimpanan
                    </h3>
                    <a href="{{ route('storage-bins.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700">
                        <i class="fa-solid fa-plus mr-2"></i> Tambah Lokasi
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-purple-50 dark:bg-purple-900/20">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kode Area</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Total Slot (Bin)</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Slot Terisi</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Utilisasi (%)</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($areas as $area)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer" onclick="window.location='{{ route('storage-bins.area', $area->rack) }}'">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="p-2 rounded bg-purple-100 text-purple-700 mr-3">
                                            <i class="fa-solid fa-server"></i>
                                        </div>
                                        <div>
                                            <span class="text-lg font-bold text-gray-800 dark:text-white">Rak {{ $area->rack }}</span>
                                            <p class="text-xs text-gray-500">Area Penyimpanan {{ $area->rack }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-gray-900 dark:text-white font-medium">
                                    {{ $area->total_bins }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800">
                                        {{ $area->filled_bins }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap align-middle w-1/4">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                        <div class="h-2.5 rounded-full {{ $area->usage_percent > 90 ? 'bg-red-600' : ($area->usage_percent > 50 ? 'bg-yellow-500' : 'bg-green-500') }}" style="width: {{ $area->usage_percent }}%"></div>
                                    </div>
                                    <p class="text-[10px] text-center mt-1 font-bold text-gray-500">{{ $area->usage_percent }}%</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <a href="{{ route('storage-bins.area', $area->rack) }}" class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300 font-bold text-xs">
                                        LIHAT ISI <i class="fa-solid fa-arrow-right ml-1"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>