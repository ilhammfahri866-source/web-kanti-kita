<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan Halaman Login Pembeli (Siswa & Guru)
     */
    public function showCustomerLogin(Request $request)
    {
        if ($request->query('redirect') === 'cart' || $request->query('intended') === 'cart') {
            session()->put('url.intended', route('cart.index'));
        }
        return view('auth.login-pembeli');
    }

    /**
     * Proses Login Pembeli (Siswa & Guru)
     */
    public function loginCustomer(Request $request)
    {
        $input = trim((string)$request->input('email'));
        $lower = strtolower($input);

        // Alias cepat untuk pengujian akun demo
        if (in_array($lower, ['guru', 'pak guru', 'bu guru', 'ahmad', 'pak ahmad'])) {
            $email = 'guru@ekantin.test';
        } elseif (in_array($lower, ['siswa', 'pelanggan', 'budi', 'budi santoso'])) {
            $email = 'pelanggan@ekantin.test';
        } elseif ($lower === 'admin') {
            $email = 'admin123@gmail.com';
        } elseif ($lower === 'berkah' || $lower === 'tenant') {
            $email = 'berkah@ekantin.test';
        } elseif (!filter_var($input, FILTER_VALIDATE_EMAIL)) {
            // Jika pengguna mengetik nama lengkap bukan email
            $userByName = User::where('name', $input)->first();
            $email = $userByName ? $userByName->email : $input;
        } else {
            $email = $input;
        }

        $credentials = [
            'email'    => $email,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Jika admin/tenant masuk lewat login pembeli, alihkan dengan aman ke dashboard
            if ($user->role === 'admin' || $user->role === 'tenant') {
                return redirect()->intended('/dashboard')->with('success', 'Berhasil masuk! Akun pengelola dialihkan ke Dashboard.');
            }

            return redirect()->intended('/')->with('success', 'Selamat datang, ' . $user->name . '! Mau pesan menu apa hari ini?');
        }

        return back()->withErrors([
            'email' => 'Email/Nama atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Tampilkan Halaman Login Khusus Admin & Tenant (Pengelola Kantin)
     */
    public function showAdminLogin(Request $request)
    {
        return view('auth.login-admin');
    }

    /**
     * Proses Login Khusus Admin & Tenant
     */
    public function loginAdmin(Request $request)
    {
        $input = trim((string)$request->input('email'));
        $lower = strtolower($input);

        // Alias pengujian demo admin & tenant
        if ($lower === 'admin') {
            $email = 'admin123@gmail.com';
        } elseif ($lower === 'tenant' || $lower === 'berkah') {
            $email = 'berkah@ekantin.test';
        } else {
            $email = $input;
        }

        $credentials = [
            'email'    => $email,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // Verifikasi bahwa akun benar-benar memiliki hak akses admin/tenant
            if ($user->role === 'admin' || $user->role === 'tenant') {
                $request->session()->regenerate();
                return redirect()->intended('/dashboard')->with('success', 'Selamat datang di Panel Pengelola, ' . $user->name . '!');
            }

            // Jika pembeli (siswa/guru) mencoba masuk dari login admin, tolak dengan pesan jelas
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Akun Anda adalah akun Pembeli (Siswa/Guru). Halaman ini khusus untuk Pengelola Kantin & Admin.',
            ])->with('is_customer_alert', true)->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi pengelola salah.',
        ])->onlyInput('email');
    }

    // Helper kompatibilitas lama
    public function showLogin(Request $request)
    {
        return $this->showCustomerLogin($request);
    }

    public function showStudentLogin(Request $request)
    {
        return $this->showCustomerLogin($request);
    }

    public function login(Request $request)
    {
        return $this->loginCustomer($request);
    }

    public function showRegister(Request $request)
    {
        if ($request->query('redirect') === 'cart' || $request->query('intended') === 'cart') {
            session()->put('url.intended', route('cart.index'));
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'classroom'    => ['required', 'string', 'max:50'],
            'password'     => ['required', 'string', 'min:8', 'confirmed'],
            'account_type' => ['nullable', 'string', 'in:siswa,guru'],
        ], [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email ini sudah terdaftar. Silakan masuk atau gunakan email lain.',
            'classroom.required' => 'Kelas (untuk siswa) atau Ruangan (untuk guru) wajib dipilih.',
            'password.required'  => 'Kata sandi wajib diisi.',
            'password.min'       => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $isTeacher = $request->account_type === 'guru' || str_starts_with(strtolower($request->classroom), 'guru');

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'classroom' => $request->classroom,
            'password'  => Hash::make($request->password),
            'role'      => 'customer',
        ]);

        Auth::login($user);

        $welcomeMsg = $isTeacher
            ? 'Selamat datang, ' . $user->name . '! Akun Guru / Staf Anda telah aktif dan siap digunakan.'
            : 'Selamat datang, ' . $user->name . '! Akun Siswa kamu berhasil dibuat dan langsung aktif.';

        return redirect()->intended(route('home'))->with('success', $welcomeMsg);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.siswa')->with('success', 'Anda telah keluar.');
    }
}
