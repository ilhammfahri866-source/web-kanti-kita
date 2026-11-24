<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesi Halaman Berakhir (419) — KantinKita</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⏳</text></svg>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased flex flex-col items-center justify-center p-4 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white">

    <div class="w-full max-w-md mx-auto text-center">
        {{-- Card Container --}}
        <div class="p-6 sm:p-8 rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl relative overflow-hidden">
            
            {{-- Top Icon --}}
            <div class="w-16 h-16 rounded-2xl bg-orange-100 dark:bg-orange-950/60 border border-orange-200 dark:border-orange-800 text-orange-600 dark:text-orange-400 flex items-center justify-center mx-auto mb-5 shadow-xs">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>

            {{-- Status Code Badge --}}
            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 border border-orange-200 dark:border-orange-800 mb-3">
                Status 419 • Sesi Kedaluwarsa
            </span>

            {{-- Title & Explanation --}}
            <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight mb-2 text-slate-900 dark:text-white">
                Sesi Halaman Telah Berakhir
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed mb-6">
                Halaman ini telah terbuka terlalu lama tanpa aktivitas sehingga sesi keamanan akun (CSRF Token) kedaluwarsa demi melindungi data Anda.
            </p>

            {{-- Action Buttons --}}
            <div class="flex flex-col gap-2.5">
                <a href="{{ route('login') }}"
                   class="btn-brand py-2.5 px-4 text-xs sm:text-sm font-bold rounded-xl shadow-sm shadow-orange-500/20 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    <span>Masuk ke Akun Saya</span>
                    <span>&rarr;</span>
                </a>

                <button type="button"
                        onclick="window.location.reload();"
                        class="btn-secondary py-2.5 px-4 text-xs sm:text-sm font-semibold rounded-xl border flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Muat Ulang Halaman</span>
                </button>

                <a href="{{ route('home') }}"
                   class="text-xs font-medium text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 hover:underline pt-2 transition-colors">
                    &larr; Kembali ke Katalog Menu Kantin
                </a>
            </div>

        </div>

        {{-- Footer Brand --}}
        <p class="text-xs text-slate-400 dark:text-slate-600 mt-6 font-medium">
            KantinKita &bull; Sistem Pemesanan Kantin Sekolah Digital
        </p>
    </div>

</body>
</html>
