<?php

namespace Database\Seeders;

use App\Models\InboundInvoice;
use App\Models\InboundItem;
use App\Models\Product;
use App\Models\StorageBin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InboundSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'Admin')->first();
        $products = Product::all();
        $faker = \Faker\Factory::create();

        // Buat 50 Transaksi Inbound
        for ($i = 0; $i < 50; $i++) {
            // Status random weighted
            $status = $faker->randomElement(['Stored', 'Stored', 'Stored', 'Received', 'Pending']);
            $date = $faker->dateTimeBetween('-3 months', 'now');

            $invoice = InboundInvoice::create([
                'invoice_number' => 'INV-' . date('Y') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'supplier' => $faker->company,
                'received_at' => $date,
                'status' => $status,
                'user_id' => $admin->id,
            ]);

            // Tiap invoice punya 1-5 item barang
            $itemCount = rand(1, 5);
            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products->random();
                $qty = rand(10, 200);
                
                // Jika status Stored, assign ke bin random (simulasi)
                $binId = null;
                if ($status == 'Stored') {
                    $binId = StorageBin::inRandomOrder()->first()->id;
                }

                InboundItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $product->id,
                    'bind_id'    => $binId,
                    'quantity_expected' => $qty,
                    'quantity_received' => $status == 'Pending' ? 0 : $qty,
                    'status' => $status,
                ]);
            }
        }
    }
}