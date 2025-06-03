@extends('layouts.user')
@section('title', 'Sistem Pakar Osteoporosis | SPPOBN')
@section('title-menu', 'Sistem Pakar Penanganan Osteoporosis')
@section('subtitle-menu', 'Berbasis Nutrisi pada Lansia - Forward Chaining')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Konsultasi Osteoporosis</li>
@endsection

@section('content')
    <x-page-header />

    <div class="page-content">
        <!-- Hero Section -->
        <div class="row justify-content-center mb-4">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="bi bi-shield-heart" style="font-size: 4rem;"></i>
                        </div>
                        <h2 class="mb-3 text-white">Sistem Pakar Penanganan Osteoporosis</h2>
                        <p class="lead mb-4">
                            Sistem berbasis Forward Chaining untuk memberikan rekomendasi nutrisi
                            yang tepat dalam penanganan osteoporosis pada lansia
                        </p>
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <i class="bi bi-people" style="font-size: 2rem;"></i>
                                </div>
                                <h6 class="text-white">Lansia 40+</h6>
                                <small>Target utama sistem</small>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <i class="bi bi-cpu" style="font-size: 2rem;"></i>
                                </div>
                                <h6 class="text-white">Forward Chaining</h6>
                                <small>Metode inferensi</small>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <i class="bi bi-heart-pulse" style="font-size: 2rem;"></i>
                                </div>
                                <h6 class="text-white">Berbasis Nutrisi</h6>
                                <small>Pendekatan holistik</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tahapan Konsultasi -->
        <div class="row justify-content-center mb-4">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 text-center">
                            <i class="bi bi-list-check text-primary me-2"></i>
                            Tahapan Konsultasi (4 Sesi)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Sesi 1 -->
                            <div class="col-md-6 col-lg-3 mb-4">
                                <div class="card h-100 border-primary">
                                    <div class="card-body text-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                            style="width: 60px; height: 60px;">
                                            <span class="badge bg-primary rounded-pill">1</span>
                                        </div>
                                        <h6 class="card-title text-primary">Skrining Faktor Risiko</h6>
                                        <p class="card-text small text-muted">
                                            <strong>One Osteoporosis Test</strong><br>
                                            • Riwayat keluarga patah tulang<br>
                                            • Faktor demografis (usia, BMI)<br>
                                            • Riwayat medis & obat-obatan<br>
                                            • Gaya hidup & aktivitas fisik
                                        </p>
                                        <div class="badge bg-light text-dark">
                                            <i class="bi bi-clock me-1"></i>5-7 menit
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sesi 2 -->
                            <div class="col-md-6 col-lg-3 mb-4">
                                <div class="card h-100 border-warning">
                                    <div class="card-body text-center">
                                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                            style="width: 60px; height: 60px;">
                                            <span class="badge bg-warning rounded-pill">2</span>
                                        </div>
                                        <h6 class="card-title text-warning">Penilaian FRAX</h6>
                                        <p class="card-text small text-muted">
                                            <strong>FRAX Risk Assessment</strong><br>
                                            • Probabilitas fraktur mayor (10 tahun)<br>
                                            • Probabilitas fraktur pinggul<br>
                                            • Klasifikasi risiko (rendah/sedang/tinggi)<br>
                                            • Rekomendasi bone densitometry
                                        </p>
                                        <div class="badge bg-light text-dark">
                                            <i class="bi bi-clock me-1"></i>8-10 menit
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sesi 3 -->
                            <div class="col-md-6 col-lg-3 mb-4">
                                <div class="card h-100 border-success">
                                    <div class="card-body text-center">
                                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                            style="width: 60px; height: 60px;">
                                            <span class="badge bg-success rounded-pill">3</span>
                                        </div>
                                        <h6 class="card-title text-success">Penilaian Nutrisi</h6>
                                        <p class="card-text small text-muted">
                                            <strong>Nutritional Assessment</strong><br>
                                            • Asupan kalsium (susu, ikan, sayuran)<br>
                                            • Status vitamin D (paparan sinar matahari)<br>
                                            • Protein hewani & nabati<br>
                                            • Magnesium & vitamin K
                                        </p>
                                        <div class="badge bg-light text-dark">
                                            <i class="bi bi-clock me-1"></i>10-12 menit
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sesi 4 -->
                            <div class="col-md-6 col-lg-3 mb-4">
                                <div class="card h-100 border-info">
                                    <div class="card-body text-center">
                                        <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                            style="width: 60px; height: 60px;">
                                            <span class="badge bg-info rounded-pill">4</span>
                                        </div>
                                        <h6 class="card-title text-info">Preferensi & Kondisi</h6>
                                        <p class="card-text small text-muted">
                                            <strong>Food Preferences & Medical</strong><br>
                                            • Alergi makanan (susu, ikan)<br>
                                            • Kondisi medis (diabetes, hipertensi)<br>
                                            • Preferensi makanan<br>
                                            • Pola makan (vegetarian)
                                        </p>
                                        <div class="badge bg-light text-dark">
                                            <i class="bi bi-clock me-1"></i>5-8 menit
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Knowledge Base Overview -->
        <div class="row justify-content-center mb-4">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0 text-center">
                            <i class="bi bi-database text-success me-2"></i>
                            Knowledge Base Sistem
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Fakta Dasar -->
                            <div class="col-md-4 mb-3 mt-2">
                                <div class="text-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                        style="width: 50px; height: 50px;">
                                        <i class="bi bi-clipboard-data text-primary"></i>
                                    </div>
                                    <h6 class="text-primary">52 Fakta Dasar</h6>
                                    <small class="text-muted">
                                        Data demografi, riwayat medis, gaya hidup,<br>
                                        asupan nutrisi, dan preferensi makanan
                                    </small>
                                </div>
                            </div>

                            <!-- Fakta Antara -->
                            <div class="col-md-4 mb-3 mt-2">
                                <div class="text-center">
                                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                        style="width: 50px; height: 50px;">
                                        <i class="bi bi-diagram-2 text-warning"></i>
                                    </div>
                                    <h6 class="text-warning">35 Fakta Antara</h6>
                                    <small class="text-muted">
                                        Inferensi tingkat menengah untuk<br>
                                        klasifikasi risiko dan defisiensi nutrisi
                                    </small>
                                </div>
                            </div>

                            <!-- Solusi -->
                            <div class="col-md-4 mb-3 mt-2">
                                <div class="text-center">
                                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                        style="width: 50px; height: 50px;">
                                        <i class="bi bi-lightbulb text-success"></i>
                                    </div>
                                    <h6 class="text-success">29 Solusi</h6>
                                    <small class="text-muted">
                                        Rekomendasi nutrisi, suplemen,<br>
                                        rujukan medis, dan alternatif makanan
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Sistem -->
        <div class="row justify-content-center mb-4">
            <div class="col-lg-10">
                <div class="row">
                    <!-- Metode Forward Chaining -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="bi bi-diagram-3 text-primary me-2"></i>
                                    Metode Forward Chaining
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="small text-muted mb-3">
                                    Sistem menggunakan metode inferensi maju untuk menganalisis data secara bertahap:
                                </p>
                                <ul class="list-unstyled small">
                                    <li class="mb-2">
                                        <i class="bi bi-arrow-right text-primary me-2"></i>
                                        <strong>Skrining Faktor Risiko:</strong> Identifikasi 52 fakta dasar
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-arrow-right text-primary me-2"></i>
                                        <strong>Inferensi Bertingkat:</strong> Generasi 35 fakta antara
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-arrow-right text-primary me-2"></i>
                                        <strong>Rule Processing:</strong> Penerapan aturan berbasis evidensi
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-arrow-right text-primary me-2"></i>
                                        <strong>Solution Generation:</strong> 29 rekomendasi terintegrasi
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Output Sistem -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="bi bi-clipboard-data text-success me-2"></i>
                                    Output Komprehensif
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="small text-muted mb-3">
                                    Hasil konsultasi berupa rekomendasi terintegrasi:
                                </p>
                                <ul class="list-unstyled small">
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        <strong>Klasifikasi Risiko:</strong> Rendah, sedang, atau tinggi
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        <strong>Rekomendasi Nutrisi:</strong> Personal berdasarkan defisiensi
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        <strong>Alternatif Makanan:</strong> Sesuai alergi & kondisi medis
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        <strong>Rujukan Medis:</strong> Evaluasi & bone densitometry
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kategori Nutrisi -->
        <div class="row justify-content-center mb-4">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-center">
                            <i class="bi bi-heart-pulse text-info me-2"></i>
                            Fokus Nutrisi Sistem Pakar
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 col-lg-3 mb-3 mt-2">
                                <div class="text-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                        style="width: 40px; height: 40px;">
                                        <i class="bi bi-droplet text-primary" style="font-size: 1.2rem;"></i>
                                    </div>
                                    <h6 class="text-primary mb-1">Kalsium</h6>
                                    <small class="text-muted">Susu, ikan, sayuran hijau</small>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3 mb-3 mt-2">
                                <div class="text-center">
                                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                        style="width: 40px; height: 40px;">
                                        <i class="bi bi-sun text-warning" style="font-size: 1.2rem;"></i>
                                    </div>
                                    <h6 class="text-warning mb-1">Vitamin D</h6>
                                    <small class="text-muted">Sinar matahari, ikan berlemak</small>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3 mb-3 mt-2">
                                <div class="text-center">
                                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                        style="width: 40px; height: 40px;">
                                        <i class="bi bi-egg text-success" style="font-size: 1.2rem;"></i>
                                    </div>
                                    <h6 class="text-success mb-1">Protein</h6>
                                    <small class="text-muted">Hewani & nabati seimbang</small>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3 mb-3">
                                <div class="text-center">
                                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                        style="width: 40px; height: 40px;">
                                        <i class="bi bi-gem text-info" style="font-size: 1.2rem;"></i>
                                    </div>
                                    <h6 class="text-info mb-1">Mineral</h6>
                                    <small class="text-muted">Magnesium, vitamin K</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Sistem -->
        <div class="row justify-content-center mb-4">
            <div class="col-lg-10">
                <div class="card bg-light">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3 col-6 mb-3">
                                <div class="h4 text-primary mb-1">116</div>
                                <small class="text-muted">Total Elemen Knowledge Base</small>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="h4 text-primary mb-1">4</div>
                                <small class="text-muted">Tahap Konsultasi</small>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="h4 text-primary mb-1">30-35</div>
                                <small class="text-muted">Menit Konsultasi</small>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="h4 text-primary mb-1">5</div>
                                <small class="text-muted">Kategori Nutrisi Utama</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow">
                    <div class="card-body text-center py-4">
                        <h5 class="mb-3">Siap Memulai Konsultasi?</h5>
                        <p class="text-muted mb-4">
                            Dapatkan rekomendasi nutrisi personal yang disesuaikan dengan kondisi medis,
                            alergi makanan, dan preferensi Anda untuk menangani osteoporosis.
                        </p>

                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <form action="{{ route('konsultasi.store') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-play-circle me-2"></i>
                                    Mulai Konsultasi Baru
                                </button>
                            </form>

                            <a href="{{ route('konsultasi.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-clock-history me-2"></i>
                                Lihat Riwayat Konsultasi
                            </a>
                        </div>

                        <div class="mt-4">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Sistem ini tidak menggantikan konsultasi medis profesional.
                                Untuk diagnosis pasti, konsultasikan dengan dokter spesialis.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css-top')
    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        }

        .card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .badge {
            font-size: 0.8rem;
        }

        @media (max-width: 768px) {
            .btn-lg {
                font-size: 1rem;
                padding: 0.75rem 1.5rem;
            }
        }
    </style>
@endpush

@push('scripts-bottom')
    <script>
        $(document).ready(function() {
            // Add animation to cards on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe all cards
            document.querySelectorAll('.card').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });

            // Add tooltip for info
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
