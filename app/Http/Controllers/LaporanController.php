<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $validMonths = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
        if (! in_array($month, $validMonths, true)) {
            $month = date('m');
        }

        $startOfMonth = Carbon::createFromDate((int) $year, (int) $month, 1)->startOfMonth();

        $barangs = Barang::orderBy('nama')->get();
        $reports = [];

        foreach ($barangs as $barang) {
            // Pergerakan stok (dalam satuan utama) pada bulan terpilih.
            $restock = (float) Transaksi::where('barang_id', $barang->id)
                ->where('jenis', 'restock')
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->sum('qty_stok');

            $penjualan = (float) Transaksi::where('barang_id', $barang->id)
                ->where('jenis', 'penjualan')
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->sum('qty_stok');

            $penyesuaian = (float) Transaksi::where('barang_id', $barang->id)
                ->where('jenis', 'penyesuaian')
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->sum('qty_stok');

            // Pergerakan sejak awal bulan ini (termasuk bulan berjalan) untuk menghitung stok awal.
            $restockSisa = (float) Transaksi::where('barang_id', $barang->id)
                ->where('jenis', 'restock')
                ->where('tanggal', '>=', $startOfMonth)
                ->sum('qty_stok');

            $penjualanSisa = (float) Transaksi::where('barang_id', $barang->id)
                ->where('jenis', 'penjualan')
                ->where('tanggal', '>=', $startOfMonth)
                ->sum('qty_stok');

            $penyesuaianSisa = (float) Transaksi::where('barang_id', $barang->id)
                ->where('jenis', 'penyesuaian')
                ->where('tanggal', '>=', $startOfMonth)
                ->sum('qty_stok');

            $stokAwal = (float) $barang->stok - $restockSisa - $penyesuaianSisa + $penjualanSisa;

            $stokAkhir = $stokAwal + $restock - $penjualan + $penyesuaian;

            $reports[] = [
                'barang' => $barang,
                'stok_awal' => $stokAwal,
                'restock' => $restock,
                'penjualan' => $penjualan,
                'penyesuaian' => $penyesuaian,
                'stok_akhir' => $stokAkhir,
                'nilai_stok' => $stokAkhir * (float) $barang->harga_beli,
            ];
        }

        $pemasukan = (float) Transaksi::where('jenis', 'penjualan')
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->sum('total');

        $pengeluaran = (float) Transaksi::where('jenis', 'restock')
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->sum('total');

        $uangBersih = $pemasukan - $pengeluaran;
        $totalNilaiStok = collect($reports)->sum('nilai_stok');

        $monthIndo = match ($month) {
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
            default => 'Januari',
        };

        return view('laporan.index', compact(
            'reports',
            'month',
            'year',
            'monthIndo',
            'pemasukan',
            'pengeluaran',
            'uangBersih',
            'totalNilaiStok',
        ));
    }
}
