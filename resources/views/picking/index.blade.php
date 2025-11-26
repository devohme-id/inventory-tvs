<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-yellow-500 rounded-lg shadow-lg text-white">
                <i class="fa-solid fa-dolly text-xl"></i>
            </div>
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-white">Proses Picking (Ambil Barang)</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Daftar pesanan yang menunggu pengambilan barang dari rak.</p>
            </div>
        </div>
    </x-slot>

    <!-- BREADCRUMB -->
    <x-breadcrumb :links="[
        ['label' => 'Outbound', 'url' => '#'],
        ['label' => 'List Picking', 'url' => route('picking.index')]
    ]" />

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
        
        <!-- TABS MENU -->
        <div class="flex border-b border-gray-200 dark:border-gray-700 mb-6">
            <a href="{{ route('picking.index', ['tab' => 'list']) }}" 
               class="py-2 px-6 font-bold border-b-2 text-sm transition {{ $tab == 'list' ? 'border-yellow-500 text-yellow-600 dark:text-yellow-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
               <i class="fa-solid fa-list-ul mr-2"></i> Antrian Picking
            </a>
            <a href="{{ route('picking.index', ['tab' => 'history']) }}" 
               class="py-2 px-6 font-bold border-b-2 text-sm transition {{ $tab == 'history' ? 'border-yellow-500 text-yellow-600 dark:text-yellow-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
               <i class="fa-solid fa-clock-rotate-left mr-2"></i> Riwayat Selesai
            </a>
        </div>

        <!-- KONTEN TAB -->
        @if($pickingOrders->isEmpty())
            <div class="text-center py-12 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg">
                <i class="fa-solid fa-check-circle text-4xl mb-3 text-green-500 opacity-50"></i>
                <p class="text-gray-500 dark:text-gray-400">
                    {{ $tab == 'history' ? 'Belum ada riwayat picking.' : 'Tidak ada antrian picking saat ini.' }}
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-yellow-50 dark:bg-yellow-900/20">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Sales Order</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Customer</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($pickingOrders as $so)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-6 py-4 font-bold text-blue-600 dark:text-blue-400">
                                {{ $so->so_number }}
                                <div class="text-xs text-gray-400 font-normal mt-1">
                                    {{ $tab == 'list' ? \Carbon\Carbon::parse($so->order_date)->diffForHumans() : 'Selesai: ' . $so->updated_at->format('d M Y H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-900 dark:text-white font-medium">
                                {{ $so->customer }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($so->status == 'Pending')
                                    <span class="bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 text-xs font-bold px-3 py-1 rounded">
                                        MENUNGGU
                                    </span>
                                @else
                                    <span class="bg-green-100 text-green-800 text-xs font-bold px-3 py-1 rounded">
                                        SELESAI PICKING
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($tab == 'list')
                                    <a href="{{ route('picking.process', $so->id) }}" class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-bold px-4 py-2 rounded shadow transition transform hover:scale-105">
                                        MULAI PICKING <i class="fa-solid fa-dolly ml-2"></i>
                                    </a>
                                @else
                                    <span class="text-gray-400 text-xs font-mono cursor-not-allowed">
                                        <i class="fa-solid fa-lock"></i> LOCKED
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $pickingOrders->appends(['tab' => $tab])->links() }}
            </div>
        @endif
    </div>
</x-app-layout>