<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'jenis',
        'kode',
        'barang_id',
        'satuan',
        'qty',
        'qty_stok',
        'harga_satuan',
        'total',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'qty' => 'decimal:4',
        'qty_stok' => 'decimal:4',
        'harga_satuan' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function labelJenis(): string
    {
        return match ($this->jenis) {
            'restock' => 'Restock',
            'penjualan' => 'Penjualan',
            'penyesuaian' => 'Penyesuaian Stok',
            default => ucfirst($this->jenis),
        };
    }

    public function badgeJenis(): string
    {
        return match ($this->jenis) {
            'penjualan' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
            'restock' => 'bg-blue-50 text-blue-600 border-blue-100',
            'penyesuaian' => 'bg-amber-50 text-amber-600 border-amber-100',
            default => 'bg-slate-100 text-slate-600 border-slate-200',
        };
    }

    public function isPemasukan(): bool
    {
        return $this->jenis === 'penjualan';
    }

    public function isPengeluaran(): bool
    {
        return $this->jenis === 'restock';
    }
}
