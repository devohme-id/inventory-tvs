<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. User Utama (Tetap Ada)
        $users = [
            ['name' => 'Admin Gudang', 'username' => 'admin', 'email' => 'admin@tvs.com', 'role' => 'Admin'],
            ['name' => 'Budi Santoso', 'username' => 'budi', 'email' => 'budi@tvs.com', 'role' => 'Kepala Gudang'],
            ['name' => 'Ani Wijaya', 'username' => 'ani', 'email' => 'ani@tvs.com', 'role' => 'Operator'],
            ['name' => 'Joko Anwar', 'username' => 'joko', 'email' => 'joko@tvs.com', 'role' => 'Operator'],
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'username' => $user['username'],
                'email' => $user['email'],
                'password' => Hash::make('password'),
                'role' => $user['role'],
            ]);
        }

        // 2. Generate 15 Operator Tambahan
        User::factory()->count(15)->create([
            'role' => 'Operator',
            'password' => Hash::make('password'),
        ]);
    }
}