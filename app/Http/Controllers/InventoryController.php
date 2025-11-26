<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\StorageBin;
use App\Models\InboundItem;
use Illuminate\Http\Request;
// use App\Models\AuditLog; // Tidak perlu jika sudah pakai Observer

class InventoryController extends Controller
{
    // ... method index & create tetap sama ...
    public function index(Request $request)
    {
        $query = Inventory::with(['product', 'bin']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('part_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            })->orWhereHas('bin', function($q) use ($search) {
                $q->where('bin_code', 'like', "%{$search}%");
            });
        }

        if ($request->has('category') && $request->category != '') {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        $inventories = $query->latest('last_updated')->paginate(15);
        return view('inventory.index', compact('inventories'));
    }

    public function create()
    {
        $products = Product::orderBy('part_number')->get();
        $bins = StorageBin::orderBy('bin_code')->get();
        return view('inventory.create', compact('products', 'bins'));
    }

    /**
     * Simpan Penyesuaian Stok (Diperbaiki)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'bind_id'    => 'required|exists:storage_bins,id',
            'quantity'   => 'required|integer|min:0',
            'inbound_item_id' => 'nullable|exists:inbound_items,id'
        ]);

        // 1. Cari Inventory yang sudah ada (berdasarkan Produk & Rak)
        $inventory = Inventory::where('product_id', $validated['product_id'])
            ->where('bind_id', $validated['bind_id'])
            ->first();

        if ($inventory) {
            // --- JIKA SUDAH ADA: UPDATE ---
            
            if ($request->has('inbound_item_id')) {
                // Mode Put-away: Tambahkan ke stok yang ada
                $inventory->quantity += $validated['quantity'];
            } else {
                // Mode Stok Opname: Timpa stok (koreksi total)
                $inventory->quantity = $validated['quantity'];
            }
            
            $inventory->save(); // Ini akan memicu Observer 'updated'
            
        } else {
            // --- JIKA BELUM ADA: CREATE ---
            
            $inventory = Inventory::create([
                'product_id' => $validated['product_id'],
                'bind_id'    => $validated['bind_id'],
                'quantity'   => $validated['quantity']
            ]); // Ini akan memicu Observer 'created'
        }

        // 2. Update Status Rak Menjadi Terisi
        // (Kita ambil ulang object bin untuk memastikan status terupdate)
        StorageBin::where('id', $validated['bind_id'])->update(['is_empty' => false]);

        // 3. Jika ini berasal dari Put-away, update status InboundItem
        if ($request->has('inbound_item_id')) {
            $inboundItem = InboundItem::find($request->inbound_item_id);
            $inboundItem->update([
                'status' => 'Stored',
                'bind_id' => $validated['bind_id']
            ]);
            
            return redirect()->route('storage-bins.index')
                ->with('success', 'Put-away berhasil! Stok bertambah & Item ditandai Stored.');
        }

        return redirect()->route('inventory.index')
            ->with('success', 'Stok berhasil disesuaikan.');
    }

    // ... method edit, update, destroy tetap sama ...
    public function edit($id)
    {
        $inventory = Inventory::with(['product', 'bin'])->findOrFail($id);
        return view('inventory.edit', compact('inventory'));
    }

    public function update(Request $request, $id)
    {
        $inventory = Inventory::findOrFail($id);
        $validated = $request->validate(['quantity' => 'required|integer|min:0']);
        
        $inventory->update($validated);
        $inventory->bin->update(['is_empty' => $inventory->quantity == 0]);

        return redirect()->route('inventory.index')->with('success', 'Jumlah stok diperbarui.');
    }

    public function destroy($id)
    {
        $inventory = Inventory::findOrFail($id);
        
        // Set bin jadi kosong sebelum dihapus (cek dulu apakah ada produk lain di bin ini? 
        // Asumsi: 1 bin bisa mix product, tapi 'is_empty' true hanya jika benar2 kosong.
        // Untuk simplifikasi, kita cek jumlah inventory lain di bin ini)
        
        $otherItemsCount = Inventory::where('bind_id', $inventory->bind_id)
            ->where('id', '!=', $id)
            ->count();

        if ($otherItemsCount == 0) {
            $inventory->bin->update(['is_empty' => true]);
        }
        
        $inventory->delete();
        return redirect()->route('inventory.index')->with('success', 'Data stok dihapus.');
    }
}