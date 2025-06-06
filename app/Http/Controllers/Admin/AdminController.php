<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\InferensiLog;
use App\Models\Konsultasi;
use App\Models\RekomendasiNutrisi;
use App\Models\Solusi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Services\ForwardChainingService;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;


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

        $sessionNames = [
            1 => 'Skrining Faktor Risiko',
            2 => 'Penilaian FRAX',
            3 => 'Penilaian Nutrisi',
            4 => 'Preferensi & Kondisi',
        ];

        foreach ($statuses as $status) {
            $konsultasiList = Konsultasi::with('currentPertanyaan')
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

        return view('admin.konsultasi.riwayat', compact('riwayatKonsultasi'));
    }

    public function showDetail($id)
    {
        $konsultasi = Konsultasi::with('user')->where('id', $id)->first();

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

        return view('admin.konsultasi.detail', compact(
            'konsultasi',
            'hasilKonsultasi',
            'detailSolusi',
            'traceInferensi',
            'jawabanUser',
            'statusMessage',
            'rekomendasiNutrisi'
        ));
    }

    public function cetak($id)
    {
        $konsultasi = Konsultasi::with('user')->where('id', $id)->first();

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
        $RekomendasiPalingSeringDiajukan = Solusi::whereIn('kode', function ($query) {
            $query->select('konklusi')
                ->from('aturan')
                ->where('jenis_konklusi', 'solusi');
        })
            ->whereNotIn('kode', ['s001', 's002', 's003', 's004', 's005', 's006', 's007', 's008'])
            ->get()
            ->map(function ($solusi) {
                // Hitung manual jumlah inferensi log
                $total = InferensiLog::whereHas('aturan', function ($query) use ($solusi) {
                    $query->where('konklusi', $solusi->kode)
                        ->where('jenis_konklusi', 'solusi');
                })->count();

                $solusi->total = $total;
                return $solusi;
            })
            ->sortByDesc('total')
            ->take(5)
            ->values();

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
            ->orderBy('created_at', 'desc') // Ubah ke desc untuk data terbaru di atas
            ->get();

        // Proses setiap konsultasi untuk mendapatkan data yang diperlukan
        $laporanKonsultasi->each(function ($konsultasi) {
            $hasilKonsultasi = $konsultasi->hasil_konsultasi; // Ini diasumsikan sudah di-cast ke array/json di model

            // Inisialisasi default values
            $konsultasi->solusi_rekomendasi = collect();
            $konsultasi->jumlah_fakta = 0;
            $konsultasi->hasil_konsultasi_text = 'Tidak ada hasil konsultasi'; // Default string jika tidak ada hasil

            // Pastikan hasil_konsultasi adalah array dan tidak null
            if (is_array($hasilKonsultasi)) {
                // Ambil detail solusi jika tersedia
                if (isset($hasilKonsultasi['solusi']) && is_array($hasilKonsultasi['solusi'])) {
                    $solusiCodes = $hasilKonsultasi['solusi'];

                    $solusiDetails = Solusi::whereIn('kode', $solusiCodes)
                        ->select('nama', 'deskripsi')
                        ->get();

                    $konsultasi->solusi_rekomendasi = $solusiDetails;

                    // Buat teks hasil konsultasi dari nama solusi yang dipisahkan koma
                    if ($solusiDetails->isNotEmpty()) {
                        $hasil = $solusiDetails->map(function ($solusi) {
                            return $solusi->nama;
                        })->implode(', ');
                        $konsultasi->hasil_konsultasi_text = $hasil; // Ini akan menjadi string "Solusi A, Solusi B, Solusi C"
                    }
                }

                // Hitung jumlah fakta yang tersedia
                if (isset($hasilKonsultasi['fakta_tersedia']) && is_array($hasilKonsultasi['fakta_tersedia'])) {
                    $konsultasi->jumlah_fakta = count($hasilKonsultasi['fakta_tersedia']);
                }
            }

            // Format waktu dan durasi
            $mulai = Carbon::parse($konsultasi->created_at);
            $selesai = $konsultasi->completed_at ? Carbon::parse($konsultasi->completed_at) : Carbon::parse($konsultasi->updated_at);

            $konsultasi->waktu_mulai = $mulai->locale('id')->isoFormat('DD MMM YYYY HH:mm');
            $konsultasi->rentang_waktu = $mulai->locale('id')->isoFormat('HH:mm:ss') . ' - ' . $selesai->locale('id')->isoFormat('HH:mm:ss');
            $konsultasi->durasi_konsultasi = $mulai->diff($selesai)->format('%H:%I:%S');
        });

        return view('admin.konsultasi.laporan', compact('laporanKonsultasi'));
    }

    public function halamanCetak(Request $request)
    {
        try {
            $data = json_decode($request->data, true);
            $action = $request->input('action', 'print');

            if (!$data || !is_array($data)) {
                return back()->with('error', 'Data tidak valid untuk dicetak.');
            }

            // Proses data untuk tampilan cetak
            $processedData = [];
            foreach ($data as $row) {
                $processedData[] = [
                    'no' => $row[0] ?? '',
                    'konsultasi_id' => $row[1] ?? '',
                    'nama_pengguna' => $row[2] ?? '',
                    'tanggal_konsultasi' => $row[3] ?? '',
                    'rentang_waktu' => $row[4] ?? '',
                    'durasi' => $row[5] ?? '',
                    'hasil_konsultasi' => $row[6] ?? '',
                ];
            }

            if ($action === 'download') {
                return $this->downloadPDF($processedData);
            }

            return view('admin.konsultasi.cetak_halaman', compact('processedData'));

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
