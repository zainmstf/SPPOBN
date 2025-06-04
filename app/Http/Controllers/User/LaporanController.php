<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DetailKonsultasi;
use App\Models\Feedback;
use App\Models\InferensiLog;
use App\Models\Konsultasi;
use App\Models\Solusi;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function grafikPerkembangan()
    {
        $userId = Auth::id();

        $chartDataJson = json_encode($this->getChartData($userId));
        $ratingData = $this->getRatingData($userId);

        return view('user.laporan.grafik-perkembangan', compact(
            'chartDataJson',
            'ratingData'
        ));
    }

    private function getChartData($userId)
    {
        // Mengambil data 30 hari terakhir
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // Ambil konsultasi dalam 7 hari terakhir milik user
        $consultations = Konsultasi::where('user_id', $userId)
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->get();

        // Inisialisasi label tanggal (7 hari terakhir)
        $labels = collect(range(29, 0))->map(function ($i) {
            return Carbon::now()->subDays($i)->format('d M');
        })->values();

        // Daftar status yang digunakan untuk grafik
        $statuses = ['belum_selesai', 'sedang_berjalan', 'selesai'];

        // Membuat array untuk menyimpan data konsultasi yang dihitung berdasarkan status dan tanggal
        $consultationData = collect($statuses)->mapWithKeys(function ($status) use ($labels) {
            // Menginisialisasi tanggal dengan nilai 0 untuk setiap status
            return [$status => array_fill_keys($labels->all(), 0)];
        })->toArray();

        // Hitung jumlah konsultasi berdasarkan status dan tanggal
        foreach ($consultations as $consultation) {
            // Status konsultasi dalam format lowercase
            $status = strtolower($consultation->status);

            // Parsing tanggal dari created_at
            $date = Carbon::parse($consultation->created_at)->format('d M');

            // Jika tanggal ada dalam label, maka update jumlah konsultasi
            if (isset($consultationData[$status][$date])) {
                $consultationData[$status][$date]++;
            }
        }

        // Format data untuk chart
        $chartData = [
            'labels' => $labels,
            'belum_selesai' => array_values($consultationData['belum_selesai']),
            'sedang_berjalan' => array_values($consultationData['sedang_berjalan']),
            'selesai' => array_values($consultationData['selesai']),
        ];

        // Kembalikan response dalam format JSON
        return response()->json($chartData);
    }

    private function getRatingData($userId)
    {
        $startDate = now()->subDays(6)->startOfDay();

        $ratings = Feedback::where('user_id', $userId)
            ->where('created_at', '>=', $startDate)
            ->get();

        $ratingDays = collect(range(0, 6))->map(function ($i) {
            return now()->subDays($i)->format('d M Y');
        })->reverse()->values();

        $ratingMap = $ratings->groupBy(function ($item) {
            return Carbon::parse($item->created_at)->format('d M Y');
        })->map(function ($group) {
            return round($group->avg('rating'), 2);
        });

        $ratingData = $ratingDays->map(function ($day) use ($ratingMap) {
            return $ratingMap[$day] ?? 0;
        })->toArray();

        return [
            'ratingLabelsJson' => json_encode($ratingDays),
            'ratingDataJson' => json_encode($ratingData)
        ];
    }

    public function tampilkanRekomendasi()
    {
        $konsultasi = Konsultasi::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $konsultasi->load('user');

        $konsultasi->each(function ($konsul) {
            $hasilKonsultasi = $konsul->hasil_konsultasi;

            // Pastikan hasilKonsultasi adalah array dan tidak null sebelum melanjutkan
            if (is_array($hasilKonsultasi)) {
                if (isset($hasilKonsultasi['solusi']) && is_array($hasilKonsultasi['solusi'])) {
                    $solusiCodes = $hasilKonsultasi['solusi'];

                    $solusiDetails = Solusi::whereIn('kode', $solusiCodes)
                        ->select('nama', 'deskripsi')
                        ->get();

                    $konsul->solusi_rekomendasi = $solusiDetails;
                } else {
                    $konsul->solusi_rekomendasi = collect();
                }

                // Hitung jumlah fakta yang tersedia
                if (isset($hasilKonsultasi['fakta_tersedia']) && is_array($hasilKonsultasi['fakta_tersedia'])) {
                    $konsul->jumlah_fakta = count($hasilKonsultasi['fakta_tersedia']);
                } else {
                    $konsul->jumlah_fakta = 0;
                }

            } else {
                // Jika hasil_konsultasi null atau bukan array yang diharapkan
                $konsul->solusi_rekomendasi = collect();
                $konsul->jumlah_fakta = 0;
            }

            // Hitung durasi (sesuai dengan kode Blade yang sudah ada)
            $mulai = Carbon::parse($konsul->created_at);
            $selesai = Carbon::parse($konsul->updated_at);
            $konsul->durasi_konsultasi = $mulai->diff($selesai)->format('%i menit %s detik');
            $konsul->waktu_mulai = $mulai->locale('id')->isoFormat('DD MMM YYYY HH:mm');
            $konsul->rentang_waktu = $mulai->locale('id')->isoFormat('HH:mm:ss') . ' - ' . $selesai->locale('id')->isoFormat('HH:mm:ss');
        });

        return view('user.laporan.tampilan-rekomendasi', compact(
            'konsultasi',
        ));
    }

    public function halamanCetak(Request $request)
    {

        $user = Auth::user();
        $data = json_decode($request->input('data'), true);

        $action = $request->input('action');

        if ($action === 'download') {
            $tanggal = date('Y-m-d'); // Format: 2025-05-12
            $namaFile = 'laporan_konsultasi_' . $tanggal . '.pdf';
            $pdf = PDF::loadView('user.konsultasi.cetak_halaman', ['data' => $data, 'user' => $user]);
            return $pdf->download($namaFile);
        }

        return view('user.konsultasi.cetak_halaman', ['data' => $data, 'user' => $user]);
    }
}
