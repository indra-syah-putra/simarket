<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SI MARKET (Toko Sembako)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('barang', BarangController::class)->except('show');

    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');

    // Penjualan (barang keluar) — satu nota bisa banyak item
    Route::get('/transaksi/create', [TransaksiController::class, 'createPenjualan'])->name('transaksi.create');
    Route::post('/transaksi', [TransaksiController::class, 'storePenjualan'])->name('transaksi.store')->middleware('throttle:30,1');

    // Restock (pembelian) — form khusus
    Route::get('/restock/create', [TransaksiController::class, 'createRestock'])->name('restock.create');
    Route::post('/restock', [TransaksiController::class, 'storeRestock'])->name('restock.store')->middleware('throttle:30,1');

    // Penyesuaian stok (stok fisik) — form khusus
    Route::get('/stok/create', [TransaksiController::class, 'createPenyesuaian'])->name('stok.create');
    Route::post('/stok', [TransaksiController::class, 'storePenyesuaian'])->name('stok.store')->middleware('throttle:30,1');

    // Cetak struk nota
    Route::get('/transaksi/nota/{kode}', [TransaksiController::class, 'struk'])->name('transaksi.nota');

    // Hapus seluruh item dalam satu nota
    Route::delete('/transaksi/nota/{kode}', [TransaksiController::class, 'destroyNota'])->name('transaksi.destroy');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});
