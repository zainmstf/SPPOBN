<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\KontenEdukasi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandingPageController extends Controller
{
    /**
     * Menampilkan halaman utama (landing page).
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        $feedback = Feedback::latest()->take(3)->get();
        $kontenEdukasi = KontenEdukasi::where('status', 1)
            ->whereIn('jenis', ['video', 'artikel', 'infografis'])
            ->latest()
            ->take(6)
            ->get()
            ->groupBy('jenis');

        return view('pages.landing', compact('feedback', 'kontenEdukasi'));
    }

    /**
     * Menampilkan daftar konten edukasi berdasarkan id.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */

    public function getEdukasi($id)
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
    public function storeFeedback(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'pesan' => 'required|string|max:500',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Feedback::create([
            'user_id' => Auth::id() ?? NULL,
            'pesan' => $request->input('pesan'),
            'rating' => $request->input('rating'),
        ]);

        return redirect()->to('/#testimonial')->with('success', 'Terima kasih atas umpan balik Anda!');
    }

}