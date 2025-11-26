<?php

namespace Database\Seeders;

use App\Models\StorageBin;
use Illuminate\Database\Seeder;

class StorageBinSeeder extends Seeder
{
    public function run(): void
    {
        $racks = ['A', 'B', 'C', 'D', 'E']; // 5 Area Rak

        foreach ($racks as $rackStr) {
            // Setiap rak punya 5 Level (Tingkat)
            for ($level = 1; $level <= 5; $level++) {
                // Setiap level punya 10 Slot (Kotak)
                for ($slot = 1; $slot <= 10; $slot++) {
                    
                    $code = sprintf("%s-%02d-%02d", $rackStr, $level, $slot);
                    
                    // Random tipe bin
                    $type = 'Standard';
                    if ($rackStr == 'E') $type = 'Bulk'; // Rak E khusus barang besar
                    if ($rackStr == 'D' && $level == 1) $type = 'Cold Storage'; // Rak D bawah dingin

                    StorageBin::create([
                        'bin_code' => $code,
                        'rack' => $rackStr,
                        'level' => $level,
                        'slot' => $slot,
                        'bin_type' => $type,
                        'is_empty' => true
                    ]);
                }
            }
        }
    }
}