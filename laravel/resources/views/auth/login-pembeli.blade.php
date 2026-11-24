
@extends('layouts.auth')

@section('title', 'Masuk Pembeli (Siswa & Guru) - KantinKita')

@section('content')
<div class="rounded-2xl border shadow-xl backdrop-blur-md overflow-hidden transition-all duration-300"
     style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);"
     x-data="{
        showPassword: false, 
        emailInput: '{{ old('email') }}',
        passwordInput: ''
     }">

    <div class="p-6 sm:p-7">
        {{-- Header Bersih & Jelas --}}
        <div class="text-center mb-6">
            <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider mb-2"
                 style="background-color: var(--brand-subtle); color: var(--brand); border: 1px solid var(--border-default);">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>Portal Pembeli</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-black tracking-tight mb-1" style="color: var(--text-primary);">
                Masuk // 
            </h2>
            <p class="text-xs leading-relaxed max-w-xs mx-auto" style="color: var(--text-muted);">
                Khusus Siswa dan Bapak/Ibu Guru untuk memesan menu kantin secara cepat tanpa antre.
            </p>
        </div>

        {{-- Form Login Pembeli --}}
        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Input Email / Nama --}}
            <div>
                <label for="customer-email" class="block text-xs font-semibold mb-1.5" style="color: var(--text-secondary);">
                    Email atau Nama Lengkap <span style="color: var(--status-danger);">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none" style="color: var(--text-muted);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </span>
                    <input type="text"
                           name="email"
                           id="customer-email"
                           x-model="emailInput"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           class="w-full pl-10 pr-3 py-2.5 text-sm rounded-xl border outline-none transition-all focus:ring-2 focus:ring-orange-500/30"
                           style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                           placeholder="Contoh: budi@ekantin.test atau Budi Santoso">
                </div>
                @error('email')
                    <p class="mt-1.5 text-xs flex items-center gap-1" style="color: var(--status-danger);">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            {{-- Input Kata Sandi --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="customer-password" class="block text-xs font-semibold" style="color: var(--text-secondary);">
                        Kata Sandi <span style="color: var(--status-danger);">*</span>
                    </label>
                </div>
                <div class="relative flex items-center">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none" style="color: var(--text-muted);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </span>
                    <input :type="showPassword ? 'text' : 'password'"
                           name="password"
                           id="customer-password"
                           x-model="passwordInput"
                           required
                           class="w-full pl-10 pr-11 py-2.5 text-sm rounded-xl border outline-none transition-all focus:ring-2 focus:ring-orange-500/30"
                           style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                           placeholder="Masukkan kata sandi">
                    <button type="button"
                            tabindex="-1"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center transition-colors hover:opacity-80 cursor-pointer"
                            style="color: var(--text-muted);"
                            title="Tampilkan / Sembunyikan Sandi">
                        <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                        <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1.5 text-xs flex items-center gap-1" style="color: var(--status-danger);">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            {{-- Ingat Saya --}}
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="remember" style="accent-color: var(--brand);" class="rounded w-3.5 h-3.5">
                    <span class="text-xs" style="color: var(--text-secondary);">Ingat akun saya di perangkat ini</span>
                </label>
            </div>

            {{-- Tombol Masuk Utama --}}
            <button type="submit"
                    class="w-full btn-brand py-3 text-xs sm:text-sm font-bold uppercase tracking-wider justify-center shadow-md shadow-orange-500/20 active:scale-[0.99] transition-all cursor-pointer">
                Masuk Sekarang
            </button>
        </form>

        {{-- Ajakan Daftar Akun --}}
        <div class="mt-5 pt-4 border-t text-center" style="border-color: var(--border-default);">
            <p class="text-xs" style="color: var(--text-secondary);">
                Belum punya akun pemesan?
                <a href="{{ route('register') }}" class="font-bold hover:underline ml-1" style="color: var(--brand);">
                    Daftar Akun Baru &rarr;
                </a>
            </p>
        </div>



        {{-- Tautan Pisah Khusus Admin / Tenant --}}
        <div class="mt-5 text-center">
            <a href="{{ route('login.admin') }}"
               class="inline-flex items-center gap-1 text-[11.5px] font-medium hover:underline transition-colors"
               style="color: var(--text-muted);">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span>Petugas Kantin atau Admin Sekolah? <b>Masuk di Sini &rarr;</b></span>
            </a>
        </div>
    </div>
</div>
@endsection

