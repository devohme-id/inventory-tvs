<?php

namespace Database\Seeders;

use App\Models\SalesOrder;
use App\Models\SoItem;
use App\Models\PickingTask;
use App\Models\Shipment;
use App\Models\Product;
use App\Models\StorageBin;
use App\Models\Inventory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OutboundSeeder extends Seeder
{
    public function run(): void
    {
        $operator = User::where('role', 'Operator')->first();
        $products = Product::all();
        $faker = \Faker\Factory::create();

        // Buat 100 Sales Order (Campur status)
        for ($i = 0; $i < 100; $i++) {
            $status = $faker->randomElement(['Shipped', 'Shipped', 'Packed', 'Picked', 'Pending']);
            $date = $faker->dateTimeBetween('-2 months', 'now');

            $so = SalesOrder::create([
                'so_number' => 'SO-' . date('Y') . '-' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'customer' => $faker->company . ' Dealer',
                'order_date' => $date,
                'status' => $status,
            ]);

            // Tiap SO punya 1-3 item
            $itemCount = rand(1, 3);
            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products->random();
                $qtyOrdered = rand(1, 10);
                
                // Tentukan qty picked/packed berdasarkan status SO
                $qtyPicked = 0;
                $qtyPacked = 0;

                if (in_array($status, ['Picked', 'Packed', 'Shipped'])) {
                    $qtyPicked = $qtyOrdered;
                }
                if (in_array($status, ['Packed', 'Shipped'])) {
                    $qtyPacked = $qtyOrdered;
                }

                $soItem = SoItem::create([
                    'so_id' => $so->id,
                    'product_id' => $product->id,
                    'quantity_ordered' => $qtyOrdered,
                    'quantity_picked' => $qtyPicked,
                    'quantity_packed' => $qtyPacked,
                ]);

                // LOGIKA PICKING TASK
                // Jika status bukan Pending, berarti sudah ada task picking yang selesai
                if ($qtyPicked > 0) {
                    // Cari bin yang punya stok produk ini (agar data valid)
                    $inventory = Inventory::where('product_id', $product->id)->where('quantity', '>=', $qtyOrdered)->first();
                    
                    // Jika tidak ada stok pas (karena random), ambil sembarang bin yang tidak kosong
                    $binId = $inventory ? $inventory->bind_id : StorageBin::where('is_empty', false)->inRandomOrder()->first()->id;
                    
                    PickingTask::create([
                        'so_item_id' => $soItem->id,
                        'bind_id'    => $binId, // Gunakan bind_id
                        'operator_id' => $operator->id,
                        'quantity_to_pick' => $qtyOrdered,
                        'status' => 'Picked',
                        'picked_at' => Carbon::parse($date)->addHours(rand(1, 5)),
                    ]);

                    // --- UPDATE KHUSUS (Agar Data Dummy Konsisten) ---
                    // Jika status SO sudah 'Shipped', kita kurangi stok Inventory secara manual di seeder ini
                    // supaya data di dashboard terlihat masuk akal.
                    if ($status == 'Shipped' && $inventory) {
                        $inventory->decrement('quantity', $qtyOrdered);
                        if ($inventory->quantity == 0) {
                            StorageBin::where('id', $binId)->update(['is_empty' => true]);
                        }
                    }

                } 
                // Jika Pending, buat Picking Task status Pending
                elseif ($status == 'Pending') {
                    // Cari potensi stok untuk dialokasikan task
                    $bin = StorageBin::where('is_empty', false)->inRandomOrder()->first();
                    
                    PickingTask::create([
                        'so_item_id' => $soItem->id,
                        'bind_id'    => $bin->id,
                        'operator_id' => null,
                        'quantity_to_pick' => $qtyOrdered,
                        'status' => 'Pending',
                    ]);
                }
            }

            // Buat Data Shipment jika Shipped
            if ($status == 'Shipped') {
                Shipment::create([
                    'so_id' => $so->id,
                    'box_id' => 'BOX-' . $so->so_number,
                    'total_weight_kg' => $faker->randomFloat(1, 1, 20),
                    'operator_id' => $operator->id,
                    'shipped_at' => Carbon::parse($date)->addDays(1),
                ]);
            }
        }
    }
}