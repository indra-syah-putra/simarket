@extends('layouts.app')

@section('title', 'Edit Barang')
@section('heading', 'Edit Barang')

@section('content')
    @php
        $kategoriOptions = ['Sembako', 'Minuman', 'Makanan Ringan', 'Bumbu Dapur', 'Alat Kebersihan', 'Rokok', 'Lainnya'];
        $selectedKategori = old('kategori', $barang->kategori);
    @endphp

    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('barang.index') }}" class="text-sm font-medium text-slate-500 hover:text-primary transition-colors">
                &larr; Kembali ke Data Barang
            </a>
            <h2 class="text-xl font-bold text-slate-800 mt-2">Edit Barang</h2>
            <p class="text-sm text-slate-500">Perbarui data barang, satuan, dan harganya.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <form action="{{ route('barang.update', $barang->id) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Kode Barang</label>
                        <input type="text" name="kode" placeholder="BRG-001" value="{{ old('kode', $barang->kode) }}"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('kode') border-red-400 @enderror">
                        @error('kode')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Barang</label>
                        <input type="text" name="nama" required value="{{ old('nama', $barang->nama) }}"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('nama') border-red-400 @enderror">
                        @error('nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Kategori</label>
                        <select name="kategori"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('kategori') border-red-400 @enderror">
                            @foreach ($kategoriOptions as $opt)
                                <option value="{{ $opt }}" {{ $selectedKategori == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                        @error('kategori')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Satuan Beli (besar)</label>
                        <input type="text" name="satuan" required value="{{ old('satuan', $barang->satuan) }}"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('satuan') border-red-400 @enderror">
                        @error('satuan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        <p class="text-[11px] text-slate-400 mt-1">Satuan saat restock & penyimpanan stok.</p>
                    </div>
                </div>

                <div class="rounded-xl border border-primary/20 bg-primary/5 p-4">
                    <p class="text-xs font-bold text-primary uppercase tracking-wide mb-3">Satuan Jual (Eceran)</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Satuan Jual</label>
                            <input type="text" name="satuan_jual" placeholder="Contoh: Rubak / Ons / Pcs" value="{{ old('satuan_jual', $barang->satuan_jual) }}"
                                class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('satuan_jual') border-red-400 @enderror">
                            @error('satuan_jual')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            <p class="text-[11px] text-slate-400 mt-1">Kosongkan bila jual satuan sama dengan satuan beli.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Isi per Satuan Jual</label>
                            <input type="number" step="0.0001" min="0.0001" name="isi_jual" required value="{{ old('isi_jual', $barang->isi_jual) }}"
                                class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('isi_jual') border-red-400 @enderror">
                            @error('isi_jual')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            <p class="text-[11px] text-slate-400 mt-1">Contoh: 1 rubak = <b>0.25</b> kg, maka isi = 0,25.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Harga Beli (Rp / <span class="text-primary">satuan beli</span>)</label>
                        <input type="number" name="harga_beli" required min="0" value="{{ old('harga_beli', (int) $barang->harga_beli) }}"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('harga_beli') border-red-400 @enderror">
                        @error('harga_beli')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        <p class="text-[11px] text-slate-400 mt-1">Harga modal per satuan beli (contoh: per kg).</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Harga Jual (Rp / <span class="text-accent">satuan jual</span>)</label>
                        <input type="number" name="harga_jual" required min="0" value="{{ old('harga_jual', (int) $barang->harga_jual) }}"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('harga_jual') border-red-400 @enderror">
                        @error('harga_jual')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        <p class="text-[11px] text-slate-400 mt-1">Harga per satuan jual (contoh: per rubak).</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Stok (<span class="text-primary">satuan beli</span>)</label>
                        <input type="number" step="0.0001" name="stok" required value="{{ old('stok', $barang->stok) }}"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('stok') border-red-400 @enderror">
                        @error('stok')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        <p class="text-[11px] text-slate-400 mt-1">Perubahan stok di sini tidak tercatat sebagai transaksi.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Minimum Stok</label>
                        <input type="number" step="0.0001" name="min_stok" required min="0" value="{{ old('min_stok', $barang->min_stok) }}"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('min_stok') border-red-400 @enderror">
                        @error('min_stok')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <a href="{{ route('barang.index') }}"
                        class="w-full sm:w-auto text-center px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="w-full sm:w-auto justify-center px-5 py-2.5 text-sm font-semibold text-white bg-primary hover:bg-emerald-700 rounded-lg shadow-sm transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
