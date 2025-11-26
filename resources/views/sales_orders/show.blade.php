<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-500 rounded-lg shadow-lg text-white">
                    <i class="fa-solid fa-file-invoice-dollar text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                        Detail Order: {{ $salesorders->so_number }}
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $salesorders->customer }}</p>
                </div>
            </div>
            
            @php
                $statusClass = match($salesorders->status) {
                    'Shipped' => 'bg-green-100 text-green-800 border-green-200',
                    'Packed' => 'bg-blue-100 text-blue-800 border-blue-200',
                    'Picked' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                    default => 'bg-red-100 text-red-800 border-red-200',
                };
            @endphp
            <span class="px-4 py-1.5 rounded-full font-bold text-sm border {{ $statusClass }}">
                {{ $salesorders->status }}
            </span>
        </div>
    </x-slot>

    <x-breadcrumb :links="[
        ['label' => 'Outbound', 'url' => '#'],
        ['label' => 'Sales Order', 'url' => route('sales-orders.index')],
        ['label' => $salesorders->so_number, 'url' => '#']
    ]" />

    <!-- SECTION 1: INFORMASI UTAMA -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Customer</p>
            <p class="font-bold text-gray-900 dark:text-white text-lg mt-1">{{ $salesorders->customer ?? '-' }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Order</p>
            <p class="font-bold text-gray-900 dark:text-white text-lg mt-1">{{ \Carbon\Carbon::parse($salesorders->order_date)->format('d F Y') }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Item</p>
            <p class="font-bold text-indigo-600 dark:text-indigo-400 text-lg mt-1">{{ $salesorders->items->count() }} SKU</p>
        </div>
    </div>

    <!-- SECTION 2: FORM TAMBAH BARANG (FULL WIDTH & LEGA) -->
    @if($salesorders->status === 'Pending')
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg mb-8 border-t-4 border-indigo-500" style="z-index: 40;">
        <div class="p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fa-solid fa-cart-plus mr-2 text-indigo-500"></i> Tambah Barang ke Pesanan
            </h3>
            
            <form action="{{ route('so-items.store') }}" method="POST" class="flex flex-col md:flex-row gap-4 items-start">
                @csrf
                <input type="hidden" name="so_id" value="{{ $salesorders->id }}">

                <!-- AUTOCOMPLETE SEARCH COMPONENT -->
                <div class="flex-grow w-full relative" 
                    x-data="{
                        query: '',
                        results: [],
                        showResults: false,
                        loading: false,
                        
                        init() {
                            // Ambil data awal (populer/terbaru) saat load agar tidak kosong
                            this.fetchProducts(''); 
                        },

                        fetchProducts(q) {
                            this.loading = true;
                            fetch('{{ route('products.search') }}?q=' + q)
                                .then(res => res.json())
                                .then(data => {
                                    this.results = data;
                                    this.loading = false;
                                });
                        },

                        onFocus() {
                            this.showResults = true;
                            if(this.results.length === 0) this.fetchProducts(this.query);
                        },

                        onInput() {
                            this.showResults = true;
                            this.fetchProducts(this.query);
                        },

                        select(product) {
                            this.query = product.part_number + ' - ' + product.description;
                            document.getElementById('product_id_hidden').value = product.id;
                            this.showResults = false;
                        },

                        clickOutside() {
                            this.showResults = false;
                        }
                    }" 
                    @click.away="clickOutside()">
                    
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari Produk (Part Number / Deskripsi)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-search text-gray-400" x-show="!loading"></i>
                            <i class="fa-solid fa-circle-notch fa-spin text-indigo-500" x-show="loading" style="display: none;"></i>
                        </div>
                        <input type="text" x-model="query" @focus="onFocus()" @input.debounce.300ms="onInput()"
                            class="block w-full pl-10 pr-4 py-3 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base" 
                            placeholder="Ketik untuk mencari barang..." autocomplete="off">
                        
                        <input type="hidden" name="product_id" id="product_id_hidden" required>

                        <!-- Dropdown Hasil -->
                        <div x-show="showResults" x-transition
                            class="absolute z-50 w-full bg-white dark:bg-gray-700 mt-1 rounded-lg shadow-xl max-h-60 overflow-y-auto border border-gray-200 dark:border-gray-600">
                            <template x-if="results.length > 0">
                                <ul>
                                    <template x-for="product in results" :key="product.id">
                                        <li @click="select(product)" 
                                            class="px-4 py-3 hover:bg-indigo-50 dark:hover:bg-gray-600 cursor-pointer border-b border-gray-100 dark:border-gray-600 last:border-0 transition duration-150">
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <div class="font-bold text-sm text-gray-800 dark:text-white" x-text="product.part_number"></div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400" x-text="product.description"></div>
                                                </div>
                                                <span class="text-xs bg-gray-200 dark:bg-gray-800 text-gray-600 dark:text-gray-300 px-2 py-1 rounded" x-text="product.category"></span>
                                            </div>
                                        </li>
                                    </template>
                                </ul>
                            </template>
                            <template x-if="results.length === 0 && !loading">
                                <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center italic">
                                    Produk tidak ditemukan.
                                </div>
                            </template>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
                </div>

                <!-- Input Qty -->
                <div class="w-full md:w-32">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Qty</label>
                    <input type="number" name="quantity_ordered" min="1" value="1" required
                        class="block w-full px-4 py-3 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-center font-bold">
                </div>

                <!-- Tombol Submit -->
                <div class="w-full md:w-auto mt-6 md:mt-0 self-end">
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg shadow-md transition duration-200 flex items-center justify-center h-[48px]">
                        <i class="fa-solid fa-plus mr-2"></i> Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- SECTION 3: LIST ITEM (WIDE TABLE) -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-700/50">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Daftar Item Pesanan</h3>
            
            @if($salesorders->status !== 'Shipped')
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="text-sm text-indigo-600 font-bold hover:underline flex items-center">
                    <i class="fa-solid fa-gear mr-1"></i> Opsi Status
                </button>
                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg z-20 border border-gray-200 dark:border-gray-600 p-2">
                    <form action="{{ route('sales-orders.update', $salesorders->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" name="status" value="Pending" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 rounded">
                            Set Pending (Draft)
                        </button>
                        <button type="submit" name="status" value="Picked" class="w-full text-left px-4 py-2 text-sm text-yellow-600 hover:bg-yellow-50 dark:hover:bg-gray-600 rounded">
                            Set Picked (Manual)
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Part Number</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Qty Order</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Picked</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($salesorders->items as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white font-mono">
                            {{ $item->product->part_number }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                            {{ $item->product->description }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-gray-900 dark:text-white">
                            {{ $item->quantity_ordered }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            <!-- Status Progress Bar Kecil -->
                            <div class="flex items-center justify-center">
                                @if($item->quantity_picked >= $item->quantity_ordered)
                                    <span class="text-green-600 font-bold flex items-center"><i class="fa-solid fa-check mr-1"></i> {{ $item->quantity_picked }}</span>
                                @elseif($item->quantity_picked > 0)
                                    <span class="text-yellow-600 font-bold">{{ $item->quantity_picked }} / {{ $item->quantity_ordered }}</span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            @if($salesorders->status === 'Pending')
                            <form action="{{ route('so-items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus item ini?');" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 p-2 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition" title="Hapus Item">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                            @else
                                <span class="text-gray-400 text-xs flex justify-end items-center"><i class="fa-solid fa-lock mr-1"></i> Locked</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <i class="fa-solid fa-basket-shopping text-4xl mb-3 opacity-30"></i>
                            <p>Belum ada item di pesanan ini.</p>
                            <p class="text-xs mt-1">Gunakan form di atas untuk menambah barang.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>