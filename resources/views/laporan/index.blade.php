@extends('layouts.app')

@section('title', 'Laporan Stok & Keuangan')
@section('heading', 'Laporan')

@section('content')
    <div class="w-full max-w-7xl mx-auto flex flex-col gap-6">

        {{-- FILTER --}}
        <div class="no-print bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h2 class="text-xl font-bold mb-4 text-slate-800">Filter Laporan</h2>
            <form action="{{ route('laporan.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Bulan</label>
                    <select name="month"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-sm font-medium">
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ sprintf('%02d', $i) }}" {{ $month == sprintf('%02d', $i) ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="w-32">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tahun</label>
                    <select name="year"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-sm font-medium">
                        @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-emerald-700 font-bold text-sm transition shadow-sm">Tampilkan</button>
                <button type="button" onclick="window.print()"
                    class="bg-emerald-600 text-white px-6 py-2 rounded-lg hover:bg-emerald-700 font-bold text-sm transition shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-print"></i> Cetak PDF
                </button>
            </form>
        </div>

        {{-- KERTAS LAPORAN --}}
        <div class="paper-container mx-auto bg-white shadow-lg p-10 border border-slate-200 min-h-[600px]">

            <div class="border-b-4 border-double border-slate-800 pb-6 mb-8 text-center">
                <h1 class="text-3xl font-extrabold uppercase tracking-widest text-slate-900 font-serif">{{ company('name') }}</h1>
                <h2 class="text-xl font-bold text-slate-700 mt-1 font-serif">LAPORAN STOK &amp; KEUANGAN TOKO</h2>
                <div class="mt-2 text-sm text-slate-600 font-bold uppercase tracking-wide">
                    Periode: <span class="underline">{{ $monthIndo }} {{ $year }}</span>
                </div>
            </div>

            <div class="text-sm text-slate-600 mb-8 text-justify leading-relaxed font-serif">
                <p><strong>Alamat:</strong> {{ company('address') }}</p>
                <p class="mt-1"><strong>Telp:</strong> {{ company('phone') }} &nbsp;|&nbsp; <strong>Email:</strong> {{ company('email') }}</p>
            </div>

            {{-- Ringkasan Keuangan --}}
            <table class="w-full text-sm border-collapse border border-slate-400 font-serif mb-8">
                <thead>
                    <tr class="bg-slate-100 font-bold uppercase text-xs">
                        <th class="border px-4 py-2 text-left">Komponen</th>
                        <th class="border px-4 py-2 text-right w-52">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border px-4 py-2">Pemasukan (Penjualan)</td>
                        <td class="border px-4 py-2 text-right font-bold">{{ rupiah($pemasukan) }}</td>
                    </tr>
                    <tr>
                        <td class="border px-4 py-2">Pengeluaran (Pembelian / Restock)</td>
                        <td class="border px-4 py-2 text-right font-bold">{{ rupiah($pengeluaran) }}</td>
                    </tr>
                    <tr class="font-bold text-lg bg-blue-50">
                        <td class="border px-4 py-3">UANG BERSIH</td>
                        <td class="border px-4 py-3 text-right {{ $uangBersih < 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ rupiah($uangBersih) }}</td>
                    </tr>
                    <tr>
                        <td class="border px-4 py-2">Total Nilai Stok (Harga Beli)</td>
                        <td class="border px-4 py-2 text-right font-bold">{{ rupiah($totalNilaiStok) }}</td>
                    </tr>
                </tbody>
            </table>

            <h3 class="text-lg font-bold text-slate-800 mb-2 font-serif">Rincian Persediaan</h3>
            <table class="w-full text-sm border-collapse border border-slate-400 font-serif">
                <thead>
                    <tr class="bg-slate-100 font-bold uppercase text-xs">
                        <th class="border px-2 py-2 w-10 text-center">No</th>
                        <th class="border px-2 py-2 w-28">Kode</th>
                        <th class="border px-4 py-2 text-left">Nama Barang</th>
                        <th class="border px-2 py-2 w-20 text-center">Awal</th>
                        <th class="border px-2 py-2 w-20 text-center">Restock</th>
                        <th class="border px-2 py-2 w-20 text-center">Jual</th>
                        <th class="border px-2 py-2 w-20 text-center">Akhir</th>
                        <th class="border px-2 py-2 w-28 text-right">H. Beli</th>
                        <th class="border px-2 py-2 w-36 text-right bg-blue-50">Nilai Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                    @endphp
                    @forelse ($reports as $item)
                        <tr class="break-inside-avoid">
                            <td class="border px-2 py-2 text-center">{{ $no++ }}</td>
                            <td class="border px-2 py-2 text-center font-mono text-xs">{{ $item['barang']->kode ?? '-' }}</td>
                            <td class="border px-4 py-2">{{ $item['barang']->nama }} <span class="text-xs text-slate-500">({{ $item['barang']->satuan }})</span></td>
                            <td class="border px-2 py-2 text-center">{{ angka($item['stok_awal']) }}</td>
                            <td class="border px-2 py-2 text-center">{{ angka($item['restock']) }}</td>
                            <td class="border px-2 py-2 text-center">{{ angka($item['penjualan']) }}</td>
                            <td class="border px-2 py-2 text-center font-bold bg-slate-50">{{ angka($item['stok_akhir']) }}</td>
                            <td class="border px-2 py-2 text-right">{{ number_format((float) $item['barang']->harga_beli, 0, ',', '.') }}</td>
                            <td class="border px-2 py-2 text-right font-bold bg-blue-50">{{ number_format($item['nilai_stok'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="border px-4 py-8 text-center text-slate-500">Tidak ada data pada periode ini.</td>
                        </tr>
                    @endforelse
                    <tr class="bg-slate-100 font-bold text-lg">
                        <td colspan="8" class="border px-4 py-3 text-right">TOTAL NILAI STOK:</td>
                        <td class="border px-2 py-3 text-right bg-blue-100">{{ rupiah($totalNilaiStok) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="mt-16 flex justify-between text-center text-sm font-serif">
                <div class="w-1/3">
                    <p class="font-bold mb-12">Dibuat Oleh,</p>
                    <p class="font-bold underline">{{ company('ttd_dibuat') }}</p>
                    <p class="text-xs text-slate-400 mt-1">NIP. {{ company('ttd_dibuat_nip') }}</p>
                </div>
                <div class="w-1/3">
                    <p class="font-bold mb-12">Diperiksa Oleh,</p>
                    <p class="font-bold underline">{{ company('ttd_diperiksa') }}</p>
                    <p class="text-xs text-slate-400 mt-1">NIP. {{ company('ttd_diperiksa_nip') }}</p>
                </div>
                <div class="w-1/3">
                    <p class="font-bold mb-12">Menyetujui,</p>
                    <p class="font-bold underline">{{ company('ttd_menyetujui') }}</p>
                    <p class="text-xs text-slate-400 mt-1">NIP. {{ company('ttd_menyetujui_nip') }}</p>
                </div>
            </div>

        </div>
    </div>

    <style>
        @media print {
            body * { visibility: hidden; }
            .paper-container, .paper-container * { visibility: visible; }
            .paper-container {
                position: absolute;
                left: 0; top: 0; width: 100%;
                margin: 0; padding: 0;
                box-shadow: none; border: none;
            }
            @page { size: A4 landscape; margin: 10mm; }
            body { background-color: white; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
@endsection
