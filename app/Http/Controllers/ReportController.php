<?php

namespace App\Http\Controllers;

use App\Models\InboundItem;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Halaman Utama Laporan Stok
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'inbound'); // inbound atau outbound
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

        $data = [];

        if ($type === 'inbound') {
            // Laporan Barang Masuk
            $data = InboundItem::with(['invoice', 'product'])
                ->whereHas('invoice', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('received_at', [$startDate, $endDate])
                      ->where('status', '!=', 'Pending'); // Hanya yang sudah diterima
                })
                ->latest()
                ->get();
        } else {
            // Laporan Barang Keluar (Shipment)
            $data = Shipment::with(['salesorders.items.product', 'salesorders'])
                ->whereBetween('shipped_at', [$startDate, $endDate])
                ->latest()
                ->get();
        }

        return view('reports.index', compact('data', 'type', 'startDate', 'endDate'));
    }

    /**
     * Cetak Laporan (HTML Print Friendly)
     */
    public function print(Request $request)
    {
        // Logika sama dengan index, tapi return view khusus cetak
        $type = $request->get('type', 'inbound');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // ... (Logika query sama, disingkat agar efisien) ...
        if ($type === 'inbound') {
            $data = InboundItem::with(['invoice', 'product'])
                ->whereHas('invoice', fn($q) => $q->whereBetween('received_at', [$startDate, $endDate])->where('status', '!=', 'Pending'))
                ->get();
        } else {
            $data = Shipment::with(['salesorders.items.product', 'salesorders'])
                ->whereBetween('shipped_at', [$startDate, $endDate])
                ->get();
        }

        return view('reports.print', compact('data', 'type', 'startDate', 'endDate'));
    }
}