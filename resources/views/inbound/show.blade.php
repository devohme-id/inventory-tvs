<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-600 rounded-lg shadow-lg text-white">
                    <i class="fa-solid fa-file-invoice text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                        Invoice: {{ $invoice->invoice_number }}
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Detail item dan proses scan barang masuk.</p>
                </div>
            </div>
            <span class="px-3 py-1 text-sm font-bold rounded-full {{ $invoice->status === 'Received' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                {{ $invoice->status }}
            </span>
        </div>
    </x-slot>

    <x-breadcrumb :links="[
        ['label' => 'Operasional', 'url' => '#'],
        ['label' => 'Penerimaan Barang', 'url' => route('incoming.index')],
        ['label' => $invoice->invoice_number, 'url' => '#']
    ]" />

    <!-- Info Invoice -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
            <p class="text-sm text-gray-500 dark:text-gray-400">Supplier</p>
            <p class="font-bold text-gray-900 dark:text-white">{{ $invoice->supplier ?? '-' }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
            <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Masuk</p>
            <p class="font-bold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($invoice->received_at)->format('d M Y') }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
            <p class="text-sm text-gray-500 dark:text-gray-400">Penerima (Admin)</p>
            <p class="font-bold text-gray-900 dark:text-white">{{ $invoice->receiver->name ?? 'Unknown' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Kolom Kiri: Form Tambah Item -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">Tambah / Scan Item</h3>
                
                <form action="{{ route('inbound-items.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

                    <!-- Pilih Produk -->
                    <div class="mb-4">
                        <x-input-label for="product_id" :value="__('Pilih Produk / Scan Part No')" />
                        <select id="product_id" name="product_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">-- Cari Produk --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->part_number }} - {{ $product->description }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
                    </div>

                    <!-- Qty Expected -->
                    <div class="mb-4">
                        <x-input-label for="quantity_expected" :value="__('Jumlah (Qty)')" />
                        <x-text-input id="quantity_expected" class="block mt-1 w-full" type="number" name="quantity_expected" min="1" value="1" required />
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                        <i class="fa-solid fa-plus mr-2"></i> Tambah Item
                    </button>
                </form>
            </div>

            <!-- Update Status Invoice -->
            @if($invoice->items->count() > 0)
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h3 class="text-md font-medium text-gray-900 dark:text-white mb-2">Aksi Invoice</h3>
                <form action="{{ route('incoming.update', $invoice->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="flex gap-2">
                        <select name="status" class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm w-full">
                            <option value="Pending" {{ $invoice->status == 'Pending' ? 'selected' : '' }}>Pending (Draft)</option>
                            <option value="Received" {{ $invoice->status == 'Received' ? 'selected' : '' }}>Received (Selesai Cek)</option>
                            <option value="Stored" {{ $invoice->status == 'Stored' ? 'selected' : '' }}>Stored (Masuk Rak)</option>
                        </select>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-bold">
                            Update
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>

        <!-- Kolom Kanan: Daftar Item -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Daftar Item di Invoice Ini</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Part Number</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Deskripsi</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Qty</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Lokasi (Rak)</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($invoice->items as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $item->product->part_number }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $item->product->description }}</td>
                                <td class="px-4 py-3 text-center text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $item->quantity_expected }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                    @if($item->bin)
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-bold">{{ $item->bin->bin_code }}</span>
                                    @else
                                        <span class="text-yellow-600 text-xs italic">Belum Put-away</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right text-sm">
                                    <form action="{{ route('inbound-items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus item ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <i class="fa-solid fa-box-open text-2xl mb-2"></i><br>
                                    Belum ada item ditambahkan. Gunakan form di kiri.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>