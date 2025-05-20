<?php

namespace App\Http\Controllers\Admin;

use App\Models\Solusi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RekomendasiNutrisi;

class RekomendasiNutrisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rekomendasiNutrisi = RekomendasiNutrisi::with('solusi')->get();
        return view('admin.rekomendasi_nutrisi.index', compact('rekomendasiNutrisi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Asumsi Anda memiliki model Solusi
        $solusi = Solusi::all();
        return view('admin.rekomendasi_nutrisi.create', compact('solusi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'solusi_id' => 'required|exists:solusi,id',
            'nutrisi' => 'required|string|max:100',
            'kontraindikasi' => 'nullable|string',
            'alternatif' => 'nullable|string',
        ]);

        RekomendasiNutrisi::create($request->all());

        return redirect()->route('admin.rekomendasi-nutrisi.index')
            ->with('success', 'Rekomendasi nutrisi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    // public function show(RekomendasiNutrisi $rekomendasiNutrisi)
    // {
    //     return view('admin.rekomendasi_nutrisi.show', compact('rekomendasiNutrisi'));
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RekomendasiNutrisi $rekomendasiNutrisi)
    {
        // Asumsi Anda memiliki model Solusi
        $solusi = Solusi::all();
        return view('admin.rekomendasi_nutrisi.edit', compact('rekomendasiNutrisi', 'solusi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RekomendasiNutrisi $rekomendasiNutrisi)
    {
        $request->validate([
            'solusi_id' => 'required|exists:solusi,id',
            'nutrisi' => 'required|string|max:100',
            'kontraindikasi' => 'nullable|string',
            'alternatif' => 'nullable|string',
        ]);

        $rekomendasiNutrisi->update($request->all());

        return redirect()->route('rekomendasi-nutrisi.index')
            ->with('success', 'Rekomendasi nutrisi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RekomendasiNutrisi $rekomendasiNutrisi)
    {
        $rekomendasiNutrisi->delete();

        return redirect()->route('admin.rekomendasi-nutrisi.index')
            ->with('success', 'Rekomendasi nutrisi berhasil dihapus.');
    }
}
