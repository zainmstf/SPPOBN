<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\User;
use App\Models\Konsultasi;
use App\Models\KontenEdukasi;
use App\Models\Aturan;
use App\Models\Fakta;
use App\Models\Solusi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index()
    {
        $statistik = $this->getStatistik();
        $grafikUmpanBalik = $this->getGrafikUmpanBalik();
        $aktivitasPenggunaTerbaru = $this->getAktivitasPenggunaTerbaru(5);
        $notifikasi = $this->getNotifikasi();

        return view('admin.dashboard', array_merge(
            $statistik,
            ['grafikUmpanBalik' => $grafikUmpanBalik],
            ['aktivitasPenggunaTerbaru' => $aktivitasPenggunaTerbaru],
            $notifikasi
        ));
    }

    public function chartDataDashboard()
    {
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $consultations = Konsultasi::where('created_at', '>=', $sevenDaysAgo)
            ->get();

        $consultationData = [
            'belum_selesai' => [],
            'sedang_berjalan' => [],
            'selesai' => [],
            'labels' => [],
        ];

        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i);
            $dayLabel = $day->format('d M Y');
            $days[] = $dayLabel;

            $consultationData['belum_selesai'][$dayLabel] = 0;
            $consultationData['sedang_berjalan'][$dayLabel] = 0;
            $consultationData['selesai'][$dayLabel] = 0;
        }

        foreach ($consultations as $consultation) {
            $consultationDay = Carbon::parse($consultation->created_at)->format('d M Y');
            if (isset($consultationData[strtolower($consultation->status)][$consultationDay])) {
                $consultationData[strtolower($consultation->status)][$consultationDay]++;
            }
        }

        $chartData = [
            'pending' => array_values($consultationData['belum_selesai']),
            'ongoing' => array_values($consultationData['sedang_berjalan']),
            'completed' => array_values($consultationData['selesai']),
            'labels' => $days,
        ];

        return response()->json($chartData);
    }


    protected function getGrafikUmpanBalik()
    {
        $startDate = now()->subDays(6)->startOfDay();

        $ratings = Feedback::where('created_at', '>=', $startDate)
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
    protected function getAktivitasPenggunaTerbaru(int $ambil = null)
    {
        $aktivitasPengguna = User::where('role', 'user')->latest()->take(5)->get()
            ->map(fn($user) => (object) [
                'nama' => $user->nama_lengkap,
                'deskripsi' => 'Pengguna baru: ' . $user->nama_lengkap,
                'created_at' => $user->created_at,
                'jenis_aktivitas' => 'Pendaftaran',
                'status_aktivitas' => 'Selesai',
            ]);

        $konsultasiBaru = Konsultasi::with('user')->latest()->take(5)->get()
            ->map(fn($konsultasi) => (object) [
                'nama' => optional($konsultasi->user)->nama_lengkap ?? 'Pengguna tidak diketahui',
                'deskripsi' => 'Konsultasi baru #' . $konsultasi->id,
                'created_at' => $konsultasi->created_at,
                'jenis_aktivitas' => 'Konsultasi',
                'status_aktivitas' => $this->translateStatus($konsultasi->status),
            ]);

        $umpanBalikBaru = Feedback::with('user')->latest()->take(5)->get()
            ->map(fn($umpanBalik) => (object) [
                'nama' => optional($umpanBalik->user)->nama_lengkap ?? 'Pengguna tidak diketahui',
                'deskripsi' => 'Umpan balik baru #' . $umpanBalik->id,
                'created_at' => $umpanBalik->created_at,
                'jenis_aktivitas' => 'Umpan Balik',
                'status_aktivitas' => 'Baru',
            ]);

        return $aktivitasPengguna->merge($konsultasiBaru)->merge($umpanBalikBaru)
            ->sortByDesc('created_at')->take($ambil);
    }

    protected function getStatistik()
    {
        return [
            'totalPengguna' => User::where('role', 'user')->count(),
            'totalKonsultasi' => Konsultasi::count(),
            'totalKontenEdukasi' => KontenEdukasi::count(),
            'totalAturan' => Aturan::count(),
            'totalPertanyaan' => Fakta::count(),
            'totalRekomendasi' => Solusi::count(),
        ];
    }

    protected function getNotifikasi()
    {
        return [
            'konsultasiPendingCount' => Konsultasi::where('status', 'belum_selesai')->count(),
            'umpanBalikRatingRendahCount' => Feedback::where('rating', '<', 3)->count(),
            'konsultasiPending' => Konsultasi::where('status', 'belum_selesai')->latest()->take(1)->get(),
            'umpanBalikRatingRendah' => Feedback::where('rating', '<=', 3)->latest()->take(1)->get(),
        ];
    }
    private function translateStatus($status)
    {
        switch (strtolower($status)) {
            case 'baru':
                return 'Baru';
            case 'diproses':
                return 'Diproses';
            case 'selesai':
                return 'Selesai';
            case 'ditolak':
                return 'Ditolak';
            case 'sedang_berjalan':
                return 'Sedang Berlangsung';
            case 'belum_selesai':
                return 'Tertunda';
            default:
                return $status;
        }
    }

    public function riwayatAktivitas()
    {
        $aktivitasPenggunaTerbaru = $this->getAktivitasPenggunaTerbaru();
        return view('admin.riwayat_aktivitas', compact('aktivitasPenggunaTerbaru'));
    }
}