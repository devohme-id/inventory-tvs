<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-teal-600 rounded-lg shadow-lg text-white">
                <i class="fa-solid fa-truck-fast text-xl"></i>
            </div>
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                    {{ __('Pengiriman (Shipment)') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Finalisasi pengiriman dan riwayat logistik.</p>
            </div>
        </div>
    </x-slot>

    <!-- BREADCRUMB DITAMBAHKAN -->
    <x-breadcrumb :links="[
        ['label' => 'Outbound', 'url' => '#'],
        ['label' => 'Riwayat Pengiriman', 'url' => route('shipments.index')]
    ]" />

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
        
        <!-- TABS -->
        <div class="flex border-b border-gray-200 dark:border-gray-700 mb-6">
            <a href="{{ route('shipments.index', ['tab' => 'waiting']) }}" class="py-2 px-4 font-bold border-b-2 {{ $tab == 'waiting' ? 'border-teal-500 text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                Waiting Shipment ({{ $readyOrders->count() }})
            </a>
            <a href="{{ route('shipments.index', ['tab' => 'history']) }}" class="py-2 px-4 font-bold border-b-2 {{ $tab == 'history' ? 'border-teal-500 text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                Log Shipped (History)
            </a>
        </div>

        <!-- CONTENT: WAITING SHIPMENT -->
        @if($tab == 'waiting')
            @if($readyOrders->isEmpty())
                <div class="text-center py-10 text-gray-500 dark:text-gray-400">
                    <i class="fa-solid fa-box-open text-4xl mb-3 opacity-50"></i>
                    <p>Tidak ada paket siap kirim (Status: Packed).</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-teal-50 dark:bg-teal-900/20">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Sales Order</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Customer</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Total Item</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase w-1/3">Input Pengiriman</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($readyOrders as $so)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td class="px-6 py-4 font-bold text-teal-600 dark:text-teal-400">
                                    {{ $so->so_number }}
                                    <div class="text-xs text-gray-400 font-normal mt-1">{{ \Carbon\Carbon::parse($so->order_date)->format('d M Y') }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                    {{ $so->customer }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 text-xs font-bold px-3 py-1 rounded">
                                        {{ $so->items_count }} SKU
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('shipments.store') }}" method="POST" class="flex gap-2 items-center">
                                        @csrf
                                        <input type="hidden" name="so_id" value="{{ $so->id }}">
                                        <input type="hidden" name="shipped_at" value="{{ now() }}">
                                        <input type="hidden" name="operator_id" value="{{ Auth::id() }}">
                                        
                                        <input type="text" name="box_id" placeholder="No. Resi / Box" required 
                                            class="w-1/2 text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-teal-500 focus:border-teal-500" 
                                            value="BOX-{{ $so->id }}-{{ rand(100,999) }}">
                                            
                                        <input type="number" name="total_weight_kg" step="0.1" placeholder="Kg" required 
                                            class="w-1/4 text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-teal-500 focus:border-teal-500">
                                            
                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-2 px-3 rounded shadow transition transform hover:scale-105">
                                            <i class="fa-solid fa-paper-plane"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        <!-- CONTENT: HISTORY -->
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Box ID</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">SO Number</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Berat</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Waktu Kirim</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($historyShipments as $ship)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                {{ $ship->box_id }}
                            </td>
                            <td class="px-6 py-4 text-teal-600 dark:text-teal-400 font-medium">
                                {{ $ship->salesorders->so_number ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                {{ $ship->total_weight_kg }} Kg
                            </td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-xs">
                                {{ $ship->shipped_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <!-- Tombol Label -->
                                <a href="{{ route('shipments.show', $ship->id) }}" class="text-indigo-600 hover:text-indigo-900 text-xs font-bold">
                                    <i class="fa-solid fa-tag"></i> LABEL
                                </a>
                                <!-- Tombol Surat Jalan (Baru) -->
                                <a href="{{ route('shipments.print_do', $ship->id) }}" target="_blank" class="text-gray-600 hover:text-gray-900 text-xs font-bold">
                                    <i class="fa-solid fa-file-lines"></i> SJ
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $historyShipments->appends(['tab' => 'history'])->links() }}
                </div>
            </div>
        @endif
    </div>
</x-app-layout>