<?php

namespace App\Http\Controllers;

use App\Models\InboundItem;
use Illuminate\Http\Request;

class InboundItemController extends Controller
{
    /**
     * Menyimpan item baru ke dalam invoice
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:inbound_invoices,id',
            'product_id' => 'required|exists:products,id',
            'quantity_expected' => 'required|integer|min:1',
        ]);

        // Default values
        $validated['quantity_received'] = 0;
        $validated['status'] = 'Pending';
        $validated['bind_id'] = null; // Belum masuk rak

        InboundItem::create($validated);

        return redirect()->back()->with('success', 'Item berhasil ditambahkan ke invoice.');
    }

    /**
     * Menghapus item dari invoice
     */
    public function destroy($id)
    {
        InboundItem::destroy($id);

        return redirect()->back()->with('success', 'Item dihapus dari list.');
    }
}
