@extends('layouts.app')

@section('title', 'Dashboard - SI MARKET')
@section('heading', 'Dashboard')

@section('content')
    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Ringkasan Toko</h2>
            <p class="text-slate-500 text-sm">Pantau stok dan perhitungan uang bersih toko Anda.</p>
        </div>
        <a href="{{ route('transaksi.create', ['jenis' => 'penjualan']) }}"
            class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold text-white bg-primary hover:bg-emerald-700 rounded-xl shadow-md transition">
            <i class="fa-solid fa-circle-plus mr-2"></i> Transaksi Baru
        </a>
    </div>

    {{-- HERO UANG BERSIH + STATS --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-5 mb-6">
        <div class="lg:col-span-2 relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 to-slate-900 text-white p-6 shadow-lg">
            <i class="fa-solid fa-sack-dollar absolute -right-4 -bottom-4 text-8xl text-white/10"></i>
            <p class="text-xs font-bold uppercase tracking-widest text-emerald-200">Uang Bersih Bulan Ini</p>
            <p class="text-4xl font-extrabold mt-2">{{ rupiah($uangBersihBulanIni) }}</p>
            <p class="text-xs text-emerald-100/80 mt-3">
                Penjualan {{ rupiah($penjualanBulanIni ?? 0) }} &minus; Pembelian {{ rupiah($pembelianBulanIni ?? 0) }}
            </p>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Barang</p>
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fa-solid fa-cubes"></i></div>
            </div>
            <p class="text-3xl font-extrabold text-slate-800 mt-3">{{ $totalItems }}</p>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Stok Tipis</p>
                <div class="w-9 h-9 rounded-xl bg-red-50 text-red-600 flex items-center justify-center"><i class="fa-solid fa-triangle-exclamation"></i></div>
            </div>
            <p class="text-3xl font-extrabold {{ $lowStockCount > 0 ? 'text-red-600' : 'text-slate-800' }} mt-3">{{ $lowStockCount }}</p>
        </div>
    </div>

    {{-- MONEY SUMMARY --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center"><i class="fa-solid fa-arrow-trend-up"></i></div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Penjualan</p>
                    <p class="text-lg font-bold text-emerald-600">{{ rupiah($totalPenjualan) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center"><i class="fa-solid fa-arrow-trend-down"></i></div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Pembelian</p>
                    <p class="text-lg font-bold text-red-600">{{ rupiah($totalPembelian) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center"><i class="fa-solid fa-scale-balanced"></i></div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Uang Bersih (Total)</p>
                    <p class="text-lg font-bold {{ $uangBersih < 0 ? 'text-red-600' : 'text-slate-800' }}">{{ rupiah($uangBersih) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        {{-- PERSEDIAAN --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-slate-800">Daftar Persediaan</h3>
                <a href="{{ route('barang.index') }}" class="text-xs font-bold text-primary hover:underline">Lihat semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3">Nama Barang</th>
                            <th class="px-5 py-3 text-center">Stok</th>
                            <th class="px-5 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($barangs->take(6) as $item)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3">
                                    <p class="font-bold text-slate-800">{{ $item->nama }}</p>
                                    <p class="text-xs text-slate-400">{{ $item->kategori }}</p>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="font-bold text-slate-800">{{ angka($item->stok) }}</span>
                                    <span class="text-slate-400 text-xs ml-1">{{ $item->satuan }}</span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @if ($item->isStokTipis())
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Tipis
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aman
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-12 text-center">
                                    <i class="fa-solid fa-box-open text-3xl text-slate-300 block mb-2"></i>
                                    <p class="text-slate-500 mb-3">Belum ada data barang.</p>
                                    <a href="{{ route('barang.create') }}" class="text-sm font-bold text-primary hover:underline">+ Tambah Barang</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TRANSAKSI TERBARU --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-slate-800">Transaksi Terbaru</h3>
                <a href="{{ route('transaksi.index') }}" class="text-xs font-bold text-primary hover:underline">Lihat semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3">Tanggal</th>
                            <th class="px-5 py-3">Barang</th>
                            <th class="px-5 py-3 text-center">Jenis</th>
                            <th class="px-5 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($transaksiTerbaru as $trx)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3 text-slate-500">{{ $trx->tanggal->format('d/m/Y') }}</td>
                                <td class="px-5 py-3 font-bold text-slate-800">{{ $trx->barang->nama ?? '-' }}</td>
                                <td class="px-5 py-3 text-center">
                                    <span class="px-2 py-1 rounded-md text-xs font-bold border {{ $trx->badgeJenis() }}">{{ $trx->labelJenis() }}</span>
                                </td>
                                <td class="px-5 py-3 text-right font-bold {{ $trx->isPemasukan() ? 'text-emerald-600' : 'text-blue-600' }}">
                                    {{ $trx->total > 0 ? rupiah($trx->total) : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center">
                                    <i class="fa-solid fa-receipt text-3xl text-slate-300 block mb-2"></i>
                                    <p class="text-slate-500 mb-3">Belum ada transaksi.</p>
                                    <a href="{{ route('transaksi.create') }}" class="text-sm font-bold text-primary hover:underline">+ Input Transaksi</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
