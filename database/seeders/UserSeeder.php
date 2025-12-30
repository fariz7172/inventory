<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Gudang
        User::create([
            'name' => 'Kepala Gudang',
            'email' => 'gudang@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin_gudang',
        ]);

        // Staff
        User::create([
            'name' => 'Staff Penjualan',
            'email' => 'staff@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);
    }
}
