@extends('layouts.app')

@section('title', 'Restock Barang')
@section('heading', 'Restock Barang')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('transaksi.index') }}" class="text-sm font-medium text-slate-500 hover:text-primary transition-colors">
                &larr; Kembali ke Transaksi
            </a>
            <h2 class="text-xl font-bold text-slate-800 mt-2">Restock Barang</h2>
            <p class="text-sm text-slate-500">Catat pembelian / penambahan stok barang (uang keluar).</p>
        </div>

        <div class="bg-white p-5 sm:p-8 rounded-2xl shadow-sm border border-slate-200" x-data="restockApp()" x-init="init()">
            <form action="{{ route('restock.store') }}" method="POST" class="space-y-6">
                @csrf

                @if (session('error'))
                    <div class="bg-red-50 border border-red-100 text-red-700 px-4 py-3 rounded-lg text-sm font-bold flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Tanggal --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Restock</label>
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
                                    data-harga-beli="{{ $b->harga_beli }}"
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
                        Stok saat ini: <span class="font-bold" x-text="infoBarang?.stokText"></span>
                        &nbsp;|&nbsp; Harga beli: <span class="font-bold" x-text="rupiah(infoBarang?.harga_beli) + ' /' + infoBarang?.satuan"></span>
                    </div>
                </div>

                {{-- Qty --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Jumlah/Qty <span x-text="'(' + (infoBarang?.satuan || 'satuan') + ')'" class="text-primary"></span>
                    </label>
                    <input type="number" name="qty" min="0.0001" step="0.0001" required placeholder="0" value="{{ old('qty', 1) }}" x-model.number="qty" @input="hitung()"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors font-bold text-slate-800 @error('qty') border-red-400 @enderror">
                    @error('qty')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    <p class="text-[11px] text-slate-400 mt-1" x-show="infoBarang" x-cloak>
                        Stok baru: <span class="font-bold text-slate-600" x-text="angka(stokBaru)"></span> <span x-text="infoBarang?.satuan"></span>
                    </p>
                </div>

                {{-- Harga beli (opsional) --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Harga Beli / <span x-text="(infoBarang?.satuan || 'satuan')"></span>
                        <span class="text-[11px] font-normal text-slate-400">(opsional — kosongkan untuk pakai harga terakhir)</span>
                    </label>
                    <input type="number" name="harga_beli" min="0" step="1" inputmode="numeric" placeholder="{{ '' }}"
                        value="{{ old('harga_beli') }}" x-model.number="hargaBeliInput" @input="hitung()"
                        :placeholder="infoBarang ? angka(infoBarang.harga_beli) : '0'"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors font-bold text-slate-800 @error('harga_beli') border-red-400 @enderror">
                    @error('harga_beli')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    <p class="text-[11px] text-slate-400 mt-1" x-show="infoBarang" x-cloak>
                        Harga terakhir: <span class="font-bold text-slate-600" x-text="rupiah(infoBarang?.harga_beli)"></span> /
                        <span x-text="infoBarang?.satuan"></span> — diisi akan memperbarui modal barang.
                    </p>
                </div>

                {{-- Total --}}
                <div class="rounded-xl border-2 border-slate-200 bg-slate-50 p-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-slate-600">Harga Beli Satuan</span>
                        <span class="text-sm font-bold text-slate-700" x-text="rupiah(hargaBeli) + ' /' + (infoBarang?.satuan || '')"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-slate-600">Total Pengeluaran</span>
                        <span class="text-xl font-extrabold text-blue-600" x-text="rupiah(total)"></span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-2">Pengeluaran: total ini mengurangi uang bersih.</p>
                </div>

                {{-- Keterangan --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Keterangan (opsional)</label>
                    <input type="text" name="keterangan" placeholder="Contoh: Kulakan pasar" value="{{ old('keterangan') }}"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('keterangan') border-red-400 @enderror">
                    @error('keterangan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
                    <a href="{{ route('transaksi.index') }}"
                        class="w-full sm:w-auto text-center px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="w-full sm:w-auto justify-center px-6 py-2.5 text-sm font-semibold text-white bg-primary hover:bg-emerald-700 rounded-lg shadow-sm transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-save"></i> Simpan Restock
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function restockApp() {
                return {
                    barangId: @json(old('barang_id')),
                    qty: parseFloat(@json(old('qty', 1))) || 1,
                    hargaBeliInput: @json(old('harga_beli')) === null ? '' : @json(old('harga_beli')),
                    infoBarang: null,

                    init() {
                        this.hitung();
                    },

                    hitung() {
                        const select = document.querySelector('select[name="barang_id"]');
                        const option = select ? select.querySelector('option[value="' + this.barangId + '"]') : null;
                        if (!option) {
                            this.infoBarang = null;
                            return;
                        }
                        this.infoBarang = {
                            stok: parseFloat(option.dataset.stok) || 0,
                            satuan: option.dataset.satuan,
                            harga_beli: parseFloat(option.dataset.hargaBeli) || 0,
                        };
                        this.infoBarang.stokText = this.angka(this.infoBarang.stok) + ' ' + this.infoBarang.satuan;
                    },

                    rupiah(v) {
                        return 'Rp ' + Number(v || 0).toLocaleString('id-ID');
                    },

                    angka(v) {
                        return Number(v || 0).toLocaleString('id-ID', { maximumFractionDigits: 4 });
                    },

                    get hargaBeli() {
                        const hargaDefault = this.infoBarang ? parseFloat(this.infoBarang.harga_beli) || 0 : 0;
                        const input = parseFloat(this.hargaBeliInput);
                        return isNaN(input) || input <= 0 ? hargaDefault : input;
                    },

                    get stokBaru() {
                        return (this.infoBarang ? this.infoBarang.stok : 0) + (this.qty || 0);
                    },

                    get total() {
                        return this.hargaBeli * (this.qty || 0);
                    },
                }
            }
        </script>
    @endpush
@endsection
