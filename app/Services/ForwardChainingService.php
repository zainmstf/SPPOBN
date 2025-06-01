<?php

namespace App\Services;

use App\Models\Konsultasi;
use App\Models\Fakta;
use App\Models\Aturan;
use App\Models\DetailKonsultasi;
use App\Models\InferensiLog;

class ForwardChainingService
{
    private $konsultasi;
    private $faktaTersedia = [];
    private $aturanTerpakai = [];

    public function __construct(Konsultasi $konsultasi)
    {
        $this->konsultasi = $konsultasi;
        $this->loadFaktaTersedia();
    }

    private function loadFaktaTersedia()
    {
        // Load fakta dari jawaban user (F001, F002, dst)
        $faktaDariJawaban = $this->konsultasi->detailKonsultasi()
            ->where('jawaban', 'ya')
            ->with('fakta')
            ->get()
            ->pluck('fakta.kode')
            ->toArray();

        // Load fakta antara yang sudah terbentuk (FA001, FA002, dst)
        $faktaAntara = $this->konsultasi->getFaktaAntara();

        $this->faktaTersedia = array_merge($faktaDariJawaban, $faktaAntara);
    }

    public function getPertanyaanSelanjutnya()
    {
        // Lakukan inferensi dulu untuk menghasilkan fakta antara
        $this->runInference();

        // Cek apakah sudah ada solusi
        if ($this->hasSolution()) {
            return $this->buildFinalResult();
        }

        // Cari pertanyaan selanjutnya berdasarkan aturan yang belum terpenuhi
        $nextFakta = $this->findNextQuestion();

        if (!$nextFakta) {
            return $this->buildFinalResult();
        }

        return [
            'status' => 'continue',
            'fakta' => $nextFakta,
            'pertanyaan' => $nextFakta->pertanyaan,
            'progress' => $this->calculateProgress()
        ];
    }

    private function runInference()
    {
        $newFactsFound = true;
        $maxIterations = 100; // Prevent infinite loop
        $iteration = 0;

        while ($newFactsFound && $iteration < $maxIterations) {
            $newFactsFound = false;
            $iteration++;

            $aturanAktif = Aturan::active()->get();

            foreach ($aturanAktif as $aturan) {
                if (in_array($aturan->id, $this->aturanTerpakai)) {
                    continue;
                }

                if ($this->evaluateRule($aturan)) {
                    $this->applyRule($aturan);
                    $this->aturanTerpakai[] = $aturan->id;
                    $newFactsFound = true;
                }
            }
        }
    }

    private function evaluateRule(Aturan $aturan)
    {
        $premisArray = $aturan->getPremisArray();

        if (empty($premisArray)) {
            return false;
        }

        // Cek apakah konklusi sudah ada
        if (in_array($aturan->konklusi, $this->faktaTersedia)) {
            return false;
        }

        // Cek apakah semua premis terpenuhi
        foreach ($premisArray as $premis) {
            if (!in_array(trim($premis), $this->faktaTersedia)) {
                return false;
            }
        }

        return true;
    }

    private function applyRule(Aturan $aturan)
    {
        $this->faktaTersedia[] = $aturan->konklusi;

        // Log inferensi
        InferensiLog::create([
            'konsultasi_id' => $this->konsultasi->id,
            'aturan_id' => $aturan->id,
            'fakta_terbentuk' => $aturan->konklusi,
            'premis_terpenuhi' => $aturan->premis
        ]);
    }

    private function findNextQuestion()
    {
        $faktaTerjawab = $this->konsultasi->getFaktaTerjawab();

        // Cari aturan yang bisa dipenuhi dengan menanyakan 1 fakta lagi
        $aturanAktif = Aturan::active()->get();
        $priorityFacts = [];

        foreach ($aturanAktif as $aturan) {
            if (in_array($aturan->id, $this->aturanTerpakai)) {
                continue;
            }

            if (in_array($aturan->konklusi, $this->faktaTersedia)) {
                continue;
            }

            $premisArray = $aturan->getPremisArray();
            $missingFacts = [];

            foreach ($premisArray as $premis) {
                $premis = trim($premis);
                if (
                    !in_array($premis, $this->faktaTersedia) &&
                    !in_array($premis, $faktaTerjawab)
                ) {
                    $missingFacts[] = $premis;
                }
            }

            // Jika hanya kurang 1 fakta, prioritaskan
            if (count($missingFacts) == 1) {
                $priorityFacts[] = $missingFacts[0];
            }
        }

        // Ambil fakta dengan prioritas tertinggi
        if (!empty($priorityFacts)) {
            $nextFactCode = $priorityFacts[0];
        } else {
            // Ambil fakta pertama yang belum ditanya
            $allFacts = Fakta::askable()
                ->whereNotIn('kode', $faktaTerjawab)
                ->orderBy('id')
                ->first();

            if (!$allFacts) {
                return null;
            }

            $nextFactCode = $allFacts->kode;
        }

        return Fakta::where('kode', $nextFactCode)->first();
    }

    private function hasSolution()
    {
        return count($this->getSolutions()) > 0;
    }

    private function getSolutions()
    {
        return array_filter($this->faktaTersedia, function ($fakta) {
            return strpos($fakta, 'S') === 0;
        });
    }

    private function buildFinalResult()
    {
        $solusiKodes = $this->getSolutions();
        $solusiData = [];

        if (!empty($solusiKodes)) {
            $solusiData = \App\Models\Solusi::whereIn('kode', $solusiKodes)->get();
        }

        // Update status konsultasi
        $this->konsultasi->update([
            'status' => 'selesai',
            'hasil_konsultasi' => $solusiKodes,
            'completed_at' => now()
        ]);

        return [
            'status' => 'completed',
            'solusi' => $solusiData,
            'fakta_antara' => $this->getFaktaAntara(),
            'total_rules_applied' => count($this->aturanTerpakai)
        ];
    }

    private function getFaktaAntara()
    {
        return array_filter($this->faktaTersedia, function ($fakta) {
            return strpos($fakta, 'FA') === 0;
        });
    }

    private function calculateProgress()
    {
        $totalFacts = Fakta::askable()->count();
        $answeredFacts = count($this->konsultasi->getFaktaTerjawab());

        return $totalFacts > 0 ? round(($answeredFacts / $totalFacts) * 100, 2) : 0;
    }

    public function saveAnswer($faktaKode, $jawaban)
    {
        $fakta = Fakta::where('kode', $faktaKode)->first();

        if (!$fakta) {
            throw new \Exception("Fakta tidak ditemukan: {$faktaKode}");
        }

        // Simpan jawaban
        DetailKonsultasi::updateOrCreate([
            'konsultasi_id' => $this->konsultasi->id,
            'fakta_id' => $fakta->id
        ], [
            'jawaban' => $jawaban
        ]);

        // Update pertanyaan terakhir
        $this->konsultasi->update([
            'pertanyaan_terakhir' => $faktaKode
        ]);

        // Reload fakta tersedia
        $this->loadFaktaTersedia();
    }

    public function resetInference()
    {
        $this->faktaTersedia = [];
        $this->aturanTerpakai = [];
        $this->loadFaktaTersedia();
    }
}