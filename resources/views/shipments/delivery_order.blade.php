<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan - {{ $shipment->box_id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 0; padding: 20px; }
        .header { border-bottom: 2px solid black; padding-bottom: 10px; margin-bottom: 20px; display: flex; justify-content: space-between; }
        .logo { font-size: 24px; font-weight: bold; }
        .company-info { font-size: 10px; color: #555; }
        .title { font-size: 18px; font-weight: bold; text-align: center; margin-bottom: 20px; text-transform: uppercase; }
        
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px; vertical-align: top; }
        .label { font-weight: bold; width: 120px; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th, .items-table td { border: 1px solid black; padding: 8px; text-align: left; }
        .items-table th { background-color: #f0f0f0; text-align: center; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .footer { display: flex; justify-content: space-between; margin-top: 50px; text-align: center; }
        .signature-box { height: 80px; }
        
        @media print {
            @page { size: A4; margin: 10mm; }
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <!-- Tombol Print (Hanya di layar) -->
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #1e3a8a; color: white; border: none; cursor: pointer; font-weight: bold;">CETAK SURAT JALAN</button>
    </div>

    <div class="header">
        <div>
            <div class="logo">TVS MOTOR COMPANY</div>
            <div class="company-info">
                Jl. Surya Cipta, Karawang, Jawa Barat<br>
                Telp: (021) 12345678 | Email: warehouse@tvsmotor.co.id
            </div>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 16px; font-weight: bold;">SURAT JALAN</div>
            <div style="font-family: monospace;">NO: DO-{{ $shipment->id }}/{{ date('m/Y') }}</div>
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Kepada Yth:</td>
            <td>
                <strong>{{ $shipment->salesorders->customer }}</strong><br>
                (Alamat Customer Di Sini)
            </td>
            <td class="label">Tanggal Kirim:</td>
            <td>{{ \Carbon\Carbon::parse($shipment->shipped_at)->format('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">No. Referensi:</td>
            <td>{{ $shipment->salesorders->so_number }}</td>
            <td class="label">No. Resi / Box:</td>
            <td>{{ $shipment->box_id }}</td>
        </tr>
        <tr>
            <td class="label">Pengirim:</td>
            <td>Gudang Pusat ({{ $shipment->users->name ?? 'Admin' }})</td>
            <td class="label">Berat Total:</td>
            <td>{{ $shipment->total_weight_kg }} Kg</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">NO</th>
                <th style="width: 20%;">PART NUMBER</th>
                <th>NAMA BARANG</th>
                <th style="width: 10%;">QTY</th>
                <th style="width: 15%;">KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($shipment->salesorders->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td style="font-family: monospace; font-weight: bold;">{{ $item->product->part_number }}</td>
                <td>{{ $item->product->description }}</td>
                <td class="text-center font-bold">{{ $item->quantity_packed }} PCS</td>
                <td></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div style="width: 30%;">
            <p>Penerima,</p>
            <div class="signature-box"></div>
            <p>( ..................................... )</p>
            <p>Tanda Tangan & Stempel</p>
        </div>
        <div style="width: 30%;">
            <p>Pengemudi / Kurir,</p>
            <div class="signature-box"></div>
            <p>( ..................................... )</p>
        </div>
        <div style="width: 30%;">
            <p>Hormat Kami,</p>
            <div class="signature-box"></div>
            <p>( {{ $shipment->users->name ?? 'Kepala Gudang' }} )</p>
            <p>Warehouse Dept.</p>
        </div>
    </div>

</body>
</html>