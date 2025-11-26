<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center no-print">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-600 rounded-lg shadow-lg text-white">
                    <i class="fa-solid fa-box-open text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                        Packing Station: {{ $salesorders->so_number }}
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Scan item untuk menyelesaikan packing & cetak label otomatis.</p>
                </div>
            </div>
            <a href="{{ route('packing.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm">
                &laquo; Kembali
            </a>
        </div>
    </x-slot>

    <!-- Breadcrumb -->
    <x-breadcrumb :links="[
        ['label' => 'Outbound', 'url' => '#'],
        ['label' => 'Antrian Packing', 'url' => route('packing.index')],
        ['label' => 'Packing ' . $salesorders->so_number, 'url' => '#']
    ]" />

    <!-- Library Barcode Generator -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- KOLOM KIRI: KONTROL UTAMA (SCANNER) -->
        <div class="lg:col-span-1 space-y-6 no-print">
            
            <!-- OPSI 1: SCANNER (AUTO FINISH) -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 border-t-4 border-blue-600">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">
                    <i class="fa-solid fa-barcode mr-2"></i> Scan Label
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Scan barcode produk. Sistem akan otomatis <strong>menyelesaikan packing</strong> untuk SKU tersebut dan mencetak label total.
                </p>

                @if(!$isComplete)
                <form action="{{ route('packing.scan', $salesorders->id) }}" method="POST">
                    @csrf
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-qrcode text-gray-400 text-lg"></i>
                        </div>
                        <input type="text" name="barcode" autofocus autocomplete="off"
                            class="block w-full pl-10 pr-4 py-3 border-2 border-blue-300 rounded-lg focus:ring-blue-500 focus:border-blue-600 text-lg font-bold text-gray-900 placeholder-gray-400 dark:bg-gray-900 dark:border-gray-600 dark:text-white"
                            placeholder="Scan Part Number..." />
                    </div>
                    <button type="submit" class="mt-3 w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 px-4 rounded-lg shadow transition flex justify-center items-center">
                        <i class="fa-solid fa-print mr-2"></i> SCAN & CETAK LABEL
                    </button>
                </form>
                @else
                <div class="p-4 bg-green-100 border border-green-300 rounded text-center text-green-800 dark:bg-green-900/50 dark:text-green-300 dark:border-green-700">
                    <i class="fa-solid fa-check-circle text-3xl mb-2"></i>
                    <p class="font-bold">Semua item selesai dipacking!</p>
                </div>
                @endif
            </div>

            <!-- PROGRESS STATUS -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
                <div class="flex justify-between text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <span>Progress Packing</span>
                    <span>{{ $progress }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-700">
                    <div class="bg-blue-600 h-4 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                </div>
                <div class="mt-4 text-center border-t pt-3 dark:border-gray-700">
                    <p class="text-xs text-gray-500 uppercase font-bold">Total Item Terproses</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $salesorders->items->sum('quantity_packed') }} <span class="text-sm font-normal text-gray-500">dari</span> {{ $salesorders->items->sum('quantity_picked') }} <span class="text-sm font-normal text-gray-500">Pcs</span>
                    </p>
                </div>
            </div>

            <!-- TOMBOL SELESAI & UPDATE STATUS -->
            @if($isComplete)
            <div class="bg-green-50 dark:bg-green-900/30 border-2 border-green-500 rounded-xl p-6 text-center animate-pulse">
                <h4 class="text-xl font-bold text-green-700 dark:text-green-400 mb-2">Packing Selesai!</h4>
                <p class="text-green-600 dark:text-green-300 mb-4 text-sm">Semua label telah dicetak. Klik tombol di bawah untuk memindahkan status ke <strong>PACKED</strong>.</p>
                
                <form action="{{ route('packing.finish', $salesorders->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow transition transform hover:scale-105 flex justify-center items-center">
                        <i class="fa-solid fa-check-double mr-2"></i> SELESAI & UPDATE
                    </button>
                </form>
            </div>
            @endif
        </div>

        <!-- KOLOM KANAN: DAFTAR ITEM & OPSI MANUAL -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex justify-between items-center no-print">
                <h3 class="font-bold text-gray-700 dark:text-gray-300 flex items-center">
                    <i class="fa-solid fa-list-check mr-2"></i> Daftar Barang
                </h3>
                <span class="text-xs bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded text-gray-600 dark:text-gray-300 font-mono">
                    SO: {{ $salesorders->so_number }}
                </span>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-700 no-print max-h-[600px] overflow-y-auto">
                @foreach($salesorders->items as $item)
                @php $sisa = $item->quantity_picked - $item->quantity_packed; @endphp
                <div class="p-4 flex flex-col sm:flex-row justify-between items-center gap-4 transition {{ $item->quantity_packed >= $item->quantity_picked ? 'bg-green-50 dark:bg-green-900/20' : 'hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    
                    <!-- Info Produk -->
                    <div class="flex items-start space-x-4 w-full">
                        <div class="flex-shrink-0 pt-1">
                             @if($item->quantity_packed >= $item->quantity_picked)
                                <div class="w-12 h-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center shadow-sm">
                                    <i class="fa-solid fa-check text-xl"></i>
                                </div>
                            @else
                                <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center border border-gray-300 font-bold text-lg">
                                    {{ $sisa }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <p class="text-xl font-black text-gray-900 dark:text-white font-mono">{{ $item->product->part_number }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-1">{{ $item->product->description }}</p>
                            <div class="mt-1">
                                <span class="text-[10px] font-mono bg-gray-200 dark:bg-gray-600 px-2 py-0.5 rounded text-gray-700 dark:text-gray-300">
                                    Packed: {{ $item->quantity_packed }} / {{ $item->quantity_picked }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Kontrol Kanan -->
                    <div class="flex items-center gap-4 w-full sm:w-auto justify-end">
                        
                        <!-- OPSI 2: TOMBOL CETAK MANUAL (Juga berfungsi sebagai Pack All) -->
                        @if($item->quantity_packed < $item->quantity_picked)
                            <form action="{{ route('packing.pack_all', $salesorders->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                                
                                <button type="submit" 
                                    class="flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-white px-5 py-3 rounded-lg shadow-lg transition transform hover:scale-105 border-2 border-gray-600" 
                                    title="Pack & Cetak Label">
                                    <i class="fa-solid fa-print text-2xl"></i>
                                    <div class="text-left leading-none ml-1">
                                        <span class="block text-[10px] font-bold uppercase text-gray-300 tracking-wider">MANUAL</span>
                                        <span class="block text-base font-black">PRINT LABEL</span>
                                    </div>
                                </button>
                            </form>
                        @else
                            <div class="px-4 py-2 bg-green-100 text-green-700 rounded-lg font-bold text-xs border border-green-200 flex items-center">
                                <i class="fa-solid fa-check mr-2"></i> SELESAI
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <!-- TEMPLATE LABEL CETAK -->
            @if(session('print_label'))
                
                <!-- Script Generate Barcode -->
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        // Barcode CODE128 (Angka & Huruf)
                        JsBarcode("#barcode", "{{ session('print_label')['part_number'] }}", {
                            format: "CODE128",
                            lineColor: "#000",
                            width: 3, 
                            height: 100,
                            displayValue: false, 
                            margin: 10
                        });
                        // Auto print popup (Opsional)
                        // window.print();
                    });
                </script>

                <div class="p-6 m-4 bg-gray-100 border-2 border-dashed border-gray-400 rounded-xl print-area-container text-center">
                    <p class="text-green-600 font-bold mb-4 text-lg animate-pulse"><i class="fa-solid fa-check-circle"></i> Label Siap Dicetak!</p>
                    
                    <!-- LABEL FISIK (10cm x 6cm) -->
                    <div class="print-area bg-white border-4 border-black w-[10cm] h-[6cm] mx-auto flex flex-col justify-center items-center p-2 box-border shadow-2xl relative overflow-hidden">
                        
                        <!-- 1. PART NUMBER -->
                        <div class="w-full text-center mb-2">
                            <h1 class="text-5xl font-black tracking-tighter leading-none uppercase" style="font-family: 'Arial Black', sans-serif;">
                                {{ session('print_label')['part_number'] }}
                            </h1>
                        </div>

                        <!-- 2. BARCODE -->
                        <div class="w-full flex justify-center items-center flex-1">
                            <svg id="barcode" class="w-full h-full"></svg>
                        </div>

                        <!-- 3. INFO TAMBAHAN -->
                        <div class="w-full flex justify-between items-end px-2 mt-1 border-t-2 border-black pt-1">
                            <div>
                                <p class="text-[10px] font-mono font-bold text-gray-500">{{ $salesorders->customer }}</p>
                                <p class="text-[8px] font-mono">{{ date('d/m/Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-bold">QTY: {{ session('print_label')['qty'] }} PCS</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Kontrol Print -->
                    <div class="mt-6 space-x-4 no-print">
                        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-bold shadow-lg transform hover:scale-105 transition text-lg">
                            <i class="fa-solid fa-print mr-2"></i> PRINT
                        </button>
                        <button onclick="this.closest('.print-area-container').style.display='none'" class="text-gray-500 underline text-sm hover:text-gray-700">
                            Tutup
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        /* Styling Khusus Cetak Label */
        @media print {
            @page { size: 10cm 6cm; margin: 0; } /* Ukuran Label Sticker */
            body * { visibility: hidden; }
            .print-area, .print-area * { visibility: visible; }
            .print-area { 
                position: fixed; 
                left: 0; 
                top: 0; 
                width: 100%; 
                height: 100%;
                margin: 0; 
                padding: 2mm;
                border: none !important;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                background: white;
            }
            .no-print { display: none !important; }
        }
    </style>
</x-app-layout>