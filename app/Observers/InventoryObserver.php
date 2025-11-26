<?php

namespace App\Observers;

use App\Models\Inventory;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class InventoryObserver
{
    public function created(Inventory $inventory): void
    {
        // Load relasi agar bisa ambil nama produk & bin
        $inventory->load(['product', 'bin']);

        AuditLog::record(
            'Stok Baru',
            "Inisialisasi stok awal {$inventory->product->part_number} di Rak {$inventory->bin->bin_code}: {$inventory->quantity} Pcs",
            Auth::id()
        );
    }

    public function updated(Inventory $inventory): void
    {
        if ($inventory->isDirty('quantity')) {
            $inventory->load(['product', 'bin']);
            
            $oldQty = $inventory->getOriginal('quantity');
            $newQty = $inventory->quantity;
            $diff = $newQty - $oldQty;
            
            $status = $diff > 0 ? 'Penambahan' : 'Pengurangan';
            
            AuditLog::record(
                'Perubahan Stok',
                "Update stok {$inventory->product->part_number} di {$inventory->bin->bin_code}. {$status} " . abs($diff) . " Pcs. (Awal: $oldQty, Akhir: $newQty)",
                Auth::id()
            );
        }
    }

    public function deleted(Inventory $inventory): void
    {
        // Hati-hati saat delete, relasi mungkin sudah hilang/null, jadi kita pakai data object inventory saja
        AuditLog::record(
            'Hapus Stok',
            "Menghapus data inventory ID #{$inventory->id} (Qty Terakhir: {$inventory->quantity})",
            Auth::id()
        );
    }
}