<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DetailKonsultasi;
use App\Models\Konsultasi;
use App\Models\Fakta;
use App\Models\RekomendasiNutrisi;
use App\Services\ForwardChainingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\PDF;

class KonsultasiController extends Controller
{
    /**
     * Display a listing of consultations
     */
    public function index()
    {
        $konsultasi = Konsultasi::with('user')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.konsultasi.index', compact('konsultasi'));
    }

    /**
     * Show the form for creating a new consultation
     */
    public function create()
    {
        return view('user.konsultasi.create');
    }

    /**
     * Store a newly created consultation
     */
    public function store(Request $request)
    {
        $konsultasi = DB::transaction(function () use ($request) {
            $konsultasi = Konsultasi::create([
                'user_id' => Auth::id(),
                'sesi' => 0,
                'status' => 'sedang_berjalan',
            ]);

            // Immediately apply the starting rule (R0.1)
            $forwardChaining = new ForwardChainingService($konsultasi);
            $forwardChaining->applyStartingRule();

            return $konsultasi;
        });

        return redirect()->route('konsultasi.question', $konsultasi->id)
            ->with('success', 'Konsultasi baru telah dimulai');
    }

    /**
     * Show the next question in consultation
     */
    public function question($id)
    {
        $konsultasi = Konsultasi::where('id', $id)->first();

        // Cek authorization
        if ($konsultasi->user_id !== Auth::id()) {
            abort(403);
        }

        $forwardChaining = new ForwardChainingService($konsultasi);

        // Check if S001 is already in solutions
        if (in_array('S001', $forwardChaining->getHasilKonsultasi()['solusi']) && $konsultasi->sesi == 0) {
            return redirect()->route('konsultasi.continue-session', $konsultasi->id);
        }

        // PERBAIKAN: Cek konsultasi selesai dengan solusi terlebih dahulu
        if ($forwardChaining->isKonsultasiSelesai()) {
            $hasilKonsultasi = $forwardChaining->getHasilKonsultasi();

            // Jika ada solusi, konsultasi berhasil selesai
            if (!empty($hasilKonsultasi['solusi'])) {
                $konsultasi->update([
                    'status' => 'selesai',
                    'hasil_konsultasi' => $hasilKonsultasi,
                    'completed_at' => now()
                ]);

                return redirect()->route('konsultasi.result', $id)
                    ->with('success', 'Konsultasi telah selesai');
            }
        }

        // Cek status message untuk konsultasi yang berakhir tanpa solusi
        $statusMessage = $forwardChaining->getStatusMessage();

        if ($statusMessage['status'] === 'tidak_ada_solusi') {
            $konsultasi->update([
                'status' => 'selesai',
                'hasil_konsultasi' => $forwardChaining->getHasilKonsultasi(),
                'completed_at' => now()
            ]);

            return redirect()->route('konsultasi.result', $id)
                ->with('warning', $statusMessage['message']);
        }

        // Ambil pertanyaan berikutnya
        $pertanyaan = $forwardChaining->getNextQuestion();

        // PERBAIKAN: Logika ketika tidak ada pertanyaan
        if (!$pertanyaan) {
            $hasilKonsultasi = $forwardChaining->getHasilKonsultasi();

            // Jika ada fakta antara atau solusi yang ditemukan di sesi ini
            if (!empty($hasilKonsultasi['fakta_antara']) || !empty($hasilKonsultasi['solusi'])) {
                // Sesi berhasil, arahkan ke session summary
                return redirect()->route('konsultasi.session-summary', $id);
            }

            // Jika tidak ada fakta antara/solusi DAN tidak bisa lanjut sesi
            if (!$hasilKonsultasi['sesi_dapat_lanjut']) {
                $konsultasi->update([
                    'status' => 'selesai',
                    'hasil_konsultasi' => $hasilKonsultasi,
                    'completed_at' => now()
                ]);

                return redirect()->route('konsultasi.result', $id)
                    ->with('warning', 'Konsultasi tidak dapat dilanjutkan karena tidak ada aturan yang dapat dipenuhi');
            }

            // Masih ada kemungkinan lanjut, arahkan ke session summary
            return redirect()->route('konsultasi.session-summary', $id);
        }

        // Get progress dan informasi sesi
        $hasilKonsultasi = $forwardChaining->getHasilKonsultasi();

        // Update pertanyaan terakhir
        $konsultasi->update([
            'pertanyaan_terakhir' => $pertanyaan->kode
        ]);

        return view('user.konsultasi.question', compact(
            'konsultasi',
            'pertanyaan',
            'hasilKonsultasi'
        ));
    }


