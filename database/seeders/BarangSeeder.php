<?php

namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    /**
     * Data dummy barang untuk uji coba.
     */
    public function run(): void
    {
        $barangs = [
            [
                'kode' => 'TEL',
                'nama' => 'Telur',
                'kategori' => 'Sembako',
                'satuan' => 'Kg',
                'satuan_jual' => null,
                'isi_jual' => 1,
                'harga_beli' => 28000,
                'harga_jual' => 32000,
                'stok' => 20,
                'min_stok' => 5,
            ],
            [
                'kode' => 'WRT',
                'nama' => 'Wortel',
                'kategori' => 'Sayur',
                'satuan' => 'Kg',
                'satuan_jual' => null,
                'isi_jual' => 1,
                'harga_beli' => 10000,
                'harga_jual' => 13000,
                'stok' => 20,
                'min_stok' => 5,
            ],
            [
                'kode' => 'TMT',
                'nama' => 'Tomat',
                'kategori' => 'Sayur',
                'satuan' => 'Kg',
                'satuan_jual' => null,
                'isi_jual' => 1,
                'harga_beli' => 8000,
                'harga_jual' => 12000,
                'stok' => 20,
                'min_stok' => 5,
            ],
            [
                'kode' => 'CB',
                'nama' => 'Cabe',
                'kategori' => 'Sayur',
                'satuan' => 'Kg',
                'satuan_jual' => 'Ons',
                'isi_jual' => 0.1,
                'harga_beli' => 40000,
                'harga_jual' => 6000,
                'stok' => 20,
                'min_stok' => 5,
            ],
            [
                // 1 dus = 12 pouch, 1 pouch = 1 liter -> isi per pouch = 1/12 dus.
                'kode' => 'MYK',
                'nama' => 'Minyak Goreng',
                'kategori' => 'Sembako',
                'satuan' => 'Dus',
                'satuan_jual' => 'Pouch',
                'isi_jual' => 0.0833,
                'harga_beli' => 180000,
                'harga_jual' => 18000,
                'stok' => 2,
                'min_stok' => 1,
            ],
        ];

        foreach ($barangs as $barang) {
            Barang::updateOrCreate(['kode' => $barang['kode']], $barang);
        }
    }
}
