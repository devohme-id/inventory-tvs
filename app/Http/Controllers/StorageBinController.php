<?php

namespace App\Http\Controllers;

use App\Models\StorageBin;
use App\Models\InboundItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StorageBinController extends Controller
{
    public function index(Request $request)
    {
        // 1. MASTER DATA: Query untuk Tabel Daftar Area (Grouping)
        $areas = StorageBin::select('rack')
            ->selectRaw('count(*) as total_bins')
            ->selectRaw('sum(case when is_empty = 0 then 1 else 0 end) as filled_bins')
            ->groupBy('rack')
            ->orderBy('rack', 'asc')
            ->get()
            ->map(function($area) {
                $area->usage_percent = $area->total_bins > 0 
                    ? round(($area->filled_bins / $area->total_bins) * 100) 
                    : 0;
                return $area;
            });

        // 2. CORE MODULE: Data untuk Visualisasi Put-away (Wajib Ada)
        $pendingItems = InboundItem::with(['product', 'invoice'])
            ->whereNull('bind_id')
            ->where(function($q) {
                $q->where('status', 'Received')
                  ->orWhereHas('invoice', function($inv) {
                      $inv->where('status', 'Received');
                  });
            })
            ->get();

        // Ambil data rak lengkap untuk visualisasi grid interaktif
        $allBins = StorageBin::with(['inventories.product:id,part_number,description'])
            ->select('id', 'bin_code', 'rack', 'level', 'slot', 'is_empty', 'bin_type')
            ->orderBy('bin_code')
            ->get()
            ->groupBy('rack');

        return view('storage_bins.index', compact('areas', 'pendingItems', 'allBins'));
    }

    /**
     * Halaman Detail Area: Menampilkan semua Bin & Produk di Area tertentu
     */
    public function showArea($rack)
    {
        // Ambil semua bin di rak ini beserta isinya
        $bins = StorageBin::where('rack', $rack)
            ->with(['inventories.product'])
            ->orderBy('level')
            ->orderBy('slot')
            ->get();

        // Hitung ringkasan produk unik di rak ini
        $totalProducts = $bins->flatMap->inventories->sum('quantity');
        $productTypes = $bins->flatMap->inventories->pluck('product.part_number')->unique()->count();

        return view('storage_bins.show_area', compact('rack', 'bins', 'totalProducts', 'productTypes'));
    }

    // ... method create, store, edit, update, destroy, show (single) tetap ada ...
    
    public function show($id)
    {
        $storageBin = StorageBin::with(['inventories.product'])->findOrFail($id);
        return view('storage_bins.show', compact('storageBin'));
    }

    public function create() { return view('storage_bins.create'); }
    
    public function store(Request $request) 
    {
        $validated = $request->validate([
            'bin_code' => 'required|string|unique:storage_bins,bin_code|max:20',
            'rack' => 'nullable|string|max:10',
            'level' => 'nullable|integer|min:1',
            'slot' => 'nullable|integer|min:1',
            'bin_type' => 'required|string',
        ]);
        $validated['is_empty'] = true;
        StorageBin::create($validated);
        return redirect()->route('storage-bins.index')->with('success', 'Lokasi rak berhasil ditambahkan.');
    }
    
    public function edit(StorageBin $storageBin) { return view('storage_bins.edit', compact('storageBin')); }
    
    public function update(Request $request, StorageBin $storageBin) 
    {
        $validated = $request->validate([
            'bin_code' => 'required|string|max:20|unique:storage_bins,bin_code,' . $storageBin->id,
            'rack' => 'nullable|string|max:10',
            'level' => 'nullable|integer|min:1',
            'slot' => 'nullable|integer|min:1',
            'bin_type' => 'required|string',
        ]);
        $storageBin->update($validated);
        return redirect()->route('storage-bins.index')->with('success', 'Data rak diperbarui.');
    }
    
    public function destroy(StorageBin $storageBin) 
    {
        if (!$storageBin->is_empty) return redirect()->back()->with('error', 'Gagal menghapus. Rak ini sedang terisi barang!');
        try {
            $storageBin->delete();
            return redirect()->route('storage-bins.index')->with('success', 'Lokasi rak dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus.');
        }
    }
}