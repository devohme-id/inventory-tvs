<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\SalesOrder;
use App\Models\Shipment;
use App\Models\StorageBin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShipmentController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'waiting');

        // Tab 1: Waiting Shipment (Status: Packed)
        $readyOrders = SalesOrder::where('status', 'Packed')
            ->withCount('items')
            ->orderBy('updated_at', 'asc')
            ->get();

        // Tab 2: Log Shipped (History)
        $historyShipments = Shipment::with(['salesorders', 'users'])
            ->latest('shipped_at')
            ->paginate(10);

        return view('shipments.index', compact('readyOrders', 'historyShipments', 'tab'));
    }

    public function create()
    {
        return redirect()->route('shipments.index');
    }

    /**
     * Proses Shipment & Pengurangan Stok Akhir dengan PROTEKSI RACE CONDITION
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'so_id'           => 'required|exists:sales_orders,id',
            'box_id'          => 'required|string|max:50',
            'total_weight_kg' => 'required|numeric|min:0.1',
            'operator_id'     => 'required|exists:users,id',
            'shipped_at'      => 'required|date',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // 1. Buat Data Shipment
                Shipment::create($validated);

                // 2. Update Status Sales Order
                $salesOrder = SalesOrder::with('items.pickingTask.storagebins', 'items.product')->find($validated['so_id']);
                $salesOrder->update(['status' => 'Shipped']);

                // 3. PENGURANGAN STOK (FINAL DEDUCTION)
                foreach ($salesOrder->items as $soItem) {
                    $task = $soItem->pickingTask;
                    
                    // PERBAIKAN: Menggunakan 'bin_id' (bukan bind_id)
                    if ($task && $task->bin_id) {
                        
                        // Lock baris inventory untuk mencegah race condition
                        $inventory = Inventory::where('product_id', $soItem->product_id)
                            ->where('bin_id', $task->bin_id) // UPDATE: bin_id
                            ->lockForUpdate() 
                            ->first();

                        $qtyToDeduct = $soItem->quantity_packed;
                        $partNo = $soItem->product->part_number;
                        $binCode = $task->storagebins->bin_code ?? 'Unknown Bin';

                        // --- VALIDASI 1: Stok Ada? ---
                        if (!$inventory) {
                            throw new \Exception("CRITICAL: Data inventory tidak ditemukan untuk {$partNo} di Rak ID {$task->bin_id}. Cek Master Data Stok.");
                        }

                        // --- VALIDASI 2: Stok Cukup? ---
                        if ($inventory->quantity < $qtyToDeduct) {
                            throw new \Exception("GAGAL KIRIM! Stok {$partNo} di {$binCode} tidak cukup. Sistem: {$inventory->quantity}, Butuh: {$qtyToDeduct}.");
                        }

                        // Eksekusi Pengurangan
                        $inventory->decrement('quantity', $qtyToDeduct);
                        
                        // Update status bin jika kosong
                        if ($inventory->quantity == 0) {
                            StorageBin::where('id', $task->bin_id)->update(['is_empty' => true]); // UPDATE: bin_id
                        }
                    }
                }
            });

            return redirect()->route('shipments.index', ['tab' => 'history'])
                ->with('success', 'Shipment berhasil! Stok telah dikurangi secara aman.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Halaman Detail & Cetak Label (Diperbaiki untuk Label Profesional)
     */
    public function show($id)
    {
        // Eager load detail item dan produk agar bisa ditampilkan di label
        $shipment = Shipment::with(['salesorders.items.product', 'users'])->findOrFail($id);
        
        // Hitung total SKU unik dan total item
        $totalSku = $shipment->salesorders->items->count();
        $totalQty = $shipment->salesorders->items->sum('quantity_packed');

        return view('shipments.show', compact('shipment', 'totalSku', 'totalQty'));
    }

    public function edit(Shipment $shipment)
    {
        $operators = User::where('role', 'Operator')->get();

        return view('shipments.edit', compact('shipment', 'operators'));
    }

    public function update(Request $request, Shipment $shipment)
    {
        $validated = $request->validate([
            'box_id' => 'required|string|max:50|unique:shipments,box_id,'.$shipment->id,
            'total_weight_kg' => 'required|numeric|min:0.1',
            'operator_id' => 'required|exists:users,id',
            'shipped_at' => 'required|date',
        ]);

        $shipment->update($validated);

        return redirect()->route('shipments.index')
            ->with('success', 'Data pengiriman diperbarui.');
    }

    public function destroy(Shipment $shipment)
    {
        // Kembalikan status SO ke 'Packed'
        $shipment->salesorders->update(['status' => 'Packed']);

        $shipment->delete();

        return redirect()->route('shipments.index')
            ->with('success', 'Data pengiriman dihapus. Status SO dikembalikan ke Packed.');
    }

    /**
     * Cetak Surat Jalan (Delivery Order)
     */
    public function printDeliveryOrder($id)
    {
        $shipment = Shipment::with(['salesorders.items.product', 'users'])->findOrFail($id);
        
        // Return view khusus surat jalan yang bersih tanpa navbar/sidebar
        return view('shipments.delivery_order', compact('shipment'));
    }
}
