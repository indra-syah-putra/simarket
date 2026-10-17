<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ company('name') }}</title>
    @vite(['resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="font-sans text-slate-800 antialiased bg-slate-100 min-h-screen flex">

    {{-- Panel Kiri (Brand) --}}
    <div class="hidden lg:flex w-1/2 bg-gradient-to-br from-emerald-600 via-emerald-700 to-slate-900 text-white flex-col justify-between p-12">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-white/10 backdrop-blur flex items-center justify-center text-2xl">
                <i class="fa-solid fa-basket-shopping"></i>
            </div>
            <div class="leading-tight">
                <p class="font-bold text-lg">{{ strtoupper(company('name')) }}</p>
                <p class="text-xs text-emerald-100/80 uppercase tracking-widest">Toko Sembako</p>
            </div>
        </div>

        <div class="space-y-6">
            <h1 class="text-4xl font-extrabold leading-tight">
                Kelola stok &amp; uang toko<br>jadi lebih mudah.
            </h1>
            <div class="space-y-3 text-emerald-50/90">
                <p class="flex items-center gap-3"><i class="fa-solid fa-check w-5 text-emerald-300"></i> Restock (pembelian) &amp; stok otomatis</p>
                <p class="flex items-center gap-3"><i class="fa-solid fa-check w-5 text-emerald-300"></i> Penjualan eceran dengan konversi satuan</p>
                <p class="flex items-center gap-3"><i class="fa-solid fa-check w-5 text-emerald-300"></i> Perhitungan uang bersih otomatis &amp; laporan cetak</p>
            </div>
        </div>

        <p class="text-sm text-emerald-100/70">&copy; {{ date('Y') }} {{ company('name') }}. Semua hak cipta dilindungi.</p>
    </div>

    {{-- Panel Kanan (Form) --}}
    <div class="flex-1 flex items-center justify-center p-6">
        <div class="w-full max-w-sm">
            <div class="lg:hidden flex items-center gap-3 mb-6 justify-center">
                <div class="w-11 h-11 rounded-xl bg-primary flex items-center justify-center text-white text-2xl">
                    <i class="fa-solid fa-basket-shopping"></i>
                </div>
                <div class="leading-tight text-center">
                    <p class="font-bold text-lg text-slate-800">{{ strtoupper(company('name')) }}</p>
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest">Toko Sembako</p>
                </div>
            </div>

            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-800">Selamat datang kembali</h2>
                <p class="text-sm text-slate-500 mt-1">Masuk untuk mengelola toko Anda.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-100 text-red-700 px-3 py-2 rounded-lg text-xs font-semibold flex items-center gap-2">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            {{ $errors->first('email') ?: $errors->first('captcha') }}
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Password</label>
                        <input type="password" name="password" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">
                            Verifikasi: <span class="text-primary font-extrabold">{{ $captchaData['question'] }}</span>
                        </label>
                        <input type="text" name="captcha" required placeholder="Hasil dari penjumlahan di atas"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition text-sm @error('captcha') border-red-400 @enderror">
                        @error('captcha')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center">
                        <input id="remember" type="checkbox" name="remember" class="rounded border-slate-300 text-primary focus:ring-primary">
                        <label for="remember" class="ml-2 text-xs text-slate-500">Ingat saya</label>
                    </div>

                    <button type="submit"
                        class="w-full bg-primary hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-sm transition-colors text-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-right-to-bracket"></i> Masuk
                    </button>
                </form>
            </div>

            <p class="text-center text-[11px] text-slate-400 mt-6">
                Akun demo: <span class="font-semibold text-slate-500">admin@simarket.test</span> / <span class="font-semibold text-slate-500">password</span>
            </p>
        </div>
    </div>

</body>
</html>
