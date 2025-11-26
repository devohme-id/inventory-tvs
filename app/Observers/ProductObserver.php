<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductObserver
{
    public function created(Product $product)
    {
        AuditLog::record(
            'Create Product',
            "Produk baru: {$product->part_number}",
            Auth::id()
        );
    }

    public function updated(Product $product)
    {
        // Cek apa yang berubah (opsional)
        $changes = $product->getChanges();
        unset($changes['updated_at']); // Hapus timestamp dari log

        AuditLog::record(
            'Update Product',
            "Update {$product->part_number}. Perubahan: ".json_encode($changes),
            Auth::id()
        );
    }

    public function deleted(Product $product)
    {
        AuditLog::record(
            'Delete Product',
            "Hapus produk: {$product->part_number}",
            Auth::id()
        );
    }
}
