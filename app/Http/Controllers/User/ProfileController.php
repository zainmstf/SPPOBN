<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Show the user's profile.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function show()
    {
        $user = Auth::user();
        return view('user.profile.show', compact('user'));
    }

    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user(); // Mengambil pengguna yang sedang login

        // Validasi data yang masuk
        $request->validate([
            'nama' => 'required|string|max:100',
            'alamat' => 'required|string',
            'no_telp' => 'required|string|max:15',
        ]);

        // Update data pengguna
        $user->update([
            'nama_lengkap' => $request->nama,
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telp,
        ]);

        return redirect()->route('profile.show')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'current_password' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('Password saat ini salah.');
                    }
                }
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return redirect()->route('profile.show')->with('success', 'Password berhasil diubah.');
    }

    /**
     * Menampilkan halaman pengaturan profil.
     */
    public function pengaturan()
    {
        $user = Auth::user();
        return view('user.profile.pengaturan', compact('user'));

    }
}