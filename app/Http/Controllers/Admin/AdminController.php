<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Konsultasi;
use App\Models\Solusi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Menampilkan informasi profil pengguna.
     */
    public function index()
    {
        $user = Auth::user();
        return view('admin.profile.index', compact('user'));
    }

    /**
     * Memperbarui informasi profil pengguna.
     */
    public function updateProfile(Request $request)
    {

        // Validasi data yang masuk
        $request->validate([
            'nama' => 'required|string|max:100',
            'alamat' => 'required|string',
            'no_telp' => 'required|string|max:15',
        ]);

        // Update data pengguna
        User::where('id', Auth::id())->update([
            'nama_lengkap' => $request->nama,
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telp,
        ]);

        return redirect()->route('admin.profile.index')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Menampilkan halaman pengaturan profil.
     */
    public function pengaturan()
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            // Jika pengguna adalah admin, tampilkan view admin
            return view('admin.profile.pengaturan', compact('user'));
        } else {
            // Jika pengguna adalah user biasa, tampilkan view user
            return view('user.profile.pengaturan', compact('user'));
        }
    }

    /**
     * Memperbarui kata sandi pengguna.
     */
    public function updatePassword(Request $request)
    {
        $userId = Auth::id(); // Ambil ID user yang sedang login
        $user = User::findOrFail($userId); // Ambil user dari model

        $validator = Validator::make($request->all(), [
            'current_password' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('Password saat ini salah.');
                    }
                },
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        User::where('id', $userId)->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.profile.index')->with('success', 'Password berhasil diubah.');
    }

    public function riwayat()
    {
        $statuses = ['belum_selesai', 'sedang_berjalan', 'selesai'];
        $riwayatKonsultasi = [];

        foreach ($statuses as $status) {
            $riwayatKonsultasi[$status] = Konsultasi::with('currentPertanyaan')
                ->where('status', $status)
                ->orderBy('created_at', 'desc')
                ->get();
        }
        return view('admin.konsultasi.riwayat', compact('riwayatKonsultasi'));
    }

    public function showDetail(Konsultasi $konsultasi)
    {
        if (Auth::user()->role !== 'admin') {
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

        return view('admin.konsultasi.detail', compact('konsultasi', 'detailKonsultasi', 'inferensiLog', 'solusiAkhir', 'inferensiSolusi'));
    }
    public function cetak(Konsultasi $konsultasi)
    {
        if (Auth::user()->role !== 'admin') {
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

        // Mengembalikan tampilan dengan data konsultasi dan aturan yang relevan
        return view('user.konsultasi.print', compact('konsultasi', 'detailKonsultasi', 'inferensiLog', 'solusiAkhir'));
    }
    public function statistik()
    {
        // Total Konsultasi
        $totalKonsultasi = Konsultasi::count();

        // Jumlah Pengguna Konsultasi (pengguna unik yang pernah melakukan konsultasi)
        $jumlahPenggunaKonsultasi = Konsultasi::distinct('user_id')->count();

        // Konsultasi Perbulan
        $konsultasiPer7Hari = Konsultasi::selectRaw('DATE(created_at) as tanggal, COUNT(*) as total')
            ->whereBetween('created_at', [
                Carbon::now()->subDays(6)->startOfDay(),
                Carbon::now()->endOfDay()
            ])
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        // Status Konsultasi
        $statusKonsultasi = Konsultasi::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                // Mengubah huruf pertama pada setiap kata menjadi kapital
                $item->status = ucwords(strtolower($item->status)); // pastikan semua huruf selain pertama adalah kecil
                return $item;
            });


        // Rata-Rata Konsultasi Per Pengguna
        $rataRataKonsultasiPerPengguna = $jumlahPenggunaKonsultasi > 0 ? round($totalKonsultasi / $jumlahPenggunaKonsultasi, 2) : 0;

        // Distribusi Waktu Konsultasi (misalnya, berdasarkan jam dibuat)
        $distribusiWaktuKonsultasi = Konsultasi::query()
            ->selectRaw('HOUR(created_at) as jam, COUNT(*) as total')
            ->groupBy('jam')
            ->orderBy('jam', 'asc')
            ->get();

        // Pertanyaan Paling Sering Diajukan
        $RekomendasiPalingSeringDiajukan = Solusi::whereHas('aturan', function ($query) {
            $query->where('jenis_konklusi', 'solusi');
        })
            ->withCount([
                'inferensiLogs as total' => function ($query) {
                    $query->join('aturan as aturan_1', 'inferensi_log.aturan_id', '=', 'aturan_1.id')
                        ->join('aturan as aturan_2', 'inferensi_log.aturan_id', '=', 'aturan_2.id')
                        ->where('aturan_1.jenis_konklusi', 'solusi');
                }
            ])
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Rata-Rata Rating Umpan Balik
        $rataRataRating = Feedback::avg('rating') ?? 0;

        // Distribusi Rating
        $distribusiRating = Feedback::query()
            ->selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->orderBy('rating')
            ->get();

        // Jumlah Konsultasi Dengan Umpan Balik
        $jumlahKonsultasiDenganFeedback = Feedback::distinct('konsultasi_id')->count();

        return view('admin.konsultasi.statistik', [
            'totalKonsultasi' => $totalKonsultasi,
            'jumlahPenggunaKonsultasi' => $jumlahPenggunaKonsultasi,
            'konsultasiPerbulan' => $konsultasiPer7Hari,
            'statusKonsultasi' => $statusKonsultasi,
            'rataRataKonsultasiPerPengguna' => $rataRataKonsultasiPerPengguna,
            'distribusiWaktuKonsultasi' => $distribusiWaktuKonsultasi,
            'RekomendasiPalingSeringDiajukan' => $RekomendasiPalingSeringDiajukan,
            'rataRataRating' => $rataRataRating,
            'distribusiRating' => $distribusiRating,
            'jumlahKonsultasiDenganFeedback' => $jumlahKonsultasiDenganFeedback,
        ]);
    }

    public function laporanKonsultasi()
    {
        $laporanKonsultasi = Konsultasi::with('user')
            ->where('status', 'selesai')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.konsultasi.laporan', compact('laporanKonsultasi'));
    }

    public function halamanCetak(Request $request)
    {
        $user = Auth::user();
        try {
            $dataJson = json_decode($request->input('data'), true);
            Log::info('Decoded data: ' . print_r($dataJson, true));

            $action = $request->input('action'); // gunakan input() bukan query()

            if (!empty($dataJson) && is_array($dataJson)) {
                $dataCetak = $dataJson;

                if ($action === 'download') {
                    // Generate PDF menggunakan library server-side
                    $tanggal = date('Y-m-d'); // Format: 2025-05-12
                    $namaFile = 'laporan_konsultasi_' . $tanggal . '.pdf';
                    $pdf = PDF::loadView('admin.konsultasi.cetak_halaman', ['data' => $dataCetak, 'user' => $user]);
                    return $pdf->download($namaFile);
                } else {
                    return view('admin.konsultasi.cetak_halaman', ['data' => $dataCetak, 'user' => $user],);
                }
            } else {
                Log::warning('Data JSON tidak valid atau kosong');
                return redirect()->back()->with('error', 'Tidak ada data valid untuk diproses.');
            }
        } catch (\Exception $e) {
            Log::error('Error pada halaman cetak: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
