<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Konsultasi;
use App\Models\RekomendasiNutrisi;
use App\Models\Solusi;
use App\Services\ForwardChainingService;
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

        $sessionNames = [
            1 => 'Skrining Faktor Risiko',
            2 => 'Penilaian FRAX',
            3 => 'Penilaian Nutrisi',
            4 => 'Preferensi & Kondisi',
        ];

        foreach ($statuses as $status) {
            $konsultasiList = Konsultasi::where('user_id', $userId)
                ->with('currentPertanyaan')
                ->where('status', $status)
                ->orderBy('created_at', 'desc')
                ->get();

            $konsultasiList->each(function ($konsultasi) use ($sessionNames) {
                $konsultasi->nama_sesi = $sessionNames[$konsultasi->sesi] ?? 'Tidak ada sesi aktif';


                if (isset($konsultasi->hasil_konsultasi['solusi']) && is_array($konsultasi->hasil_konsultasi['solusi'])) {
                    $solusiCodes = $konsultasi->hasil_konsultasi['solusi'];

                    // Fetch solution names from the 'solusi' table
                    $solusiNames = Solusi::whereIn('kode', $solusiCodes)
                        ->pluck('nama')
                        ->toArray();

                    $konsultasi->nama_solusi = $solusiNames;
                } else {
                    $konsultasi->nama_solusi = []; // No solutions found or invalid format
                }

            });

            $riwayatKonsultasi[$status] = $konsultasiList;
        }

        return view('user.riwayat.index', compact('riwayatKonsultasi'));
    }

    /**
     * Menampilkan detail dari satu sesi konsultasi.
     *
     * @param  \App\Models\Konsultasi  $konsultasi
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse
     */
    /**
     * Show consultation details
     */
    public function show($id)
    {
        $konsultasi = Konsultasi::with('user')->where('id', $id)->first();

        // Cek authorization
        if ($konsultasi->user_id !== Auth::id()) {
            abort(403);
        }

        $forwardChaining = new ForwardChainingService($konsultasi);

        // Get hasil konsultasi
        $hasilKonsultasi = $forwardChaining->getHasilKonsultasi();
        $detailSolusi = $forwardChaining->getDetailSolusi();
        $traceInferensi = $forwardChaining->getTraceInferensi();

        // PERBAIKAN: Get status message untuk hasil
        $statusMessage = $forwardChaining->getStatusMessage();

        // Get jawaban user untuk display
        $jawabanUser = $konsultasi->detailKonsultasi()
            ->with('fakta')
            ->get()
            ->map(function ($detail) {
                return [
                    'kode' => $detail->fakta->kode,
                    'pertanyaan' => $detail->fakta->pertanyaan,
                    'jawaban' => $detail->jawaban,
                    'kategori' => $detail->fakta->kategori
                ];
            });

        $rekomendasiNutrisi = collect();

        if (!empty($hasilKonsultasi)) {
            // Ambil ID solusi dari hasil konsultasi
            $solusiIds = collect($detailSolusi)->pluck('id');

            // Get rekomendasi nutrisi dengan sumber nutrisi
            $rekomendasiNutrisi = RekomendasiNutrisi::whereIn('solusi_id', $solusiIds)
                ->with([
                    'sumberNutrisi' => function ($query) {
                        $query->orderBy('jenis_sumber');
                    },
                    'solusi' => function ($query) { // Eager load relasi 'solusi'
                        $query->select('id', 'nama'); // Pilih kolom 'id' dan 'nama' dari tabel solusi
                    }
                ])
                ->orderBy('solusi_id')
                ->get()
                ->groupBy('nutrisi'); // Group berdasarkan jenis nutrisi
        }

        return view('user.konsultasi.result', compact(
            'konsultasi',
            'hasilKonsultasi',
            'detailSolusi',
            'traceInferensi',
            'jawabanUser',
            'statusMessage',
            'rekomendasiNutrisi'
        ));
    }
}