    /**
     * Process the answer and proceed to next question
     */
    public function answer(Request $request, $id)
    {
        $request->validate([
            'jawaban' => 'required|in:ya,tidak',
            'kode_fakta' => 'required|string'
        ]);

        $konsultasi = Konsultasi::where('id', $id)->first();

        // Cek authorization
        if ($konsultasi->user_id !== Auth::id()) {
            abort(403);
        }

        $forwardChaining = new ForwardChainingService($konsultasi);

        // Proses jawaban
        $forwardChaining->processAnswer(
            $request->kode_fakta,
            $request->jawaban
        );

        // PERBAIKAN: Cek status setelah memproses jawaban
        $statusMessage = $forwardChaining->getStatusMessage();

        // Jika konsultasi berakhir tanpa solusi setelah memproses jawaban
        if ($statusMessage['status'] === 'tidak_ada_solusi') {
            $konsultasi->update([
                'status' => 'selesai',
                'hasil_konsultasi' => $forwardChaining->getHasilKonsultasi(),
                'completed_at' => now()
            ]);

            return redirect()->route('konsultasi.result', $id)
                ->with('warning', $statusMessage['message']);
        }

        // Cek apakah konsultasi sudah selesai dengan solusi
        if ($forwardChaining->isKonsultasiSelesai()) {
            $hasilKonsultasi = $forwardChaining->getHasilKonsultasi();

            $konsultasi->update([
                'status' => 'selesai',
                'hasil_konsultasi' => $hasilKonsultasi,
                'completed_at' => now()
            ]);

            return redirect()->route('konsultasi.result', $id)
                ->with('success', 'Konsultasi telah selesai');
        }

        // Lanjut ke pertanyaan berikutnya
        return redirect()->route('konsultasi.question', $id);
    }

    /**
     * Show consultation result
     */
    public function result($id)
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

    /**
     * Restart consultation
     */
    public function restart($id)
    {
        $konsultasi = Konsultasi::where('id', $id)->first();

        // Cek authorization
        if ($konsultasi->user_id !== Auth::id()) {
            abort(403);
        }

        $forwardChaining = new ForwardChainingService($konsultasi);
        $forwardChaining->resetKonsultasi();

        return redirect()->route('konsultasi.question', $id)
            ->with('success', 'Konsultasi telah direset dan dimulai dari awal');
    }

