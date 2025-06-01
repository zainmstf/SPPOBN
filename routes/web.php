<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RekomendasiNutrisiController;
use App\Http\Controllers\Admin\SumberNutrisiController;
use App\Http\Controllers\User\LaporanController;
use App\Http\Controllers\User\MenuController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\KonsultasiController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\RiwayatController;
use App\Http\Controllers\User\KontenEdukasiController as UserKontenEdukasiController;
use App\Http\Controllers\User\FeedbackController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\FaktaController;
use App\Http\Controllers\Admin\SolusiController;
use App\Http\Controllers\Admin\AturanController;
use App\Http\Controllers\Admin\KontenEdukasiController;
use App\Http\Controllers\Admin\FeedbackManagementController;
use App\Http\Controllers\Admin\VisualisasiController;
use App\Http\Controllers\LandingPageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing Page Routes
Route::get('/', [LandingPageController::class, 'index'])->name('landing');
Route::post('/feedback', [LandingPageController::class, 'storeFeedback'])->name('landing.feedback.store');
Route::get('/edukasi/konten/{id}', [LandingPageController::class, 'getEdukasi'])->name('landing.edukasi');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::get('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// User Routes
Route::middleware(['auth', 'user'])->prefix('user')->group(function () {
    // Dashboard
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/chart-data-dashboard', [UserDashboardController::class, 'getChartData'])->name('getChartData');
    Route::get('/edukasi/konten/{id}', [LandingPageController::class, 'getEdukasi'])->name('user.landing.edukasi');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::get('/profil/pengaturan', [ProfileController::class, 'pengaturan'])->name('profile.pengaturan');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Konsultasi Forward Chaining
    Route::prefix('konsultasi')->name('konsultasi.')->group(function () {
        Route::get('/', [KonsultasiController::class, 'index'])->name('index');
        Route::get('/create', [KonsultasiController::class, 'create'])->name('create');
        Route::post('/store', [KonsultasiController::class, 'store'])->name('store');
        Route::get('/{id}/question', [KonsultasiController::class, 'question'])->name('question');
        Route::post('/{id}/answer', [KonsultasiController::class, 'answer'])->name('answer');
        Route::get('/{id}/result', [KonsultasiController::class, 'result'])->name('result');
        Route::get('/{id}/show', [KonsultasiController::class, 'show'])->name('show');
        Route::post('/{id}/restart', [KonsultasiController::class, 'restart'])->name('restart');
        Route::get('/{id}/export-pdf', [KonsultasiController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/{id}/progress', [KonsultasiController::class, 'getProgress'])->name('progress');
    });

    // Riwayat Konsultasi
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
    Route::get('/riwayat/{konsultasi}', [RiwayatController::class, 'show'])->name('riwayat.show');

    // Konten Edukasi
    route::get('/tentang-osteoporosis', [UserKontenEdukasiController::class, 'aboutOsteoporosis'])->name('about.osteoporosis');

    Route::get('/edukasi', [UserKontenEdukasiController::class, 'index'])->name('edukasi.index');
    Route::get('/edukasi/konten/{id}', [UserKontenEdukasiController::class, 'getKonten'])->name('edukasi.konten');
    Route::get('/edukasi/daftar-makanan', [MenuController::class, 'index'])->name('edukasi.daftarMakanan');
    Route::get('/edukasi/daftar-makanan/jenis/{jenis}', [MenuController::class, 'byJenis'])->name('edukasi.daftarMakanan.byJenis');

    // Laporan 
    Route::get('/grafik-perkembangan', [LaporanController::class, 'grafikPerkembangan'])->name('laporan.grafikPerkembangan');
    Route::get('/tampilkan-rekomendasi', [LaporanController::class, 'tampilkanRekomendasi'])->name('laporan.tampilkanRekomendasi');
    Route::post('/laporan/cetak/', [LaporanController::class, 'halamanCetak'])->name('laporan.cetak_halaman');

    // Feedback
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::post('/feedback/konsultasi/{konsultasi}', [FeedbackController::class, 'storeKonsultasiFeedback'])->name('feedback.konsultasi');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/chart-data-dashboard', [AdminDashboardController::class, 'chartDataDashboard']);

    // User Management
    Route::resource('users', UserManagementController::class)->names('admin.users');

    // Basis Pengetahuan 
    Route::prefix('/basis-pengetahuan')->group(function () {
        // Fakta Management (Basis Pengetahuan)
        Route::resource('fakta', FaktaController::class)->names('admin.basisPengetahuan.fakta');
        Route::post('/fakta/reorder', [FaktaController::class, 'reorder'])->name('fakta.reorder');

        // Solusi Management
        Route::resource('solusi', SolusiController::class)->names('admin.basisPengetahuan.solusi');
        Route::resource('solusi.nutrisi', SolusiController::class);

        // Aturan Forward Chaining Management
        Route::resource('aturan', AturanController::class)->names('admin.basisPengetahuan.aturan');
        Route::post('/aturan/activate/{aturan}', [AturanController::class, 'activate'])->name('aturan.activate');
        Route::post('/aturan/deactivate/{aturan}', [AturanController::class, 'deactivate'])->name('aturan.deactivate');

        Route::post('/admin/basis-pengetahuan/aturan/set-default-solusi', [AturanController::class, 'setDefaultSolusi'])->name('admin.basisPengetahuan.aturan.setDefaultSolusi');
        Route::post('/admin/basis-pengetahuan/aturan/set-default-fakta', [AturanController::class, 'setDefaultFakta'])->name('admin.basisPengetahuan.aturan.setDefaultFakta');

        // Visualisasi Pohon Keputusan
        Route::get('/visualisasi', [VisualisasiController::class, 'index'])->name('admin.basisPengetahuan.visualisasi.index');
        Route::get('/visualisasi/{kategori}', [VisualisasiController::class, 'show'])->name('admin.basisPengetahuan.visualisasi.show');
        Route::get('/visualisasi/data/{jenisFakta}', [VisualisasiController::class, 'getPohonKeputusanData'])->name('admin.basisPengetahuan.pohonKeputusan.data');
    });

    // Konten Edukasi Management
    Route::resource('konten-edukasi', KontenEdukasiController::class)->names('admin.konten-edukasi');
    Route::get('/edukasi/konten/{id}', [KontenEdukasiController::class, 'getKonten'])->name('admin.edukasi.konten');
    Route::post('/konten-edukasi/upload-image', [KontenEdukasiController::class, 'uploadImage'])->name('konten-edukasi.upload-image');
    Route::post('/konten-edukasi/{kontenEdukasi}/toggle-status', [KontenEdukasiController::class, 'toggleStatus'])->name('konten-edukasi.toggle-status');

    // Rekomendasi Management
    Route::resource('sumber-nutrisi', SumberNutrisiController::class)->names('admin.sumber-nutrisi');
    Route::resource('rekomendasi-nutrisi', RekomendasiNutrisiController::class)->names('admin.rekomendasi-nutrisi');

    // Konsultasi
    Route::prefix('/konsultasi')->group(function () {
        Route::get('/riwayat', [AdminController::class, 'riwayat'])->name('admin.konsultasi.riwayat');
        Route::get('/{konsultasi}/show', [AdminController::class, 'showDetail'])->name('admin.konsultasi.show');
        Route::get('/{konsultasi}/cetak', [AdminController::class, 'cetak'])->name('admin.konsultasi.print');
        Route::get('/statistik', [AdminController::class, 'statistik'])->name('admin.konsultasi.statistik');
    });

    // Laporan Konsultasi
    Route::get('/laporan', [AdminController::class, 'laporanKonsultasi'])->name('admin.laporan');
    Route::post('/laporan/cetak/', [AdminController::class, 'halamanCetak'])->name('admin.laporan.cetak_halaman');

    // Feedback Management
    Route::get('/feedback', [FeedbackManagementController::class, 'index'])->name('admin.feedback.index');
    Route::get('/feedback/{feedback}', [FeedbackManagementController::class, 'show'])->name('admin.feedback.show');
    Route::delete('/feedback/{feedback}', [FeedbackManagementController::class, 'destroy'])->name('admin.feedback.destroy');

    // Riwayat Aktivitas
    Route::get('/riwayat-aktivitas', [DashboardController::class, 'riwayatAktivitas'])->name('admin.riwayatAktivitas');

    // Profil Pengguna
    Route::get('/profile', [AdminController::class, 'index'])->name('admin.profile.index');
    Route::post('/profile', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
    Route::get('/profil/pengaturan', [AdminController::class, 'pengaturan'])->name('admin.profile.pengaturan');
    Route::post('/profile/password', [AdminController::class, 'updatePassword'])->name('admin.profile.password');

    //Logout
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout.admin');
});

// Testing Routes (hanya untuk development)
if (app()->environment('local')) {
    Route::get('/test-forward-chaining', function () {
        // Test forward chaining implementation
        $konsultasi = App\Models\Konsultasi::latest()->first();
        if ($konsultasi) {
            $service = new App\Services\ForwardChainingService($konsultasi);
            return response()->json($service->getPertanyaanSelanjutnya());
        }
        return response()->json(['error' => 'No consultation found']);
    });

    Route::get('/test-rules', function () {
        // Test rules implementation
        return response()->json([
            'total_rules' => App\Models\Aturan::count(),
            'active_rules' => App\Models\Aturan::active()->count(),
            'total_facts' => App\Models\Fakta::count(),
            'askable_facts' => App\Models\Fakta::askable()->count()
        ]);
    });
}