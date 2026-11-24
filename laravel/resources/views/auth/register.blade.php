@extends('layouts.auth')

@section('title', 'Daftar Akun (Siswa & Guru) - KantinKita')

@section('content')
<div class="rounded-2xl border shadow-xl backdrop-blur-md overflow-hidden transition-all duration-300"
     style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);"
     x-data="{
        accountType: '{{ old('account_type', 'siswa') }}',
        nameInput: '{{ old('name') }}',
        classInput: '{{ old('classroom') }}',
        emailInput: '{{ old('email') }}',
        passwordInput: '',
        confirmPasswordInput: '',
        showPassword: false,
        showConfirmPassword: false,
        selectType(type) {
            this.accountType = type;
            if (type === 'guru' && (!this.classInput || !this.classInput.startsWith('Guru'))) {
                this.classInput = 'Guru (Ruang Guru)';
            } else if (type === 'siswa' && this.classInput && this.classInput.startsWith('Guru')) {
                this.classInput = '';
            }
        }
     }">

    {{-- ── 1. Tab Navigasi: Masuk vs Daftar ────────────────── --}}
    <div class="grid grid-cols-2 p-1.5 border-b"
         style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
        <a href="{{ route('login') }}"
           class="flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-xs font-semibold transition-colors hover:opacity-80"
           style="color: var(--text-secondary);"
           title="Sudah punya akun? Masuk di sini">
            <svg class="w-4 h-4 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
            <span>Masuk</span>
        </a>
        <a href="{{ route('register') }}"
           class="flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-xs font-bold transition-all shadow-xs"
           style="background-color: var(--bg-surface); color: var(--brand); border: 1px solid var(--border-default);"
           title="Pendaftaran akun baru">
            <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            <span>Daftar Akun Baru</span>
        </a>
    </div>

    <div class="p-4 sm:p-5">
        <div class="text-center mb-3.5">
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold uppercase tracking-wider mb-1.5 shadow-2xs"
                 style="background-color: var(--brand-subtle); color: var(--brand); border: 1px solid var(--border-default);">
                <span>Pendaftaran Akun Pemesan</span>
            </div>
            <h2 class="text-lg sm:text-xl font-extrabold tracking-tight mb-0.5" style="color: var(--text-primary);">
                Buat Akun Pemesan
            </h2>
            <p class="text-xs leading-relaxed" style="color: var(--text-muted);">
                Untuk Siswa dan Bapak/Ibu Guru memesan aneka menu kantin tanpa antre.
            </p>
        </div>

        {{-- ── Pilihan Tipe Akun (Siswa vs Guru) ────────────────── --}}
        <div class="mb-4">
            <label class="block text-xs font-semibold mb-1.5 text-center" style="color: var(--text-secondary);">
                Mendaftar Sebagai:
            </label>
            <div class="grid grid-cols-2 gap-2 p-1 rounded-xl border" style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                <button
                    type="button"
                    @click="selectType('siswa')"
                    class="py-2 px-3 rounded-lg text-xs font-bold flex items-center justify-center gap-2 transition-all cursor-pointer"
                    :class="accountType === 'siswa' ? 'bg-orange-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:opacity-80'"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                    <span>Siswa</span>
                </button>
                <button
                    type="button"
                    @click="selectType('guru')"
                    class="py-2 px-3 rounded-lg text-xs font-bold flex items-center justify-center gap-2 transition-all cursor-pointer"
                    :class="accountType === 'guru' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:opacity-80'"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Guru / Staf</span>
                </button>
            </div>
        </div>

        <form action="{{ route('register') }}" method="POST" autocomplete="off">
            @csrf
            <input type="hidden" name="account_type" :value="accountType">

            {{-- Input 1: Nama Lengkap --}}
            <div class="mb-3">
                <label for="name" class="block text-xs font-semibold mb-1" style="color: var(--text-secondary);">
                    <span x-text="accountType === 'guru' ? 'Nama Lengkap Guru / Staf' : 'Nama Lengkap Siswa'"></span>
                    <span style="color: var(--status-danger);">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none" style="color: var(--text-muted);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                    <input type="text"
                           name="name"
                           id="name"
                           x-model="nameInput"
                           value="{{ old('name') }}"
                           required
                           autofocus
                           class="w-full pl-9 pr-3 py-2 text-xs sm:text-sm rounded-xl border outline-none transition-all focus:ring-2 focus:ring-orange-500/30"
                           style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                           :placeholder="accountType === 'guru' ? 'Contoh: Bpk. Ahmad Fauzi, S.Pd.' : 'Contoh: Budi Santoso'">
                </div>
                @error('name')
                    <p class="mt-1 text-[11px] flex items-center gap-1" style="color: var(--status-danger);">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            {{-- Input 2: Kelas (Siswa) ATAU Ruangan (Guru) --}}
            <div class="mb-3">
                <label for="classroom" class="block text-xs font-semibold mb-1 flex items-center justify-between" style="color: var(--text-secondary);">
                    <span>
                        <span x-text="accountType === 'guru' ? 'Ruangan / Posisi' : 'Kelas & Jurusan'"></span>
                        <span style="color: var(--status-danger);">*</span>
                    </span>
                    <span class="text-[10px] font-normal" style="color: var(--text-muted);">(Pilih dari daftar)</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10" style="color: var(--text-muted);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </span>
                    <select name="classroom"
                            id="classroom"
                            x-model="classInput"
                            required
                            class="w-full pl-9 pr-9 py-2 text-xs sm:text-sm rounded-xl border outline-none transition-all focus:ring-2 focus:ring-orange-500/30 cursor-pointer"
                            style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default); -webkit-appearance: none; -moz-appearance: none; appearance: none;">
                        
                        <template x-if="accountType === 'siswa'">
                            <option value="" disabled :selected="!classInput">-- Pilih Kelas & Jurusan --</option>
                        </template>
                        <template x-if="accountType === 'guru'">
                            <option value="" disabled :selected="!classInput">-- Pilih Ruangan / Posisi Guru --</option>
                        </template>

                        {{-- Opsi Khusus Guru / Tenaga Pendidik --}}
                        <optgroup label="Guru & Tenaga Pendidik / Staf" x-show="accountType === 'guru'">
                            <option value="Guru (Ruang Guru)">Guru (Ruang Guru)</option>
                            <option value="Guru (Perpustakaan)">Guru (Perpustakaan)</option>
                            <option value="Guru (Lab Komputer)">Guru (Lab Komputer)</option>
                            <option value="Guru (Ruang Piket)">Guru (Ruang Piket)</option>
                            <option value="Guru (Ruang TU / Tata Usaha)">Guru (Ruang TU / Tata Usaha)</option>
                            <option value="Guru (Ruang Kepala Sekolah)">Guru (Ruang Kepala Sekolah)</option>
                            <option value="Guru (Bengkel Praktek)">Guru (Bengkel Praktek)</option>
                            <option value="Guru (Ruang Kelas / Mengajar)">Guru (Ruang Kelas / Mengajar)</option>
                            <option value="Guru / Staf Karyawan">Guru / Staf Karyawan</option>
                        </optgroup>

                        {{-- Opsi Kelas Siswa --}}
                        <optgroup label="TO (Teknik Otomotif)" x-show="accountType === 'siswa'">
                            <option value="10 TO 1" {{ old('classroom') == '10 TO 1' ? 'selected' : '' }}>10 TO 1</option>
                            <option value="10 TO 2" {{ old('classroom') == '10 TO 2' ? 'selected' : '' }}>10 TO 2</option>
                            <option value="10 TO 3" {{ old('classroom') == '10 TO 3' ? 'selected' : '' }}>10 TO 3</option>
                            <option value="11 TO 1" {{ old('classroom') == '11 TO 1' ? 'selected' : '' }}>11 TO 1</option>
                            <option value="11 TO 2" {{ old('classroom') == '11 TO 2' ? 'selected' : '' }}>11 TO 2</option>
                            <option value="11 TO 3" {{ old('classroom') == '11 TO 3' ? 'selected' : '' }}>11 TO 3</option>
                            <option value="12 TO 1" {{ old('classroom') == '12 TO 1' ? 'selected' : '' }}>12 TO 1</option>
                            <option value="12 TO 2" {{ old('classroom') == '12 TO 2' ? 'selected' : '' }}>12 TO 2</option>
                            <option value="12 TO 3" {{ old('classroom') == '12 TO 3' ? 'selected' : '' }}>12 TO 3</option>
                        </optgroup>

                        <optgroup label="PPLG (Pengembangan Perangkat Lunak & Gim)" x-show="accountType === 'siswa'">
                            <option value="10 PPLG 1" {{ old('classroom') == '10 PPLG 1' ? 'selected' : '' }}>10 PPLG 1</option>
                            <option value="10 PPLG 2" {{ old('classroom') == '10 PPLG 2' ? 'selected' : '' }}>10 PPLG 2</option>
                            <option value="10 PPLG 3" {{ old('classroom') == '10 PPLG 3' ? 'selected' : '' }}>10 PPLG 3</option>
                            <option value="11 PPLG 1" {{ old('classroom') == '11 PPLG 1' ? 'selected' : '' }}>11 PPLG 1</option>
                            <option value="11 PPLG 2" {{ old('classroom') == '11 PPLG 2' ? 'selected' : '' }}>11 PPLG 2</option>
                            <option value="11 PPLG 3" {{ old('classroom') == '11 PPLG 3' ? 'selected' : '' }}>11 PPLG 3</option>
                            <option value="12 PPLG 1" {{ old('classroom') == '12 PPLG 1' ? 'selected' : '' }}>12 PPLG 1</option>
                            <option value="12 PPLG 2" {{ old('classroom') == '12 PPLG 2' ? 'selected' : '' }}>12 PPLG 2</option>
                            <option value="12 PPLG 3" {{ old('classroom') == '12 PPLG 3' ? 'selected' : '' }}>12 PPLG 3</option>
                        </optgroup>

                        <optgroup label="BCF (Broadcasting & Perfilman)" x-show="accountType === 'siswa'">
                            <option value="10 BCF 1" {{ old('classroom') == '10 BCF 1' ? 'selected' : '' }}>10 BCF 1</option>
                            <option value="10 BCF 2" {{ old('classroom') == '10 BCF 2' ? 'selected' : '' }}>10 BCF 2</option>
                            <option value="10 BCF 3" {{ old('classroom') == '10 BCF 3' ? 'selected' : '' }}>10 BCF 3</option>
                            <option value="11 BCF 1" {{ old('classroom') == '11 BCF 1' ? 'selected' : '' }}>11 BCF 1</option>
                            <option value="11 BCF 2" {{ old('classroom') == '11 BCF 2' ? 'selected' : '' }}>11 BCF 2</option>
                            <option value="11 BCF 3" {{ old('classroom') == '11 BCF 3' ? 'selected' : '' }}>11 BCF 3</option>
                            <option value="12 BCF 1" {{ old('classroom') == '12 BCF 1' ? 'selected' : '' }}>12 BCF 1</option>
                            <option value="12 BCF 2" {{ old('classroom') == '12 BCF 2' ? 'selected' : '' }}>12 BCF 2</option>
                            <option value="12 BCF 3" {{ old('classroom') == '12 BCF 3' ? 'selected' : '' }}>12 BCF 3</option>
                        </optgroup>

                        <optgroup label="TPFL (Teknik Pengelasan & Fabrikasi Logam)" x-show="accountType === 'siswa'">
                            <option value="10 TPFL 1" {{ old('classroom') == '10 TPFL 1' ? 'selected' : '' }}>10 TPFL 1</option>
                            <option value="10 TPFL 2" {{ old('classroom') == '10 TPFL 2' ? 'selected' : '' }}>10 TPFL 2</option>
                            <option value="10 TPFL 3" {{ old('classroom') == '10 TPFL 3' ? 'selected' : '' }}>10 TPFL 3</option>
                            <option value="11 TPFL 1" {{ old('classroom') == '11 TPFL 1' ? 'selected' : '' }}>11 TPFL 1</option>
                            <option value="11 TPFL 2" {{ old('classroom') == '11 TPFL 2' ? 'selected' : '' }}>11 TPFL 2</option>
                            <option value="11 TPFL 3" {{ old('classroom') == '11 TPFL 3' ? 'selected' : '' }}>11 TPFL 3</option>
                            <option value="12 TPFL 1" {{ old('classroom') == '12 TPFL 1' ? 'selected' : '' }}>12 TPFL 1</option>
                            <option value="12 TPFL 2" {{ old('classroom') == '12 TPFL 2' ? 'selected' : '' }}>12 TPFL 2</option>
                            <option value="12 TPFL 3" {{ old('classroom') == '12 TPFL 3' ? 'selected' : '' }}>12 TPFL 3</option>
                        </optgroup>

                        <optgroup label="Animasi" x-show="accountType === 'siswa'">
                            <option value="10 Animasi 1" {{ old('classroom') == '10 Animasi 1' ? 'selected' : '' }}>10 Animasi 1</option>
                            <option value="10 Animasi 2" {{ old('classroom') == '10 Animasi 2' ? 'selected' : '' }}>10 Animasi 2</option>
                            <option value="10 Animasi 3" {{ old('classroom') == '10 Animasi 3' ? 'selected' : '' }}>10 Animasi 3</option>
                            <option value="11 Animasi 1" {{ old('classroom') == '11 Animasi 1' ? 'selected' : '' }}>11 Animasi 1</option>
                            <option value="11 Animasi 2" {{ old('classroom') == '11 Animasi 2' ? 'selected' : '' }}>11 Animasi 2</option>
                            <option value="11 Animasi 3" {{ old('classroom') == '11 Animasi 3' ? 'selected' : '' }}>11 Animasi 3</option>
                            <option value="12 Animasi 1" {{ old('classroom') == '12 Animasi 1' ? 'selected' : '' }}>12 Animasi 1</option>
                            <option value="12 Animasi 2" {{ old('classroom') == '12 Animasi 2' ? 'selected' : '' }}>12 Animasi 2</option>
                            <option value="12 Animasi 3" {{ old('classroom') == '12 Animasi 3' ? 'selected' : '' }}>12 Animasi 3</option>
                        </optgroup>
                    </select>
                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none" style="color: var(--text-muted);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </span>
                </div>
                @error('classroom')
                    <p class="mt-1 text-[11px] flex items-center gap-1" style="color: var(--status-danger);">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>
                @error('classroom')
                    <p class="mt-1 text-[11px] flex items-center gap-1" style="color: var(--status-danger);">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            {{-- Input 3: Email Siswa --}}
            <div class="mb-3">
                <label for="email" class="block text-xs font-semibold mb-1" style="color: var(--text-secondary);">
                    Email Siswa <span style="color: var(--status-danger);">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none" style="color: var(--text-muted);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </span>
                    <input type="email"
                           name="email"
                           id="email"
                           x-model="emailInput"
                           value="{{ old('email') }}"
                           required
                           class="w-full pl-9 pr-3 py-2 text-xs sm:text-sm rounded-xl border outline-none transition-all focus:ring-2 focus:ring-orange-500/30"
                           style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                           placeholder="nama@email.com">
                </div>
                @error('email')
                    <p class="mt-1 text-[11px] flex items-center gap-1" style="color: var(--status-danger);">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            {{-- Baris 3: Kata Sandi & Ulangi Kata Sandi (2 Kolom di Desktop/Tablet) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3 mb-3.5">
                {{-- Kata Sandi --}}
                <div>
                    <label for="password" class="block text-xs font-semibold mb-1" style="color: var(--text-secondary);">
                        Kata Sandi <span style="color: var(--status-danger);">*</span>
                    </label>
                    <div class="relative flex items-center">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none" style="color: var(--text-muted);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </span>
                        <input :type="showPassword ? 'text' : 'password'"
                               name="password"
                               id="password"
                               x-model="passwordInput"
                               required
                               class="w-full pl-9 pr-9 py-2 text-xs sm:text-sm rounded-xl border outline-none transition-all focus:ring-2 focus:ring-orange-500/30"
                               style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                               placeholder="Min. 8 karakter">
                        <button type="button"
                                tabindex="-1"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center transition-colors hover:opacity-80"
                                style="color: var(--text-muted);"
                                title="Tampilkan / Sembunyikan Sandi">
                            <svg x-show="!showPassword" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            <svg x-show="showPassword" x-cloak class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-[11px] flex items-center gap-1" style="color: var(--status-danger);">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                {{-- Konfirmasi Kata Sandi --}}
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold mb-1" style="color: var(--text-secondary);">
                        Ulangi Sandi <span style="color: var(--status-danger);">*</span>
                    </label>
                    <div class="relative flex items-center">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none" style="color: var(--text-muted);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </span>
                        <input :type="showConfirmPassword ? 'text' : 'password'"
                               name="password_confirmation"
                               id="password_confirmation"
                               x-model="confirmPasswordInput"
                               required
                               class="w-full pl-9 pr-9 py-2 text-xs sm:text-sm rounded-xl border outline-none transition-all focus:ring-2 focus:ring-orange-500/30"
                               style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                               placeholder="Ulangi sandi">
                        <button type="button"
                                tabindex="-1"
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center transition-colors hover:opacity-80"
                                style="color: var(--text-muted);"
                                title="Tampilkan / Sembunyikan Sandi">
                            <svg x-show="!showConfirmPassword" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            <svg x-show="showConfirmPassword" x-cloak class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <button type="submit"
                    id="btn-register-submit"
                    class="w-full btn-brand py-2.5 sm:py-3 text-xs sm:text-sm font-bold uppercase tracking-wider justify-center shadow-md shadow-orange-500/20 active:scale-[0.99] transition-all mb-3 cursor-pointer">
                <span x-text="accountType === 'guru' ? 'Daftar Akun Guru / Staf' : 'Daftar Akun Siswa'"></span>
            </button>
        </form>



        <div class="text-center text-xs space-y-1.5 border-t pt-3" style="border-color: var(--border-default); color: var(--text-muted);">
            <p>
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-bold hover:underline" style="color: var(--brand);">
                    Masuk ke Akun Pembeli &rarr;
                </a>
            </p>
            <p>
                <a href="{{ route('login.admin') }}" class="hover:underline transition-colors font-medium text-[11px]" style="color: var(--text-secondary);">
                    Login Pengelola Stand / Admin &rarr;
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
