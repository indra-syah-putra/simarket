@extends('layouts.app')

@section('title', 'Input Transaksi')
@section('heading', 'Input Transaksi')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('transaksi.index') }}" class="text-sm font-medium text-slate-500 hover:text-primary transition-colors">
                &larr; Kembali ke Transaksi
            </a>
            <h2 class="text-2xl font-bold text-slate-800 mt-2">Input Transaksi</h2>
            <p class="text-sm text-slate-500">Catat <b>restock</b> (tambah stok/pembelian) atau <b>penjualan</b> ke pembeli.</p>
        </div>

        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200"
            x-data="transaksiApp()" x-init="init()">
            <form action="{{ route('transaksi.store') }}" method="POST" class="space-y-6">
                @csrf

                @if (session('error'))
                    <div class="bg-red-50 border border-red-100 text-red-700 px-4 py-3 rounded-lg text-sm font-bold flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Tanggal --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Transaksi</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('tanggal') border-red-400 @enderror">
                    @error('tanggal')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Jenis --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Jenis Transaksi</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="jenis" value="penjualan" x-model="jenis" class="hidden" {{ old('jenis', $jenis) === 'penjualan' ? 'checked' : '' }}>
                            <div class="p-3 rounded-lg border-2 text-center transition-colors"
                                :class="jenis === 'penjualan' ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-white text-slate-500 hover:border-slate-300'">
                                <i class="fa-solid fa-cash-register block text-lg mb-1"></i>
                                <span class="text-xs font-bold">Penjualan</span>
                                <span class="block text-[10px] opacity-70">Uang masuk</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="jenis" value="restock" x-model="jenis" class="hidden" {{ old('jenis', $jenis) === 'restock' ? 'checked' : '' }}>
                            <div class="p-3 rounded-lg border-2 text-center transition-colors"
                                :class="jenis === 'restock' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-slate-200 bg-white text-slate-500 hover:border-slate-300'">
                                <i class="fa-solid fa-truck-ramp-box block text-lg mb-1"></i>
                                <span class="text-xs font-bold">Restock</span>
                                <span class="block text-[10px] opacity-70">Pembelian / tambah stok</span>
                            </div>
                        </label>
                    </div>
                    @error('jenis')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
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
                                    data-satuan-jual="{{ $b->satuanJual() }}" data-isi-jual="{{ $b->isiJual() }}"
                                    data-harga-jual="{{ $b->harga_jual }}" data-harga-beli="{{ $b->harga_beli }}"
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
                    @if ($barangs->isEmpty())
                        <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            Belum ada data barang. <a href="{{ route('barang.create') }}" class="underline font-bold ml-1">Tambah Barang</a>
                        </p>
                    @endif
                    <div x-show="infoBarang" x-cloak class="mt-2 text-xs text-slate-500 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2">
                        Stok: <span class="font-bold" x-text="infoBarang?.stokText"></span>
                        &nbsp;|&nbsp; Harga beli: <span class="font-bold" x-text="rupiah(infoBarang?.harga_beli) + ' /' + infoBarang?.satuan"></span>
                        &nbsp;|&nbsp; Harga jual: <span class="font-bold" x-text="rupiah(infoBarang?.harga_jual) + ' /' + infoBarang?.satuan_jual"></span>
                    </div>
                </div>

                {{-- Qty --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Jumlah/Qty <span x-text="'(' + unitLabel + ')'" class="text-primary"></span>
                    </label>
                    <input type="number" name="qty" min="0.0001" step="0.0001" required placeholder="0" value="{{ old('qty', 1) }}" x-model.number="qty" @input="hitung()"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors font-bold text-slate-800 @error('qty') border-red-400 @enderror">
                    @error('qty')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror

                    {{-- Konversi penjualan --}}
                    <p class="text-[11px] text-slate-400 mt-1" x-show="jenis === 'penjualan' && infoBarang" x-cloak>
                        <span x-text="qty || 0"></span> <span x-text="infoBarang?.satuan_jual"></span>
                        = <span class="font-bold text-slate-600" x-text="angka(qtyStok)"></span>
                        <span x-text="infoBarang?.satuan"></span>
                        <template x-if="infoBarang && infoBarang.isi_jual != 1">
                            <span> (1 <span x-text="infoBarang?.satuan_jual"></span> = <span x-text="angka(infoBarang?.isi_jual)"></span> <span x-text="infoBarang?.satuan"></span>)</span>
                        </template>
                    </p>
                    <p class="text-[11px] text-amber-600 mt-1 flex items-center gap-1" x-show="jenis === 'penjualan' && infoBarang && !stokCukup" x-cloak>
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        Stok tidak cukup. Tersedia <span x-text="infoBarang?.stokText"></span>.
                    </p>
                </div>

                {{-- Total --}}
                <div class="rounded-xl border-2 border-slate-200 bg-slate-50 p-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-slate-600">Harga Satuan</span>
                        <span class="text-sm font-bold text-slate-700" x-text="rupiah(hargaSatuan) + ' /' + unitLabel"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-slate-600">Total</span>
                        <span class="text-2xl font-extrabold" :class="jenis === 'penjualan' ? 'text-emerald-600' : 'text-blue-600'" x-text="rupiah(total)"></span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-2" x-show="jenis === 'penjualan'" x-cloak>
                        Pemasukan: total ini menambah uang bersih.
                    </p>
                    <p class="text-[11px] text-slate-400 mt-2" x-show="jenis === 'restock'" x-cloak>
                        Pengeluaran: total ini mengurangi uang bersih (harga beli).
                    </p>
                </div>

                {{-- Keterangan --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Keterangan (opsional)</label>
                    <input type="text" name="keterangan" placeholder="Contoh: Pembeli Ibu Siti / Kulakan pasar" value="{{ old('keterangan') }}"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('keterangan') border-red-400 @enderror">
                    @error('keterangan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('transaksi.index') }}"
                        class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 text-sm font-semibold text-white bg-primary hover:bg-emerald-700 rounded-lg shadow-sm transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-save"></i> Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function transaksiApp() {
                return {
                    jenis: @json(old('jenis', $jenis)),
                    barangId: @json(old('barang_id')),
                    qty: parseFloat(@json(old('qty', 1))) || 1,

                    hitungKonversi() {
                        const select = document.querySelector('select[name="barang_id"]');
                        const option = select ? select.querySelector('option[value="' + this.barangId + '"]') : null;
                        if (!option) {
                            this.infoBarang = null;
                            return;
                        }
                        this.infoBarang = {
                            stok: parseFloat(option.dataset.stok),
                            satuan: option.dataset.satuan,
                            satuan_jual: option.dataset.satuanJual,
                            isi_jual: parseFloat(option.dataset.isiJual) || 1,
                            harga_jual: parseFloat(option.dataset.hargaJual),
                            harga_beli: parseFloat(option.dataset.hargaBeli),
                        };
                        this.infoBarang.stokText = this.angka(this.infoBarang.stok) + ' ' + this.infoBarang.satuan;
                    },

                    rupiah(v) {
                        return 'Rp ' + Number(v || 0).toLocaleString('id-ID');
                    },

                    angka(v) {
                        return Number(v || 0).toLocaleString('id-ID', { maximumFractionDigits: 4 });
                    },

                    init() {
                        this.hitung();
                    },

                    hitung() {
                        this.hitungKonversi();
                    },

                    get hargaSatuan() {
                        if (!this.infoBarang) return 0;
                        return this.jenis === 'penjualan' ? this.infoBarang.harga_jual : this.infoBarang.harga_beli;
                    },

                    get qtyStok() {
                        if (!this.infoBarang) return this.qty || 0;
                        return this.jenis === 'penjualan' ? (this.qty || 0) * this.infoBarang.isi_jual : (this.qty || 0);
                    },

                    get total() {
                        return this.hargaSatuan * (this.qty || 0);
                    },

                    get unitLabel() {
                        if (!this.infoBarang) return '';
                        return this.jenis === 'penjualan' ? this.infoBarang.satuan_jual : this.infoBarang.satuan;
                    },

                    get stokCukup() {
                        if (this.jenis !== 'penjualan' || !this.infoBarang) return true;
                        return this.qtyStok <= this.infoBarang.stok;
                    }
                }
            }
        </script>
    @endpush
@endsection
