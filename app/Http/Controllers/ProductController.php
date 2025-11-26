<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
// Tidak perlu lagi import AuditLog atau Auth untuk keperluan log

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::withSum('inventories', 'quantity');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('part_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate(10);
        
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'part_number' => 'required|string|unique:products,part_number|max:50',
            'description' => 'nullable|string|max:255',
            'category'    => 'nullable|string|max:50',
            'weight_kg'   => 'nullable|numeric|min:0',
        ]);

        // CREATE: Observer akan otomatis mencatat log "Create Product"
        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan ke Master Data.');
    }

    public function show($id)
    {
        $product = Product::with(['inventories.bin'])->findOrFail($id);
        $totalStock = $product->inventories->sum('quantity');

        return view('products.show', compact('product', 'totalStock'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'part_number' => 'required|string|max:50|unique:products,part_number,' . $product->id,
            'description' => 'nullable|string|max:255',
            'category'    => 'nullable|string|max:50',
            'weight_kg'   => 'nullable|numeric|min:0',
        ]);

        // UPDATE: Observer akan otomatis mencatat log "Update Product" beserta perubahannya
        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Data produk diperbarui.');
    }

    public function destroy(Product $product)
    {
        try {
            // DELETE: Observer akan otomatis mencatat log "Delete Product"
            $product->delete();
            return redirect()->route('products.index')
                ->with('success', 'Produk dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus. Produk ini masih memiliki stok atau riwayat transaksi.');
        }
    }

    /**
     * API Endpoint untuk pencarian produk (Server Side Autocomplete)
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $products = Product::query()
            ->select('id', 'part_number', 'description', 'category');

        if ($query) {
            // Cari berdasarkan Part Number ATAU Deskripsi
            $products->where(function($q) use ($query) {
                $q->where('part_number', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            });
        }
        
        // Batasi hasil agar ringan, tapi "tetap tampilkan beberapa" jika query kosong
        return response()->json($products->limit(15)->get());
    }
}