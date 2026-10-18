<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $jenis = $request->input('jenis');
        $search = $request->input('q');
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $rows = Transaksi::with('barang')
            ->when($jenis, fn ($q) => $q->where('jenis', $jenis))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('kode', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%")
                        ->orWhereHas('barang', fn ($b) => $b->where('nama', 'like', "%{$search}%")->orWhere('kode', 'like', "%{$search}%"));
                });
            })
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();

        $notas = $rows
            ->groupBy(fn ($trx) => $trx->kode ?: 'ID-' . $trx->id)
            ->map(function ($items) {
                return (object) [
                    'kode' => $items->first()->kode,
                    'tanggal' => $items->first()->tanggal,
                    'jenis' => $items->first()->jenis,
                    'keterangan' => $items->first()->keterangan,
                    'items' => $items,
                    'jumlah_item' => $items->count(),
                    'total' => $items->sum(fn ($t) => (float) $t->total),
                    'delta_stok' => $items->sum(fn ($t) => (float) $t->qty_stok),
                ];
            })
            ->values();

        $perPage = 15;
        $page = LengthAwarePaginator::resolveCurrentPage();

        $notas = new LengthAwarePaginator(
            $notas->forPage($page, $perPage)->values(),
            $notas->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('transaksi.index', compact('notas', 'jenis', 'search', 'month', 'year'));
    }

    public function createPenjualan()
    {
        $barangs = Barang::orderBy('nama')->get()->map(fn (Barang $b) => [
            'id' => $b->id,
            'kode' => $b->kode,
            'nama' => $b->nama,
            'satuan' => $b->satuan,
            'satuan_jual' => $b->satuanJual(),
            'isi_jual' => $b->isiJual(),
            'harga_jual' => (float) $b->harga_jual,
            'stok' => (float) $b->stok,
        ])->values();

        return view('transaksi.create', compact('barangs'));
    }

    public function storePenjualan(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.qty' => 'required|numeric|gt:0',
        ], [
            'items.required' => 'Tambahkan minimal satu barang ke dalam nota.',
            'items.*.barang_id.exists' => 'Barang tidak ditemukan.',
            'items.*.qty.gt' => 'Jumlah barang harus lebih dari 0.',
        ]);

        return DB::transaction(function () use ($validated) {
            // Gabungkan qty per barang (jaga-jaga bila barang yang sama muncul lebih dari sekali).
            $qtyPerBarang = [];
            foreach ($validated['items'] as $item) {
                $qtyPerBarang[$item['barang_id']] = ($qtyPerBarang[$item['barang_id']] ?? 0) + (float) $item['qty'];
            }

            $barangs = Barang::whereIn('id', array_keys($qtyPerBarang))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // Validasi stok terlebih dahulu.
            foreach ($qtyPerBarang as $barangId => $qty) {
                $barang = $barangs->get($barangId);
                if (! $barang) {
                    continue;
                }
                $qtyStok = $qty * $barang->isiJual();
                if ((float) $barang->stok < $qtyStok) {
                    return back()
                        ->withInput()
                        ->with('error', "Stok {$barang->nama} tidak mencukupi! Tersedia " . angka($barang->stok) . " {$barang->satuan}.");
                }
            }

            $kode = $this->generateKode('INV');

            foreach ($qtyPerBarang as $barangId => $qty) {
                /** @var Barang $barang */
                $barang = $barangs->get($barangId);
                $qtyStok = $qty * $barang->isiJual();

                $barang->stok = (float) $barang->stok - $qtyStok;
                $barang->save();

                Transaksi::create([
                    'tanggal' => $validated['tanggal'],
                    'jenis' => 'penjualan',
                    'kode' => $kode,
                    'barang_id' => $barang->id,
                    'satuan' => $barang->satuanJual(),
                    'qty' => $qty,
                    'qty_stok' => $qtyStok,
                    'harga_satuan' => (float) $barang->harga_jual,
                    'total' => (float) $barang->harga_jual * $qty,
                    'keterangan' => $validated['keterangan'] ?? null,
                ]);
            }

            return redirect()->route('transaksi.index')
                ->with('success', "Penjualan {$kode} berhasil disimpan.");
        });
    }

    public function createRestock()
    {
        $barangs = Barang::orderBy('nama')->get();

        return view('transaksi.restock', compact('barangs'));
    }

    public function storeRestock(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'barang_id' => 'required|exists:barangs,id',
            'qty' => 'required|numeric|gt:0',
            'harga_beli' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($validated) {
            /** @var Barang $barang */
            $barang = Barang::lockForUpdate()->findOrFail($validated['barang_id']);
            $qty = (float) $validated['qty'];
            $hargaBeli = isset($validated['harga_beli']) ? (float) $validated['harga_beli'] : (float) $barang->harga_beli;

            $barang->stok = (float) $barang->stok + $qty;
            // Bila harga beli diisi di form, perbarui modal terakhir barang.
            if ($hargaBeli > 0 && $hargaBeli != (float) $barang->harga_beli) {
                $barang->harga_beli = $hargaBeli;
            }
            $barang->save();

            $kode = $this->generateKode('RST');

            Transaksi::create([
                'tanggal' => $validated['tanggal'],
                'jenis' => 'restock',
                'kode' => $kode,
                'barang_id' => $barang->id,
                'satuan' => $barang->satuan,
                'qty' => $qty,
                'qty_stok' => $qty,
                'harga_satuan' => $hargaBeli,
                'total' => $hargaBeli * $qty,
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            return redirect()->route('transaksi.index')
                ->with('success', "Restock {$kode} berhasil disimpan.");
        });
    }

    public function createPenyesuaian()
    {
        $barangs = Barang::orderBy('nama')->get();

        return view('transaksi.stok', compact('barangs'));
    }

    public function storePenyesuaian(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'barang_id' => 'required|exists:barangs,id',
            'stok_fisik' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($validated) {
            /** @var Barang $barang */
            $barang = Barang::lockForUpdate()->findOrFail($validated['barang_id']);
            $stokFisik = (float) $validated['stok_fisik'];
            $selisih = $stokFisik - ((float) $barang->stok);

            if ($selisih == 0.0) {
                return back()->withInput()->with('error', "Stok {$barang->nama} sudah sesuai (tidak ada selisih).");
            }

            $barang->stok = $stokFisik;
            $barang->save();

            $kode = $this->generateKode('ADJ');

            Transaksi::create([
                'tanggal' => $validated['tanggal'],
                'jenis' => 'penyesuaian',
                'kode' => $kode,
                'barang_id' => $barang->id,
                'satuan' => $barang->satuan,
                'qty' => $selisih,
                'qty_stok' => $selisih,
                'harga_satuan' => 0,
                'total' => 0,
                'keterangan' => $validated['keterangan'] ?? 'Penyesuaian stok',
            ]);

            $warna = $selisih > 0 ? 'bertambah' : 'berkurang';

            return redirect()->route('transaksi.index')
                ->with('success', "Penyesuaian {$kode} disimpan — stok {$barang->nama} {$warna} " . angka(abs($selisih)) . " {$barang->satuan}.");
        });
    }

    public function struk(string $kode)
    {
        $items = Transaksi::with(['barang' => fn ($q) => $q->withTrashed()])
            ->where('kode', $kode)
            ->orderBy('id')
            ->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'Nota tidak ditemukan.');
        }

        return view('transaksi.nota', ['items' => $items, 'kode' => $kode]);
    }

    public function destroyNota(string $kode)
    {
        return DB::transaction(function () use ($kode) {
            // Bila kode hasil fallback "ID-{id}" (nota lama tanpa kode), temukan lewat id.
            if (str_starts_with($kode, 'ID-')) {
                $first = Transaksi::find((int) substr($kode, 3));
                if (! $first) {
                    return back()->with('error', 'Nota transaksi tidak ditemukan.');
                }
                $kode = $first->kode ?? 'ID-' . $first->id;

                if ($kode === 'ID-' . $first->id) {
                    $rows = collect([$first]);
                } else {
                    $rows = Transaksi::where('kode', $kode)->get();
                }
            } else {
                $rows = Transaksi::where('kode', $kode)->get();
            }

            if ($rows->isEmpty()) {
                return back()->with('error', 'Nota transaksi tidak ditemukan.');
            }

            foreach ($rows as $row) {
                /** @var Barang|null $barang */
                $barang = Barang::lockForUpdate()->withTrashed()->find($row->barang_id);
                if (! $barang) {
                    $row->delete();
                    continue;
                }

                $qtyStok = (float) $row->qty_stok;

                // Kembalikan perubahan stok saat transaksi dihapus.
                if ($row->jenis === 'penjualan') {
                    $barang->stok = (float) $barang->stok + $qtyStok;
                } else {
                    // restock / penyesuaian dibatalkan -> kurangi kembali (boleh minus, data jujur).
                    $barang->stok = (float) $barang->stok - $qtyStok;
                }
                $barang->save();

                $row->delete();
            }

            return redirect()->route('transaksi.index')
                ->with('success', 'Nota transaksi berhasil dihapus dan stok dikembalikan.');
        });
    }

    private function generateKode(string $prefix): string
    {
        $base = $prefix . '-' . now()->format('Ymd') . '-';

        for ($i = 1; $i <= 5; $i++) {
            $terpakai = Transaksi::where('kode', 'like', $base . '%')
                ->distinct()
                ->count('kode');

            $kode = $base . str_pad((string) ($terpakai + $i), 4, '0', STR_PAD_LEFT);

            if (! Transaksi::where('kode', $kode)->exists()) {
                return $kode;
            }
        }

        throw new \RuntimeException('Gagal membuat kode nota, silakan coba lagi.');
    }
}
