<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $jenis = $request->input('jenis');
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $transaksis = Transaksi::with('barang')
            ->when($jenis, fn ($q) => $q->where('jenis', $jenis))
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->latest('tanggal')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('transaksi.index', compact('transaksis', 'jenis', 'month', 'year'));
    }

    public function create(Request $request)
    {
        $barangs = Barang::orderBy('nama')->get();
        $jenis = $request->input('jenis', 'penjualan');

        if (! in_array($jenis, ['restock', 'penjualan'], true)) {
            $jenis = 'penjualan';
        }

        return view('transaksi.create', compact('barangs', 'jenis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jenis' => 'required|in:restock,penjualan',
            'barang_id' => 'required|exists:barangs,id',
            'qty' => 'required|numeric|gt:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($validated) {
            /** @var Barang $barang */
            $barang = Barang::lockForUpdate()->findOrFail($validated['barang_id']);
            $qty = (float) $validated['qty'];

            if ($validated['jenis'] === 'restock') {
                // Restock / pembelian: qty dalam satuan utama (stok).
                $satuan = $barang->satuan;
                $qtyStok = $qty;
                $hargaSatuan = (float) $barang->harga_beli;

                $barang->stok = (float) $barang->stok + $qtyStok;
            } else {
                // Penjualan: qty dalam satuan jual (eceran), dikonversi ke satuan utama.
                $satuan = $barang->satuanJual();
                $qtyStok = $qty * $barang->isiJual();
                $hargaSatuan = (float) $barang->harga_jual;

                if ((float) $barang->stok < $qtyStok) {
                    return back()
                        ->withInput()
                        ->with('error', "Stok {$barang->nama} tidak mencukupi! Tersedia " . angka($barang->stok) . " {$barang->satuan}.");
                }

                $barang->stok = (float) $barang->stok - $qtyStok;
            }

            $barang->save();

            Transaksi::create([
                'tanggal' => $validated['tanggal'],
                'jenis' => $validated['jenis'],
                'barang_id' => $barang->id,
                'satuan' => $satuan,
                'qty' => $qty,
                'qty_stok' => $qtyStok,
                'harga_satuan' => $hargaSatuan,
                'total' => $hargaSatuan * $qty,
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            $pesan = $validated['jenis'] === 'restock'
                ? 'Transaksi restock berhasil disimpan.'
                : 'Transaksi penjualan berhasil disimpan.';

            return redirect()->route('transaksi.index')->with('success', $pesan);
        });
    }

    public function destroy(Transaksi $transaksi)
    {
        return DB::transaction(function () use ($transaksi) {
            $barang = Barang::lockForUpdate()->findOrFail($transaksi->barang_id);
            $qtyStok = (float) $transaksi->qty_stok;

            // Kembalikan perubahan stok saat transaksi dihapus.
            if ($transaksi->jenis === 'restock') {
                $barang->stok = max(0, (float) $barang->stok - $qtyStok);
            } else {
                $barang->stok = (float) $barang->stok + $qtyStok;
            }
            $barang->save();

            $transaksi->delete();

            return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dihapus dan stok dikembalikan.');
        });
    }
}
