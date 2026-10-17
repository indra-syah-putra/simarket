<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Barang extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kode',
        'nama',
        'kategori',
        'satuan',
        'satuan_jual',
        'isi_jual',
        'harga_beli',
        'harga_jual',
        'stok',
        'min_stok',
    ];

    protected $casts = [
        'isi_jual' => 'decimal:4',
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'stok' => 'decimal:4',
        'min_stok' => 'decimal:4',
    ];

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'barang_id');
    }

    public function isStokTipis(): bool
    {
        return (float) $this->stok <= (float) $this->min_stok;
    }

    /**
     * Label satuan eceran (jatuh ke satuan utama bila tidak diisi).
     */
    public function satuanJual(): string
    {
        return $this->satuan_jual ?: $this->satuan;
    }

    /**
     * Isi satuan eceran dalam satuan utama. Contoh: 1 rubak = 0.25 kg.
     */
    public function isiJual(): float
    {
        $isi = (float) $this->isi_jual;

        return $isi > 0 ? $isi : 1;
    }

    /**
     * Nilai stok berdasarkan harga beli (stok dalam satuan utama).
     */
    public function nilaiStok(): float
    {
        return (float) $this->stok * (float) $this->harga_beli;
    }
}
