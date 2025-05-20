<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KontenEdukasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class KontenEdukasiController extends Controller
{
    /**
     * Menampilkan daftar semua konten edukasi.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index(Request $request)
    {
        $jenis = $request->input('jenis'); // Ambil parameter jenis (jika ada)

        $kontenEdukasi = KontenEdukasi::whereIn('jenis', ['artikel', 'video', 'infografis'])
            ->when($jenis, fn($q) => $q->where('jenis', $jenis)) // Filter jika jenis dipilih
            ->paginate(6); // Paginasi 6 item per halaman

        return view('admin.nutrisi.index', compact('kontenEdukasi', 'jenis'));
    }

    /**
     * Menampilkan form untuk membuat konten edukasi baru.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function create()
    {
        return view('admin.nutrisi.create');
    }

    /**
     * Menyimpan konten edukasi baru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        // Validasi data yang masuk
        $request->validate([
            'judul' => 'required|max:255',
            'jenis' => ['required', Rule::in(['artikel', 'video', 'infografis'])],
            'kategori' => ['required', Rule::in(['osteoporosis_dasar', 'nutrisi_tulang', 'pencegahan', 'pengobatan'])],
            'deskripsi' => 'required',
            'path' => 'nullable|file|max:2048', // Validasi untuk file pendukung (jika ada)
            'path_infografis' => 'nullable|file|max:2048', // Validasi untuk infografis
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Contoh validasi file
            'url' => 'nullable|url', // Validasi untuk URL video
            'isActive' => 'nullable|boolean',
        ]);

        // Generate slug
        $slug = Str::slug($request->judul);
        $uniqueSlug = $slug;
        $counter = 1;
        while (KontenEdukasi::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = $slug . '-' . $counter++;
        }
        $slug = $uniqueSlug;

        $data = $request->except(['thumbnail', 'path', 'path_infografis', 'url']); // Exclude files and URL from initial data array.
        $data['slug'] = $slug;
        $data['created_by'] = Auth::id();
        $data['status'] = $request->input('isActive', false); // Default to true jika tidak ada

        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails'); // Simpan di direktori 'thumbnails' di storage/app/public
            $data['thumbnail'] = $thumbnailPath; // Simpan path relatif yang bisa diakses
        }

        // Handle file upload for 'path' (general file)
        if ($request->hasFile('path')) {
            $filePath = $request->file('path')->store('files'); // Simpan di direktori 'files'
            $data['path'] = $filePath; // Simpan path relatif
        }

        // Handle file upload for 'path_infografis'
        if ($request->hasFile('path_infografis')) {
            $infografisPath = $request->file('path_infografis')->store('infografis'); // Simpan di direktori 'infografis'
            $data['path'] = $infografisPath;
        }
        // Handle URL for video
        if ($request->input('jenis') == 'video') {
            $data['path'] = $request->input('url'); // Simpan URL di kolom 'path'
        }

        // Buat dan simpan konten edukasi
        KontenEdukasi::create($data);

        // Redirect dengan pesan sukses
        return redirect()->route('admin.konten-edukasi.index')
            ->with('success', 'Materi nutrisi berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail konten edukasi.
     *
     * @param  \App\Models\KontenEdukasi  $kontenEdukasi
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
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

    /**
     * Menampilkan form untuk mengedit konten edukasi.
     *
     * @param  \App\Models\KontenEdukasi  $kontenEdukasi
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function edit(KontenEdukasi $kontenEdukasi)
    {
        return view('admin.nutrisi.edit', compact('kontenEdukasi'));
    }

    /**
     * Memperbarui informasi konten edukasi.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\KontenEdukasi  $kontenEdukasi
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, KontenEdukasi $kontenEdukasi)
    {
        // Validasi data yang masuk
        $request->validate([
            'judul' => 'required|max:255',
            'jenis' => ['required', Rule::in(['artikel', 'video', 'infografis'])],
            'kategori' => ['required', Rule::in(['osteoporosis_dasar', 'nutrisi_tulang', 'pencegahan', 'pengobatan'])],
            'deskripsi' => 'required',
            'path' => 'nullable|file|max:2048', // Validasi untuk file pendukung (jika ada)
            'path_infografis' => 'nullable|file|mimes:pdf|max:2048', // Validasi untuk infografis
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Contoh validasi file
            'url' => 'nullable|url', // Validasi untuk URL video
            'isActive' => 'nullable|boolean',
        ]);

        $data = $request->except(['thumbnail', 'path', 'path_infografis', 'url']);
        $data['updated_by'] = Auth::id();
        $data['status'] = $request->input('isActive', false);

        // Handle file upload for thumbnail
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if exists
            if ($kontenEdukasi->thumbnail) {
                Storage::delete($kontenEdukasi->thumbnail);
            }
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails');
            $data['thumbnail'] = $thumbnailPath;
        }

        // Handle file upload for 'path'
        if ($request->hasFile('path')) {
            // Delete old file if exists
            if ($kontenEdukasi->path && strpos($kontenEdukasi->path, 'http') !== 0) { //cek apakah path bukan URL
                Storage::delete($kontenEdukasi->path);
            }
            $filePath = $request->file('path')->store('files');
            $data['path'] = $filePath;
        }

        // Handle file upload for 'path_infografis'
        if ($request->hasFile('path_infografis')) {
            if ($kontenEdukasi->path && strpos($kontenEdukasi->path, 'http') !== 0) {
                Storage::delete($kontenEdukasi->path);
            }
            $infografisPath = $request->file('path_infografis')->store('infografis');
            $data['path'] = $infografisPath;
        }

        // Handle URL for video
        if ($request->input('jenis') == 'video') {
            $data['path'] = $request->input('url');
        }

        $kontenEdukasi->update($data);

        // Redirect dengan pesan sukses
        return redirect()->route('admin.konten-edukasi.index')
            ->with('success', 'Materi nutrisi berhasil diubah.');
    }

    /**
     * Menghapus konten edukasi.
     *
     * @param  \App\Models\KontenEdukasi  $kontenEdukasi
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(KontenEdukasi $kontenEdukasi)
    {
        // Hapus thumbnail jika ada
        if ($kontenEdukasi->thumbnail && Storage::disk('public')->exists($kontenEdukasi->thumbnail)) {
            Storage::disk('public')->delete($kontenEdukasi->thumbnail);
        }

        $kontenEdukasi->delete();
        return redirect()->route('admin.konten-edukasi.index')->with('success', 'Konten edukasi berhasil dihapus.');
    }

    /**
     * Upload gambar untuk konten edukasi (misalnya untuk editor).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(Request $request)
    {
        if ($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileName = $fileName . '_' . time() . '.' . $extension;

            $request->file('upload')->storeAs('public/images/konten_edukasi', $fileName);

            $CKEditorFuncNum = $request->input('CKEditorFuncNum');
            $url = asset('storage/images/konten_edukasi/' . $fileName);
            $msg = 'Gambar berhasil diunggah';
            $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";

            @header('Content-type: text/html; charset=utf-8');
            echo $response;
        }

        return response()->json(['error' => 'No file uploaded.'], 400);
    }

    /**
     * Mengubah status publikasi konten edukasi.
     *
     * @param  \App\Models\KontenEdukasi  $kontenEdukasi
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(KontenEdukasi $kontenEdukasi)
    {
        $kontenEdukasi->update(['status' => !$kontenEdukasi->status]);
        $statusText = $kontenEdukasi->status ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Konten '{$kontenEdukasi->judul}' berhasil $statusText.");
    }
}