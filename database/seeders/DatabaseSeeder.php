<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Membuat akun admin dan data dummy barang untuk uji coba.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin Toko',
            'email' => 'admin@simarket.test',
            'password' => bcrypt('password'),
        ]);

        $this->call(BarangSeeder::class);
    }
}
