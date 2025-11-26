<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center no-print">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-teal-600 rounded-lg shadow-lg text-white">
                    <i class="fa-solid fa-print text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                        Detail Pengiriman: {{ $shipment->box_id }}
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Cetak label pengiriman untuk ditempel di paket.</p>
                </div>
            </div>
            <button onclick="window.print()" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-bold flex items-center shadow transition hover:scale-105">
                <i class="fa-solid fa-print mr-2"></i> CETAK LABEL
            </button>
        </div>
    </x-slot>

    <x-breadcrumb :links="[
        ['label' => 'Outbound', 'url' => '#'],
        ['label' => 'Riwayat Pengiriman', 'url' => route('shipments.index')],
        ['label' => $shipment->box_id, 'url' => '#']
    ]" />

    <!-- Load JsBarcode Library -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

    <div class="max-w-4xl mx-auto">
        
        <!-- LABEL PENGIRIMAN (SHIPPING LABEL) -->
        <!-- Desain Standar Industri Otomotif (Mirip Label Ekspedisi/OEM) -->
        <div class="print-area bg-white border-2 border-black mx-auto relative mb-8 shadow-lg">
            
            <!-- HEADER: LOGO & TIPE PENGIRIMAN -->
            <div class="flex border-b-2 border-black">
                <div class="w-full p-4 text-center">
                    <h1 class="text-4xl font-black tracking-tighter">TVS MOTOR</h1>
                    <p class="text-xs font-bold uppercase mt-1 tracking-widest">SPAREPARTS LOGISTICS CENTER</p>
                </div>
            </div>

            <!-- ALAMAT TUJUAN (SHIP TO) -->
            <div class="p-4 border-b-2 border-black">
                <p class="text-[10px] font-bold text-gray-500 mb-1">SHIP TO (PENERIMA):</p>
                <p class="text-3xl font-bold leading-none mb-2">{{ $shipment->salesorders->customer ?? 'UNKNOWN CUSTOMER' }}</p>
                
                <div class="flex gap-8 mt-4">
                    <div>
                        <p class="text-[10px] font-bold text-gray-500">SALES ORDER #</p>
                        <p class="text-lg font-mono font-bold">{{ $shipment->salesorders->so_number }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-500">TANGGAL KIRIM</p>
                        <p class="text-lg font-bold">{{ \Carbon\Carbon::parse($shipment->shipped_at)->format('d-M-Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- BARCODE BOX ID (MASTER LABEL) -->
            <div class="p-6 text-center border-b-2 border-black flex flex-col items-center justify-center bg-white">
                <svg id="barcode-box"></svg>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        JsBarcode("#barcode-box", "{{ $shipment->box_id }}", {
                            format: "CODE128",
                            lineColor: "#000",
                            width: 3,
                            height: 80,
                            displayValue: true,
                            fontSize: 20,
                            fontOptions: "bold",
                            margin: 10
                        });
                    });
                </script>
            </div>

            <!-- DETAIL BERAT & ISI -->
            <div class="grid grid-cols-3 border-b-2 border-black text-center divide-x-2 divide-black">
                <div class="p-2">
                    <p class="text-[10px] font-bold text-gray-500">BERAT (KG)</p>
                    <p class="text-2xl font-bold">{{ $shipment->total_weight_kg }}</p>
                </div>
                <div class="p-2">
                    <p class="text-[10px] font-bold text-gray-500">TOTAL SKU</p>
                    <p class="text-2xl font-bold">{{ $totalSku }}</p>
                </div>
                <div class="p-2">
                    <p class="text-[10px] font-bold text-gray-500">TOTAL QTY</p>
                    <p class="text-2xl font-bold">{{ $totalQty }}</p>
                </div>
            </div>
             <!-- Footer removed as per request -->
        </div>

        <!-- PACKING LIST (Hanya Tampil di Layar, Tidak Dicetak di Label Sticker) -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden print:hidden">
            <div class="px-6 py-4 border-b dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Manifest Barang (Packing List)</h3>
                <span class="text-xs bg-gray-200 dark:bg-gray-600 px-2 py-1 rounded">Dokumen Internal</span>
            </div>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-600">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Part Number</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Deskripsi</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Qty</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($shipment->salesorders->items as $item)
                    <tr>
                        <td class="px-6 py-3 text-sm font-bold font-mono text-gray-900 dark:text-white">{{ $item->product->part_number }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $item->product->description }}</td>
                        <td class="px-6 py-3 text-center text-sm text-gray-900 dark:text-white font-bold">{{ $item->quantity_packed }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <style>
        @media print {
            @page { 
                size: 100mm 150mm; /* Ukuran Standar Label Resi (4x6 Inch) */
                margin: 0; 
            }
            body * { 
                visibility: hidden; 
            }
            .print-area, .print-area * { 
                visibility: visible; 
            }
            .print-area { 
                position: fixed; 
                left: 0; 
                top: 0; 
                width: 100%; 
                height: 100%;
                margin: 0; 
                padding: 5mm;
                border: 4px solid black !important; /* Border tebal saat cetak */
                box-sizing: border-box;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                background: white;
            }
            .no-print, header, nav, .shadow-lg { 
                display: none !important; 
            }
            /* Paksa cetak background & warna hitam pekat */
            * { 
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important; 
                color: black !important;
            }
            /* Invert area hitam agar tetap hitam saat dicetak */
            .bg-black {
                background-color: black !important;
                color: white !important;
            }
            .text-white {
                color: white !important;
            }
        }
    </style>
</x-app-layout>