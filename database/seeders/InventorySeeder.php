<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\StorageBin;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{

    public function run(): void
    {
        $products = Product::all();
        $bins = StorageBin::all(); // Total 250 bin

        // Isi 150 bin secara acak (60% Kapasitas)
        $binsToFill = $bins->random(150);

        foreach ($binsToFill as $bin) {
            $product = $products->random();
            
            // Hindari duplikasi (1 produk di 1 bin)
            $exists = Inventory::where('product_id', $product->id)
                                ->where('bind_id', $bin->id)
                                ->exists();

            if (!$exists) {
                Inventory::create([
                    'product_id' => $product->id,
                    'bind_id'    => $bin->id,
                    'quantity'   => rand(5, 500), // Stok acak
                ]);

                $bin->update(['is_empty' => false]);
            }
        }
    }
}