    /**
     * Get consultation progress (AJAX)
     */
    public function getProgress($id)
    {
        $konsultasi = Konsultasi::where('id', $id)->first();

        // Cek authorization
        if ($konsultasi->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $forwardChaining = new ForwardChainingService($konsultasi);
        $hasilKonsultasi = $forwardChaining->getHasilKonsultasi();
        $statusMessage = $forwardChaining->getStatusMessage();

        // TAMBAHAN: Get detail progress untuk debugging (opsional)
        $detailProgress = $forwardChaining->getDetailProgress();

        return response()->json([
            'progress' => $hasilKonsultasi['progress'],
            'sesi' => $hasilKonsultasi['sesi'],
            'sesi_nama' => $this->getSesiNama($hasilKonsultasi['sesi']),
            'total_fakta' => count($hasilKonsultasi['fakta_tersedia']),
            'total_solusi' => count($hasilKonsultasi['solusi']),
            'selesai' => $hasilKonsultasi['selesai'],
            'status_message' => $statusMessage,
            'sesi_dapat_lanjut' => $hasilKonsultasi['sesi_dapat_lanjut'] ?? true,

            // TAMBAHAN: Detail progress untuk debugging
            'detail_progress' => $detailProgress, // Hapus ini di production

            // TAMBAHAN: Info pertanyaan untuk tampilan yang lebih informatif
            'pertanyaan_terjawab' => $detailProgress['pertanyaan_terjawab_sesi'],
            'total_pertanyaan' => $detailProgress['total_pertanyaan_sesi']
        ]);
    }

    /**
     * Get session name
     */
    private function getSesiNama($sesi)
    {
        $namaSesi = [
            1 => 'Skrining Awal',
            2 => 'Klasifikasi Risiko FRAX',
            3 => 'Penilaian Asupan Nutrisi',
            4 => 'Preferensi Makanan'
        ];

        return $namaSesi[$sesi] ?? 'Tidak Diketahui';
    }

    /**
     * Get session description
     */
    private function getSesiDeskripsi($sesi)
    {
        $deskripsiSesi = [
            1 => 'Menilai faktor risiko osteoporosis berdasarkan riwayat kesehatan dan gaya hidup',
            2 => 'Menghitung risiko fraktur menggunakan FRAX tool berdasarkan data klinis',
            3 => 'Mengevaluasi kecukupan asupan nutrisi penting untuk kesehatan tulang',
            4 => 'Menentukan rekomendasi makanan berdasarkan preferensi dan kondisi kesehatan'
        ];

        return $deskripsiSesi[$sesi] ?? '';
    }

    /**
     * Show session summary when no more questions in current session
     */
    public function showSessionSummary($id)
    {
        $konsultasi = Konsultasi::where('id', $id)->first();

        // Cek authorization
        if ($konsultasi->user_id !== Auth::id()) {
            abort(403);
        }

        $forwardChaining = new ForwardChainingService($konsultasi);
        $hasilKonsultasi = $forwardChaining->getHasilKonsultasi();

        // PERBAIKAN: Cek status konsultasi
        $statusMessage = $forwardChaining->getStatusMessage();
        if ($statusMessage['status'] === 'tidak_ada_solusi') {
            $konsultasi->update([
                'status' => 'selesai',
                'hasil_konsultasi' => $hasilKonsultasi,
                'completed_at' => now()
            ]);

            return redirect()->route('konsultasi.result', $id)
                ->with('warning', $statusMessage['message']);
        }

        // Get detail solusi yang ditemukan di sesi ini
        $detailSolusi = $forwardChaining->getDetailSolusi();
        $traceInferensi = $forwardChaining->getTraceInferensi();
        $jawabanUser = $this->getSessionAnswers($konsultasi, $hasilKonsultasi['sesi']);

        // PERBAIKAN: Cek hasil sesi saat ini secara spesifik
        $sesiSekarang = $hasilKonsultasi['sesi'];
        $hasilSesiSekarang = $forwardChaining->getSesiResults($sesiSekarang);

        // PERBAIKAN: Logika untuk menentukan apakah bisa lanjut sesi
        $bisaLanjutSesi = false;

        if ($sesiSekarang <= 4) {
            // Bisa lanjut hanya jika sesi saat ini menghasilkan fakta antara yang relevan untuk sesi berikutnya
            // atau solusi yang merupakan output akhir sesi tersebut
            $bisaLanjutSesi = $hasilSesiSekarang['relevant_for_next_session'];
        }

        if ($sesiSekarang == 0) {
            $bisaLanjutSesi = true;
        }

        \Log::info('Session Summary Debug:', [
            'sesi' => $sesiSekarang,
            'hasil_sesi_sekarang' => $hasilSesiSekarang,
            'bisa_lanjut_sesi' => $bisaLanjutSesi
        ]);

        // PERBAIKAN: Jika tidak bisa lanjut sesi dan belum di sesi 4
        if (!$bisaLanjutSesi && $sesiSekarang <= 4) {
            $konsultasi->update([
                'status' => 'selesai',
                'hasil_konsultasi' => $hasilKonsultasi,
                'completed_at' => now()
            ]);

            return redirect()->route('konsultasi.result', $id)
                ->with('warning', "Konsultasi berakhir karena sesi {$sesiSekarang} tidak menghasilkan hasil yang diperlukan untuk melanjutkan");
        }

        // Get info sesi
        $infoSesiSekarang = [
            'nomor' => $sesiSekarang,
            'nama' => $this->getSesiNama($sesiSekarang),
            'deskripsi' => $this->getSesiDeskripsi($sesiSekarang),
            'hasil_sesi' => $hasilSesiSekarang // TAMBAHAN: Info hasil sesi
        ];

        $infoSesiBerikutnya = null;
        if ($bisaLanjutSesi) {
            $sesiBerikutnya = $sesiSekarang + 1;
            $infoSesiBerikutnya = [
                'nomor' => $sesiBerikutnya,
                'nama' => $this->getSesiNama($sesiBerikutnya),
                'deskripsi' => $this->getSesiDeskripsi($sesiBerikutnya)
            ];
        }

        return view('user.konsultasi.session_summary', compact(
            'konsultasi',
            'hasilKonsultasi',
            'detailSolusi',
            'traceInferensi',
            'jawabanUser',
            'bisaLanjutSesi',
            'infoSesiSekarang',
            'infoSesiBerikutnya',
            'statusMessage'
        ));
    }

    /**
     * Continue to next session
     */
    public function continueSession(Request $request, $id)
    {
        $konsultasi = Konsultasi::where('id', $id)->first();

        // Cek authorization
        if ($konsultasi->user_id !== Auth::id()) {
            abort(403);
        }

        $forwardChaining = new ForwardChainingService($konsultasi);
        $hasilKonsultasi = $forwardChaining->getHasilKonsultasi();
        if ($konsultasi->sesi == 0 && in_array('S001', $hasilKonsultasi['solusi'])) {
            $konsultasi->update([
                'sesi' => 1,
                'status' => 'sedang_berjalan'
            ]);

            return redirect()->route('konsultasi.question', $id)
                ->with('success', "Melanjutkan ke Skrining Awal");
        }
        // PERBAIKAN: Cek apakah benar-benar bisa lanjut ke sesi berikutnya
        if ($hasilKonsultasi['sesi'] >= 4) {
            return redirect()->route('konsultasi.result', $id)
                ->with('info', 'Konsultasi sudah mencapai sesi terakhir');
        }

        // PERBAIKAN UTAMA: Cek hasil sesi saat ini secara spesifik
        $sesiSekarang = $hasilKonsultasi['sesi'];
        $hasilSesiSekarang = $forwardChaining->getSesiResults($sesiSekarang);

        \Log::info('Continue Session - Hasil Sesi:', [
            'sesi' => $sesiSekarang,
            'hasil_sesi' => $hasilSesiSekarang,
            'has_results' => $hasilSesiSekarang['has_results']
        ]);

        // Jika sesi saat ini tidak menghasilkan fakta antara atau solusi, tidak bisa lanjut
        if (!$hasilSesiSekarang['has_results']) {
            return redirect()->route('konsultasi.result', $id)
                ->with('warning', "Tidak dapat melanjutkan ke sesi berikutnya karena sesi {$sesiSekarang} tidak menghasilkan hasil yang diperlukan");
        }

        // Update sesi konsultasi
        $sesiBerikutnya = $sesiSekarang + 1;

        $konsultasi->update([
            'sesi' => $sesiBerikutnya,
            'status' => 'sedang_berjalan'
        ]);

        return redirect()->route('konsultasi.question', $id)
            ->with('success', "Melanjutkan ke {$this->getSesiNama($sesiBerikutnya)}");
    }

    /**
     * Set consultation status to pending
     */
    public function pending(Request $request)
    {
        $request->validate([
            'konsultasi_id' => 'required|exists:konsultasi,id'
        ]);

        $konsultasi = Konsultasi::where('id', $request->konsultasi_id)->first();

        // Cek authorization
        if ($konsultasi->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $konsultasi->update([
            'status' => 'belum_selesai',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Konsultasi telah disimpan dan dapat dilanjutkan nanti'
        ]);
    }

    /**
     * Continue pending consultation
     */
    public function continueKonsultasi($id)
    {
        $konsultasi = Konsultasi::where('id', $id)->first();

        // Cek authorization
        if ($konsultasi->user_id !== Auth::id()) {
            abort(403);
        }

        // Cek status konsultasi
        if ($konsultasi->status === 'selesai') {
            return redirect()->route('konsultasi.result', $id)
                ->with('info', 'Konsultasi ini sudah selesai');
        }

        $forwardChaining = new ForwardChainingService($konsultasi);

        // PERBAIKAN: Cek status konsultasi lebih teliti
        $statusMessage = $forwardChaining->getStatusMessage();

        // Jika konsultasi berakhir tanpa solusi
        if ($statusMessage['status'] === 'tidak_ada_solusi') {
            $konsultasi->update([
                'status' => 'selesai',
                'hasil_konsultasi' => $forwardChaining->getHasilKonsultasi(),
                'completed_at' => now()
            ]);

            return redirect()->route('konsultasi.result', $id)
                ->with('warning', $statusMessage['message']);
        }

        // Cek apakah konsultasi sudah selesai dengan solusi
        if ($forwardChaining->isKonsultasiSelesai()) {
            $hasilKonsultasi = $forwardChaining->getHasilKonsultasi();

            $konsultasi->update([
                'status' => 'selesai',
                'hasil_konsultasi' => $hasilKonsultasi,
                'completed_at' => now()
            ]);

            return redirect()->route('konsultasi.result', $id)
                ->with('success', 'Konsultasi telah selesai');
        }

        // Ambil detail konsultasi terakhir untuk konsultasi ini
        $detailKonsultasiTerakhir = DetailKonsultasi::where('konsultasi_id', $konsultasi->id)
            ->orderBy('created_at', 'desc')
            ->first();

        // Ambil fakta untuk pertanyaan terakhir yang tercatat di konsultasi
        $faktaTerakhirDiKonsultasi = Fakta::where('kode', $konsultasi->pertanyaan_terakhir)
            ->where('kategori', $konsultasi->sesi) // Asumsi kategori fakta sesuai dengan sesi
            ->first();

        // Periksa apakah ada detail konsultasi dan apakah fakta terakhir sesuai dengan detail terakhir
        if ($faktaTerakhirDiKonsultasi && $detailKonsultasiTerakhir && $detailKonsultasiTerakhir->fakta_id == $faktaTerakhirDiKonsultasi->id) {
            // Pertanyaan terakhir di sesi ini tampaknya sudah dijawab, lanjut ke sesi berikutnya
            return $this->showSessionSummary($konsultasi);
        } else {
            // Jika pertanyaan terakhir belum dijawab (tidak ada detail konsultasi terakhir
            // atau fakta_id tidak sesuai), tetap lanjutkan ke pertanyaan saat ini
            $konsultasi->status = 'sedang_berjalan';
            $konsultasi->save();
            return redirect()->route('konsultasi.question', $konsultasi->id);
        }
    }

    /**
     * Get answers for specific session
     */
    private function getSessionAnswers($konsultasi, $sesi)
    {
        // Mapping sesi ke kategori fakta
        $kategoriBySesi = [
            1 => ['skrining_awal'],
            2 => ['risiko_fraktur', 'klasifikasi_frax'],
            3 => ['asupan_nutrisi', 'evaluasi_nutrisi'],
            4 => ['preferensi_makanan', 'rekomendasi_makanan']
        ];

        $kategoriSesi = $kategoriBySesi[$sesi] ?? [];

        if (empty($kategoriSesi)) {
            return collect([]);
        }

        return $konsultasi->detailKonsultasi()
            ->with('fakta')
            ->whereHas('fakta', function ($query) use ($kategoriSesi) {
                $query->whereIn('kategori', $kategoriSesi);
            })
            ->get()
            ->map(function ($detail) {
                return [
                    'kode' => $detail->fakta->kode,
                    'pertanyaan' => $detail->fakta->pertanyaan,
                    'jawaban' => $detail->jawaban,
                    'kategori' => $detail->fakta->kategori
                ];
            });
    }
    /**
     * Complete consultation manually
     */
    public function complete(Request $request)
    {
        $request->validate([
            'konsultasi_id' => 'required|exists:konsultasi,id'
        ]);

        $konsultasi = Konsultasi::where('id', $request->konsultasi_id)->first();

        // Cek authorization
        if ($konsultasi->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $forwardChaining = new ForwardChainingService($konsultasi);
        $hasilKonsultasi = $forwardChaining->getHasilKonsultasi();

        // Update status konsultasi menjadi selesai
        $konsultasi->update([
            'status' => 'selesai',
            'hasil_konsultasi' => $hasilKonsultasi,
            'completed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Konsultasi telah diselesaikan'
        ]);
    }

    public function print($id)
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
                    }
                ])
                ->get()
                ->groupBy('nutrisi'); // Group berdasarkan jenis nutrisi
        }

        return view('user.konsultasi.print', compact(
            'konsultasi',
            'hasilKonsultasi',
            'detailSolusi',
            'traceInferensi',
            'jawabanUser',
            'statusMessage',
            'rekomendasiNutrisi'
        ));
    }
    public function recent()
    {
        $konsultasi = Konsultasi::where('user_id', Auth::id())
            ->where('status', 'selesai')
            ->latest()
            ->first();

        if (!$konsultasi) {
            return redirect()->route('riwayat.index')
                ->with('warning', 'Anda belum memiliki konsultasi selesai, segera selesaikan konsultasi yang tertunda.');
        }

        return redirect()->route('konsultasi.result', $konsultasi->id)
            ->with('info', 'Menampilkan hasil konsultasi terbaru Anda.');
    }
}