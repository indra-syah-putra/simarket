<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalItems = Barang::count();
        $barangs = Barang::latest()->get();

        $lowStockCount = $barangs->filter(fn ($item) => $item->isStokTipis())->count();

        $totalPenjualan = (float) Transaksi::where('jenis', 'penjualan')->sum('total');
        $totalPembelian = (float) Transaksi::where('jenis', 'restock')->sum('total');
        $uangBersih = $totalPenjualan - $totalPembelian;

        $bulanIni = now()->startOfMonth();
        $penjualanBulanIni = (float) Transaksi::where('jenis', 'penjualan')
            ->where('tanggal', '>=', $bulanIni)
            ->sum('total');
        $pembelianBulanIni = (float) Transaksi::where('jenis', 'restock')
            ->where('tanggal', '>=', $bulanIni)
            ->sum('total');
        $uangBersihBulanIni = $penjualanBulanIni - $pembelianBulanIni;

        $transaksiTerbaru = Transaksi::with('barang')->latest('tanggal')->latest('id')->take(5)->get();

        return view('dashboard', compact(
            'barangs',
            'totalItems',
            'lowStockCount',
            'totalPenjualan',
            'totalPembelian',
            'uangBersih',
            'penjualanBulanIni',
            'pembelianBulanIni',
            'uangBersihBulanIni',
            'transaksiTerbaru',
        ));
    }
}
