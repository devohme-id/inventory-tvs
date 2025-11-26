<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SalesOrderController extends Controller
{
    public function index()
    {
        $orders = SalesOrder::withCount('items')
            ->latest('order_date')
            ->paginate(10);

        return view('sales_orders.index', compact('orders'));
    }

    /**
     * Form buat SO baru dengan Auto Number
     */
    public function create()
    {
        // 1. Generate Nomor SO Otomatis
        // Format: SO-YYYYMMDD-XXXX (Urutan per hari)
        $dateCode = now()->format('Ymd');
        $prefix = 'SO-' . $dateCode . '-';

        // Cari order terakhir hari ini
        $lastOrder = SalesOrder::where('so_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastOrder) {
            // Ambil 4 digit terakhir, ubah ke int, tambah 1
            $lastSequence = (int) substr($lastOrder->so_number, -4);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        // Padding angka jadi 4 digit (contoh: 1 -> 0001)
        $autoSoNumber = $prefix . str_pad($newSequence, 4, '0', STR_PAD_LEFT);

        return view('sales_orders.create', compact('autoSoNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'so_number'   => 'required|string|unique:sales_orders,so_number|max:50',
            'customer'    => 'required|string|max:100', // Customer Name
            'order_date'  => 'required|date',
        ]);

        $validated['status'] = 'Pending';

        $salesorders = SalesOrder::create($validated);

        return redirect()->route('sales-orders.show', $salesorders->id)
            ->with('success', 'Sales Order berhasil dibuat. Silakan tambahkan item.');
    }

    public function show($id)
    {
        $salesorders = SalesOrder::with(['items.product'])->findOrFail($id);
        $products = Product::orderBy('part_number')->get();

        return view('sales_orders.show', compact('salesorders', 'products'));
    }

    public function update(Request $request, $id)
    {
        $salesorders = SalesOrder::findOrFail($id);
        $validated = $request->validate([
            'status' => 'required|in:Pending,Picked,Packed,Shipped',
        ]);
        $salesorders->update($validated);
        return redirect()->back()->with('success', 'Status Sales Order diperbarui.');
    }

    public function destroy($id)
    {
        try {
            SalesOrder::destroy($id);
            return redirect()->route('sales-orders.index')->with('success', 'Sales Order dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus. Pastikan order kosong.');
        }
    }
}