<?php

namespace App\Http\Controllers;

use App\Models\InboundInvoice;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InboundInvoiceController extends Controller
{
    public function index()
    {
        $invoices = InboundInvoice::with('receiver')
            ->withCount('items')
            ->latest('received_at')
            ->paginate(10);

        return view('inbound.index', compact('invoices'));
    }

    public function create()
    {
        return view('inbound.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:inbound_invoices,invoice_number|max:50',
            'supplier'       => 'nullable|string|max:100',
            'received_at'    => 'required|date',
        ]);

        $validated['status'] = 'Pending';
        $validated['user_id'] = Auth::id();

        InboundInvoice::create($validated);

        return redirect()->route('incoming.index')
            ->with('success', 'Invoice berhasil dibuat. Silakan tambah item.');
    }

    public function show($id)
    {
        $invoice = InboundInvoice::with(['items.product', 'items.bin', 'receiver'])->findOrFail($id);
        $products = Product::orderBy('part_number')->get();

        return view('inbound.show', compact('invoice', 'products'));
    }

    public function update(Request $request, $id)
    {
        $invoice = InboundInvoice::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:Pending,Received,Stored',
        ]);

        $invoice->update($validated);

        // --- PERBAIKAN DI SINI ---
        // Jika status Invoice jadi 'Received', update semua item 'Pending' jadi 'Received'
        if ($validated['status'] === 'Received') {
            $invoice->items()->where('status', 'Pending')->update(['status' => 'Received']);
        }
        // Jika status Invoice jadi 'Stored', update semua item jadi 'Stored'
        elseif ($validated['status'] === 'Stored') {
            $invoice->items()->where('status', '!=', 'Stored')->update(['status' => 'Stored']);
        }

        return redirect()->back()->with('success', 'Status invoice & item diperbarui.');
    }

    public function destroy($id)
    {
        try {
            InboundInvoice::destroy($id);
            return redirect()->route('incoming.index')->with('success', 'Invoice dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus. Pastikan invoice kosong.');
        }
    }
}