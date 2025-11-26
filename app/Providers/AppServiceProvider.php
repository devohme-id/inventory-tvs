<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

// Import Models & Observers
use App\Models\Product;
use App\Observers\ProductObserver;
use App\Models\Inventory;
use App\Observers\InventoryObserver;
use App\Models\SalesOrder;
use App\Observers\SalesOrderObserver;
use App\Models\InboundInvoice;
use App\Observers\InboundInvoiceObserver;
use App\Models\SoItem;
use App\Observers\SoItemObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // PERBAIKAN PAGINASI
        // Ubah dari Bootstrap ke Tailwind agar sesuai dengan tema aplikasi
        Paginator::useTailwind();

        // Daftarkan Observer
        Product::observe(ProductObserver::class);
        Inventory::observe(InventoryObserver::class);
        SalesOrder::observe(SalesOrderObserver::class);
        InboundInvoice::observe(InboundInvoiceObserver::class);
        SoItem::observe(SoItemObserver::class);
    }
}