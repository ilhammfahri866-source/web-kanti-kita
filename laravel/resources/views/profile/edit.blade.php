@extends('layouts.app')

@section('title', 'Profil Saya - KantinKita')
@section('meta_description', 'Kelola profil akun dan kata sandi kamu di KantinKita.')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8 space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-2">
        <a href="{{ route('home') }}" class="p-2 rounded-xl border transition-colors hover:opacity-80"
           style="background-color: var(--bg-surface-2); border-color: var(--border-default); color: var(--text-secondary);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight" style="color: var(--text-primary);">Profil Saya</h1>
            <p class="text-xs mt-0.5" style="color: var(--text-muted);">Kelola informasi akun dan keamanan kamu</p>
        </div>
    </div>

    {{-- Avatar Card --}}
    <div class="rounded-2xl border p-5 flex items-center gap-4"
         style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-2xl font-extrabold shrink-0 shadow-md"
             style="background: linear-gradient(135deg, var(--brand), var(--brand-dark, #5a3a1a)); color: #fff;">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-bold text-lg truncate" style="color: var(--text-primary);">{{ $user->name }}</p>
            <p class="text-xs truncate" style="color: var(--text-muted);">{{ $user->email }}</p>
            <span class="mt-1.5 inline-block text-[0.65rem] font-bold uppercase tracking-widest px-2 py-0.5 rounded-full"
                  style="background-color: var(--brand-subtle); color: var(--brand); border: 1px solid var(--brand-border);">
                {{ match($user->role) { 'admin' => 'Admin', 'tenant' => 'Tenant', default => 'Siswa' } }}
            </span>
        </div>
        @if($user->classroom)
        <div class="text-right shrink-0">
            <p class="text-xs" style="color: var(--text-muted);">Kelas</p>
            <p class="font-bold text-sm" style="color: var(--text-primary);">{{ $user->classroom }}</p>
        </div>
        @endif
    </div>

    {{-- Form Edit Profil --}}
    <div class="rounded-2xl border p-5 sm:p-6"
         style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
        <h2 class="text-base font-bold mb-4 pb-3 border-b flex items-center gap-2"
            style="color: var(--text-primary); border-color: var(--border-default);">
            Informasi Akun
        </h2>

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Nama --}}
                <div>
                    <label for="name" class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">
                        Nama Lengkap <span style="color: var(--status-danger);">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-3 py-2.5 text-sm rounded-xl border outline-none transition-colors"
                        style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                        placeholder="Nama lengkap kamu">
                    @error('name')
                        <p class="mt-1 text-xs" style="color: var(--status-danger);">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email (readonly) --}}
                <div>
                    <label class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">Email</label>
                    <input type="email" value="{{ $user->email }}" disabled
                        class="w-full px-3 py-2.5 text-sm rounded-xl border outline-none opacity-60 cursor-not-allowed"
                        style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                    <p class="mt-1 text-[0.68rem]" style="color: var(--text-muted);">Email tidak bisa diubah</p>
                </div>

                {{-- Kelas & Jurusan / Ruangan Guru --}}
                <div>
                    <label for="classroom" class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">
                        {{ $user->isTeacher() ? 'Ruangan / Posisi Guru' : 'Kelas & Jurusan' }}
                    </label>
                    <div class="relative">
                        <select
                            name="classroom"
                            id="classroom"
                            class="w-full pl-3 pr-8 py-2.5 text-sm rounded-xl border outline-none transition-colors cursor-pointer appearance-none"
                            style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                        >
                            <option value="" disabled {{ empty($user->classroom) ? 'selected' : '' }}>-- Pilih Kelas / Ruangan --</option>

                            <optgroup label="Guru & Tenaga Pendidik / Staf">
                                <option value="Guru (Ruang Guru)" {{ old('classroom', $user->classroom) == 'Guru (Ruang Guru)' ? 'selected' : '' }}>Guru (Ruang Guru)</option>
                                <option value="Guru (Perpustakaan)" {{ old('classroom', $user->classroom) == 'Guru (Perpustakaan)' ? 'selected' : '' }}>Guru (Perpustakaan)</option>
                                <option value="Guru (Lab Komputer)" {{ old('classroom', $user->classroom) == 'Guru (Lab Komputer)' ? 'selected' : '' }}>Guru (Lab Komputer)</option>
                                <option value="Guru (Ruang Piket)" {{ old('classroom', $user->classroom) == 'Guru (Ruang Piket)' ? 'selected' : '' }}>Guru (Ruang Piket)</option>
                                <option value="Guru (Ruang TU / Tata Usaha)" {{ old('classroom', $user->classroom) == 'Guru (Ruang TU / Tata Usaha)' ? 'selected' : '' }}>Guru (Ruang TU / Tata Usaha)</option>
                                <option value="Guru (Ruang Kepala Sekolah)" {{ old('classroom', $user->classroom) == 'Guru (Ruang Kepala Sekolah)' ? 'selected' : '' }}>Guru (Ruang Kepala Sekolah)</option>
                                <option value="Guru (Bengkel Praktek)" {{ old('classroom', $user->classroom) == 'Guru (Bengkel Praktek)' ? 'selected' : '' }}>Guru (Bengkel Praktek)</option>
                                <option value="Guru (Ruang Kelas / Mengajar)" {{ old('classroom', $user->classroom) == 'Guru (Ruang Kelas / Mengajar)' ? 'selected' : '' }}>Guru (Ruang Kelas / Mengajar)</option>
                                <option value="Guru / Staf Karyawan" {{ old('classroom', $user->classroom) == 'Guru / Staf Karyawan' ? 'selected' : '' }}>Guru / Staf Karyawan</option>
                            </optgroup>

                            <optgroup label="TO (Teknik Otomotif)">
                                <option value="10 TO 1" {{ old('classroom', $user->classroom) == '10 TO 1' ? 'selected' : '' }}>10 TO 1</option>
                                <option value="10 TO 2" {{ old('classroom', $user->classroom) == '10 TO 2' ? 'selected' : '' }}>10 TO 2</option>
                                <option value="10 TO 3" {{ old('classroom', $user->classroom) == '10 TO 3' ? 'selected' : '' }}>10 TO 3</option>
                                <option value="11 TO 1" {{ old('classroom', $user->classroom) == '11 TO 1' ? 'selected' : '' }}>11 TO 1</option>
                                <option value="11 TO 2" {{ old('classroom', $user->classroom) == '11 TO 2' ? 'selected' : '' }}>11 TO 2</option>
                                <option value="11 TO 3" {{ old('classroom', $user->classroom) == '11 TO 3' ? 'selected' : '' }}>11 TO 3</option>
                                <option value="12 TO 1" {{ old('classroom', $user->classroom) == '12 TO 1' ? 'selected' : '' }}>12 TO 1</option>
                                <option value="12 TO 2" {{ old('classroom', $user->classroom) == '12 TO 2' ? 'selected' : '' }}>12 TO 2</option>
                                <option value="12 TO 3" {{ old('classroom', $user->classroom) == '12 TO 3' ? 'selected' : '' }}>12 TO 3</option>
                            </optgroup>

                            <optgroup label="PPLG (Pengembangan Perangkat Lunak & Gim)">
                                <option value="10 PPLG 1" {{ old('classroom', $user->classroom) == '10 PPLG 1' ? 'selected' : '' }}>10 PPLG 1</option>
                                <option value="10 PPLG 2" {{ old('classroom', $user->classroom) == '10 PPLG 2' ? 'selected' : '' }}>10 PPLG 2</option>
                                <option value="10 PPLG 3" {{ old('classroom', $user->classroom) == '10 PPLG 3' ? 'selected' : '' }}>10 PPLG 3</option>
                                <option value="11 PPLG 1" {{ old('classroom', $user->classroom) == '11 PPLG 1' ? 'selected' : '' }}>11 PPLG 1</option>
                                <option value="11 PPLG 2" {{ old('classroom', $user->classroom) == '11 PPLG 2' ? 'selected' : '' }}>11 PPLG 2</option>
                                <option value="11 PPLG 3" {{ old('classroom', $user->classroom) == '11 PPLG 3' ? 'selected' : '' }}>11 PPLG 3</option>
                                <option value="12 PPLG 1" {{ old('classroom', $user->classroom) == '12 PPLG 1' ? 'selected' : '' }}>12 PPLG 1</option>
                                <option value="12 PPLG 2" {{ old('classroom', $user->classroom) == '12 PPLG 2' ? 'selected' : '' }}>12 PPLG 2</option>
                                <option value="12 PPLG 3" {{ old('classroom', $user->classroom) == '12 PPLG 3' ? 'selected' : '' }}>12 PPLG 3</option>
                            </optgroup>

                            <optgroup label="BCF (Broadcasting & Perfilman)">
                                <option value="10 BCF 1" {{ old('classroom', $user->classroom) == '10 BCF 1' ? 'selected' : '' }}>10 BCF 1</option>
                                <option value="10 BCF 2" {{ old('classroom', $user->classroom) == '10 BCF 2' ? 'selected' : '' }}>10 BCF 2</option>
                                <option value="10 BCF 3" {{ old('classroom', $user->classroom) == '10 BCF 3' ? 'selected' : '' }}>10 BCF 3</option>
                                <option value="11 BCF 1" {{ old('classroom', $user->classroom) == '11 BCF 1' ? 'selected' : '' }}>11 BCF 1</option>
                                <option value="11 BCF 2" {{ old('classroom', $user->classroom) == '11 BCF 2' ? 'selected' : '' }}>11 BCF 2</option>
                                <option value="11 BCF 3" {{ old('classroom', $user->classroom) == '11 BCF 3' ? 'selected' : '' }}>11 BCF 3</option>
                                <option value="12 BCF 1" {{ old('classroom', $user->classroom) == '12 BCF 1' ? 'selected' : '' }}>12 BCF 1</option>
                                <option value="12 BCF 2" {{ old('classroom', $user->classroom) == '12 BCF 2' ? 'selected' : '' }}>12 BCF 2</option>
                                <option value="12 BCF 3" {{ old('classroom', $user->classroom) == '12 BCF 3' ? 'selected' : '' }}>12 BCF 3</option>
                            </optgroup>

                            <optgroup label="TPFL (Teknik Pengelasan & Fabrikasi Logam)">
                                <option value="10 TPFL 1" {{ old('classroom', $user->classroom) == '10 TPFL 1' ? 'selected' : '' }}>10 TPFL 1</option>
                                <option value="10 TPFL 2" {{ old('classroom', $user->classroom) == '10 TPFL 2' ? 'selected' : '' }}>10 TPFL 2</option>
                                <option value="10 TPFL 3" {{ old('classroom', $user->classroom) == '10 TPFL 3' ? 'selected' : '' }}>10 TPFL 3</option>
                                <option value="11 TPFL 1" {{ old('classroom', $user->classroom) == '11 TPFL 1' ? 'selected' : '' }}>11 TPFL 1</option>
                                <option value="11 TPFL 2" {{ old('classroom', $user->classroom) == '11 TPFL 2' ? 'selected' : '' }}>11 TPFL 2</option>
                                <option value="11 TPFL 3" {{ old('classroom', $user->classroom) == '11 TPFL 3' ? 'selected' : '' }}>11 TPFL 3</option>
                                <option value="12 TPFL 1" {{ old('classroom', $user->classroom) == '12 TPFL 1' ? 'selected' : '' }}>12 TPFL 1</option>
                                <option value="12 TPFL 2" {{ old('classroom', $user->classroom) == '12 TPFL 2' ? 'selected' : '' }}>12 TPFL 2</option>
                                <option value="12 TPFL 3" {{ old('classroom', $user->classroom) == '12 TPFL 3' ? 'selected' : '' }}>12 TPFL 3</option>
                            </optgroup>

                            <optgroup label="Animasi">
                                <option value="10 Animasi 1" {{ old('classroom', $user->classroom) == '10 Animasi 1' ? 'selected' : '' }}>10 Animasi 1</option>
                                <option value="10 Animasi 2" {{ old('classroom', $user->classroom) == '10 Animasi 2' ? 'selected' : '' }}>10 Animasi 2</option>
                                <option value="10 Animasi 3" {{ old('classroom', $user->classroom) == '10 Animasi 3' ? 'selected' : '' }}>10 Animasi 3</option>
                                <option value="11 Animasi 1" {{ old('classroom', $user->classroom) == '11 Animasi 1' ? 'selected' : '' }}>11 Animasi 1</option>
                                <option value="11 Animasi 2" {{ old('classroom', $user->classroom) == '11 Animasi 2' ? 'selected' : '' }}>11 Animasi 2</option>
                                <option value="11 Animasi 3" {{ old('classroom', $user->classroom) == '11 Animasi 3' ? 'selected' : '' }}>11 Animasi 3</option>
                                <option value="12 Animasi 1" {{ old('classroom', $user->classroom) == '12 Animasi 1' ? 'selected' : '' }}>12 Animasi 1</option>
                                <option value="12 Animasi 2" {{ old('classroom', $user->classroom) == '12 Animasi 2' ? 'selected' : '' }}>12 Animasi 2</option>
                                <option value="12 Animasi 3" {{ old('classroom', $user->classroom) == '12 Animasi 3' ? 'selected' : '' }}>12 Animasi 3</option>
                            </optgroup>
                        </select>
                        <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-xs" style="color: var(--text-muted);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </span>
                    </div>
                    @error('classroom')
                        <p class="mt-1 text-xs" style="color: var(--status-danger);">{{ $message }}</p>
                    @enderror
                </div>

                {{-- No HP --}}
                <div>
                    <label for="phone" class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">
                        No. HP (opsional)
                    </label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                        class="w-full px-3 py-2.5 text-sm rounded-xl border outline-none transition-colors"
                        style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                        placeholder="Contoh: 08123456789">
                    @error('phone')
                        <p class="mt-1 text-xs" style="color: var(--status-danger);">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="btn-brand px-6 py-2.5 text-xs font-bold rounded-xl flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Ganti Password --}}
    <div class="rounded-2xl border p-5 sm:p-6" id="password-section"
         style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
        <h2 class="text-base font-bold mb-4 pb-3 border-b flex items-center gap-2"
            style="color: var(--text-primary); border-color: var(--border-default);">
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <span>Ganti Kata Sandi</span>
        </h2>

        <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label for="current_password" class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">
                    Kata Sandi Saat Ini <span style="color: var(--status-danger);">*</span>
                </label>
                <input type="password" name="current_password" id="current_password" required
                    class="w-full px-3 py-2.5 text-sm rounded-xl border outline-none transition-colors"
                    style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                    placeholder="••••••••">
                @error('current_password')
                    <p class="mt-1 text-xs" style="color: var(--status-danger);">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">
                        Kata Sandi Baru <span style="color: var(--status-danger);">*</span>
                    </label>
                    <input type="password" name="password" id="password" required
                        class="w-full px-3 py-2.5 text-sm rounded-xl border outline-none transition-colors"
                        style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                        placeholder="Minimal 8 karakter">
                    @error('password')
                        <p class="mt-1 text-xs" style="color: var(--status-danger);">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">
                        Konfirmasi Kata Sandi Baru <span style="color: var(--status-danger);">*</span>
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full px-3 py-2.5 text-sm rounded-xl border outline-none transition-colors"
                        style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                        placeholder="Ulangi kata sandi baru">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="btn-secondary px-6 py-2.5 text-xs font-bold rounded-xl flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    <span>Ganti Kata Sandi</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Riwayat Pesanan Terakhir --}}
    @if($orders->isNotEmpty())
    <div class="rounded-2xl border p-5 sm:p-6"
         style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
        <div class="flex items-center justify-between mb-4 pb-3 border-b" style="border-color: var(--border-default);">
            <h2 class="text-base font-bold flex items-center gap-2" style="color: var(--text-primary);">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                <span>Pesanan Terakhir</span>
            </h2>
            <a href="{{ route('orders.index') }}" class="text-xs font-semibold hover:underline" style="color: var(--brand);">
                Lihat Semua →
            </a>
        </div>
        <div class="space-y-3">
            @foreach($orders as $order)
            <div class="flex items-center justify-between p-3 rounded-xl border"
                 style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                <div class="min-w-0">
                    <p class="text-xs font-mono font-bold truncate" style="color: var(--text-primary);">{{ $order->order_number }}</p>
                    <p class="text-[0.68rem] mt-0.5" style="color: var(--text-muted);">{{ $order->created_at->format('d M Y') }} · {{ $order->items->count() }} item</p>
                    @if($order->status === 'cancelled' && $order->cancel_reason)
                        <p class="text-[0.65rem] mt-1 font-medium truncate max-w-xs" style="color: var(--status-danger);">⚠️ {{ $order->cancel_reason }}</p>
                    @endif
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <span class="text-sm font-bold" style="color: var(--brand);">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    @php
                        $pillClass = match($order->status) {
                            'pending'   => 'status-pill status-pill-pending',
                            'confirmed' => 'status-pill status-pill-ready',
                            'preparing' => 'status-pill status-pill-processing',
                            'ready'     => 'status-pill status-pill-ready',
                            'completed' => 'status-pill status-pill-completed',
                            'cancelled' => 'status-pill status-pill-cancelled',
                            default     => 'status-pill',
                        };
                    @endphp
                    <span class="{{ $pillClass }} text-[0.65rem]">{{ $order->status_label }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
