<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Konsultasi;
use App\Models\Solusi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class RiwayatController extends Controller
{
    /**
     * Menampilkan daftar riwayat konsultasi pengguna.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        $userId = Auth::id();
        $statuses = ['belum_selesai', 'sedang_berjalan', 'selesai'];
        $riwayatKonsultasi = [];

        foreach ($statuses as $status) {
            $riwayatKonsultasi[$status] = Konsultasi::where('user_id', $userId)
                ->with('currentPertanyaan')
                ->where('status', $status)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('user.riwayat.index', compact('riwayatKonsultasi'));
    }

    /**
     * Menampilkan detail dari satu sesi konsultasi.
     *
     * @param  \App\Models\Konsultasi  $konsultasi
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse
     */
    public function show(Konsultasi $konsultasi)
    {
        if ($konsultasi->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke riwayat konsultasi ini.');
        }
        $konsultasi->load('user');
        $detailKonsultasi = $konsultasi->detailKonsultasi()->with('fakta')->get();
        $inferensiLog = $konsultasi->inferensiLog()->with('aturan')->get();
        $solusiAkhir = null;
        $inferensiSolusi = $inferensiLog->filter(function ($item) {
            if (!$item->aturan) {
                Log::warning("InferensiLog ID {$item->id} tidak memiliki aturan terkait.");
                return false;
            }
            return $item->aturan->jenis_konklusi === 'solusi';
        })->last();

        if ($inferensiSolusi) {
            $solusiAkhir = Solusi::find($inferensiSolusi->aturan->solusi_id);
            if ($solusiAkhir) {
                $solusiAkhir->load('rekomendasiNutrisi.sumberNutrisi');
            }
        }

        return view('user.riwayat.show', compact('konsultasi', 'detailKonsultasi', 'inferensiLog', 'solusiAkhir', 'inferensiSolusi'));
    }
}
