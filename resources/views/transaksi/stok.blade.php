@extends('layouts.app')

@section('title', 'Penyesuaian Stok')
@section('heading', 'Penyesuaian Stok')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('transaksi.index') }}" class="text-sm font-medium text-slate-500 hover:text-primary transition-colors">
                &larr; Kembali ke Transaksi
            </a>
            <h2 class="text-xl font-bold text-slate-800 mt-2">Penyesuaian Stok</h2>
            <p class="text-sm text-slate-500">Selaraskan stok dengan stok fisik hasil hitung (stok opname).</p>
        </div>

        <div class="bg-white p-5 sm:p-8 rounded-2xl shadow-sm border border-slate-200" x-data="stokApp()" x-init="init()">
            <form action="{{ route('stok.store') }}" method="POST" class="space-y-6">
                @csrf

                @if (session('error'))
                    <div class="bg-red-50 border border-red-100 text-red-700 px-4 py-3 rounded-lg text-sm font-bold flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Tanggal --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Penyesuaian</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('tanggal') border-red-400 @enderror">
                    @error('tanggal')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Barang --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Barang</label>
                    <div class="relative">
                        <select name="barang_id" required x-model="barangId" @change="hitung()"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none appearance-none transition-colors cursor-pointer @error('barang_id') border-red-400 @enderror">
                            <option value="" disabled selected>-- Pilih Barang --</option>
                            @foreach ($barangs as $b)
                                <option value="{{ $b->id }}"
                                    data-stok="{{ $b->stok }}" data-satuan="{{ $b->satuan }}"
                                    {{ old('barang_id') == $b->id ? 'selected' : '' }}>
                                    {{ $b->nama }} (Stok: {{ angka($b->stok) }} {{ $b->satuan }})
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                    @error('barang_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    <div x-show="infoBarang" x-cloak class="mt-2 text-xs text-slate-500 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2">
                        Stok sistem: <span class="font-bold" x-text="angka(infoBarang?.stok) + ' ' + (infoBarang?.satuan || '')"></span>
                    </div>
                </div>

                {{-- Stok fisik --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Stok Fisik Baru <span x-text="'(' + (infoBarang?.satuan || 'satuan') + ')'" class="text-primary"></span>
                    </label>
                    <input type="number" name="stok_fisik" min="0" step="0.0001" required placeholder="0"
                        value="{{ old('stok_fisik', '') }}" x-model="stokFisik" @input="hitung()"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors font-bold text-slate-800 @error('stok_fisik') border-red-400 @enderror">
                    @error('stok_fisik')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    <p x-show="infoBarang" x-cloak class="text-xs mt-2 px-3 py-2 rounded-lg border font-semibold"
                        :class="selisih > 0 ? 'bg-emerald-50 border-emerald-100 text-emerald-700' : (selisih < 0 ? 'bg-red-50 border-red-100 text-red-700' : 'bg-slate-50 border-slate-200 text-slate-500')">
                        Selisih vs sistem: <span class="font-bold" x-text="(selisih > 0 ? '+ ' : (selisih < 0 ? '- ' : '')) + angka(Math.abs(selisih))"></span>
                        <span x-text="infoBarang?.satuan"></span> — stok baru <span class="font-bold" x-text="angka(stokFisik)"></span>
                    </p>
                </div>

                {{-- Keterangan --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Keterangan (alasan)</label>
                    <input type="text" name="keterangan" placeholder="Contoh: Stok opname, barang rusak/pecah" value="{{ old('keterangan') }}"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('keterangan') border-red-400 @enderror">
                    @error('keterangan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="rounded-xl border-2 border-amber-100 bg-amber-50 p-4 text-[12px] text-amber-800 flex items-start gap-2">
                    <i class="fa-solid fa-circle-info mt-0.5"></i>
                    <span>Penyesuaian <b>tidak memengaruhi uang bersih</b> — hanya mengoreksi jumlah stok. Riwayatnya tetap tercatat di halaman Transaksi.</span>
                </div>

                <div class="pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
                    <a href="{{ route('transaksi.index') }}"
                        class="w-full sm:w-auto text-center px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="w-full sm:w-auto justify-center px-6 py-2.5 text-sm font-semibold text-white bg-primary hover:bg-emerald-700 rounded-lg shadow-sm transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-save"></i> Simpan Penyesuaian
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function stokApp() {
                return {
                    barangId: @json(old('barang_id')),
                    stokFisik: @json(old('stok_fisik', '')) === null ? '' : @json(old('stok_fisik', '')),
                    infoBarang: null,

                    init() {
                        this.hitung();
                    },

                    hitung() {
                        const select = document.querySelector('select[name="barang_id"]');
                        const option = select ? select.querySelector('option[value="' + this.barangId + '"]') : null;
                        this.infoBarang = option
                            ? { stok: parseFloat(option.dataset.stok) || 0, satuan: option.dataset.satuan }
                            : null;
                    },

                    get selisih() {
                        if (!this.infoBarang) return 0;
                        const fisik = parseFloat(this.stokFisik) || 0;
                        const sel = fisik - this.infoBarang.stok;
                        return Math.abs(sel) < 0.00001 ? 0 : sel;
                    },

                    angka(v) {
                        return Number(v || 0).toLocaleString('id-ID', { maximumFractionDigits: 4 });
                    },
                }
            }
        </script>
    @endpush
@endsection
