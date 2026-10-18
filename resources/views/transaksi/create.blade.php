@extends('layouts.app')

@section('title', 'Penjualan Baru')
@section('heading', 'Penjualan Baru')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('transaksi.index') }}" class="text-sm font-medium text-slate-500 hover:text-primary transition-colors">
                &larr; Kembali ke Transaksi
            </a>
            <h2 class="text-xl font-bold text-slate-800 mt-2">Penjualan Baru</h2>
            <p class="text-sm text-slate-500">Catatan pembelanjaan pembeli — bisa menambahkan banyak barang dalam satu nota.</p>
        </div>

        <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-200" x-data="penjualanApp()">
            <form action="{{ route('transaksi.store') }}" method="POST" class="space-y-6" @submit="if (adaMasalah) { $event.preventDefault(); }">
                @csrf

                @if (session('error'))
                    <div class="bg-red-50 border border-red-100 text-red-700 px-4 py-3 rounded-lg text-sm font-bold flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @error('items')
                    <div class="bg-red-50 border border-red-100 text-red-700 px-4 py-3 rounded-lg text-sm font-bold flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        {{ $message }}
                    </div>
                @enderror

                {{-- Tanggal & Pencarian Barang --}}
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Penjualan</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('tanggal') border-red-400 @enderror">
                        @error('tanggal')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Cari / Scan Barang
                            <span class="text-[10px] font-normal text-slate-400">(ketik kode lalu Enter)</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="text" x-ref="searchInput" x-model="search" @keydown.enter.prevent="onSearchEnter()"
                                @keydown.escape="search = ''" @focus="showResults = true" @blur="showResults = false"
                                autocomplete="off" placeholder="Scan barcode / ketik kode / nama barang..."
                                class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors">

                            <div x-show="showResults && results.length" x-cloak
                                class="absolute z-30 mt-1 w-full bg-white rounded-xl shadow-lg border border-slate-200 max-h-72 overflow-y-auto">
                                <template x-for="b in results" :key="b.id">
                                    <button type="button" @mousedown.prevent="addItem(b)"
                                        class="w-full text-left px-4 py-2.5 hover:bg-primary/5 transition-colors flex items-center justify-between gap-3 border-b border-slate-50 last:border-0">
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-slate-800 truncate" x-text="b.nama"></p>
                                            <p class="text-[11px] text-slate-400"><span x-text="b.kode"></span> &middot; stok <span x-text="angka(b.stok) + ' ' + b.satuan"></span></p>
                                        </div>
                                        <span class="text-xs font-bold text-primary whitespace-nowrap" x-text="rupiah(b.harga_jual) + '/' + b.satuan_jual"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($barangs->isEmpty())
                    <p class="text-red-500 text-xs flex items-center gap-1">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        Belum ada data barang. <a href="{{ route('barang.create') }}" class="underline font-bold ml-1">Tambah Barang</a>
                    </p>
                @endif

                {{-- Daftar Belanja --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Daftar Barang</label>
                    <div class="rounded-xl border border-slate-200 overflow-hidden divide-y divide-slate-100">
                        <template x-for="(item, index) in items" :key="item.barang_id">
                            <div class="p-4 flex flex-wrap items-center gap-x-4 gap-y-3" :class="!stokCukup(item) ? 'bg-amber-50' : ''">
                                <input type="hidden" :name="`items[${index}][barang_id]`" :value="item.barang_id">

                                <div class="w-full sm:flex-1 sm:min-w-0">
                                    <p class="font-bold text-slate-800 truncate" x-text="item.nama"></p>
                                    <p class="text-[11px] text-slate-400"><span x-text="item.kode"></span> &middot; sisa stok <span x-text="angka(item.stok) + ' ' + item.satuan"></span></p>
                                </div>

                                <div class="flex items-center gap-2">
                                    <input type="number" :name="`items[${index}][qty]`" x-model.number="item.qty"
                                        min="0.0001" step="0.0001" required
                                        class="w-20 px-3 py-1.5 text-center bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none font-bold text-slate-800">
                                    <div class="leading-tight">
                                        <p class="text-[11px] text-slate-500" x-text="item.satuan_jual"></p>
                                        <p class="text-[11px] text-slate-500 font-semibold" x-text="'= ' + angka(qtyStok(item)) + ' ' + item.satuan"></p>
                                        <p x-show="!stokCukup(item)" x-cloak class="text-[11px] text-amber-600 font-bold flex items-center gap-1">
                                            <i class="fa-solid fa-triangle-exclamation"></i> stok kurang
                                        </p>
                                    </div>
                                </div>

                                <div class="ml-auto text-right">
                                    <p class="font-bold text-slate-800" x-text="rupiah(subtotal(item))"></p>
                                    <p class="text-[11px] text-slate-400" x-text="angka(item.qty) + ' × ' + rupiah(item.harga_jual) + ' /' + item.satuan_jual"></p>
                                </div>

                                <button type="button" @click="removeItem(index)"
                                    class="w-8 h-8 shrink-0 rounded-lg bg-red-50 text-red-600 inline-flex items-center justify-center hover:bg-red-600 hover:text-white transition-colors" title="Hapus">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                            </div>
                        </template>
                        <div x-show="items.length === 0" x-cloak class="px-4 py-10 text-center text-slate-400 text-sm">
                            Belum ada barang. Cari/scan barang di atas untuk menambahkan.
                        </div>
                    </div>
                </div>

                {{-- Ringkasan --}}
                <div class="rounded-xl border-2 border-slate-200 bg-slate-50 p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-slate-600">Total Barang</span>
                        <span class="text-sm font-bold text-slate-700" x-text="items.length + ' jenis'"></span>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-sm font-semibold text-slate-600">Total Pembayaran</span>
                        <span class="text-xl font-extrabold text-emerald-600" x-text="rupiah(total)"></span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-2">Pemasukan: total ini menambah uang bersih.</p>
                </div>

                {{-- Keterangan --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Keterangan (opsional)</label>
                    <input type="text" name="keterangan" placeholder="Contoh: Pembeli Ibu Siti" value="{{ old('keterangan') }}"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('keterangan') border-red-400 @enderror">
                    @error('keterangan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
                    <a href="{{ route('transaksi.index') }}"
                        class="w-full sm:w-auto text-center px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" :disabled="adaMasalah"
                        :class="adaMasalah ? 'opacity-50 cursor-not-allowed' : 'hover:bg-emerald-700'"
                        class="w-full sm:w-auto justify-center px-6 py-2.5 text-sm font-semibold text-white bg-primary rounded-lg shadow-sm transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-save"></i> Simpan Penjualan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function penjualanApp() {
                return {
                    barangs: @json($barangs),
                    search: '',
                    showResults: false,
                    items: [],

                    get results() {
                        const q = this.search.trim().toLowerCase();
                        if (!q) return this.barangs.slice(0, 8);
                        return this.barangs
                            .filter(b => (b.kode || '').toLowerCase().includes(q) || b.nama.toLowerCase().includes(q))
                            .slice(0, 8);
                    },

                    addItem(b) {
                        const ada = this.items.find(i => i.barang_id === b.id);
                        if (ada) {
                            ada.qty = (parseFloat(ada.qty) || 0) + 1;
                        } else {
                            this.items.push({
                                barang_id: b.id,
                                kode: b.kode,
                                nama: b.nama,
                                satuan: b.satuan,
                                satuan_jual: b.satuan_jual,
                                isi_jual: parseFloat(b.isi_jual) || 1,
                                harga_jual: parseFloat(b.harga_jual) || 0,
                                stok: parseFloat(b.stok) || 0,
                                qty: 1,
                            });
                        }
                        this.search = '';
                        this.$nextTick(() => this.$refs.searchInput?.focus());
                    },

                    onSearchEnter() {
                        const q = this.search.trim().toLowerCase();
                        if (!q) return;
                        let match = this.barangs.find(b => (b.kode || '').toLowerCase() === q);
                        if (!match) match = this.results[0];
                        if (match) this.addItem(match);
                    },

                    removeItem(index) {
                        this.items.splice(index, 1);
                    },

                    qtyStok(item) {
                        return (parseFloat(item.qty) || 0) * (item.isi_jual || 1);
                    },

                    subtotal(item) {
                        return (parseFloat(item.qty) || 0) * item.harga_jual;
                    },

                    stokCukup(item) {
                        return this.qtyStok(item) <= item.stok;
                    },

                    get total() {
                        return this.items.reduce((sum, i) => sum + this.subtotal(i), 0);
                    },

                    get adaMasalah() {
                        return this.items.length === 0 || this.items.some(i => !this.stokCukup(i));
                    },

                    rupiah(v) {
                        return 'Rp ' + Number(v || 0).toLocaleString('id-ID');
                    },

                    angka(v) {
                        return Number(v || 0).toLocaleString('id-ID', { maximumFractionDigits: 4 });
                    },
                }
            }
        </script>
    @endpush
@endsection
