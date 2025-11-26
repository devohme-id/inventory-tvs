<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Carbon\Carbon;

// Import Models
use App\Models\SalesOrder;
use App\Models\StorageBin;
use App\Models\Shipment;
use App\Models\PickingTask;
use App\Models\InboundInvoice;
use App\Models\Product; // Tambahkan Model Product

class DashboardController extends Controller
{
    public function index()
    {
        // --- 1. Data KPI (Realtime Database) ---
        $kpi = [
            'pending' => SalesOrder::where('status', 'Pending')->count(),
            'picking' => SalesOrder::whereIn('status', ['Picking', 'Picked'])->count(),
            'packing' => SalesOrder::where('status', 'Packed')->count(),
            'shipped' => Shipment::whereDate('shipped_at', Carbon::today())->count()
        ];

        // --- 2. Data Grafik Utilitas Gudang ---
        $totalBins = StorageBin::count();
        $emptyBins = StorageBin::where('is_empty', true)->count();
        
        $storage = [
            'total' => $totalBins,
            'used'  => $totalBins - $emptyBins,
            'empty' => $emptyBins
        ];

        // --- 3. URGENSI SALES ORDER (Aging Order) ---
        // Ambil order pending, urutkan dari yang terlama
        $urgentOrders = SalesOrder::where('status', 'Pending')
            ->orderBy('order_date', 'asc')
            ->limit(5)
            ->get()
            ->map(function ($order) {
                // Hitung selisih hari
                $daysPending = Carbon::parse($order->order_date)->diffInDays(Carbon::now());
                
                return [
                    'id' => $order->so_number,
                    'customer' => $order->customer,
                    'date' => $order->order_date,
                    'days_pending' => $daysPending, // Info durasi keterlambatan
                    'is_late' => $daysPending >= 3  // Flag jika sudah > 3 hari
                ];
            });

        // --- 4. URGENSI PRODUK (Low Stock Alert) ---
        // Ambil 5 produk dengan total stok terendah
        $lowStockProducts = Product::withSum('inventories', 'quantity')
            ->orderBy('inventories_sum_quantity', 'asc') // Dari stok terkecil
            ->limit(5)
            ->get();

        // --- 5. Audit Log Terbaru ---
        $logs = collect();

        // A. Log Pengiriman
        $shipments = Shipment::with(['users', 'salesorders'])
            ->latest('shipped_at')
            ->limit(5)
            ->get();
            
        foreach ($shipments as $shipment) {
            $logs->push([
                'timestamp' => $shipment->shipped_at,
                'type'      => 'Shipment',
                'detail'    => "Pengiriman Box {$shipment->box_id}",
                'user'      => $shipment->users->name ?? 'System',
                'time'      => Carbon::parse($shipment->shipped_at)->diffForHumans(),
                'icon'      => 'fa-solid fa-truck-fast',
                'color'     => 'text-green-600',
                'bg'        => 'bg-green-100'
            ]);
        }

        // B. Log Picking
        $pickings = PickingTask::with(['users', 'soitems.salesorders'])
            ->whereNotNull('picked_at')
            ->latest('picked_at')
            ->limit(5)
            ->get();

        foreach ($pickings as $picking) {
            $soNumber = $picking->soitems->salesorders->so_number ?? 'SO-Unknown';
            $logs->push([
                'timestamp' => $picking->picked_at,
                'type'      => 'Picking',
                'detail'    => "Picking Item untuk {$soNumber}",
                'user'      => $picking->users->name ?? 'Operator',
                'time'      => Carbon::parse($picking->picked_at)->diffForHumans(),
                'icon'      => 'fa-solid fa-barcode',
                'color'     => 'text-yellow-600',
                'bg'        => 'bg-yellow-100'
            ]);
        }

        $sortedLogs = $logs->sortByDesc('timestamp')->take(5)->values();

        return view('dashboard', [
            'kpi' => $kpi,
            'storage' => $storage,
            'urgentOrders' => $urgentOrders,
            'lowStockProducts' => $lowStockProducts, // Data baru
            'logs' => $sortedLogs
        ]);
    }
}