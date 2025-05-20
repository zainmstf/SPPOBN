<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\KontenEdukasi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class KontenEdukasiController extends Controller
{
    /**
     * Menampilkan daftar konten edukasi yang aktif.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index(Request $request)
    {
        $jenis = $request->input('jenis'); // Ambil parameter jenis (jika ada)

        $kontenEdukasi = KontenEdukasi::where('status', 1)
            ->whereIn('jenis', ['artikel', 'video', 'infografis'])
            ->when($jenis, fn($q) => $q->where('jenis', $jenis)) // Filter jika jenis dipilih
            ->paginate(6); // Paginasi 6 item per halaman

        return view('user.nutrisi.edukasi', compact('kontenEdukasi', 'jenis'));
    }

    /**
     * Menampilkan detail dari satu konten edukasi berdasarkan slug.
     *
     * @param  string  $slug
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse
     */
    public function show(string $slug)
    {
        $konten = KontenEdukasi::where('slug', $slug)
            ->where('status', 1)
            ->firstOrFail();

        // Tingkatkan jumlah tampilan
        $konten->increment('view_count');

        return view('user.edukasi.show', compact('konten'));
    }

    public function aboutOsteoporosis()
    {
        return view('user.tentang-osteoporosis');
    }
    public function getKonten($id)
    {
        $konten = KontenEdukasi::where('status', 1)->find($id);

        if (!$konten) {
            return response()->json(['message' => 'Konten tidak ditemukan.'], 404);
        }

        // Optional: Naikkan jumlah view
        $konten->increment('view_count');

        return response()->json([
            'id' => $konten->id,
            'judul' => $konten->judul,
            'slug' => $konten->slug,
            'jenis' => $konten->jenis,
            'kategori' => $konten->kategori,
            'deskripsi' => $konten->deskripsi,
            'konten' => $konten->konten,
            'path' => $konten->path ? $konten->path : null,
            'thumbnail' => $konten->thumbnail ? Storage::url($konten->thumbnail) : null,
            'created_at' => $konten->created_at,
            'penulis' => optional($konten->creator)->nama_lengkap ?? null,
        ]);
    }
}