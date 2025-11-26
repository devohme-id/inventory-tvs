<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ReportController;
use App\Http\Controllers\SoItemController;
use App\Http\Controllers\PackingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\StorageBinController;
use App\Http\Controllers\InboundItemController;
use App\Http\Controllers\PickingTaskController;
use App\Http\Controllers\InboundInvoiceController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- MASTER DATA ---
    // Route pencarian produk (AJAX) - Letakkan SEBELUM resource route
    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::resource('products', ProductController::class);
    
    Route::get('/storage-bins/area/{rack}', [StorageBinController::class, 'showArea'])->name('storage-bins.area');
    Route::resource('storage-bins', StorageBinController::class);

    // --- OPERASIONAL ---
    Route::resource('inbound-invoices', InboundInvoiceController::class);
    Route::resource('inbound-items', InboundItemController::class);
    Route::resource('inventory', InventoryController::class);
    Route::resource('incoming', InboundInvoiceController::class); 

    Route::resource('sales-orders', SalesOrderController::class);
    Route::resource('so-items', SoItemController::class);

    // Picking
    Route::get('/picking', [PickingTaskController::class, 'index'])->name('picking.index'); 
    Route::get('/picking/{so_id}/process', [PickingTaskController::class, 'processSO'])->name('picking.process');
    Route::post('/picking/{so_id}/scan', [PickingTaskController::class, 'scanItem'])->name('picking.scan');
    Route::resource('picking-tasks', PickingTaskController::class)->except(['index']);

    // Packing
    Route::get('/packing', [PackingController::class, 'index'])->name('packing.index');
    Route::get('/packing/{so_id}/process', [PackingController::class, 'process'])->name('packing.process');
    Route::post('/packing/{so_id}/scan', [PackingController::class, 'scan'])->name('packing.scan');
    Route::post('/packing/{so_id}/pack-all', [PackingController::class, 'packAllItems'])->name('packing.pack_all');
    Route::post('/packing/{so_id}/finish', [PackingController::class, 'finish'])->name('packing.finish');

    // Shipment
    Route::get('/shipments', [ShipmentController::class, 'index'])->name('shipments.index');
    Route::post('/shipments', [ShipmentController::class, 'store'])->name('shipments.store');
    Route::get('/shipments/{shipment}', [ShipmentController::class, 'show'])->name('shipments.show');
    Route::put('/shipments/{shipment}', [ShipmentController::class, 'update'])->name('shipments.update');
    Route::delete('/shipments/{shipment}', [ShipmentController::class, 'destroy'])->name('shipments.destroy');
    Route::get('/shipments/{shipment}/edit', [ShipmentController::class, 'edit'])->name('shipments.edit');

    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    // REPORTING
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');

    // SHIPMENT PRINT
    Route::get('/shipments/{shipment}/delivery-order', [ShipmentController::class, 'printDeliveryOrder'])->name('shipments.print_do');
   
});

require __DIR__.'/auth.php';