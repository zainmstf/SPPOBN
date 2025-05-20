<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RekomendasiNutrisi;
use Illuminate\Http\Request;
use App\Models\SumberNutrisi;
use Illuminate\Support\Facades\Storage;

class SumberNutrisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sumberNutrisi = SumberNutrisi::with('rekomendasiNutrisi')->get();
        return view('admin.sumber_nutrisi.index', compact('sumberNutrisi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Asumsi Anda memiliki model RekomendasiNutrisi
        $rekomendasiNutrisi = RekomendasiNutrisi::all();
        return view('admin.sumber_nutrisi.create', compact('rekomendasiNutrisi'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {

        $validated = $request->validate([
            'nutrisi' => 'required|string|max:100',
            'jenis_sumber' => 'required|in:suplemen,makanan',
            'nama_sumber' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validasi untuk thumbnail
            'takaran' => 'nullable|string|max:100',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->except(['image']); // Exclude image from initial data array.

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('sumber-nutrisi'); // Simpan di direktori 'public/sumber-nutrisi'
            $data['image'] = $imagePath;
        }

        SumberNutrisi::create($data);

        return redirect()->route('admin.sumber-nutrisi.index')
            ->with('success', 'Sumber nutrisi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SumberNutrisi $sumberNutrisi)
    {
        return view('admin.sumber_nutrisi.show', compact('sumberNutrisi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SumberNutrisi $sumberNutrisi)
    {
        // Asumsi Anda memiliki model RekomendasiNutrisi
        $rekomendasiNutrisi = RekomendasiNutrisi::all();
        return view('admin.sumber_nutrisi.edit', compact('sumberNutrisi', 'rekomendasiNutrisi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SumberNutrisi $sumberNutrisi)
    {
        $request->validate([
            'rekomendasi_nutrisi_id' => 'required|exists:rekomendasi_nutrisi,id',
            'jenis_sumber' => 'required|in:suplemen,makanan',
            'nama_sumber' => 'required|string|max:255',
            'takaran' => 'nullable|string|max:100',
            'catatan' => 'nullable|string',
        ]);

        $sumberNutrisi->update($request->all());

        return redirect()->route('admin.sumber-nutrisi.index')
            ->with('success', 'Sumber nutrisi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SumberNutrisi $sumberNutrisi)
    {
        $sumberNutrisi->delete();

        return redirect()->route('admin.sumber-nutrisi.index')
            ->with('success', 'Sumber nutrisi berhasil dihapus.');
    }
}
