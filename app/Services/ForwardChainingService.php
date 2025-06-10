<?php

namespace App\Services;

use App\Models\Konsultasi;
use App\Models\Aturan;
use App\Models\Fakta;
use App\Models\DetailKonsultasi;
use App\Models\InferensiLog;
use App\Models\Solusi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ForwardChainingService
{
    protected $konsultasi;
    protected $faktaTersedia = [];
    protected $faktaAntara = [];
    protected $solusiDitemukan = [];
    protected $aturanTerpakai = [];
    protected $sesiSekarang = 1;

    public function __construct(Konsultasi $konsultasi)
    {
        $this->konsultasi = $konsultasi;
        $this->sesiSekarang = $konsultasi->sesi ?? 1;
        $this->loadFaktaTersedia();
    }

    /**
     * Load fakta yang sudah dijawab sebelumnya
     */
    protected function loadFaktaTersedia()
    {
        // Load fakta dari jawaban user (ya)
        $faktaJawaban = $this->konsultasi->detailKonsultasi()
            ->where('jawaban', 'ya')
            ->with('fakta')
            ->get()
            ->pluck('fakta.kode')
            ->toArray();

        // Load fakta antara dari log inferensi
        $faktaAntaraLog = $this->konsultasi->inferensiLog()
            ->where('fakta_terbentuk', 'like', 'FA%')
            ->pluck('fakta_terbentuk')
            ->toArray();

        // Load solusi dari log inferensi
        $solusiLog = $this->konsultasi->inferensiLog()
            ->where('fakta_terbentuk', 'like', 'S%')
            ->pluck('fakta_terbentuk')
            ->toArray();

        // Load aturan yang sudah terpakai
        $this->aturanTerpakai = $this->konsultasi->inferensiLog()
            ->pluck('aturan_id')
            ->toArray();

        $this->faktaTersedia = array_merge($faktaJawaban, $faktaAntaraLog, $solusiLog);
        $this->faktaAntara = $faktaAntaraLog;
        $this->solusiDitemukan = $solusiLog;
    }

    /**
     * Proses forward chaining untuk mendapatkan pertanyaan berikutnya
     */
    public function getNextQuestion()
    {
        // Proses inferensi berdasarkan sesi
        $this->prosesInferensi();

        // Cek apakah sesi ini dapat menghasilkan fakta antara atau solusi
        if (!$this->canSesiProgress()) {
            return null;
        }

        // PERBAIKAN: Ambil pertanyaan berdasarkan prioritas aturan yang tepat
        $pertanyaanBerikutnya = $this->getPertanyaanBerikutnyaDenganPrioritas();

        if ($pertanyaanBerikutnya) {
            $fakta = Fakta::where('kode', $pertanyaanBerikutnya)->first();
            if ($fakta) {
                return $fakta;
            }
        }

        return null;
    }



    /**
     * METODE BARU: Cek apakah sesi tertentu menghasilkan fakta antara atau solusi
     */
    public function hasSesiResults($sesi)
    {
        $prefixAturan = [
            1 => 'R1',
            2 => 'R2',
            3 => 'R3',
            4 => 'R4'
        ];

        $prefix = $prefixAturan[$sesi] ?? 'R1';

        // Cek apakah ada fakta antara atau solusi yang dihasilkan dari aturan sesi ini
        $hasilSesi = $this->konsultasi->inferensiLog()
            ->whereHas('aturan', function ($q) use ($prefix) {
                $q->where('kode', 'like', $prefix . '%');
            })
            ->where(function ($q) {
                $q->where('fakta_terbentuk', 'like', 'FA%')
                    ->orWhere('fakta_terbentuk', 'like', 'S%');
            })
            ->exists();

        return $hasilSesi;
    }

    /**
     * METODE BARU: Get fakta antara dan solusi untuk sesi spesifik
     */
    public function getSesiResults($sesi = null)
    {
        $sesi = $sesi ?? $this->sesiSekarang;
        $prefixAturan = [
            0 => 'R0',
            1 => 'R1',
            2 => 'R2',
            3 => 'R3',
            4 => 'R4'
        ];

        $prefix = $prefixAturan[$sesi] ?? 'R1';
        $nextPrefix = $prefixAturan[$sesi + 1] ?? null;

        $faktaAntaraSesi = $this->konsultasi->inferensiLog()
            ->whereHas('aturan', function ($q) use ($prefix) {
                $q->where('kode', 'like', $prefix . '%');
            })
            ->where('fakta_terbentuk', 'like', 'FA%')
            ->pluck('fakta_terbentuk')
            ->toArray();

        $solusiSesi = $this->konsultasi->inferensiLog()
            ->whereHas('aturan', function ($q) use ($prefix) {
                $q->where('kode', 'like', $prefix . '%');
            })
            ->where('fakta_terbentuk', 'like', 'S%')
            ->pluck('fakta_terbentuk')
            ->toArray();

        $relevanUntukSesiBerikutnya = false;
        if ($nextPrefix) {
            $faktaDibutuhkanSesiBerikutnya = Aturan::where('kode', 'like', $nextPrefix . '%')
                ->pluck('premis')
                ->flatMap(function ($premise) {
                    return array_map('trim', explode('^', $premise));
                })
                ->filter(function ($kode) {
                    return strpos($kode, 'FA') === 0;
                })
                ->unique()
                ->values()
                ->toArray();

            $relevanUntukSesiBerikutnya = count(array_intersect($faktaAntaraSesi, $faktaDibutuhkanSesiBerikutnya)) > 0;
        }

        return [
            'fakta_antara' => $faktaAntaraSesi,
            'solusi' => $solusiSesi,
            'has_results' => !empty($faktaAntaraSesi) || !empty($solusiSesi),
            'relevant_for_next_session' => $relevanUntukSesiBerikutnya
        ];
    }

    /**
     * METODE BARU: Cek apakah sesi masih bisa berlanjut
     * Mengecek apakah ada kemungkinan menghasilkan fakta antara atau solusi
     */
    protected function canSesiProgress()
    {
        $aturanSesi = $this->getAturanBySesi();

        if ($aturanSesi->isEmpty()) {
            Log::debug("No rules available for session");
            return false;
        }

        foreach ($aturanSesi as $aturan) {
            if (in_array($aturan->id, $this->aturanTerpakai)) {
                Log::debug("Rule {$aturan->id} already used");
                continue;
            }
            if ($this->hasRulePotential($aturan)) {
                Log::debug("Rule {$aturan->id} has potential");
                return true;
            }
            Log::debug("Rule {$aturan->id} has no potential");
        }

        if ($this->hasSesiResults($this->sesiSekarang)) {
            $sesiResults = $this->getSesiResults($this->sesiSekarang);
            $result = $sesiResults['relevant_for_next_session'] && $this->sesiSekarang < 4;

            Log::debug("Has results check - relevant: {$sesiResults['relevant_for_next_session']}, sesi: {$this->sesiSekarang}, returning: " . ($result ? 'true' : 'false'));
            return $result;
        }

        Log::debug("No rules with potential found");
        return false;
    }


    /**
     * METODE BARU: Cek apakah aturan masih memiliki potensi untuk dipenuhi
     */
    protected function hasRulePotential($aturan)
    {
        $premisArray = explode('^', $aturan->premis);
        $potentialCount = 0;
        $totalPremis = count($premisArray);

        foreach ($premisArray as $premis) {
            $premis = trim($premis);

            // Jika premis sudah tersedia
            if (in_array($premis, $this->faktaTersedia)) {
                $potentialCount++;
                continue;
            }

            // Jika premis adalah fakta yang bisa ditanyakan
            $fakta = Fakta::where('kode', $premis)->first();
            if ($fakta && $fakta->is_askable) {
                // Cek apakah sudah dijawab 'tidak'
                $sudahDijawabTidak = $this->konsultasi->detailKonsultasi()
                    ->whereHas('fakta', function ($q) use ($premis) {
                        $q->where('kode', $premis);
                    })
                    ->where('jawaban', 'tidak')
                    ->exists();

                if (!$sudahDijawabTidak) {
                    $potentialCount++;
                }
            }
            // Jika premis adalah fakta antara yang mungkin terbentuk dari aturan lain
            elseif (strpos($premis, 'FA') === 0) {
                // Cek apakah ada aturan lain yang bisa menghasilkan fakta antara ini
                if ($this->canFormIntermediateFact($premis)) {
                    $potentialCount++;
                }
            }
        }

        // Aturan memiliki potensi jika semua premisnya bisa dipenuhi
        return $potentialCount == $totalPremis;
    }

    /**
     * METODE BARU: Cek apakah fakta antara bisa terbentuk
     */
    protected function canFormIntermediateFact($faktaAntara)
    {
        $aturanSesi = $this->getAturanBySesi();

        foreach ($aturanSesi as $aturan) {
            if ($aturan->konklusi == $faktaAntara && !in_array($aturan->id, $this->aturanTerpakai)) {
                if ($this->hasRulePotential($aturan)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Proses jawaban user dan lakukan inferensi
     */
    public function processAnswer($kodeFakta, $jawaban)
    {
        DB::transaction(function () use ($kodeFakta, $jawaban) {
            // Simpan jawaban user
            $fakta = Fakta::where('kode', $kodeFakta)->first();

            if ($fakta) {
                DetailKonsultasi::updateOrCreate(
                    [
                        'konsultasi_id' => $this->konsultasi->id,
                        'fakta_id' => $fakta->id
                    ],
                    [
                        'jawaban' => $jawaban
                    ]
                );

                // Jika jawaban ya, tambahkan ke fakta tersedia
                if ($jawaban === 'ya') {
                    $this->faktaTersedia[] = $kodeFakta;
                }

                // Proses inferensi
                $this->prosesInferensi();
            }
        });
    }

    /**
     * Proses inferensi berdasarkan aturan yang tersedia
     */
    protected function prosesInferensi()
    {
        $iterasi = 0;
        $maxIterasi = 100;

        do {
            $faktaBaruDitemukan = false;
            $aturanSesi = $this->getAturanBySesi();

            foreach ($aturanSesi as $aturan) {
                if (in_array($aturan->id, $this->aturanTerpakai)) {
                    continue;
                }

                if ($this->cekAturanTerpenuhi($aturan)) {
                    $konklusi = $aturan->konklusi;

                    if (!in_array($konklusi, $this->faktaTersedia)) {
                        $this->faktaTersedia[] = $konklusi;

                        if (strpos($konklusi, 'FA') === 0) {
                            $this->faktaAntara[] = $konklusi;
                        } elseif (strpos($konklusi, 'S') === 0) {
                            $this->solusiDitemukan[] = $konklusi;
                        }

                        $this->logInferensi($aturan, $konklusi);
                        $this->aturanTerpakai[] = $aturan->id;
                        $faktaBaruDitemukan = true;

                        Log::info("Aturan {$aturan->kode} terpakai, menghasilkan {$konklusi}");
                    }
                }
            }

            $iterasi++;
        } while ($faktaBaruDitemukan && $iterasi < $maxIterasi);
    }

    /**
     * Cek apakah aturan terpenuhi
     */
    protected function cekAturanTerpenuhi($aturan)
    {
        $premisArray = explode('^', $aturan->premis);

        foreach ($premisArray as $premis) {
            $premis = trim($premis);
            if (!in_array($premis, $this->faktaTersedia)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get aturan berdasarkan sesi dengan pengurutan yang benar
     */
    protected function getAturanBySesi()
    {
        $prefixAturan = [
            1 => 'R1',
            2 => 'R2',
            3 => 'R3',
            4 => 'R4'
        ];

        $prefix = $prefixAturan[$this->sesiSekarang] ?? 'R1';

        $aturan = Aturan::where('kode', 'like', $prefix . '%')
            ->where('is_active', true)
            ->orderBy('kode')
            ->get();

        return $aturan->sortBy(function ($item) {
            preg_match('/\d+\.(\d+)$/', $item->kode, $matches);
            return isset($matches[1]) ? (int) $matches[1] : 0;
        })->values();
    }

    /**
     * Get aturan yang belum terpenuhi dan memiliki pertanyaan yang bisa ditanya
     */
    protected function getPertanyaanBerikutnyaDenganPrioritas()
    {
        $aturanSesi = $this->getAturanBySesi();

        // Buat array untuk menyimpan semua pertanyaan yang mungkin dengan prioritasnya
        $pertanyaanDenganPrioritas = [];

        foreach ($aturanSesi as $aturan) {
            // Skip aturan yang sudah terpakai
            if (in_array($aturan->id, $this->aturanTerpakai)) {
                continue;
            }

            // Skip aturan yang sudah terpenuhi
            if ($this->cekAturanTerpenuhi($aturan)) {
                continue;
            }

            // Cek apakah aturan ini memiliki potensi untuk dipenuhi
            if (!$this->hasRulePotential($aturan)) {
                continue;
            }

            // Ambil pertanyaan yang belum dijawab dari aturan ini
            $pertanyaanBelumDijawab = $this->getPertanyaanBelumDijawab($aturan);

            if (!empty($pertanyaanBelumDijawab)) {
                // Ekstrak nomor aturan untuk prioritas
                $prioritas = $this->ekstrakPrioritasAturan($aturan->kode);

                foreach ($pertanyaanBelumDijawab as $kodeFakta) {
                    // Jika pertanyaan belum ada dalam array atau prioritas lebih tinggi
                    if (
                        !isset($pertanyaanDenganPrioritas[$kodeFakta]) ||
                        $prioritas < $pertanyaanDenganPrioritas[$kodeFakta]['prioritas']
                    ) {

                        $pertanyaanDenganPrioritas[$kodeFakta] = [
                            'prioritas' => $prioritas,
                            'aturan_kode' => $aturan->kode,
                            'fakta_kode' => $kodeFakta
                        ];
                    }
                }
            }
        }

        // Jika tidak ada pertanyaan yang bisa ditanyakan
        if (empty($pertanyaanDenganPrioritas)) {
            return null;
        }

        // Urutkan berdasarkan prioritas (nomor terkecil = prioritas tertinggi)
        uasort($pertanyaanDenganPrioritas, function ($a, $b) {
            return $a['prioritas'] <=> $b['prioritas'];
        });

        // Ambil pertanyaan dengan prioritas tertinggi
        $pertanyaanPrioritas = reset($pertanyaanDenganPrioritas);

        Log::info("Pertanyaan berikutnya: {$pertanyaanPrioritas['fakta_kode']} dari aturan {$pertanyaanPrioritas['aturan_kode']} dengan prioritas {$pertanyaanPrioritas['prioritas']}");

        return $pertanyaanPrioritas['fakta_kode'];
    }


    protected function ekstrakPrioritasAturan($kodeAturan)
    {
        // Contoh: R4.1 -> 1, R4.2 -> 2, R4.5b -> 5.1, R4.18a -> 18.1
        if (preg_match('/R\d+\.(\d+)([a-z]?)/', $kodeAturan, $matches)) {
            $nomorUtama = (int) $matches[1];
            $subNomor = 0;

            // Jika ada huruf (seperti 'a', 'b', 'c'), berikan sub-prioritas
            if (isset($matches[2]) && !empty($matches[2])) {
                $subNomor = ord($matches[2]) - ord('a') + 1; // a=1, b=2, c=3, dst
            }

            // Gabungkan nomor utama dengan sub-nomor (contoh: 5.1, 5.2, 5.3)
            return $nomorUtama + ($subNomor * 0.1);
        }

        return 999; // Default prioritas rendah jika tidak bisa diparse
    }
    public function applyStartingRule()
    {
        // Find the starting rule (R0.1)
        $startingRule = Aturan::where('kode', 'R0.1')->first();
        if ($startingRule) {
            // Immediately apply the rule and log the inference
            $this->logInferensi($startingRule, $startingRule->konklusi);

            // Add the solution to available facts
            $this->faktaTersedia[] = $startingRule->konklusi;
            $this->solusiDitemukan[] = $startingRule->konklusi;
            $this->aturanTerpakai[] = $startingRule->id;
        }

    }

    /**
     * Get pertanyaan yang belum dijawab dari aturan
     */
    protected function getPertanyaanBelumDijawab($aturan)
    {
        $premisArray = explode('^', $aturan->premis);
        $pertanyaanBelumDijawab = [];

        foreach ($premisArray as $premis) {
            $premis = trim($premis);

            // Skip jika premis sudah tersedia
            if (in_array($premis, $this->faktaTersedia)) {
                continue;
            }

            // Hanya ambil fakta yang bisa ditanyakan (is_askable = 1)
            $fakta = Fakta::where('kode', $premis)->first();

            if ($fakta && $fakta->is_askable) {
                // Cek apakah sudah pernah dijawab 'tidak'
                $sudahDijawabTidak = $this->konsultasi->detailKonsultasi()
                    ->whereHas('fakta', function ($q) use ($premis) {
                        $q->where('kode', $premis);
                    })
                    ->where('jawaban', 'tidak')
                    ->exists();

                if (!$sudahDijawabTidak) {
                    $pertanyaanBelumDijawab[] = $premis;
                }
            }
        }

        return $pertanyaanBelumDijawab;
    }

    /**
     * Log inferensi
     */
    protected function logInferensi($aturan, $faktaTerbentuk)
    {
        InferensiLog::create([
            'konsultasi_id' => $this->konsultasi->id,
            'aturan_id' => $aturan->id,
            'fakta_terbentuk' => $faktaTerbentuk,
            'premis_terpenuhi' => $aturan->premis,
            'waktu' => now()
        ]);
    }

    /**
     * Get hasil konsultasi
     */
    public function getHasilKonsultasi()
    {
        return [
            'sesi' => $this->sesiSekarang,
            'fakta_tersedia' => $this->faktaTersedia,
            'fakta_antara' => $this->faktaAntara,
            'solusi' => $this->solusiDitemukan,
            'progress' => $this->hitungProgress(),
            'selesai' => $this->isKonsultasiSelesai(),
            'sesi_dapat_lanjut' => $this->canSesiProgress() // TAMBAHAN BARU
        ];
    }

    /**
     * PERBAIKAN: Cek apakah konsultasi sudah selesai
     */
    public function isKonsultasiSelesai()
    {
        // Jika tidak bisa lanjut sesi dan belum menemukan solusi
        if (!$this->canSesiProgress() && empty($this->solusiDitemukan)) {
            return true;
        }

        // Jika sudah mencapai sesi maksimal (sesi 4) DAN sudah selesai proses sesi 4
        if ($this->sesiSekarang > 4) {
            return true;
        }

        return false;
    }

    /**
     * PERBAIKAN: Cek apakah sesi saat ini sudah selesai
     */
    public function isSesiSelesai()
    {

        // Sesi selesai jika tidak ada aturan yang bisa dijalankan lagi
        if (!$this->canSesiProgress()) {
            return true;
        }

        // Tambahan: Sesi juga selesai jika sudah menghasilkan hasil 
        $sesiResults = $this->getSesiResults($this->sesiSekarang);

        // Jika sesi ini sudah ada hasil tapi tidak relevan untuk sesi berikutnya
        if ($sesiResults['has_results'] && !$sesiResults['relevant_for_next_session']) {
            return true;
        }

        return false;
    }

    /**
     * METODE BARU: Cek apakah konsultasi berakhir tanpa solusi
     */
    public function isKonsultasiBerakhirTanpaSolusi()
    {
        return !$this->canSesiProgress() && empty($this->solusiDitemukan);
    }

    /**
     * METODE BARU: Get pesan status konsultasi
     */
    public function getStatusMessage()
    {
        if ($this->isKonsultasiBerakhirTanpaSolusi()) {
            return [
                'status' => 'tidak_ada_solusi',
                'message' => 'Konsultasi tidak dapat dilanjutkan karena tidak ada aturan yang dapat dipenuhi untuk menghasilkan solusi.',
                'detail' => 'Berdasarkan jawaban yang diberikan, sistem tidak dapat menentukan rekomendasi yang sesuai.'
            ];
        }

        // Kasus khusus ketika di sesi 4 dengan solusi
        if ($this->sesiSekarang == 4 && !empty($this->solusiDitemukan)) {
            $sesiResults = $this->getSesiResults($this->sesiSekarang);

            if (!$sesiResults['relevant_for_next_session']) {
                return [
                    'status' => 'selesai_dengan_solusi',
                    'message' => 'Konsultasi telah selesai dengan berhasil menemukan rekomendasi.',
                    'detail' => 'Sistem telah menganalisis jawaban Anda dan memberikan rekomendasi yang sesuai.'
                ];
            }
        }

        if ($this->isSesiSelesai()) {
            return [
                'status' => 'sesi_selesai',
                'message' => "Konsultasi berakhir karena sesi {$this->sesiSekarang} tidak menghasilkan hasil yang diperlukan untuk melanjutkan",
                'detail' => 'Dapat melanjutkan ke sesi berikutnya atau menyelesaikan konsultasi.'
            ];
        }

        return [
            'status' => 'berjalan',
            'message' => 'Konsultasi sedang berjalan.',
            'detail' => 'Silakan jawab pertanyaan yang tersedia.'
        ];
    }

    /**
     * Hitung progress konsultasi
     */
    protected function hitungProgress()
    {
        $totalSesi = 4;
        $progressPerSesi = 100 / $totalSesi;

        // Progress dasar berdasarkan sesi yang sudah selesai
        $baseProgress = ($this->sesiSekarang - 1) * $progressPerSesi;

        // Hitung progress dalam sesi saat ini berdasarkan pertanyaan yang dijawab
        $totalPertanyaanSesi = $this->getTotalPertanyaanSesi();
        $pertanyaanTerjawabSesi = $this->getPertanyaanTerjawabSesi();

        if ($totalPertanyaanSesi > 0) {
            $progressDalamSesi = ($pertanyaanTerjawabSesi / $totalPertanyaanSesi) * $progressPerSesi;
            $baseProgress += $progressDalamSesi;
        }

        return min(100, round($baseProgress, 2));
    }

    /**
     * METODE BARU: Get total pertanyaan unik yang bisa ditanyakan dalam sesi
     */
    protected function getTotalPertanyaanSesi()
    {
        $aturanSesi = $this->getAturanBySesi();
        $pertanyaanUnik = [];

        foreach ($aturanSesi as $aturan) {
            $premisArray = explode('^', $aturan->premis);

            foreach ($premisArray as $premis) {
                $premis = trim($premis);

                // Hanya hitung fakta yang bisa ditanyakan (is_askable = 1)
                $fakta = Fakta::where('kode', $premis)->first();

                if ($fakta && $fakta->is_askable && !in_array($premis, $pertanyaanUnik)) {
                    $pertanyaanUnik[] = $premis;
                }
            }
        }

        return count($pertanyaanUnik);
    }

    /**
     * METODE BARU: Get jumlah pertanyaan yang sudah dijawab dalam sesi
     */
    protected function getPertanyaanTerjawabSesi()
    {
        // Mapping sesi ke kategori fakta
        $kategoriBySesi = [
            1 => ['skrining_awal'],
            2 => ['risiko_fraktur', 'klasifikasi_frax'],
            3 => ['asupan_nutrisi', 'evaluasi_nutrisi'],
            4 => ['preferensi_makanan', 'rekomendasi_makanan']
        ];

        $kategoriSesi = $kategoriBySesi[$this->sesiSekarang] ?? [];

        if (empty($kategoriSesi)) {
            return 0;
        }

        // Hitung pertanyaan yang sudah dijawab dalam sesi ini
        $pertanyaanTerjawab = $this->konsultasi->detailKonsultasi()
            ->whereHas('fakta', function ($query) use ($kategoriSesi) {
                $query->whereIn('kategori', $kategoriSesi);
            })
            ->count();

        return $pertanyaanTerjawab;
    }

    public function getDetailProgress()
    {
        $totalSesi = 4;
        $progressPerSesi = 100 / $totalSesi;
        $baseProgress = ($this->sesiSekarang - 1) * $progressPerSesi;

        $totalPertanyaanSesi = $this->getTotalPertanyaanSesi();
        $pertanyaanTerjawabSesi = $this->getPertanyaanTerjawabSesi();

        $aturanSesi = $this->getAturanBySesi();
        $totalAturanSesi = $aturanSesi->count();
        $aturanTerpakaiSesi = 0;

        foreach ($aturanSesi as $aturan) {
            if (in_array($aturan->id, $this->aturanTerpakai)) {
                $aturanTerpakaiSesi++;
            }
        }

        return [
            'sesi' => $this->sesiSekarang,
            'base_progress' => $baseProgress,
            'total_pertanyaan_sesi' => $totalPertanyaanSesi,
            'pertanyaan_terjawab_sesi' => $pertanyaanTerjawabSesi,
            'total_aturan_sesi' => $totalAturanSesi,
            'aturan_terpakai_sesi' => $aturanTerpakaiSesi,
            'progress_akhir' => $this->hitungProgress()
        ];
    }

    /**
     * Get detail solusi untuk sesi tertentu
     */
    public function getDetailSolusi($sesiSpesifik = null)
    {
        $sesi = $sesiSpesifik ?? $this->sesiSekarang;
        $solusiSesi = $this->solusiDitemukan;

        if (empty($solusiSesi)) {
            return [];
        }

        return Solusi::whereIn('kode', $solusiSesi)
            ->get()
            ->map(function ($solusi) {
                return [
                    'id' => $solusi->id,
                    'kode' => $solusi->kode,
                    'nama' => $solusi->nama,
                    'deskripsi' => $solusi->deskripsi,
                    'peringatan' => $solusi->peringatan_konsultasi ?? null
                ];
            })
            ->toArray();
    }

    /**
     * Get trace/jejak inferensi untuk sesi tertentu
     */
    public function getTraceInferensi($sesiSpesifik = null)
    {
        $query = $this->konsultasi->inferensiLog()
            ->with(['aturan', 'aturan.solusi'])
            ->orderBy('waktu');

        if ($sesiSpesifik !== null) {
            $prefixAturan = [
                1 => 'R1',
                2 => 'R2',
                3 => 'R3',
                4 => 'R4'
            ];

            $prefix = $prefixAturan[$sesiSpesifik] ?? 'R1';

            $query->whereHas('aturan', function ($q) use ($prefix) {
                $q->where('kode', 'like', $prefix . '%');
            });
        }

        return $query->get()
            ->map(function ($log) {
                return [
                    'waktu' => $log->waktu,
                    'aturan_kode' => $log->aturan->kode,
                    'aturan_deskripsi' => $log->aturan->deskripsi,
                    'premis' => $log->premis_terpenuhi,
                    'fakta_terbentuk' => $log->fakta_terbentuk,
                    'jenis' => strpos($log->fakta_terbentuk, 'FA') === 0 ? 'Fakta Antara' : 'Solusi'
                ];
            })
            ->toArray();
    }

    /**
     * Reset konsultasi
     */
    public function resetKonsultasi()
    {
        DB::transaction(function () {
            $this->konsultasi->detailKonsultasi()->delete();
            $this->konsultasi->inferensiLog()->delete();

            $this->konsultasi->update([
                'status' => 'sedang_berjalan',
                'sesi' => 1,
                'pertanyaan_terakhir' => null,
                'hasil_konsultasi' => null,
                'completed_at' => null
            ]);

            $this->faktaTersedia = [];
            $this->faktaAntara = [];
            $this->solusiDitemukan = [];
            $this->aturanTerpakai = [];
            $this->sesiSekarang = 1;
        });
    }
}