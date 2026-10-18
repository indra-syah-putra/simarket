<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk {{ $kode }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Courier New", monospace; color: #000; padding: 16px; }
        .struk { width: 300px; margin: 0 auto; }
        .head { text-align: center; border-bottom: 1px dashed #000; padding-bottom: 8px; margin-bottom: 8px; }
        .head h1 { font-size: 18px; letter-spacing: 1px; }
        .head p { font-size: 11px; }
        .meta { font-size: 11px; margin-bottom: 6px; }
        .meta div { display: flex; justify-content: space-between; }
        .items { width: 100%; font-size: 11px; border-collapse: collapse; margin-bottom: 8px; }
        .items td { padding: 2px 0; vertical-align: top; }
        .qtycol { width: 45%; }
        .totalcol { text-align: right; white-space: nowrap; }
        .subtotal td { border-top: 1px dashed #000; padding-top: 6px; font-weight: bold; font-size: 13px; }
        .foot { border-top: 1px dashed #000; margin-top: 8px; padding-top: 6px; font-size: 10px; text-align: center; }
        .toolbar { max-width: 300px; margin: 12px auto 0; display: flex; gap: 8px; font-family: system-ui, sans-serif; }
        .toolbar a, .toolbar button {
            flex: 1; text-align: center; padding: 8px; font-size: 13px; font-weight: 600;
            border-radius: 8px; cursor: pointer; text-decoration: none;
        }
        .toolbar a { background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; }
        .toolbar button { background: #059669; color: #fff; border: none; }
        @media print { .toolbar { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    @php
        $judul = $items->first()->jenis === 'penjualan' ? 'FAKTUR PENJUALAN' : 'NOTA';
    @endphp

    <div class="struk">
        <div class="head">
            <h1>SIMARKET</h1>
            <p>{{ $judul }}</p>
        </div>

        <div class="meta">
            <div><span>No.</span><span>{{ $kode }}</span></div>
            <div><span>Tanggal</span><span>{{ $items->first()->tanggal->translatedFormat('d/m/Y') }}</span></div>
            @if ($items->first()->keterangan)
                <div><span>Ket.</span><span>{{ $items->first()->keterangan }}</span></div>
            @endif
        </div>

        <table class="items">
            @foreach ($items as $item)
                <tr>
                    <td class="qtycol">
                        {{ $item->barang->nama }}<br>
                        <small>{{ angka($item->qty) }} {{ $item->satuan }} x {{ rupiah($item->harga_satuan) }}</small>
                    </td>
                    <td class="totalcol">{{ rupiah($item->total) }}</td>
                </tr>
            @endforeach
            @php
                $totalNota = $items->sum('total');
            @endphp
            <tr class="subtotal">
                <td>TOTAL</td>
                <td class="totalcol">{{ rupiah($totalNota) }}</td>
            </tr>
        </table>

        <div class="foot">
            Terima kasih atas kunjungannya<br>
            Barang yang sudah dibeli tidak dapat dikembalikan
        </div>
    </div>

    <div class="toolbar">
        <a href="{{ route('transaksi.index') }}">Kembali</a>
        <button onclick="window.print()">Cetak</button>
    </div>

    <script>window.addEventListener('load', function () { window.print(); });</script>
</body>
</html>
