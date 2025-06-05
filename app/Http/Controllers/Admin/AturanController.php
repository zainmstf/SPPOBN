<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aturan;
use App\Models\Fakta;
use App\Models\Solusi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AturanController extends Controller
{
    /**
     * Menampilkan daftar semua aturan.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        $aturan = Aturan::with('solusi')->get();
        $solusi = Solusi::all();
        $fakta = Fakta::where('is_askable', '0')->get();

        return view('admin.aturan.index', compact('aturan', 'solusi', 'fakta'));
    }

    /**
     * Menampilkan form untuk membuat aturan baru.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    // Controller
    public function create()
    {
        $fakta = Fakta::all(['id', 'kode', 'deskripsi'])->toArray();
        $solusi = Solusi::all(['id', 'kode', 'deskripsi'])->toArray();
        return view('admin.aturan.create', compact('fakta', 'solusi'));
    }


    /**
     * Menyimpan aturan baru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        // Validasi input dari form
        $request->validate([
            'kode' => 'required|string|max:255|unique:aturan,kode',
            'deskripsi' => 'required|string|max:255',
            'premis' => 'nullable|array',  // Multiple select premis
            'jenis_konklusi' => 'required|in:fakta,solusi',
            'konklusi' => 'required', // Konklusi bisa berupa id fakta atau solusi
            'is_active' => 'nullable|boolean', // Checkbox is_active
        ]);


        // Format premis menjadi F001^F002
        $premis = null;

        if (!empty($request->premis)) {
            $premis = implode('^', $request->premis);
        }

        // Tentukan konklusi berdasarkan jenis konklusi yang dipilih
        $konklusi = $request->konklusi;

        // Jika jenis konklusi adalah solusi, set solusi_id dari solusi yang memiliki kode sesuai dengan konklusi
        $solusi_id = null;
        if ($request->jenis_konklusi == 'solusi') {
            $solusi = Solusi::where('kode', $request->konklusi)->first();
            // Pastikan solusi ditemukan
            if ($solusi) {
                $solusi_id = $solusi->id; // Ambil ID solusi
            } else {
                // Jika solusi tidak ditemukan, beri feedback error
                return redirect()->back()->withErrors(['konklusi' => 'Solusi dengan kode tersebut tidak ditemukan.']);
            }
        }

        // Simpan aturan menggunakan metode create
        Aturan::create([
            'kode' => $request->kode,
            'deskripsi' => $request->deskripsi,
            'premis' => $premis,
            'konklusi' => $konklusi,
            'jenis_konklusi' => $request->jenis_konklusi,
            'solusi_id' => $solusi_id, // Hanya diset jika jenis konklusi adalah solusi
            'is_active' => $request->has('is_active') ? 1 : 0, // Tentukan nilai is_active
        ]);

        // Redirect dengan pesan sukses
        return redirect()->route('admin.basisPengetahuan.aturan.index')->with('success', 'Aturan berhasil disimpan.');
    }

    /**
     * Menampilkan form untuk mengedit aturan.
     *
     * @param  \App\Models\Aturan  $aturan
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function edit($id)
    {
        $aturan = Aturan::findOrFail($id);
        $fakta = Fakta::all(['id', 'kode', 'deskripsi'])->toArray();
        $solusi = Solusi::all(['id', 'kode', 'deskripsi'])->toArray();
        return view('admin.aturan.edit', compact('aturan', 'fakta', 'solusi'));
    }

    /**
     * Memperbarui informasi aturan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Aturan  $aturan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Ambil data aturan yang ingin diubah
        $aturan = Aturan::findOrFail($id);

        // Validasi input
        $request->validate([
            'kode' => 'required|string|max:255|unique:aturan,kode,' . $aturan->id,
            'deskripsi' => 'required|string|max:255',
            'premis' => 'nullable|array',
            'jenis_konklusi' => 'required|in:fakta,solusi',
            'konklusi' => 'required',
            'is_active' => 'nullable|boolean',
        ]);

        // Format premis menjadi F001^F002
        $premis = null;

        if (!empty($request->premis)) {
            $premis = implode('^', $request->premis);
        }

        // Inisialisasi variabel konklusi dan solusi_id
        $konklusi = $request->konklusi;
        $solusi_id = null;

        if ($request->jenis_konklusi === 'solusi') {
            $solusi = Solusi::where('kode', $request->konklusi)->first();

            if ($solusi) {
                $solusi_id = $solusi->id;
            } else {
                return redirect()->back()->withErrors(['konklusi' => 'Solusi dengan kode tersebut tidak ditemukan.']);
            }
        }

        // Update data aturan
        $aturan->update([
            'kode' => $request->kode,
            'deskripsi' => $request->deskripsi,
            'premis' => $premis,
            'konklusi' => $konklusi,
            'jenis_konklusi' => $request->jenis_konklusi,
            'solusi_id' => $solusi_id,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        // Redirect kembali ke halaman daftar aturan
        return redirect()->route('admin.basisPengetahuan.aturan.index')
            ->with('success', 'Aturan berhasil diperbarui.');
    }


    /**
     * Menghapus aturan.
     *
     * @param  \App\Models\Aturan  $aturan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Aturan $aturan)
    {
        $aturan->delete();
        return redirect()->route('admin.basisPengetahuan.aturan.index')->with('success', 'Aturan berhasil dihapus.');
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $aturan = Aturan::findOrFail($id);

            // Validasi input
            $request->validate([
                'is_active' => 'required|boolean'
            ]);

            // Update status
            $aturan->update([
                'is_active' => $request->is_active
            ]);

            $status = $request->is_active ? '1' : '0';

            return response()->json([
                'success' => true,
                'message' => "Aturan '{$aturan->kode}' berhasil {$status}.",
                'status' => $request->is_active ? 'Aktif' : 'Tidak Aktif'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status aturan: ' . $e->getMessage()
            ], 500);
        }
    }
}
