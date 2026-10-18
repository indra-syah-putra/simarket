@extends('layouts.app')

@section('title', 'Riwayat Transaksi')
@section('heading', 'Riwayat Transaksi')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Riwayat Transaksi</h2>
            <p class="text-sm text-slate-500">Restock, penjualan, dan penyesuaian stok — dikelompokkan per nota.</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ route('stok.create') }}"
                class="inline-flex items-center px-5 py-2.5 text-sm font-bold text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-lg shadow-sm transition">
                <i class="fa-solid fa-sliders mr-2"></i> Penyesuaian
            </a>
            <a href="{{ route('restock.create') }}"
                class="inline-flex items-center px-5 py-2.5 text-sm font-bold text-primary bg-primary/10 hover:bg-primary/20 rounded-lg shadow-sm transition">
                <i class="fa-solid fa-truck-ramp-box mr-2"></i> Restock
            </a>
            <a href="{{ route('transaksi.create') }}"
                class="inline-flex items-center px-5 py-2.5 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-md transition">
                <i class="fa-solid fa-circle-plus mr-2"></i> Transaksi Baru
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-4">
        <form action="{{ route('transaksi.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Cari</label>
                <input type="text" name="q" value="{{ $search }}" placeholder="Kode nota, nama barang, keterangan..."
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-sm font-medium">
            </div>
            <div class="w-full sm:w-40">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Jenis</label>
                <select name="jenis"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-sm font-medium">
                    <option value="" {{ !$jenis ? 'selected' : '' }}>Semua Jenis</option>
                    <option value="penjualan" {{ $jenis == 'penjualan' ? 'selected' : '' }}>Penjualan</option>
                    <option value="restock" {{ $jenis == 'restock' ? 'selected' : '' }}>Restock</option>
                    <option value="penyesuaian" {{ $jenis == 'penyesuaian' ? 'selected' : '' }}>Penyesuaian Stok</option>
                </select>
            </div>
            <div class="w-full sm:w-36">
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
            <div class="w-full sm:w-28">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tahun</label>
                <select name="year"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-sm font-medium">
                    @for ($y = date('Y'); $y >= date('Y') - 3; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="w-full sm:w-auto bg-primary text-white px-6 py-2 rounded-lg hover:bg-emerald-700 font-bold text-sm transition shadow-sm">Tampilkan</button>
        </form>
    </div>

    {{-- Daftar Nota --}}
    <div class="space-y-3">
        @forelse ($notas as $nota)
            @php
                $isJual = $nota->jenis === 'penjualan';
                $isAdj = $nota->jenis === 'penyesuaian';
            @endphp
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden" x-data="{ open: {{ $nota->jumlah_item > 1 ? 'false' : 'true' }} }">
                <div class="flex flex-wrap items-center gap-3 px-5 py-4">
                    <button type="button" @click="open = !open"
                        class="flex items-center gap-3 flex-1 text-left min-w-0">
                        <span class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 {{ $isJual ? 'bg-emerald-50 text-emerald-600' : ($isAdj ? 'bg-amber-50 text-amber-600' : 'bg-blue-50 text-blue-600') }}">
                            <i class="fa-solid {{ $isJual ? 'fa-cash-register' : ($isAdj ? 'fa-sliders' : 'fa-truck-ramp-box') }}"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-800 truncate">
                                {{ $nota->kode ?? 'Tanpa Kode' }}
                                @if ($nota->keterangan)
                                    <span class="text-slate-400 font-normal">— {{ $nota->keterangan }}</span>
                                @endif
                            </p>
                            <p class="text-[11px] text-slate-400">
                                {{ $nota->tanggal->format('d/m/Y') }} &middot; {{ $nota->jumlah_item }} item
                                <i class="fa-solid fa-chevron-down ml-1 transition-transform" :class="{ 'rotate-180': open }"></i>
                            </p>
                        </div>
                    </button>
                    <span class="px-2 py-1 rounded-md text-xs font-bold border {{ $isJual ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($isAdj ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-blue-50 text-blue-700 border-blue-200') }}">
                        {{ $isJual ? 'Penjualan' : ($isAdj ? 'Penyesuaian Stok' : 'Restock') }}
                    </span>
                    <span class="text-base font-extrabold {{ $isJual ? 'text-emerald-600' : ($isAdj ? 'text-amber-600' : 'text-blue-600') }}">
                        @if ($isAdj)
                            {{ $nota->delta_stok > 0 ? '+' : '' }}{{ angka($nota->delta_stok) }} stok
                        @else
                            {{ $nota->total > 0 ? rupiah($nota->total) : '-' }}
                        @endif
                    </span>
                    <a href="{{ route('transaksi.nota', $nota->kode) }}" target="_blank"
                        class="w-8 h-8 rounded-lg bg-slate-50 text-slate-500 flex items-center justify-center hover:bg-primary hover:text-white transition-colors" title="Cetak struk">
                        <i class="fa-solid fa-print text-xs"></i>
                    </a>
                    <form action="{{ route('transaksi.destroy', $nota->kode ?? 'ID-' . $nota->items->first()->id) }}" method="POST"
                        onsubmit="return confirm('Hapus nota ini? Stok akan dikembalikan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-colors" title="Hapus nota">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>

                <div x-show="open" x-cloak class="border-t border-slate-100 bg-slate-50/50 divide-y divide-slate-100">
                    @foreach ($nota->items as $trx)
                        <div class="px-5 py-2.5 flex flex-wrap items-center gap-x-4 gap-y-1">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-slate-700 truncate">{{ $trx->barang->nama ?? '-' }}</p>
                                <p class="text-[11px] text-slate-400">
                                    {{ $trx->qty > 0 ? '+' : '' }}{{ angka($trx->qty) }} {{ $trx->satuan }}
                                    @if ($trx->jenis === 'penjualan' && (float) $trx->qty_stok != (float) $trx->qty)
                                        ({{ angka($trx->qty_stok) }} {{ $trx->barang->satuan ?? '' }})
                                    @endif
                                    @if ((float) $trx->harga_satuan > 0)
                                        &middot; {{ rupiah($trx->harga_satuan) }}/{{ $trx->satuan }}
                                    @endif
                                </p>
                            </div>
                            <div class="ml-auto text-right">
                                <p class="font-bold text-slate-700">{{ rupiah($trx->total) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 px-6 py-16 text-center text-slate-500">
                Belum ada transaksi pada periode ini.
            </div>
        @endforelse
    </div>

    @if ($notas->hasPages())
        <div class="mt-6 flex justify-center">{{ $notas->links() }}</div>
    @endif
@endsection
