@extends('layouts.app')

@section('title', 'Data Barang')
@section('heading', 'Data Barang')

@section('content')
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Data Barang</h2>
            <p class="text-sm text-slate-500">Kelola data barang beserta satuan dan harganya.</p>
        </div>
        <a href="{{ route('barang.create') }}"
            class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold text-white bg-primary hover:bg-blue-700 rounded-lg shadow-sm transition">
            <i class="fa-solid fa-plus mr-2"></i> Tambah Barang
        </a>
    </div>

    {{-- Search --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-3 sm:p-4 mb-4">
        <form action="{{ route('barang.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama, kode, atau kategori..."
                    class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors text-sm">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 sm:flex-none px-5 py-2.5 text-sm font-bold text-white bg-primary hover:bg-blue-700 rounded-lg transition">Cari</button>
                @if ($search)
                    <a href="{{ route('barang.index') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition text-center">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- ===== Desktop: Table --}}
    <div class="hidden md:block bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3">Kode</th>
                        <th class="px-5 py-3">Nama Barang</th>
                        <th class="px-5 py-3">Kategori</th>
                        <th class="px-5 py-3">Satuan</th>
                        <th class="px-5 py-3 text-right">Harga Beli</th>
                        <th class="px-5 py-3 text-right">Harga Jual</th>
                        <th class="px-5 py-3 text-center">Stok</th>
                        <th class="px-5 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($barangs as $item)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3 font-mono text-xs text-slate-500">{{ $item->kode ?? '-' }}</td>
                            <td class="px-5 py-3 font-bold text-slate-800">{{ $item->nama }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $item->kategori }}</td>
                            <td class="px-5 py-3 text-slate-500">
                                {{ $item->satuan }}
                                @if ($item->satuan_jual)
                                    <div class="text-[11px] text-slate-400">1 {{ $item->satuanJual() }} = {{ angka($item->isiJual()) }} {{ $item->satuan }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right text-slate-600">{{ rupiah($item->harga_beli) }}<span class="text-xs text-slate-400">/{{ $item->satuan }}</span></td>
                            <td class="px-5 py-3 text-right text-slate-600">{{ rupiah($item->harga_jual) }}<span class="text-xs text-slate-400">/{{ $item->satuanJual() }}</span></td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold border {{ $item->stok < 0 || $item->isStokTipis() ? 'bg-red-50 text-red-600 border-red-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100' }}">
                                    {{ angka($item->stok) }} {{ $item->satuan }} @if ((float) $item->stok < 0) <span class="font-normal">(minus)</span> @endif
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('barang.edit', $item->id) }}"
                                        class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-colors" title="Edit">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>
                                    <form action="{{ route('barang.destroy', $item->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-colors" title="Hapus">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500">Belum ada data barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3 border-t border-slate-200 bg-slate-50 flex flex-col sm:flex-row justify-center sm:justify-between items-center gap-2">
            <div class="text-xs text-slate-500">Total {{ $barangs->total() }} data</div>
            {{ $barangs->links() }}
        </div>
    </div>

    {{-- Cards (mobile) --}}
    <div class="md:hidden space-y-3">
        @forelse ($barangs as $item)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-bold text-slate-800 truncate">{{ $item->nama }}</p>
                        <p class="text-[11px] text-slate-400"><span class="font-mono">{{ $item->kode ?? '-' }}</span> &middot; {{ $item->kategori }}</p>
                    </div>
                    @if ((float) $item->stok < 0)
                        <span class="shrink-0 px-2 py-1 rounded-md text-xs font-bold bg-red-50 text-red-600 border border-red-100">{{ angka($item->stok) }} {{ $item->satuan }} (minus)</span>
                    @elseif ($item->isStokTipis())
                        <span class="shrink-0 px-2 py-1 rounded-md text-xs font-bold bg-red-50 text-red-600 border border-red-100">{{ angka($item->stok) }} {{ $item->satuan }}</span>
                    @else
                        <span class="shrink-0 px-2 py-1 rounded-md text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">{{ angka($item->stok) }} {{ $item->satuan }}</span>
                    @endif
                </div>

                <div class="mt-3 grid grid-cols-2 gap-2 text-xs">
                    <div class="bg-slate-50 rounded-lg px-3 py-2">
                        <p class="text-slate-400 font-semibold uppercase text-[10px]">Harga Beli</p>
                        <p class="font-bold text-slate-700">{{ rupiah($item->harga_beli) }}<span class="text-slate-400 font-normal">/{{ $item->satuan }}</span></p>
                    </div>
                    <div class="bg-slate-50 rounded-lg px-3 py-2">
                        <p class="text-slate-400 font-semibold uppercase text-[10px]">Harga Jual</p>
                        <p class="font-bold text-slate-700">{{ rupiah($item->harga_jual) }}<span class="text-slate-400 font-normal">/{{ $item->satuanJual() }}</span></p>
                    </div>
                </div>

                @if ($item->satuan_jual)
                    <p class="text-[11px] text-slate-400 mt-2">1 {{ $item->satuanJual() }} = {{ angka($item->isiJual()) }} {{ $item->satuan }} &middot; min stok {{ angka($item->min_stok) }} {{ $item->satuan }}</p>
                @else
                    <p class="text-[11px] text-slate-400 mt-2">min stok {{ angka($item->min_stok) }} {{ $item->satuan }}</p>
                @endif

                <div class="mt-3 flex gap-2">
                    <a href="{{ route('barang.edit', $item->id) }}"
                        class="flex-1 inline-flex items-center justify-center gap-2 py-2 text-xs font-bold text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-600 hover:text-white transition">
                        <i class="fa-solid fa-pen"></i> Edit
                    </a>
                    <form action="{{ route('barang.destroy', $item->id) }}" method="POST" class="flex-1"
                        onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 py-2 text-xs font-bold text-red-600 bg-red-50 rounded-lg hover:bg-red-600 hover:text-white transition">
                            <i class="fa-solid fa-trash"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 px-6 py-12 text-center text-slate-500">
                Belum ada data barang.
            </div>
        @endforelse

        @if ($barangs->hasPages())
            <div class="pt-2 flex justify-center">{{ $barangs->links() }}</div>
        @endif
    </div>
@endsection
