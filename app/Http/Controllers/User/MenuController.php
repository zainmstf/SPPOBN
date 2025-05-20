<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SumberNutrisi;

class MenuController extends Controller
{
    public function index()
    {
        $sumberNutrisi = SumberNutrisi::with('rekomendasiNutrisi')->orderBy('nama_sumber')->paginate(6);
        return view('user.menu.nutrisi', compact('sumberNutrisi'));
    }

    public function byJenis($jenis)
    {
        $sumberNutrisi = SumberNutrisi::where('jenis_sumber', $jenis)->orderBy('nama_sumber')->paginate(6);
        return view('user.menu.nutrisi', compact('sumberNutrisi'));
    }
}
