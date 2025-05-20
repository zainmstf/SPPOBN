<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Solusi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SolusiController extends Controller
{
    /**
     * Menampilkan daftar semua solusi.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        $solusis = Solusi::all(); // Mengambil seluruh data solusi
        return view('admin.solusi.index', compact('solusis'));
    }

    /**
     * Menampilkan form untuk membuat solusi baru.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function create()
    {
        return view('admin.solusi.create');
    }

    /**
     * Menyimpan solusi baru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi data
        $validated = $request->validate([
            'kode' => 'required|string|max:255|unique:solusi,kode',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'peringatan_konsultasi' => 'nullable|string',
            'is_default' => 'nullable|boolean', // Tambahkan validasi untuk is_default
        ]);

        // Simpan data solusi baru
        Solusi::create([
            'kode' => $validated['kode'],
            'nama' => $validated['nama'],
            'deskripsi' => $validated['deskripsi'],
            'peringatan_konsultasi' => $validated['peringatan_konsultasi'] ?? null,
            'is_default' => $request->has('is_default') ? 1 : 0, // Simpan nilai is_default
        ]);

        // Redirect ke halaman daftar solusi dengan pesan sukses
        return redirect()->route('admin.basisPengetahuan.solusi.index')->with('success', 'Solusi berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail solusi.
     *
     * @param  \App\Models\Solusi  $solusi
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function show(Solusi $solusi)
    {
        $solusi->load('rekomendasiNutrisi.sumberNutrisi');
        return view('admin.solusi.show', compact('solusi'));
    }

    /**
     * Menampilkan form untuk mengedit solusi.
     *
     * @param  \App\Models\Solusi  $solusi
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function edit($id)
    {
        // Mencari data solusi berdasarkan ID
        $solusi = Solusi::findOrFail($id);

        // Mengembalikan view edit dengan membawa data solusi yang ditemukan
        return view('admin.solusi.edit', compact('solusi'));
    }


    /**
     * Memperbarui informasi solusi.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Solusi  $solusi
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Validasi data yang diterima dari form
        $validated = $request->validate([
            'kode' => 'required|string|max:255|unique:solusi,kode,' . $id,
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'peringatan_konsultasi' => 'nullable|string',
            'is_default' => 'nullable|boolean', // Tambahkan validasi untuk is_default
        ]);

        // Mencari data solusi yang akan diupdate
        $solusi = Solusi::findOrFail($id);

        // Menyimpan perubahan pada solusi
        $solusi->update([
            'kode' => $validated['kode'],
            'nama' => $validated['nama'],
            'deskripsi' => $validated['deskripsi'],
            'peringatan_konsultasi' => $validated['peringatan_konsultasi'] ?? null,
            'is_default' => $request->has('is_default') ? 1 : 0, // Simpan nilai is_default
        ]);

        // Redirect ke halaman daftar solusi dengan pesan sukses
        return redirect()->route('admin.basisPengetahuan.solusi.index')
            ->with('success', 'Solusi berhasil diperbarui!');
    }

    /**
     * Menghapus solusi.
     *
     * @param  \App\Models\Solusi  $solusi
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Solusi $solusi)
    {
        $solusi->delete();
        return redirect()->route('admin.basisPengetahuan.solusi.index')->with('success', 'Solusi berhasil dihapus.');
    }
}
