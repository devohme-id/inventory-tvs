<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Stok - {{ ucfirst($type) }}</title>
    <style>
        /* Reset & Base Style */
        body { 
            font-family: Arial, sans-serif; 
            font-size: 10pt; /* Font lebih kecil agar muat banyak */
            margin: 0; 
            padding: 10mm; 
            color: #000;
        }
        
        /* Header yang lebih ringkas */
        .header { 
            text-align: center; 
            margin-bottom: 15px; 
            border-bottom: 2px solid black; 
            padding-bottom: 5px; 
        }
        .header h1 { 
            margin: 0; 
            font-size: 16pt; 
            text-transform: uppercase; 
            letter-spacing: 1px;
        }
        .header p { 
            margin: 2px 0 0; 
            font-size: 9pt; 
            color: #333; 
        }

        /* Tabel Data Compact */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 5px; 
        }
        th, td { 
            border: 1px solid #333; 
            padding: 4px 6px; /* Padding dikurangi */
            text-align: left; 
            font-size: 9pt;
            vertical-align: middle;
        }
        th { 
            background-color: #e0e0e0; 
            text-align: center; 
            font-weight: bold;
            font-size: 8pt;
            text-transform: uppercase;
        }
        
        /* Helper Classes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        
        /* Alternating Row Colors (Optional, usually good for reading) */
        tr:nth-child(even) { background-color: #f9f9f9; }

        /* Footer Info */
        .footer-info {
            margin-top: 20px;
            font-size: 8pt;
            text-align: right;
            color: #555;
            font-style: italic;
        }

        /* Print Settings */
        @media print {
            @page { 
                size: A4 landscape; 
                margin: 10mm; 
            }
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
        }
    </style>
</head>
<body>
    <!-- Tombol Print (Hanya di Layar) -->
    <div class="no-print" style="text-align: right; margin-bottom: 10px; border-bottom: 1px dashed #ccc; padding-bottom: 10px;">
        <button onclick="window.print()" style="padding: 6px 12px; cursor: pointer; background: #1e3a8a; color: white; border: none; font-weight: bold; border-radius: 4px;">
            <i class="fa-solid fa-print"></i> Cetak Laporan PDF
        </button>
    </div>

    <div class="header">
        <h1>Laporan {{ $type == 'inbound' ? 'Barang Masuk (Inbound)' : 'Barang Keluar (Outbound)' }}</h1>
        <p>Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 15%;">No. Dokumen</th>
                @if($type == 'inbound')
                    <th style="width: 20%;">Supplier</th>
                    <th>Produk (Part No & Deskripsi)</th>
                    <th style="width: 8%;">Qty</th>
                @else
                    <th style="width: 20%;">Customer</th>
                    <th style="width: 10%;">Total Item</th>
                    <th style="width: 8%;">Berat (Kg)</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                @if($type == 'inbound')
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->invoice->received_at)->format('d/m/Y') }}</td>
                    <td class="font-mono font-bold">{{ $item->invoice->invoice_number }}</td>
                    <td>{{ Str::limit($item->invoice->supplier, 25) }}</td>
                    <td>
                        <span class="font-bold">{{ $item->product->part_number }}</span><br>
                        <span style="font-size: 8pt; color: #555;">{{ Str::limit($item->product->description, 40) }}</span>
                    </td>
                    <td class="text-center font-bold">{{ $item->quantity_received }}</td>
                @else
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->shipped_at)->format('d/m/Y') }}</td>
                    <td class="font-mono font-bold">
                        {{ $item->box_id }}<br>
                        <span style="font-size: 7pt; font-weight: normal; color: #666;">Ref: {{ $item->salesorders->so_number }}</span>
                    </td>
                    <td>{{ Str::limit($item->salesorders->customer, 30) }}</td>
                    <td class="text-center">{{ $item->salesorders->items->count() }} SKU</td>
                    <td class="text-center">{{ $item->total_weight_kg }}</td>
                @endif
            </tr>
            @endforeach
            
            @if(count($data) == 0)
            <tr>
                <td colspan="6" class="text-center" style="padding: 20px; color: #777; font-style: italic;">
                    Tidak ada data transaksi pada periode ini.
                </td>
            </tr>
            @endif
        </tbody>
    </table>
    
    <div class="footer-info">
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} | User: {{ Auth::user()->name }} | Halaman ini digenerate oleh sistem TVS WMS.</p>
    </div>
</body>
</html>