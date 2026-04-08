<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * FORM LOGIN
     */
    public function showLogin()
    {
        return view('pages.auth.login');
    }

    /**
     * PROSES LOGIN
     */
    public function login(Request $request)
    {
        // ✅ VALIDASI INPUT
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email'    => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
        ]);

        // 🔥 Ambil user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // ❌ Jika email tidak ditemukan
        if (!$user) {
            return back()->withErrors([
                'email' => 'Email tidak terdaftar',
            ])->onlyInput('email');
        }

        // ❌ Jika password salah
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Password salah',
            ])->onlyInput('email');
        }

        // 🔥 Hapus session lama
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // ✅ Login manual
        Auth::login($user);
        $request->session()->regenerate();

        $role = strtoupper(trim($user->roles));

        // 🔥 Redirect berdasarkan role
        if ($role === 'ADMIN') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('user.dashboard');
    }

    /**
     * FORM REGISTER
     */
    public function showRegister()
    {
        return view('pages.auth.register');
    }

    /**
     * PROSES REGISTER
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.unique' => 'Email sudah digunakan',
            'password.required' => 'Password wajib diisi',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone ?? '',
            'roles'    => 'USER',
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')->with('success', 'Register berhasil');
    }

    /**
     * LOGOUT
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}