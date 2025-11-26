<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{

    public function run(): void
    {
        // Data Real (10 Item)
        $realProducts = [
            ['part_number' => 'N9040110', 'description' => 'Brake Pad Set, Front', 'category' => 'Fast Moving', 'weight_kg' => 0.5],
            ['part_number' => 'R1010150', 'description' => 'Spark Plug (4pcs)', 'category' => 'Fast Moving', 'weight_kg' => 0.2],
            ['part_number' => 'M5020110', 'description' => 'Air Filter Element', 'category' => 'Fast Moving', 'weight_kg' => 0.3],
            ['part_number' => 'N4040120', 'description' => 'Brake Shoe Set, Rear', 'category' => 'Fast Moving', 'weight_kg' => 0.6],
            ['part_number' => 'R4070190', 'description' => 'Chain Lube', 'category' => 'Consumable', 'weight_kg' => 0.4],
            ['part_number' => 'K0010010', 'description' => 'Gasket, Cylinder Head', 'category' => 'Slow Moving', 'weight_kg' => 0.1],
            ['part_number' => 'M1010110', 'description' => 'Oil Filter', 'category' => 'Fast Moving', 'weight_kg' => 0.25],
            ['part_number' => 'R3060110', 'description' => 'Clutch Cable', 'category' => 'Slow Moving', 'weight_kg' => 0.3],
            ['part_number' => 'N9010110', 'description' => 'Disc Plate, Front', 'category' => 'Slow Moving', 'weight_kg' => 1.2],
            ['part_number' => 'M4010130', 'description' => 'Engine Oil TVS 1L', 'category' => 'Consumable', 'weight_kg' => 1.0],
        ];

        foreach ($realProducts as $product) {
            Product::create($product);
        }

        // Generate 90 Produk Dummy Tambahan
        $faker = \Faker\Factory::create();
        $categories = ['Fast Moving', 'Slow Moving', 'Consumable', 'Sparepart', 'Accessories'];

        for ($i = 0; $i < 90; $i++) {
            Product::create([
                'part_number' => strtoupper($faker->bothify('TVS-#####-??')),
                'description' => $faker->words(3, true) . ' (Part)',
                'category' => $faker->randomElement($categories),
                'weight_kg' => $faker->randomFloat(2, 0.1, 20.0),
            ]);
        }
    }
}