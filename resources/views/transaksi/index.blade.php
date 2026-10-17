@extends('layouts.app')

@section('title', 'Riwayat Transaksi')
@section('heading', 'Riwayat Transaksi')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Riwayat Transaksi</h2>
            <p class="text-sm text-slate-500">Catatan transaksi restock (pembelian) dan penjualan.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('transaksi.create', ['jenis' => 'restock']) }}"
                class="inline-flex items-center px-5 py-2.5 text-sm font-bold text-primary bg-primary/10 hover:bg-primary/20 rounded-lg shadow-sm transition">
                <i class="fa-solid fa-truck-ramp-box mr-2"></i> Restock
            </a>
            <a href="{{ route('transaksi.create', ['jenis' => 'penjualan']) }}"
                class="inline-flex items-center px-5 py-2.5 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-md transition">
                <i class="fa-solid fa-circle-plus mr-2"></i> Transaksi Baru
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-4">
        <form action="{{ route('transaksi.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Jenis</label>
                <select name="jenis"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-sm font-medium">
                    <option value="" {{ !$jenis ? 'selected' : '' }}>Semua Jenis</option>
                    <option value="penjualan" {{ $jenis == 'penjualan' ? 'selected' : '' }}>Penjualan</option>
                    <option value="restock" {{ $jenis == 'restock' ? 'selected' : '' }}>Restock</option>
                </select>
            </div>
            <div class="w-36">
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
            <div class="w-28">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tahun</label>
                <select name="year"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-sm font-medium">
                    @for ($y = date('Y'); $y >= date('Y') - 3; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-emerald-700 font-bold text-sm transition shadow-sm">Tampilkan</button>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-4">Tanggal</th>
                        <th class="px-5 py-4">Barang</th>
                        <th class="px-5 py-4">Jenis</th>
                        <th class="px-5 py-4 text-center">Qty</th>
                        <th class="px-5 py-4 text-right">Harga</th>
                        <th class="px-5 py-4 text-right">Total</th>
                        <th class="px-5 py-4">Keterangan</th>
                        <th class="px-5 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($transaksis as $trx)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-4 text-slate-500">{{ $trx->tanggal->format('d/m/Y') }}</td>
                            <td class="px-5 py-4 font-bold text-slate-800">{{ $trx->barang->nama ?? '-' }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-1 rounded-md text-xs font-bold border {{ $trx->badgeJenis() }}">{{ $trx->labelJenis() }}</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                {{ angka($trx->qty) }} <span class="text-slate-400 text-xs">{{ $trx->satuan }}</span>
                                @if ($trx->jenis === 'penjualan' && (float) $trx->qty_stok != (float) $trx->qty)
                                    <div class="text-[11px] text-slate-400">= {{ angka($trx->qty_stok) }} {{ $trx->barang->satuan ?? '' }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right text-slate-600">{{ $trx->harga_satuan > 0 ? rupiah($trx->harga_satuan) : '-' }}<span class="text-xs text-slate-400">/{{ $trx->satuan }}</span></td>
                            <td class="px-5 py-4 text-right font-bold {{ $trx->isPemasukan() ? 'text-emerald-600' : 'text-blue-600' }}">
                                {{ $trx->total > 0 ? rupiah($trx->total) : '-' }}
                            </td>
                            <td class="px-5 py-4 text-slate-500">{{ $trx->keterangan ?? '-' }}</td>
                            <td class="px-5 py-4 text-center">
                                <form action="{{ route('transaksi.destroy', $trx->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus transaksi ini? Stok akan dikembalikan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-colors mx-auto" title="Hapus">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500">Belum ada transaksi pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex justify-center md:justify-between items-center">
            <div class="text-xs text-slate-500 hidden md:block">Total {{ $transaksis->total() }} transaksi</div>
            {{ $transaksis->links() }}
        </div>
    </div>
@endsection
