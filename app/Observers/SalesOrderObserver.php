<?php

namespace App\Observers;

use App\Models\SalesOrder;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class SalesOrderObserver
{
    /**
     * Handle the SalesOrder "created" event.
     */
    public function created(SalesOrder $salesorders): void
    {
        AuditLog::record(
            'Create Sales Order',
            "Membuat Sales Order baru: {$salesorders->so_number} untuk {$salesorders->customer}",
            Auth::id()
        );
    }

    /**
     * Handle the SalesOrder "updated" event.
     */
    public function updated(SalesOrder $salesorders): void
    {
        // Cek khusus jika Status berubah (ini yang paling penting dilacak)
        if ($salesorders->isDirty('status')) {
            $oldStatus = $salesorders->getOriginal('status');
            $newStatus = $salesorders->status;
            
            AuditLog::record(
                'Status SO Berubah',
                "Status SO {$salesorders->so_number} berubah dari '{$oldStatus}' menjadi '{$newStatus}'",
                Auth::id()
            );
        } 
        // Cek perubahan data lainnya (misal edit customer atau tanggal)
        else {
            $changes = $salesorders->getChanges();
            unset($changes['updated_at']); // Abaikan timestamp

            if (!empty($changes)) {
                $changeDetails = [];
                foreach ($changes as $key => $newValue) {
                    $originalValue = $salesorders->getOriginal($key);
                    $changeDetails[] = "$key: '$originalValue' -> '$newValue'";
                }
                $detailString = implode(', ', $changeDetails);

                AuditLog::record(
                    'Update Sales Order',
                    "Edit data SO {$salesorders->so_number}. Detail: [{$detailString}]",
                    Auth::id()
                );
            }
        }
    }

    /**
     * Handle the SalesOrder "deleted" event.
     */
    public function deleted(SalesOrder $salesorders): void
    {
        AuditLog::record(
            'Delete Sales Order',
            "Menghapus Sales Order: {$salesorders->so_number} (Customer: {$salesorders->customer})",
            Auth::id()
        );
    }
}