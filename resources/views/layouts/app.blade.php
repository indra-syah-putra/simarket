<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'SI MARKET')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>[x-cloak] { display: none !important; }</style>
    @stack('styles')
</head>

<body class="font-sans text-slate-800 antialiased bg-slate-100 flex flex-col min-h-screen">

    {{-- Navbar --}}
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                {{-- Brand --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-white text-xl shadow-sm">
                        <i class="fa-solid fa-basket-shopping"></i>
                    </div>
                    <div class="hidden md:block leading-tight">
                        <h1 class="text-lg font-extrabold text-slate-900 leading-none tracking-tight">{{ strtoupper(company('name')) }}</h1>
                        <p class="text-[10px] text-primary font-bold uppercase tracking-widest mt-0.5">Toko Sembako</p>
                    </div>
                </a>

                {{-- Desktop Menu --}}
                <div class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary/10 text-primary' : 'text-slate-600 hover:bg-slate-100 hover:text-primary' }}">
                        <i class="fa-solid fa-chart-pie"></i> Dashboard
                    </a>

                    <a href="{{ route('barang.index') }}"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('barang*') ? 'bg-primary/10 text-primary' : 'text-slate-600 hover:bg-slate-100 hover:text-primary' }}">
                        <i class="fa-solid fa-box-open"></i> Data Barang
                    </a>

                    <a href="{{ route('transaksi.create', ['jenis' => 'restock']) }}"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('transaksi.create') && request('jenis') === 'restock' ? 'bg-primary/10 text-primary' : 'text-slate-600 hover:bg-slate-100 hover:text-primary' }}">
                        <i class="fa-solid fa-truck-ramp-box"></i> Restock
                    </a>

                    {{-- Laporan & Transaksi (gabungan) --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('laporan*') || request()->routeIs('transaksi.index') ? 'bg-primary/10 text-primary' : 'text-slate-600 hover:bg-slate-100 hover:text-primary' }}">
                            <i class="fa-solid fa-file-invoice-dollar"></i> Laporan &amp; Transaksi
                            <i class="fa-solid fa-chevron-down text-[10px] transition-transform" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" x-cloak x-transition
                            class="absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-50">
                            <a href="{{ route('transaksi.index') }}"
                                class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors {{ request()->routeIs('transaksi.index') ? 'bg-slate-50 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-primary' }}">
                                <i class="fa-solid fa-clock-rotate-left w-4 text-center"></i> Riwayat Transaksi
                            </a>
                            <a href="{{ route('laporan.index') }}"
                                class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors {{ request()->routeIs('laporan*') ? 'bg-slate-50 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-primary' }}">
                                <i class="fa-solid fa-file-invoice-dollar w-4 text-center"></i> Laporan
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('transaksi.create', ['jenis' => 'penjualan']) }}"
                        class="flex items-center gap-2 px-5 py-2.5 ml-4 bg-accent hover:bg-amber-600 text-white text-sm font-bold rounded-lg shadow-md transition-colors">
                        <i class="fa-solid fa-circle-plus"></i> Transaksi Baru
                    </a>

                    {{-- Profile Dropdown --}}
                    <div class="relative ml-4 pl-4 border-l border-slate-200" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false"
                            class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-slate-50 transition-colors group">
                            <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center group-hover:ring-2 group-hover:ring-primary/40 transition-all">
                                <span class="text-sm font-bold text-primary">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            </div>
                            <div class="text-left hidden lg:block">
                                <p class="text-sm font-bold text-slate-700 leading-tight">{{ auth()->user()->name }}</p>
                                <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wide">Administrator</p>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform" :class="{ 'rotate-180': open }"></i>
                        </button>

                        <div x-show="open" @click.outside="open = false" x-cloak x-transition
                            class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-50">
                            <div class="px-4 py-3 border-b border-slate-100">
                                <p class="text-sm font-bold text-slate-800">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-primary/10 text-primary">Administrator</span>
                            </div>

                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-primary transition-colors">
                                <i class="fa-solid fa-user w-4 text-center"></i> Edit Profil
                            </a>

                            <div class="border-t border-slate-100 my-1"></div>

                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <i class="fa-solid fa-right-from-bracket w-4 text-center"></i> Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Mobile Button --}}
                <div class="flex items-center md:hidden">
                    <button id="mobile-menu-btn" class="text-slate-500 hover:text-primary p-2">
                        <i class="fa-solid fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-slate-100 p-4 space-y-1">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-slate-600 font-medium hover:bg-slate-100">Dashboard</a>
            <a href="{{ route('barang.index') }}" class="block px-3 py-2 rounded-lg text-slate-600 font-medium hover:bg-slate-100">Data Barang</a>
            <a href="{{ route('transaksi.create', ['jenis' => 'restock']) }}" class="block px-3 py-2 rounded-lg text-slate-600 font-medium hover:bg-slate-100">Restock</a>
            <a href="{{ route('transaksi.index') }}" class="block px-3 py-2 rounded-lg text-slate-600 font-medium hover:bg-slate-100">Riwayat Transaksi</a>
            <a href="{{ route('laporan.index') }}" class="block px-3 py-2 rounded-lg text-slate-600 font-medium hover:bg-slate-100">Laporan</a>
            <a href="{{ route('transaksi.create', ['jenis' => 'penjualan']) }}" class="block px-3 py-2 rounded-lg text-white font-bold bg-accent text-center mt-2">+ Transaksi Baru (Penjualan)</a>

            <div class="mt-4 border-t pt-3">
                <div class="flex items-center gap-2 py-2 text-slate-500 text-sm">
                    <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center text-sm font-bold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-bold text-slate-700">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-400">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-lg text-slate-600 font-medium hover:bg-slate-100">Edit Profil</a>
                <form action="{{ route('logout') }}" method="POST" class="mt-1">
                    @csrf
                    <button type="submit" class="px-3 py-2 text-red-500 text-sm font-bold">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if (session('success') || session('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition>
            @if (session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-4 py-3 rounded-lg text-sm font-bold flex items-center gap-2">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    </div>
                </div>
            @endif
            @if (session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="bg-red-50 border border-red-100 text-red-700 px-4 py-3 rounded-lg text-sm font-bold flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- Main Content --}}
    <main class="flex-grow w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="border-t border-slate-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 text-center text-xs text-slate-400">
            {{ company('name') }} &copy; {{ date('Y') }} — Sistem Stok &amp; Perhitungan Uang Bersih
        </div>
    </footer>

    <script>
        document.getElementById('mobile-menu-btn')?.addEventListener('click', () => {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>

    @stack('scripts')
</body>

</html>
