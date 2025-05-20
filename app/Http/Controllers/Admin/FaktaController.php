<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fakta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class FaktaController extends Controller
{
    /**
     * Menampilkan daftar semua fakta.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        $fakta = Fakta::all();
        return view('admin.fakta.index', compact('fakta'));
    }

    /**
     * Menampilkan form untuk membuat fakta baru.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function create()
    {
        $faktaList = Fakta::select('id', 'kode', 'pertanyaan')->orderBy('kode')->get();

        return view('admin.fakta.create', compact('faktaList'));
    }

    /**
     * Menyimpan fakta baru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:10|unique:fakta,kode',
            'pertanyaan' => 'nullable|string', // Pertanyaan bisa null jika is_askable false
            'deskripsi' => 'required|string',
            'kategori' => 'required|in:risiko_osteoporosis,asupan_nutrisi,preferensi_makanan',
            'is_first' => 'nullable|boolean',
            'is_askable' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
        ]);

        Fakta::create([
            'kode' => $validated['kode'],
            'pertanyaan' => $validated['is_askable'] ? ($validated['pertanyaan'] ?? null) : null,
            'deskripsi' => $validated['deskripsi'],
            'kategori' => $validated['kategori'],
            'is_first' => $request->has('is_first'),
            'is_askable' => $request->has('is_askable'),
            'is_default' => $request->has('is_default'),
        ]);

        return redirect()->route('admin.basisPengetahuan.fakta.index')
            ->with('success', 'Fakta berhasil disimpan.');
    }
    /**
     * Menampilkan detail fakta.
     *
     * @param  \App\Models\Fakta  $fakta
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function show()
    {
        return view('admin.fakta.show');
    }

    /**
     * Menampilkan form untuk mengedit fakta.
     *
     * @param  \App\Models\Fakta  $fakta
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function edit($id)
    {
        $fakta = Fakta::findOrFail($id);
        $allFakta = Fakta::where('id', '!=', $id)->get(); // Hindari referensi ke dirinya sendiri

        return view('admin.fakta.edit', compact('fakta', 'allFakta'));
    }


    /**
     * Memperbarui informasi fakta.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Fakta  $fakta
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Fakta $faktum)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:10|unique:fakta,kode,' . $faktum->id,
            'deskripsi' => 'required|string',
            'kategori' => 'required|in:risiko_osteoporosis,asupan_nutrisi,preferensi_makanan',
            'pertanyaan' => 'nullable|string', // Bisa null jika is_askable false
            'is_first' => 'nullable|boolean',
            'is_askable' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
        ]);

        $faktum->update([
            'kode' => $validated['kode'],
            'deskripsi' => $validated['deskripsi'],
            'kategori' => $validated['kategori'],
            'pertanyaan' => $validated['is_askable'] ? ($validated['pertanyaan'] ?? null) : null,
            'is_first' => $request->has('is_first'),
            'is_askable' => $request->has('is_askable'),
            'is_default' => $request->has('is_default'),
        ]);

        return redirect()->route('admin.basisPengetahuan.fakta.index')->with('success', 'Fakta berhasil diperbarui.');
    }

    /**
     * Menghapus fakta.
     *
     * @param  \App\Models\Fakta  $fakta
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Fakta $faktum)
    {
        // Menghapus data Fakta
        $faktum->delete();

        // Redirect kembali ke daftar fakta dengan pesan sukses
        return redirect()->route('admin.basisPengetahuan.fakta.index')
            ->with('success', 'Fakta berhasil dihapus.');
    }


    /**
     * Mengurutkan ulang fakta (jika diperlukan).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorder(Request $request)
    {
        // Implementasi logika pengurutan ulang fakta jika diperlukan
        return response()->json(['info' => 'Fitur reorder fakta belum diimplementasikan.']);
    }
}
