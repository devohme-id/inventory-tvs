<?php

namespace App\Http\Controllers;

use App\Models\SoItem;
use Illuminate\Http\Request;

class SoItemController extends Controller
{
    /**
     * Tambah Item ke SO
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'so_id'            => 'required|exists:sales_orders,id',
            'product_id'       => 'required|exists:products,id',
            'quantity_ordered' => 'required|integer|min:1',
        ]);

        // Default value untuk proses picking/packing nanti
        $validated['quantity_picked'] = 0;
        $validated['quantity_packed'] = 0;

        // Cek apakah produk sudah ada di SO ini? Jika ya, update quantity saja (opsional)
        $existingItem = SoItem::where('so_id', $validated['so_id'])
                              ->where('product_id', $validated['product_id'])
                              ->first();

        if ($existingItem) {
            $existingItem->increment('quantity_ordered', $validated['quantity_ordered']);
        } else {
            SoItem::create($validated);
        }

        return redirect()->back()->with('success', 'Item berhasil ditambahkan ke pesanan.');
    }

    /**
     * Hapus Item dari SO
     */
    public function destroy($id)
    {
        SoItem::destroy($id);
        return redirect()->back()->with('success', 'Item dihapus dari pesanan.');
    }
}