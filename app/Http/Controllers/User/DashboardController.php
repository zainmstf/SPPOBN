<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SumberNutrisi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Konsultasi;
use App\Models\KontenEdukasi;
use App\Models\Sessions;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil ID user yang sedang login
        $userId = Auth::id();

        // Hitung total konsultasi yang dilakukan user
        $totalKonsultasi = Konsultasi::where('user_id', $userId)->count();

        // Hitung total konten edukasi yang tersedia
        $totalKontenEdukasi = KontenEdukasi::where('status', 1)->count();

        // Total Sumber Nutrisi 
        $totalSumberNutrisi = SumberNutrisi::count();

        // Ambil 3 konten edukasi terbaru yang aktif
        $kontenEdukasiTerbaru = KontenEdukasi::where('status', 1)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Ambil 3 konsultasi terbaru user
        $recentKonsultasi = Konsultasi::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Atur progress dan warna tampilan berdasarkan sesi konsultasi saat ini
        foreach ($recentKonsultasi as $konsultasi) {
            switch ($konsultasi->sesi) {
                case 'risiko_osteoporosis':
                    $konsultasi->progress = 33;
                    $konsultasi->bgColor = 'primary';
                    break;
                case 'asupan_nutrisi':
                    $konsultasi->progress = 66;
                    $konsultasi->bgColor = 'warning';
                    break;
                case 'preferensi_makanan':
                    $konsultasi->progress = 100;
                    $konsultasi->bgColor = 'success';
                    break;
                default:
                    $konsultasi->progress = 0;
                    break;
            }
            // Set tinggi progress bar untuk tampilan UI
            $konsultasi->height = $konsultasi->progress . '%';
        }


        // Ambil konsultasi terakhir yang statusnya 'selesai'
        $lastKonsultasi = Konsultasi::where('user_id', $userId)
            ->where('status', 'selesai')
            ->latest()
            ->first();

        // Ambil sesi terakhir user untuk mengetahui aktivitas terakhir
        $session = Sessions::where('user_id', $userId)
            ->latest('last_activity')
            ->first();

        $lastActivity = null;

        if ($session) {
            // Konversi timestamp ke format waktu
            $lastActivity = Carbon::createFromTimestamp($session->last_activity);
        }

        // Kirim data ke view dashboard
        return view('user.dashboard', compact(
            'totalKonsultasi',
            'totalKontenEdukasi',
            'kontenEdukasiTerbaru',
            'recentKonsultasi',
            'lastKonsultasi',
            'lastActivity',
            'totalSumberNutrisi'
        ));
    }
    public function getChartData()
    {
        // Ambil user_id dari sesi pengguna yang sedang login
        $userId = Auth::id();

        // Mengambil data 7 hari terakhir
        $sevenDaysAgo = Carbon::now()->subDays(7);

        // Ambil konsultasi dalam 7 hari terakhir milik user
        $consultations = Konsultasi::where('user_id', $userId)
            ->where('created_at', '>=', $sevenDaysAgo)
            ->get();

        // Inisialisasi label tanggal (7 hari terakhir)
        $labels = collect(range(6, 0))->map(function ($i) {
            return Carbon::now()->subDays($i)->format('d M Y');
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
            $date = Carbon::parse($consultation->created_at)->format('d M Y');

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

}
