<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\Product;
use App\Models\SoItem;
use Illuminate\Http\Request;

class PackingController extends Controller
{
    /**
     * Halaman List Antrian PACKING (Dengan Tab Riwayat)
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'list');

        if ($tab === 'history') {
            // Riwayat: SO yang sudah selesai Packing (Packed/Shipped)
            $packingOrders = SalesOrder::whereIn('status', ['Packed', 'Shipped'])
                ->withCount('items')
                ->orderBy('updated_at', 'desc')
                ->paginate(10);
        } else {
            // List: Antrian Packing (Picked)
            $packingOrders = SalesOrder::where('status', 'Picked')
                ->withCount('items')
                ->orderBy('updated_at', 'asc') // FIFO
                ->paginate(10);
        }

        return view('packing.index', compact('packingOrders', 'tab'));
    }

    public function process($soId)
    {
        $salesorders = SalesOrder::with(['items.product'])->findOrFail($soId);
        
        $totalItems = $salesorders->items->sum('quantity_picked');
        $totalPacked = $salesorders->items->sum('quantity_packed');
        
        $progress = $totalItems > 0 ? round(($totalPacked / $totalItems) * 100) : 0;
        $isComplete = $totalPacked >= $totalItems && $totalItems > 0;

        // PASTIKAN INI: 'packing.process', BUKAN 'picking_tasks.pack'
        return view('packing.process', compact('salesorders', 'progress', 'isComplete'));
    }

    // ... (Method scan, packAllItems, finish tetap sama, tidak berhubungan dengan view) ...
    
    public function scan(Request $request, $soId)
    {
        $request->validate(['barcode' => 'required|string']);
        $barcode = $request->barcode;
        
        $product = Product::where('part_number', $barcode)->first();
        if (!$product) return redirect()->back()->with('error', 'Barcode tidak valid.');

        $item = SoItem::where('so_id', $soId)->where('product_id', $product->id)->first();
        if (!$item) return redirect()->back()->with('error', 'Item tidak ada dalam order ini.');

        if ($item->quantity_packed >= $item->quantity_picked) {
            return redirect()->back()->with('warning', 'Item ini sudah selesai di-packing.');
        }

        $item->increment('quantity_packed');
        
        return redirect()->route('packing.process', $soId)
            ->with('print_label', [
                'part_number' => $product->part_number,
                'description' => $product->description,
                'qty' => 1
            ])
            ->with('success', "Item discan.");
    }

    public function packAllItems(Request $request, $soId)
    {
        $item = SoItem::where('so_id', $soId)->where('product_id', $request->product_id)->firstOrFail();
        $remaining = $item->quantity_picked - $item->quantity_packed;

        if ($remaining > 0) {
            $item->update(['quantity_packed' => $item->quantity_picked]);
            return redirect()->route('packing.process', $soId)
                ->with('print_label', [
                    'part_number' => $item->product->part_number,
                    'description' => $item->product->description,
                    'qty' => $remaining
                ])
                ->with('success', "Label Bulk dicetak.");
        }
        return redirect()->back()->with('warning', 'Item sudah selesai.');
    }

    public function finish($soId)
    {
        $so = SalesOrder::findOrFail($soId);
        $totalPicked = $so->items->sum('quantity_picked');
        $totalPacked = $so->items->sum('quantity_packed');

        if ($totalPacked < $totalPicked) {
            return redirect()->back()->with('error', 'Belum semua item dipacking!');
        }

        $so->update(['status' => 'Packed']);
        return redirect()->route('packing.index')
            ->with('success', "Packing Selesai! SO {$so->so_number} berstatus PACKED.");
    }
}