<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Konsultasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            Konsultasi::where('user_id', Auth::id())
                ->where('status', 'sedang_berjalan')
                ->update(['status' => 'belum_selesai']);

            if (Auth::user()->role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            } else {
                return redirect()->intended('/user/dashboard');
            }
        }

        throw ValidationException::withMessages([
            'username' => ['Login gagal. Periksa kembali username dan password Anda.'],
        ]);
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        // Validasi data input
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
            'nama_lengkap' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users,email',
            'no_telp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
        ]);

        // Cek apakah validasi gagal
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Simpan data user ke database
        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password), // Enkripsi password
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'no_telepon' => $request->no_telp,
            'alamat' => $request->alamat,
            'role' => 'user', // Default role sebagai user
        ]);

        // Redirect atau beri respon sukses
        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan masuk.');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        try {
            if (Auth::check()) {
                Konsultasi::where('user_id', Auth::id())
                    ->where('status', 'sedang_berjalan')
                    ->update(['status' => 'belum_selesai']);
            }

            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat logout: ' . $e->getMessage());
        }
    }
}