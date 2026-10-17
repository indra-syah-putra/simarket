<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Hanya membuat akun admin agar bisa login.
     * Data barang & transaksi dikosongkan (diisi manual lewat aplikasi).
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin Toko',
            'email' => 'admin@simarket.test',
            'password' => bcrypt('password'),
        ]);
    }
}
