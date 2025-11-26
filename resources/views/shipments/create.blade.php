<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-teal-600 rounded-lg shadow-lg text-white">
                <i class="fa-solid fa-truck-fast text-xl"></i>
            </div>
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                    {{ __('Proses Pengiriman (Shipment)') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Finalisasi pengiriman dan input berat paket.</p>
            </div>
        </div>
    </x-slot>

    <x-breadcrumb :links="[
        ['label' => 'Outbound', 'url' => '#'],
        ['label' => 'Riwayat Pengiriman', 'url' => route('shipments.index')],
        ['label' => 'Buat Pengiriman', 'url' => '#']
    ]" />

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            
            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                    <i class="fa-solid fa-boxes-packing mr-2 text-blue-600"></i> Antrian Siap Kirim (Packed)
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Masukkan berat total box dan klik "Kirim" untuk menyelesaikan order dan mengurangi stok secara permanen.
                </p>
            </div>

            @if($readyOrders->isEmpty())
                <div class="text-center py-12 border-2 border-dashed border-gray-300 rounded-lg">
                    <i class="fa-solid fa-truck-fast text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Tidak ada pesanan yang siap dikirim.</p>
                    <a href="{{ route('packing.index') }}" class="text-blue-600 hover:underline text-sm">Cek status Packing</a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Sales Order</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Total Item</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase w-64">Input Data Pengiriman</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($readyOrders as $so)
                            <tr class="hover:bg-blue-50 dark:hover:bg-gray-700 transition">
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-blue-600 dark:text-blue-400">
                                    {{ $so->so_number }}
                                    <div class="text-xs text-gray-400 font-normal">{{ \Carbon\Carbon::parse($so->order_date)->format('d M Y') }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                    {{ $so->customer }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded">
                                        {{ $so->items_count }} SKU
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <!-- FORM SHIPMENT PER BARIS -->
                                    <form action="{{ route('shipments.store') }}" method="POST" class="space-y-2">
                                        @csrf
                                        <input type="hidden" name="so_id" value="{{ $so->id }}">
                                        <input type="hidden" name="shipped_at" value="{{ now() }}">
                                        <input type="hidden" name="operator_id" value="{{ Auth::id() }}">

                                        <div class="flex gap-2">
                                            <input type="text" name="box_id" placeholder="Box ID / Resi" required
                                                class="w-1/2 text-xs rounded border-gray-300 dark:bg-gray-900 dark:border-gray-600" 
                                                value="BOX-{{ $so->id }}-{{ rand(100,999) }}">
                                                
                                            <input type="number" name="total_weight_kg" step="0.1" placeholder="Berat (Kg)" required
                                                class="w-1/2 text-xs rounded border-gray-300 dark:bg-gray-900 dark:border-gray-600">
                                        </div>
                                        
                                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-2 px-4 rounded shadow flex justify-center items-center transition transform hover:scale-105">
                                            <i class="fa-solid fa-check mr-1"></i> KIRIM & UPDATE STOK
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>