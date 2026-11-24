<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman edit profil.
     */
    public function edit()
    {
        $user = auth()->user();
        $orders = $user->orders()->with(['items', 'tenant'])->latest()->take(5)->get();

        return view('profile.edit', compact('user', 'orders'));
    }

    /**
     * Update informasi profil (nama, kelas, no HP).
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'classroom' => ['nullable', 'string', 'max:50'],
            'phone'     => ['nullable', 'string', 'max:20'],
        ]);

        $user->update([
            'name'      => $request->name,
            'classroom' => $request->classroom,
            'phone'     => $request->phone,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Ganti kata sandi.
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password'      => ['required'],
            'password'              => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak cocok.'])->withFragment('password-section');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Kata sandi berhasil diperbarui!');
    }
}
