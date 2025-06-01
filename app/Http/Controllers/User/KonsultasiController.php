<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Aturan;
use App\Models\DetailKonsultasi;
use App\Models\Fakta;
use App\Models\InferensiLog;
use App\Models\Konsultasi;
use App\Models\Solusi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class KonsultasiController extends Controller
{
    // Session sequence constants
    const SESI_SEQUENCE = ['risiko_osteoporosis', 'asupan_nutrisi', 'preferensi_makanan'];

    public function index()
    {
        return view('konsultasi.index');
    }

    public function startKonsultasi()
    {
        $pertanyaanPertama = Fakta::where('kategori', 'risiko_osteoporosis')
            ->where('is_first', 1)
            ->where('is_askable', 1)
            ->first();

        if (!$pertanyaanPertama) {
            return back()->with('error', 'Tidak ada pertanyaan awal untuk konsultasi.');
        }

        $konsultasi = Konsultasi::create([
            'user_id' => Auth::id(),
            'pertanyaan_terakhir' => $pertanyaanPertama->kode,
            'status' => 'sedang_berjalan',
            'sesi' => 'risiko_osteoporosis',
        ]);

        return redirect()->route('konsultasi.question', $konsultasi->id);
    }

    public function showQuestion(Konsultasi $konsultasi)
    {
        if ($konsultasi->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke konsultasi ini.');
        }

        if ($konsultasi->status === 'selesai') {
            return redirect()->route('konsultasi.result', $konsultasi->id);
        }

        $currentSesiIndex = array_search($konsultasi->sesi, self::SESI_SEQUENCE);
        $tampilkanRingkasanSesi = false;
        $ringkasanSesi = null;

        if ($konsultasi->wasChanged('sesi') && $currentSesiIndex > 0) {
            $tampilkanRingkasanSesi = true;
            $riwayatHasil = explode("\n\n", $konsultasi->hasil_konsultasi ?? '');
            if (count($riwayatHasil) >= $currentSesiIndex) {
                $ringkasanSesi = $riwayatHasil[$currentSesiIndex - 1];
            }
        }

        $fakta = Fakta::where('kode', $konsultasi->pertanyaan_terakhir)
            ->where('is_askable', 1)
            ->first();

        $dataSesi = $this->getDataSesi($konsultasi->sesi);

        if (!$fakta) {
            return $this->showSessionSummary($konsultasi);
        }

        extract($dataSesi['progress']);
        return view('user.konsultasi.question', compact(
            'konsultasi',
            'fakta',
            'judul',
            'value',
            'class',
            'sesi',
            'tampilkanRingkasanSesi',
            'ringkasanSesi'
        ));
    }

    public function processAnswer(Request $request, Konsultasi $konsultasi)
    {
        if ($konsultasi->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke konsultasi ini.');
        }

        $request->validate([
            'jawaban' => 'required|in:ya,tidak',
            'fakta_kode' => 'required|exists:fakta,kode',
        ]);

        $faktaSaatIni = Fakta::where('kode', $request->input('fakta_kode'))->firstOrFail();

        // Record user's answer
        DetailKonsultasi::create([
            'konsultasi_id' => $konsultasi->id,
            'fakta_id' => $faktaSaatIni->id,
            'jawaban' => $request->input('jawaban'),
        ]);

        // Get current session information
        $currentSesiIndex = array_search($konsultasi->sesi, self::SESI_SEQUENCE);
        $currentSesi = self::SESI_SEQUENCE[$currentSesiIndex];

        // Collect all facts that have been answered "yes" in this session
        $faktaDiketahui = $this->collectKnownFacts($konsultasi->id, $currentSesi);

        // Run forward chaining to derive new facts
        $newInferences = $this->forwardChainingInSession($konsultasi, $faktaDiketahui, $currentSesi);

        // Find the next question based on inference strategy
        $nextFakta = $this->findNextQuestion($faktaDiketahui, $currentSesi, $konsultasi);

        if ($nextFakta) {
            $konsultasi->update(['pertanyaan_terakhir' => $nextFakta->kode]);
            return redirect()->route('konsultasi.question', $konsultasi->id);
        } else {
            // No more questions, finish this session
            $faktaSesiIni = $this->sessionFinalInference($konsultasi, $currentSesi);
            $pesanSesiIni = $this->generatePesanSesi($currentSesi, $konsultasi->id);
            $this->simpanHasilSesi($konsultasi, $pesanSesiIni);

            return $this->showSessionSummary($konsultasi);
        }
    }

    /**
     * Collect all known facts (answered "yes") for a specific session
     */
    protected function collectKnownFacts($konsultasiId, $sesi)
    {
        return DetailKonsultasi::where('konsultasi_id', $konsultasiId)
            ->where('jawaban', 'ya')
            ->whereHas('fakta', function ($query) use ($sesi) {
                $query->where('kategori', $sesi);
            })
            ->with('fakta')
            ->get()
            ->pluck('fakta.kode')
            ->toArray();
    }

    /**
     * Perform forward chaining within a session to derive new facts
     */
    protected function forwardChainingInSession(Konsultasi $konsultasi, array $knownFacts, string $sesi)
    {
        Log::info("🔍 Menjalankan forward chaining untuk sesi: {$sesi}");

        // Ambil semua fakta yang sebelumnya sudah disimpulkan dari session ini
        $previousInferences = InferensiLog::where('konsultasi_id', $konsultasi->id)
            ->whereHas('aturan.konklusiFakta', function ($query) use ($sesi) {
                $query->where('kategori', $sesi);
            })
            ->pluck('fakta_terbentuk')
            ->toArray();

        // Kumpulkan semua fakta yang diketahui (jawaban user + inferensi sebelumnya)
        $allKnownFacts = array_unique(array_merge($knownFacts, $previousInferences));

        // Cek apakah sesi sudah selesai (tidak ada lagi fakta yang bisa ditanyakan)
        $isSessionComplete = $this->isSessionComplete($konsultasi->id, $sesi);

        // Jika sesi belum selesai, hanya lakukan "dry run" untuk memprediksi inferensi
        // tanpa menyimpan ke database
        if (!$isSessionComplete) {
            // Kembalikan array prediksi fakta yang mungkin terbentuk, tapi jangan simpan ke DB
            $potentialNewFacts = $this->predictPotentialInferences($allKnownFacts, $sesi, $previousInferences);
            Log::info("💡 Potensi inferensi terdeteksi: " . implode(", ", $potentialNewFacts) .
                " (tidak disimpan karena sesi belum selesai)");
            return $potentialNewFacts;
        }

        // ==== EKSEKUSI KODE DI BAWAH HANYA JIKA SESI SUDAH SELESAI ====

        // Ambil semua aturan aktif yang kesimpulannya berupa fakta
        $aturanAktif = Aturan::where('is_active', 1)
            ->where('jenis_konklusi', 'fakta')
            ->get();

        // Kelompokkan kandidat inferensi berdasarkan konklusi
        $inferencesByConclusion = [];
        $newFactsFound = [];

        foreach ($aturanAktif as $aturan) {
            $premis = explode('^', $aturan->premis);
            $konklusiFakta = Fakta::where('kode', $aturan->konklusi)->first();

            // Lewati jika bukan fakta untuk sesi ini
            if (!$konklusiFakta || $konklusiFakta->kategori !== $sesi) {
                continue;
            }

            // Periksa apakah semua premis terpenuhi
            $allPremisesSatisfied = true;
            foreach ($premis as $kodePremis) {
                if (!in_array($kodePremis, $allKnownFacts)) {
                    $allPremisesSatisfied = false;
                    break;
                }
            }

            if ($allPremisesSatisfied) {
                $konklusi = $aturan->konklusi;

                // Lewati jika konklusi ini sudah disimpulkan sebelumnya
                if (in_array($konklusi, $previousInferences)) {
                    continue;
                }

                $premisCount = count($premis);

                // Simpan kandidat inferensi, dikelompokkan berdasarkan konklusi
                if (!isset($inferencesByConclusion[$konklusi])) {
                    $inferencesByConclusion[$konklusi] = [];
                }

                $inferencesByConclusion[$konklusi][] = [
                    'aturan' => $aturan,
                    'premis' => $premis,
                    'jumlah_premis' => $premisCount
                ];

                // Tandai ini sebagai fakta baru
                if (!in_array($konklusi, $newFactsFound)) {
                    $newFactsFound[] = $konklusi;
                }
            }
        }

        // Jika tidak ada fakta baru, kembalikan array kosong
        if (empty($newFactsFound)) {
            Log::info("ℹ️ Tidak ada inferensi valid untuk sesi {$sesi}.");
            return [];
        }

        // HANYA di akhir sesi, simpan inferensi dengan aturan yang memiliki
        // premis terbanyak secara keseluruhan
        $allCandidates = [];
        foreach ($inferencesByConclusion as $konklusi => $kandidatInferensi) {
            foreach ($kandidatInferensi as $candidate) {
                $allCandidates[] = $candidate + ['konklusi' => $konklusi];
            }
        }

        if (!empty($allCandidates)) {
            usort($allCandidates, function ($a, $b) {
                return $b['jumlah_premis'] - $a['jumlah_premis'];
            });

            $bestInferenceOverall = $allCandidates[0];

            InferensiLog::create([
                'konsultasi_id' => $konsultasi->id,
                'aturan_id' => $bestInferenceOverall['aturan']->id,
                'fakta_terbentuk' => $bestInferenceOverall['konklusi'],
                'premis_terpenuhi' => implode(',', $bestInferenceOverall['premis']),
            ]);
            Log::info("✅ Inferensi disimpan: {$bestInferenceOverall['konklusi']} dari aturan {$bestInferenceOverall['aturan']->kode} dengan {$bestInferenceOverall['jumlah_premis']} premis (terbanyak keseluruhan)");
        } else {
            Log::info("ℹ️ Tidak ada inferensi valid untuk disimpan.");
        }

        return $newFactsFound;
    }

    /**
     * Memeriksa apakah sesi sudah selesai (tidak ada lagi fakta yang bisa ditanyakan)
     */
    private function isSessionComplete($konsultasiId, $sesi)
    {
        // Fakta yang sudah ditanyakan di sesi ini
        $askedFactIds = DetailKonsultasi::where('konsultasi_id', $konsultasiId)
            ->whereHas('fakta', function ($q) use ($sesi) {
                $q->where('kategori', $sesi);
            })
            ->pluck('fakta_id')
            ->toArray();

        // Cek apakah masih ada fakta yang bisa ditanyakan
        $remainingAskableFacts = Fakta::where('kategori', $sesi)
            ->where('is_askable', 1)
            ->whereNotIn('id', $askedFactIds)
            ->count();

        return $remainingAskableFacts === 0;
    }

    /**
     * Memprediksi inferensi yang mungkin terbentuk berdasarkan fakta yang diketahui,
     * tanpa menyimpan ke database
     */
    private function predictPotentialInferences(array $knownFacts, string $sesi, array $previousInferences)
    {
        $aturanAktif = Aturan::where('is_active', 1)
            ->where('jenis_konklusi', 'fakta')
            ->get();

        $potentialConclusions = [];

        foreach ($aturanAktif as $aturan) {
            $premis = explode('^', $aturan->premis);
            $konklusiFakta = Fakta::where('kode', $aturan->konklusi)->first();

            // Lewati jika bukan fakta untuk sesi ini
            if (!$konklusiFakta || $konklusiFakta->kategori !== $sesi) {
                continue;
            }

            // Lewati jika konklusi ini sudah disimpulkan sebelumnya
            if (in_array($aturan->konklusi, $previousInferences)) {
                continue;
            }

            // Periksa apakah semua premis terpenuhi
            $allPremisesSatisfied = true;
            foreach ($premis as $kodePremis) {
                if (!in_array($kodePremis, $knownFacts)) {
                    $allPremisesSatisfied = false;
                    break;
                }
            }

            if ($allPremisesSatisfied) {
                $potentialConclusions[] = $aturan->konklusi;
            }
        }

        return array_unique($potentialConclusions);
    }

    /**
     * Find the next question to ask based on the current state
     */
    protected function findNextQuestion(array $faktaDiketahui, string $sesi, Konsultasi $konsultasi)
    {
        Log::info("Finding next question for session: {$sesi}");

        // Get facts that have already been asked in this session
        $faktaSudahDitanyakan = DetailKonsultasi::where('konsultasi_id', $konsultasi->id)
            ->whereHas('fakta', function ($query) use ($sesi) {
                $query->where('kategori', $sesi);
            })
            ->with('fakta')
            ->get()
            ->pluck('fakta.kode')
            ->toArray();

        // Check if any session conclusion has been reached
        $kesimpulanSesiTercapai = $this->checkSessionConclusionReached($konsultasi->id, $sesi);
        if ($kesimpulanSesiTercapai) {
            Log::info("Session conclusion already reached for {$sesi}, no more questions needed.");
            return null;
        }

        // Find rules that are partially satisfied
        $ruleGroups = $this->findPartiallyMatchedRules($faktaDiketahui, $faktaSudahDitanyakan, $sesi);

        // Sort rules by number of known premises (descending)
        // Gunakan stable sort untuk mempertahankan urutan asli jika jumlah premis sama
        uasort($ruleGroups, function ($a, $b) {
            if ($b['knownCount'] === $a['knownCount']) {
                // Jika jumlah premis yang diketahui sama, bandingkan jumlah total premis
                return $b['totalPremis'] <=> $a['totalPremis'];
            }
            return $b['knownCount'] <=> $a['knownCount'];
        });

        // Try to find the next question from partially matched rules
        // Sorted berdasarkan aturan dengan premis terbanyak (dari fungsi di atas)
        foreach ($ruleGroups as $group) {
            foreach ($group['unaskedPremises'] as $kodePremis) {
                $faktaPremis = Fakta::where('kode', $kodePremis)
                    ->where('is_askable', 1)
                    ->where('kategori', $sesi)
                    ->first();

                if ($faktaPremis) {
                    Log::info("Next question selected: {$faktaPremis->kode} from rule {$group['aturan']->kode}");
                    return $faktaPremis;
                }
            }
        }

        // If no rule-based questions found, try any remaining askable facts
        $faktaTersisa = Fakta::where('kategori', $sesi)
            ->where('is_askable', 1)
            ->whereNotIn('kode', $faktaSudahDitanyakan)
            ->first();

        if ($faktaTersisa) {
            Log::info("No rule-based questions found, using remaining fact: {$faktaTersisa->kode}");
        } else {
            Log::info("No more questions available in this session.");
        }

        return $faktaTersisa;
    }


    /**
     * Check if a session conclusion has been reached
     */
    private function checkSessionConclusionReached(int $konsultasiId, string $sesi)
    {
        // Dapatkan semua fakta yang sudah diinferensi untuk sesi ini
        $inferredFacts = InferensiLog::where('konsultasi_id', $konsultasiId)
            ->whereHas('aturan.konklusiFakta', function ($query) use ($sesi) {
                $query->where('kategori', $sesi);
            })
            ->pluck('fakta_terbentuk')
            ->toArray();

        // Dapatkan semua aturan untuk sesi ini
        $allRules = Aturan::where('is_active', 1)
            ->whereHas('konklusiFakta', function ($query) use ($sesi) {
                $query->where('kategori', $sesi);
            })
            ->get();

        // Cek apakah ada aturan yang belum terpenuhi
        foreach ($allRules as $rule) {
            $premis = explode('^', $rule->premis);
            $allPremisesSatisfied = true;

            foreach ($premis as $kodePremis) {
                if (!in_array($kodePremis, $inferredFacts)) {
                    $allPremisesSatisfied = false;
                    break;
                }
            }

            // Jika ada aturan yang belum terpenuhi, sesi belum selesai
            if (!$allPremisesSatisfied) {
                return false;
            }
        }

        return !empty($inferredFacts);
    }

    /**
     * Find rules that are partially matched based on known facts
     */
    /**
     * Temukan aturan yang belum sepenuhnya terpenuhi tapi sebagian premisnya sudah cocok.
     */
    protected function findPartiallyMatchedRules(array $faktaDiketahui, array $faktaSudahDitanyakan, string $sesi)
    {
        $aturanList = Aturan::where('is_active', 1)
            ->where('jenis_konklusi', 'fakta')
            ->get();

        $result = [];

        foreach ($aturanList as $aturan) {
            $premis = explode('^', $aturan->premis);
            $knownCount = 0;
            $unaskedPremises = [];

            foreach ($premis as $kodePremis) {
                if (in_array($kodePremis, $faktaDiketahui)) {
                    $knownCount++;
                } elseif (!in_array($kodePremis, $faktaSudahDitanyakan)) {
                    $unaskedPremises[] = $kodePremis;
                }
            }

            // Tambahkan hanya jika sebagian premis diketahui dan belum semua terpenuhi
            if ($knownCount > 0 && $knownCount < count($premis)) {
                $result[$aturan->id] = [
                    'aturan' => $aturan,
                    'knownCount' => $knownCount,
                    'totalPremis' => count($premis),
                    'unaskedPremises' => $unaskedPremises,
                ];
            }
        }

        // Urutkan terlebih dahulu berdasarkan jumlah total premis DESC, lalu knownCount DESC
        // Gunakan stable sort untuk mempertahankan urutan asli jika nilai sama
        uasort($result, function ($a, $b) {
            if ($b['totalPremis'] === $a['totalPremis']) {
                return $b['knownCount'] <=> $a['knownCount'];
            }
            return $b['totalPremis'] <=> $a['totalPremis'];
        });

        return $result;
    }

    /**
     * Perform final inference for a session
     */
    /**
     * Perform final inference for a session
     */
    protected function sessionFinalInference(Konsultasi $konsultasi, string $sesi): array
    {
        Log::info("Performing final inference for session: {$sesi}");

        // Get facts inferred in this session from inference logs
        $inferredFacts = InferensiLog::where('konsultasi_id', $konsultasi->id)
            ->whereHas('aturan.konklusiFakta', function ($query) use ($sesi) {
                $query->where('kategori', $sesi);
            })
            ->pluck('fakta_terbentuk')
            ->toArray();

        // Jika ada inferensi, ambil yang pertama
        if (!empty($inferredFacts)) {
            Log::info("Session {$sesi} inference (first):", [reset($inferredFacts)]);
            return [reset($inferredFacts)];
        } else {
            // Jika tidak ada inferensi, coba ambil fakta default
            $faktaDefault = Fakta::where('is_default', true)
                ->where('kategori', $sesi)
                ->first();

            if ($faktaDefault) {
                Log::info("No conclusions reached, using default fact for session {$sesi}: {$faktaDefault->kode}");

                // Periksa apakah fakta default sudah ada di log
                $defaultAlreadyInferred = InferensiLog::where('konsultasi_id', $konsultasi->id)
                    ->where('fakta_terbentuk', $faktaDefault->kode)
                    ->exists();

                if (!$defaultAlreadyInferred) {
                    // Record the default fact inference
                    InferensiLog::create([
                        'konsultasi_id' => $konsultasi->id,
                        'aturan_id' => null,
                        'fakta_terbentuk' => $faktaDefault->kode,
                        'premis_terpenuhi' => 'default',
                    ]);
                }
                return [$faktaDefault->kode];
            } else {
                Log::info("Session {$sesi}: No inference and no default fact found.");
                return [];
            }
        }
    }
    /**
     * Execute final inference to find solutions based on all accumulated facts
     */
    protected function finalInference(Konsultasi $konsultasi): array
    {
        Log::info("Starting final inference for consultation ID: {$konsultasi->id}");

        // Collect all known facts (from answers and session conclusions)
        $allKnownFacts = $this->collectAllKnownFacts($konsultasi);
        Log::info("All accumulated facts for final inference:", $allKnownFacts);

        // Ambil daftar solusi yang sudah pernah disimpulkan sebelumnya
        $previousSolutions = InferensiLog::where('konsultasi_id', $konsultasi->id)
            ->whereHas('aturan', function ($q) {
                $q->where('jenis_konklusi', 'solusi');
            })
            ->pluck('fakta_terbentuk')
            ->toArray();

        // Forward chaining untuk menemukan solusi
        $aturanSolusiAktif = Aturan::where('is_active', 1)
            ->where('jenis_konklusi', 'solusi')
            ->get();

        // Kumpulkan semua kandidat solusi
        $solutionCandidates = [];
        $foundSolutions = [];

        foreach ($aturanSolusiAktif as $aturan) {
            $premis = explode('^', $aturan->premis);
            $konklusi = $aturan->konklusi;

            // Lewati jika solusi ini sudah pernah disimpulkan
            if (in_array($konklusi, $previousSolutions)) {
                $foundSolutions[] = $konklusi;
                continue;
            }

            // Periksa apakah semua premis terpenuhi
            $allPremisesSatisfied = true;
            foreach ($premis as $kodePremis) {
                if (!in_array($kodePremis, $allKnownFacts)) {
                    $allPremisesSatisfied = false;
                    break;
                }
            }

            if ($allPremisesSatisfied) {
                // Simpan kandidat solusi dikelompokkan berdasarkan kode solusi
                if (!isset($solutionCandidates[$konklusi])) {
                    $solutionCandidates[$konklusi] = [];
                }

                $solutionCandidates[$konklusi][] = [
                    'aturan' => $aturan,
                    'premis' => $premis,
                    'jumlah_premis' => count($premis)
                ];

                // Tandai sebagai solusi yang ditemukan
                if (!in_array($konklusi, $foundSolutions)) {
                    $foundSolutions[] = $konklusi;
                }
            }
        }

        // Untuk setiap kode solusi, simpan hanya aturan dengan premis terbanyak
        foreach ($solutionCandidates as $konklusi => $candidates) {
            // Urutkan kandidat berdasarkan jumlah premis (terbanyak di awal)
            // Gunakan stable sort untuk mempertahankan urutan asli jika jumlah premis sama
            usort($candidates, function ($a, $b) {
                return $b['jumlah_premis'] <=> $a['jumlah_premis'];
            });

            // Ambil kandidat terbaik (premis terbanyak)
            $bestSolution = $candidates[0];

            // Simpan solusi terbaik ke database
            InferensiLog::create([
                'konsultasi_id' => $konsultasi->id,
                'aturan_id' => $bestSolution['aturan']->id,
                'fakta_terbentuk' => $konklusi,
                'premis_terpenuhi' => implode(',', $bestSolution['premis']),
            ]);

            Log::info("✅ Solusi disimpan: {$konklusi} dari aturan {$bestSolution['aturan']->kode} dengan {$bestSolution['jumlah_premis']} premis");
        }

        // Jika tidak ada solusi yang ditemukan, gunakan solusi default
        if (empty($foundSolutions)) {
            $solusiDefault = Solusi::where('is_default', true)->first();

            if ($solusiDefault) {
                $aturanDefaultSolusi = Aturan::where('solusi_id', $solusiDefault->id)
                    ->where('jenis_konklusi', 'solusi')
                    ->first();

                if ($aturanDefaultSolusi) {
                    $foundSolutions[] = $aturanDefaultSolusi->konklusi;

                    // Periksa apakah solusi default sudah disimpan sebelumnya
                    $defaultAlreadyInferred = InferensiLog::where('konsultasi_id', $konsultasi->id)
                        ->where('fakta_terbentuk', $aturanDefaultSolusi->konklusi)
                        ->exists();

                    if (!$defaultAlreadyInferred) {
                        // Catat inferensi solusi default
                        InferensiLog::create([
                            'konsultasi_id' => $konsultasi->id,
                            'aturan_id' => $aturanDefaultSolusi->id,
                            'fakta_terbentuk' => $aturanDefaultSolusi->konklusi,
                            'premis_terpenuhi' => 'default',
                        ]);

                        Log::info("Using default solution: {$aturanDefaultSolusi->konklusi}");
                    }
                }
            }
        }

        Log::info("Final inference complete. Solutions found:", $foundSolutions);
        return $foundSolutions;
    }


    /**
     * Collect all known facts from the entire consultation
     */
    private function collectAllKnownFacts(Konsultasi $konsultasi): array
    {
        // Get all user "yes" answers
        $userYesAnswers = DetailKonsultasi::where('konsultasi_id', $konsultasi->id)
            ->where('jawaban', 'ya')
            ->get()
            ->pluck('fakta.kode')
            ->filter()
            ->toArray();

        // Get all inferred facts
        $inferredFacts = InferensiLog::where('konsultasi_id', $konsultasi->id)
            ->whereHas('aturan', function ($q) {
                $q->where('jenis_konklusi', 'fakta');
            })
            ->pluck('fakta_terbentuk')
            ->toArray();

        // Combine and return unique facts
        return array_unique(array_merge($userYesAnswers, $inferredFacts));
    }

    /**
     * Generate session summary message
     */
    protected function generatePesanSesi(string $sesiName, int $konsultasi)
    {
        $pesanSesi = [];

        // Mendefinisikan pemetaan sesi ke fakta yang seharusnya ditampilkan
        $sesiToFaktaMap = [
            'risiko_osteoporosis' => ['Risiko'],
            'asupan_nutrisi' => ['Asupan'],
            'preferensi_makanan' => ['Preferensi']
        ];

        // Menentukan pola kata kunci untuk sesi ini
        $keywordsForThisSesi = $sesiToFaktaMap[strtolower($sesiName)] ?? [];

        // Mendapatkan semua fakta (hasil inferensi) untuk konsultasi ini yang sesuai dengan sesi
        $semuaFaktaInferensiSesi = InferensiLog::where('konsultasi_id', $konsultasi)
            ->whereHas('aturan', function ($q) {
                $q->where('jenis_konklusi', 'fakta');
            })
            ->whereHas('faktaTerbentukData', function ($q) {
                $q->whereNotNull('deskripsi');
            })
            ->get()
            ->filter(function ($log) use ($keywordsForThisSesi) {
                if ($log->faktaTerbentukData && $log->faktaTerbentukData->deskripsi) {
                    foreach ($keywordsForThisSesi as $keyword) {
                        if (strpos(trim($log->faktaTerbentukData->deskripsi), $keyword) === 0) {
                            return true;
                        }
                    }
                }
                return false;
            });

        if ($semuaFaktaInferensiSesi->isNotEmpty()) {
            $pesanSesi[] = "Berdasarkan inferensi pada sesi {$this->formatSesiName($sesiName)}:";
            foreach ($semuaFaktaInferensiSesi as $log) {
                $pesanSesi[] = "- " . $log->faktaTerbentukData->deskripsi;
            }
            return implode("\n", $pesanSesi);
        } else {
            // Tidak ada fakta inferensi, coba ambil fakta default yang tercatat untuk sesi ini
            $faktaDefaultLog = InferensiLog::where('konsultasi_id', $konsultasi)
                ->where('premis_terpenuhi', 'default')
                ->whereHas('faktaTerbentukData', function ($q) use ($sesiName) {
                    $q->where('kategori', strtolower($sesiName))
                        ->whereNotNull('deskripsi');
                })
                ->first();

            if ($faktaDefaultLog && $faktaDefaultLog->faktaTerbentukData) {
                return "Berdasarkan konfigurasi default untuk sesi {$this->formatSesiName($sesiName)}:\n- " . $faktaDefaultLog->faktaTerbentukData->deskripsi;
            } else {
                return "Sesi {$this->formatSesiName($sesiName)} telah selesai. Tidak ada inferensi atau fakta default yang dapat ditampilkan saat ini.";
            }
        }
    }

    private function formatSesiName(string $sesiName): string
    {
        return ucwords(str_replace('_', ' ', $sesiName));
    }

    /**
     * Save session results to consultation record
     */
    protected function simpanHasilSesi(Konsultasi $konsultasi, string $pesan)
    {
        $hasilSaatIni = $konsultasi->hasil_konsultasi ?? '';
        $separator = $hasilSaatIni ? "\n\n" : '';
        $konsultasi->update(['hasil_konsultasi' => $hasilSaatIni . $separator . $pesan]);
    }

    /**
     * Show session summary before continuing to next session
     */
    public function showSessionSummary($konsultasi)
    {
        if ($konsultasi->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke konsultasi ini.');
        }
        // Find the index of the current session
        $currentSesiIndex = array_search($konsultasi->sesi, self::SESI_SEQUENCE);

        // Generate session message
        $sessionMessage = $this->generatePesanSesi($konsultasi->sesi, $konsultasi->id);

        // Get progress data
        $dataSesi = $this->getDataSesi($konsultasi->sesi);
        extract($dataSesi['progress']);

        return view('user.konsultasi.session_summary', compact(
            'konsultasi',
            'sessionMessage',
            'judul',
            'value',
            'class',
            'sesi',
        ));
    }

    /**
     * Continue to next session after viewing summary
     */
    public function continueSession(Request $request, Konsultasi $konsultasi)
    {
        if ($konsultasi->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke konsultasi ini.');
        }

        // Find the index of the current session
        $currentSesiIndex = array_search($konsultasi->sesi, self::SESI_SEQUENCE);

        if ($currentSesiIndex !== false && $currentSesiIndex < count(self::SESI_SEQUENCE) - 1) {
            $nextSesiIndex = $currentSesiIndex + 1;
            $nextSesi = self::SESI_SEQUENCE[$nextSesiIndex];

            $pertanyaanPertamaSesiBerikut = Fakta::where('kategori', $nextSesi)
                ->where('is_first', 1)
                ->first();

            if ($pertanyaanPertamaSesiBerikut) {
                $konsultasi->update([
                    'sesi' => $nextSesi,
                    'pertanyaan_terakhir' => $pertanyaanPertamaSesiBerikut->kode,
                ]);
                return redirect()->route('konsultasi.question', $konsultasi->id);
            }
        }

        return $this->finishKonsultasi($konsultasi);
    }

    /**
     * Move to next session or finish consultation
     */
    protected function lanjutkanSesi(Konsultasi $konsultasi)
    {
        // Generate and save session results
        $pesan = $this->generatePesanSesi($konsultasi->sesi, $konsultasi->id);
        $this->simpanHasilSesi($konsultasi, $pesan);

        $currentSesiIndex = array_search($konsultasi->sesi, self::SESI_SEQUENCE);

        if ($currentSesiIndex !== false && $currentSesiIndex < count(self::SESI_SEQUENCE) - 1) {
            // Redirect to session summary instead of immediately going to next session
            return redirect()->route('konsultasi.session-summary', $konsultasi->id);
        }

        return $this->finishKonsultasi($konsultasi);
    }

    protected function getFilteredInferenceLogs($konsultasiId)
    {
        // Dapatkan semua log inferensi
        $allLogs = InferensiLog::where('konsultasi_id', $konsultasiId)
            ->with('aturan')
            ->get()
            ->groupBy('fakta_terbentuk');

        $filteredLogs = collect();

        foreach ($allLogs as $conclusion => $logs) {
            // Cari log dengan premis terbanyak untuk setiap konklusi
            $mostSpecific = $logs->sortByDesc(function ($log) {
                return count(explode(',', $log->premis_terpenuhi));
            })->first();

            $filteredLogs->push($mostSpecific);
        }

        return $filteredLogs->sortBy('created_at');
    }

    /**
     * Show consultation results
     */
    public function showResult(Konsultasi $konsultasi)
    {
        if ($konsultasi->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke konsultasi ini.');
        }

        if ($konsultasi->status !== 'selesai') {
            return redirect()->route('konsultasi.question', $konsultasi->id);
        }

        $detailKonsultasi = DetailKonsultasi::where('konsultasi_id', $konsultasi->id)
            ->with('fakta')
            ->get();

        $inferensiLog = InferensiLog::where('konsultasi_id', $konsultasi->id)
            ->with('aturan')
            ->get();

        $filteredLogs = $this->getFilteredInferenceLogs($konsultasi->id);
        $solusiAkhir = null;

        $inferensiSolusi = $inferensiLog->filter(function ($item) {
            return $item->aturan && $item->aturan->jenis_konklusi === 'solusi';
        })->last();

        if ($inferensiSolusi) {
            $solusiAkhir = Solusi::find($inferensiSolusi->aturan->solusi_id);
            if ($solusiAkhir) {
                $solusiAkhir->load('rekomendasiNutrisi.sumberNutrisi');
            }
        }

        return view('user.konsultasi.result', compact(
            'konsultasi',
            'inferensiSolusi',
            'detailKonsultasi',
            'inferensiLog',
            'filteredLogs',
            'solusiAkhir'
        ));
    }

    /**
     * Get session data for progress display
     */
    private function getDataSesi(string $sesi): ?array
    {
        $pertanyaan = Fakta::where('kategori', $sesi)->where('is_first', 1)->first();
        if (!$pertanyaan) {
            return null;
        }

        $urutanSesi = array_search($sesi, self::SESI_SEQUENCE) + 1;
        $totalSesi = count(self::SESI_SEQUENCE);
        $progressValue = round(($urutanSesi / $totalSesi) * 100);
        $progressClass = 'progress-' . $progressValue;
        $sesiKe = "{$urutanSesi}/{$totalSesi}";

        return [
            'progress' => [
                'judul' => ucwords(str_replace('_', ' ', $sesi)),
                'value' => $progressValue,
                'class' => $progressClass,
                'sesi' => $sesiKe,
            ],
        ];
    }

    /**
     * Finish consultation and calculate final results
     */
    protected function finishKonsultasi(Konsultasi $konsultasi)
    {
        // Perform final inference to find solutions
        $this->finalInference($konsultasi);

        $konsultasi->update([
            'status' => 'selesai',
            'completed_at' => now(),
            'pertanyaan_terakhir' => null,
        ]);

        return redirect()->route('konsultasi.result', $konsultasi->id);
    }

    /**
     * Set consultation to pending status
     */
    /**
     * Set consultation to pending status
     */
    public function pending(Request $request)
    {
        $konsultasiId = $request->input('konsultasi_id');
        $konsultasi = Konsultasi::where('id', $konsultasiId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$konsultasi) {
            return response()->json(['danger' => false, 'message' => 'Konsultasi tidak ditemukan.'], 404);
        }

        if ($konsultasi->status == 'selesai') {
            return response()->json(['success' => false, 'message' => 'Konsultasi yang sudah selesai tidak dapat ditunda.'], 400);
        }

        // Check if we are coming from a session summary page
        $isSummary = $request->input('is_summary', false);

        if ($isSummary) {
            // If we're showing a session summary (no more questions in current session),
            // prepare for the next session before pausing
            $currentSesiIndex = array_search($konsultasi->sesi, self::SESI_SEQUENCE);

            if ($currentSesiIndex !== false && $currentSesiIndex < count(self::SESI_SEQUENCE) - 1) {
                $nextSesiIndex = $currentSesiIndex + 1;
                $nextSesi = self::SESI_SEQUENCE[$nextSesiIndex];

                $pertanyaanPertamaSesiBerikut = Fakta::where('kategori', $nextSesi)
                    ->where('is_first', 1)
                    ->first();

                if ($pertanyaanPertamaSesiBerikut) {
                    // Update to the next session and set the first question
                    $konsultasi->update([
                        'sesi' => $nextSesi,
                        'pertanyaan_terakhir' => $pertanyaanPertamaSesiBerikut->kode,
                    ]);
                    Log::info("Konsultasi ditunda dari halaman summary: konsultasi {$konsultasi->id} dipersiapkan untuk sesi berikutnya ({$nextSesi})");
                }
            }
        } else {
            // For regular postponing during active questioning, keep the current question
            Log::info("Konsultasi ditunda pada pertanyaan {$konsultasi->pertanyaan_terakhir}");
        }

        $konsultasi->status = 'belum_selesai';
        $konsultasi->save();

        return response()->json(['success' => true, 'message' => 'Konsultasi berhasil ditunda.']);
    }

    /**
     * Print consultation results
     */
    public function printKonsultasi(Konsultasi $konsultasi)
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

        return view('user.konsultasi.print', compact('konsultasi', 'detailKonsultasi', 'inferensiLog', 'solusiAkhir'));
    }

    /**
     * Show most recent completed consultation
     */
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

        $detailKonsultasi = DetailKonsultasi::where('konsultasi_id', $konsultasi->id)->with('fakta')->get();
        $inferensiLog = InferensiLog::where('konsultasi_id', $konsultasi->id)->with('aturan')->get();
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


        return view('user.konsultasi.result', compact('konsultasi', 'detailKonsultasi', 'inferensiLog', 'solusiAkhir', 'inferensiSolusi'));
    }

    public function continueKonsultasi($konsultasiId)
    {
        $konsultasi = Konsultasi::findOrFail($konsultasiId);

        if ($konsultasi->user_id !== Auth::id()) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak memiliki akses ke konsultasi ini.');
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

}