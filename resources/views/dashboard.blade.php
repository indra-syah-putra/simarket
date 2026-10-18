@extends('layouts.app')

@section('title', 'Dashboard - SI MARKET')
@section('heading', 'Dashboard')

@section('content')
    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Ringkasan Toko</h2>
            <p class="text-slate-500 text-sm">Pantau stok dan perhitungan uang bersih toko Anda.</p>
        </div>
        <a href="{{ route('transaksi.create') }}"
            class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold text-white bg-primary hover:bg-emerald-700 rounded-xl shadow-md transition">
            <i class="fa-solid fa-circle-plus mr-2"></i> Transaksi Baru
        </a>
    </div>

    {{-- HERO UANG BERSIH + STATS --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-5 mb-6">
        <div class="lg:col-span-2 relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 to-slate-900 text-white p-6 shadow-lg">
            <i class="fa-solid fa-sack-dollar absolute -right-4 -bottom-4 text-8xl text-white/10"></i>
            <p class="text-xs font-bold uppercase tracking-widest text-emerald-200">Uang Bersih Bulan Ini</p>
            <p class="text-3xl font-extrabold mt-2">{{ rupiah($uangBersihBulanIni) }}</p>
            <p class="text-xs text-emerald-100/80 mt-3">
                Penjualan {{ rupiah($penjualanBulanIni ?? 0) }} &minus; Pembelian {{ rupiah($pembelianBulanIni ?? 0) }}
            </p>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Barang</p>
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fa-solid fa-cubes"></i></div>
            </div>
            <p class="text-2xl font-extrabold text-slate-800 mt-3">{{ $totalItems }}</p>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Stok Tipis</p>
                <div class="w-9 h-9 rounded-xl bg-red-50 text-red-600 flex items-center justify-center"><i class="fa-solid fa-triangle-exclamation"></i></div>
            </div>
            <p class="text-2xl font-extrabold {{ $lowStockCount > 0 ? 'text-red-600' : 'text-slate-800' }} mt-3">{{ $lowStockCount }}</p>
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
            <div class="divide-y divide-slate-100">
                @forelse ($barangs->take(6) as $item)
                    <div class="px-5 py-3 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-bold text-slate-800 truncate">{{ $item->nama }}</p>
                            <p class="text-xs text-slate-400">{{ $item->kategori }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="font-bold {{ $item->stok < 0 ? 'text-red-600' : 'text-slate-800' }}">{{ angka($item->stok) }}<span class="text-slate-400 text-xs ml-1">{{ $item->satuan }}</span></p>
                            @if ((float) $item->stok < 0)
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-bold bg-red-50 text-red-600 border border-red-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Minus
                                </span>
                            @elseif ($item->isStokTipis())
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-bold bg-red-50 text-red-600 border border-red-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Tipis
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aman
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-12 text-center">
                        <i class="fa-solid fa-box-open text-3xl text-slate-300 block mb-2"></i>
                        <p class="text-slate-500 mb-3">Belum ada data barang.</p>
                        <a href="{{ route('barang.create') }}" class="text-sm font-bold text-primary hover:underline">+ Tambah Barang</a>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- TRANSAKSI TERBARU --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-slate-800">Transaksi Terbaru</h3>
                <a href="{{ route('transaksi.index') }}" class="text-xs font-bold text-primary hover:underline">Lihat semua</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($transaksiTerbaru as $trx)
                    <div class="px-5 py-3 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-bold text-slate-800 truncate">{{ $trx->barang->nama ?? '-' }}</p>
                            <p class="text-xs text-slate-400">{{ $trx->tanggal->format('d/m/Y') }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="font-bold {{ $trx->isPemasukan() ? 'text-emerald-600' : 'text-blue-600' }}">
                                {{ $trx->total > 0 ? rupiah($trx->total) : '-' }}
                            </p>
                            <span class="inline-block px-2 py-0.5 rounded-md text-[11px] font-bold border {{ $trx->badgeJenis() }}">{{ $trx->labelJenis() }}</span>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-12 text-center">
                        <i class="fa-solid fa-receipt text-3xl text-slate-300 block mb-2"></i>
                        <p class="text-slate-500 mb-3">Belum ada transaksi.</p>
                        <a href="{{ route('transaksi.create') }}" class="text-sm font-bold text-primary hover:underline">+ Input Transaksi</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
