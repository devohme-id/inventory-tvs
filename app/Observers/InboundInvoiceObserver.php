<?php

namespace App\Observers;

use App\Models\InboundInvoice;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class InboundInvoiceObserver
{
    /**
     * Handle the InboundInvoice "created" event.
     */
    public function created(InboundInvoice $invoice): void
    {
        AuditLog::record(
            'Create Invoice',
            "Membuat Invoice Masuk baru: {$invoice->invoice_number} dari {$invoice->supplier}",
            Auth::id()
        );
    }

    /**
     * Handle the InboundInvoice "updated" event.
     */
    public function updated(InboundInvoice $invoice): void
    {
        // Lacak perubahan Status Penerimaan
        if ($invoice->isDirty('status')) {
            $oldStatus = $invoice->getOriginal('status');
            $newStatus = $invoice->status;
            
            AuditLog::record(
                'Status Invoice Berubah',
                "Status Invoice {$invoice->invoice_number} berubah dari '{$oldStatus}' menjadi '{$newStatus}'",
                Auth::id()
            );
        } 
        else {
            // Lacak perubahan data lain (misal edit supplier)
            $changes = $invoice->getChanges();
            unset($changes['updated_at']);

            if (!empty($changes)) {
                $changeDetails = [];
                foreach ($changes as $key => $newValue) {
                    $originalValue = $invoice->getOriginal($key);
                    $changeDetails[] = "$key: '$originalValue' -> '$newValue'";
                }
                $detailString = implode(', ', $changeDetails);

                AuditLog::record(
                    'Update Invoice',
                    "Edit data Invoice {$invoice->invoice_number}. Detail: [{$detailString}]",
                    Auth::id()
                );
            }
        }
    }

    /**
     * Handle the InboundInvoice "deleted" event.
     */
    public function deleted(InboundInvoice $invoice): void
    {
        AuditLog::record(
            'Delete Invoice',
            "Menghapus Invoice Masuk: {$invoice->invoice_number}",
            Auth::id()
        );
    }
}