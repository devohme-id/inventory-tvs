<?php

namespace App\Observers;

use App\Models\SoItem;
use App\Models\PickingTask;
use App\Models\Inventory;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class SoItemObserver
{
    /**
     * Handle the SoItem "created" event.
     */
    public function created(SoItem $soItem): void
    {
        // 1. Cari Stok Tersedia (Algoritma: Ambil dari rak dengan stok terbanyak)
        $inventory = Inventory::where('product_id', $soItem->product_id)
            ->where('quantity', '>=', $soItem->quantity_ordered) // Pastikan stok cukup
            ->orderByDesc('quantity')
            ->first();

        // Jika stok tidak cukup di satu rak, ambil yang ada (Logic sederhana)
        if (!$inventory) {
            $inventory = Inventory::where('product_id', $soItem->product_id)
                ->orderByDesc('quantity')
                ->first();
        }

        // 2. Jika stok ditemukan, buat Tugas Picking otomatis
        if ($inventory) {
            PickingTask::create([
                'so_item_id'       => $soItem->id,
                'bind_id'          => $inventory->bind_id, // Lokasi rak otomatis
                'quantity_to_pick' => $soItem->quantity_ordered,
                'operator_id'      => null, // Belum ada operator spesifik (Open Task)
                'status'           => 'Pending'
            ]);

            AuditLog::record(
                'Auto Picking Task', 
                "Tugas picking otomatis dibuat untuk Item #{$soItem->id} di Rak {$inventory->bin->bin_code}",
                Auth::id()
            );
        } else {
            // Optional: Log warning jika stok kosong
            AuditLog::record(
                'Stok Kosong', 
                "Gagal membuat picking task otomatis untuk Item #{$soItem->id}. Stok tidak ditemukan.",
                Auth::id()
            );
        }
    }

    /**
     * Handle the SoItem "deleted" event.
     */
    public function deleted(SoItem $soItem): void
    {
        // Hapus task terkait jika item dihapus dari SO
        PickingTask::where('so_item_id', $soItem->id)->delete();
    }
}