@extends('layouts.user')

@section('title')
    Konsultasi Osteoporosis -
    @php
        $sesiNames = [
            1 => 'Skrining Awal',
            2 => 'Klasifikasi Risiko FRAX',
            3 => 'Penilaian Asupan Nutrisi',
            4 => 'Preferensi Makanan',
        ];
        $currentSesi = $hasilKonsultasi['sesi'] ?? 1;
        $sesiName = $sesiNames[$currentSesi] ?? 'Tidak Diketahui';
    @endphp
    {{ $sesiName }} | SPPOBN
@endsection
@section('title-menu', 'Konsultasi Osteoporosis')
@section('subtitle-menu', 'Jawab pertanyaan untuk mendapatkan rekomendasi nutrisi')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('konsultasi.index') }}">Konsultasi</a></li>
    <li class="breadcrumb-item active" aria-current="page">Sesi Konsultasi</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi bi-question-circle-fill"></i> Pertanyaan</h4>
                        <span class="badge bg-primary fs-6">
                            Sesi {{ $hasilKonsultasi['sesi'] ?? 1 }}: {{ $sesiName }}
                        </span>
                    </div>
                    <div class="card-body">
                        {{-- Progress Bar --}}
                        @if (isset($hasilKonsultasi['progress']))
                            <div class="progress mb-4" style="height: 25px;">
                                <div class="progress-bar 
                                    @if ($hasilKonsultasi['progress'] < 25) bg-danger
                                    @elseif($hasilKonsultasi['progress'] < 50) bg-warning
                                    @elseif($hasilKonsultasi['progress'] < 75) bg-info
                                    @else bg-success @endif"
                                    role="progressbar" style="width: {{ $hasilKonsultasi['progress'] }}%"
                                    aria-valuenow="{{ $hasilKonsultasi['progress'] }}" aria-valuemin="0"
                                    aria-valuemax="100">
                                    {{ round($hasilKonsultasi['progress'], 1) }}%
                                </div>
                            </div>
                        @endif

                        {{-- Informasi Sesi
                        <div class="alert alert-info mb-4">
                            <h5 class="alert-heading mb-2">
                                <i class="bi bi-info-circle"></i>
                                {{ $sesiName }}
                            </h5>
                            <p class="mb-0"> @php
                                $sesiDescriptions = [
                                    1 => 'Menilai faktor risiko osteoporosis berdasarkan riwayat kesehatan dan gaya hidup',
                                    2 => 'Menghitung risiko fraktur menggunakan FRAX tool berdasarkan data klinis',
                                    3 => 'Mengevaluasi kecukupan asupan nutrisi penting untuk kesehatan tulang',
                                    4 => 'Menentukan rekomendasi makanan berdasarkan preferensi dan kondisi kesehatan',
                                ];
                                $sesiDescription = $sesiDescriptions[$currentSesi] ?? '';
                            @endphp
                                {{ $sesiDescription }}</p>
                        </div> --}}

                        {{-- Pertanyaan --}}
                        @if ($pertanyaan)
                            <div class="question-container"
                                style="border: 1px solid #ddd; padding: 30px; border-radius: 15px; margin-bottom: 30px; background-color: #f8f9fa;">
                                <div class="text-center mb-3">
                                    <span class="badge bg-secondary fs-6">Kode: {{ $pertanyaan->kode }}</span>
                                </div>
                                <h3 class="text-center mb-4" style="line-height: 1.4;">
                                    {{ $pertanyaan->pertanyaan }}

                                    @if ($pertanyaan->kode === 'F007')
                                        <button type="button" class="btn btn-outline-primary btn-sm ms-2"
                                            data-bs-toggle="modal" data-bs-target="#bmiCalculatorModal">
                                            <i class="bi bi-calculator"></i> Hitung BMI
                                        </button>
                                    @endif
                                </h3>


                                {{-- Kategori atau info tambahan jika ada --}}
                                @if ($pertanyaan->kategori)
                                    <div class="text-center mb-3">
                                        <span class="badge bg-light text-dark">
                                            Kategori: {{ ucwords(str_replace('_', ' ', $pertanyaan->kategori)) }}
                                        </span>
                                    </div>
                                @endif

                                {{-- Catatan tambahan jika ada --}}
                                @if ($pertanyaan->catatan)
                                    <div class="mt-3 p-2 rounded"
                                        style="background-color: #e9ecef; font-size: 0.9em; border-left: 3px solid #6c757d;">
                                        <p class="mb-0"><strong>Catatan:</strong> {{ $pertanyaan->catatan }}</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Tombol Jawaban --}}
                            <div class="d-flex justify-content-center gap-4 mb-4">
                                <button type="button" id="btnYa" class="btn btn-success btn-lg px-5 py-3"
                                    style="font-size: 1.2em; min-width: 120px;">
                                    <i class="bi bi-check-circle"></i> Ya
                                </button>
                                <button type="button" id="btnTidak" class="btn btn-danger btn-lg px-5 py-3"
                                    style="font-size: 1.2em; min-width: 120px;">
                                    <i class="bi bi-x-circle"></i> Tidak
                                </button>
                            </div>

                            {{-- Form Hidden untuk Submit --}}
                            <form id="formJawaban" method="POST"
                                action="{{ route('konsultasi.answer', $konsultasi->id) }}" style="display: none;">
                                @csrf
                                <input type="hidden" name="kode_fakta" value="{{ $pertanyaan->kode }}">
                                <input type="hidden" name="jawaban" id="jawabanInput">
                            </form>

                            {{-- Informasi Progress --}}
                            <div class="row mt-4">
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Fakta Tersedia</h6>
                                            <h4 class="text-primary">{{ count($hasilKonsultasi['fakta_tersedia'] ?? []) }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Sesi Saat Ini</h6>
                                            <h4 class="text-info">{{ $hasilKonsultasi['sesi'] ?? 1 }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Solusi Ditemukan</h6>
                                            <h4 class="text-success">{{ count($hasilKonsultasi['solusi'] ?? []) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Tidak ada pertanyaan --}}
                            <div class="text-center py-5">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                                <h3 class="mt-3 mb-4">Konsultasi Selesai!</h3>
                                <p class="fs-5 text-muted mb-4">
                                    Tidak ada pertanyaan lagi untuk sesi ini. Silakan lihat hasil konsultasi Anda.
                                </p>
                                <a href="{{ route('konsultasi.result', $konsultasi->id) }}"
                                    class="btn btn-success btn-lg px-4 py-3" style="font-size: 1.2em;">
                                    <i class="bi bi-eye"></i> Lihat Hasil Konsultasi
                                </a>
                            </div>
                        @endif

                        {{-- Tombol Aksi Tambahan --}}
                        <div class="text-center mt-4">
                            <div class="btn-group" role="group">
                                <a href="{{ route('konsultasi.index') }}" class="btn btn-outline-secondary me-3">
                                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                                </a>
                                @if ($konsultasi->status !== 'selesai')
                                    {{-- Mengubah form submit biasa menjadi button yang memicu SweetAlert --}}
                                    <button type="button" class="btn btn-outline-warning" id="restartConsultationBtn">
                                        <i class="bi bi-arrow-clockwise"></i> Ulang dari Awal
                                    </button>
                                    {{-- Form hidden untuk submit POST request restart --}}
                                    <form id="restartForm" action="{{ route('konsultasi.restart', $konsultasi->id) }}"
                                        method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bmiCalculatorModal" tabindex="-1" aria-labelledby="bmiCalculatorModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bmiCalculatorModalLabel">Kalkulator Indeks Massa Tubuh (BMI)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Masukkan berat badan Anda dalam kilogram (kg) dan tinggi badan dalam centimeter (cm) untuk menghitung
                        BMI Anda.</p>
                    <div class="mb-3">
                        <label for="beratBadan" class="form-label">Berat Badan (kg)</label>
                        <input type="number" class="form-control" id="beratBadan" placeholder="Contoh: 60"
                            step="0.1" min="1" max="500">
                    </div>
                    <div class="mb-3">
                        <label for="tinggiBadan" class="form-label">Tinggi Badan (cm)</label>
                        <input type="number" class="form-control" id="tinggiBadan" placeholder="Contoh: 170"
                            step="0.1" min="1" max="300">
                    </div>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary" id="hitungBmiBtn">Hitung BMI</button>
                    </div>
                    <div id="bmiError" class="mt-4 alert nodismissable alert-danger" role="alert"
                        style="display: none;">
                        Mohon masukkan berat badan dan tinggi badan yang valid.
                    </div>
                    <div id="bmiResult" class="mt-4 p-3 bg-light rounded" style="display: none;">
                        <h6 class="mb-2">Hasil BMI Anda: <span id="bmiValue" class="fw-bold fs-5"></span></h6>
                        <p class="mb-0">Kategori: <span id="bmiCategory" class="fw-bold"></span></p>
                        <p class="mt-2"><small><i>Gunakan hasil ini untuk menjawab pertanyaan konsultasi.</i></small></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts-bottom')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnYa = document.getElementById('btnYa');
            const btnTidak = document.getElementById('btnTidak');
            const jawabanInput = document.getElementById('jawabanInput');
            const formJawaban = document.getElementById('formJawaban');
            const restartConsultationBtn = document.getElementById('restartConsultationBtn');
            const restartForm = document.getElementById('restartForm');

            let isSubmitting = false;

            function handleAnswer(jawaban) {
                if (isSubmitting) {
                    console.log('Pengiriman sudah dalam proses...');
                    return;
                }

                isSubmitting = true;

                btnYa.disabled = true;
                btnTidak.disabled = true;

                if (jawaban === 'ya') {
                    btnYa.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses...';
                    btnYa.classList.add('btn-success');
                } else {
                    btnTidak.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses...';
                    btnTidak.classList.add('btn-danger');
                }

                jawabanInput.value = jawaban;

                setTimeout(() => {
                    formJawaban.submit();
                }, 300);
            }

            if (btnYa && btnTidak) {
                btnYa.addEventListener('click', () => handleAnswer('ya'));
                btnTidak.addEventListener('click', () => handleAnswer('tidak'));
            }

            if (restartConsultationBtn) {
                restartConsultationBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Anda yakin?',
                        text: "Konsultasi akan diulang dari awal!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Ulang Sekarang!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            restartForm.submit();
                        }
                    });
                });
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !isSubmitting) {
                    e.preventDefault();
                }
            });

            // --- Logika Kalkulator BMI ---
            const beratBadanInput = document.getElementById('beratBadan');
            const tinggiBadanInput = document.getElementById('tinggiBadan');
            const hitungBmiBtn = document.getElementById('hitungBmiBtn');
            const bmiResultDiv = document.getElementById('bmiResult');
            const bmiValueSpan = document.getElementById('bmiValue');
            const bmiCategorySpan = document.getElementById('bmiCategory');
            const bmiErrorDiv = document.getElementById('bmiError');

            // Fungsi untuk melakukan validasi dan perhitungan BMI
            function calculateAndValidateBmi() {
                const beratBadan = parseFloat(beratBadanInput.value);
                const tinggiBadanCm = parseFloat(tinggiBadanInput.value);

                // Sembunyikan pesan error dan hasil sebelumnya
                bmiErrorDiv.style.display = 'none';
                bmiResultDiv.style.display = 'none';

                // Reset teks error
                bmiErrorDiv.textContent = 'Mohon masukkan berat badan dan tinggi badan yang valid.';

                // Validasi input: harus angka positif dan dalam rentang wajar
                if (isNaN(beratBadan) || isNaN(tinggiBadanCm) || beratBadan <= 0 || tinggiBadanCm <= 0) {
                    bmiErrorDiv.style.display = 'block';
                    return;
                }

                // Validasi rentang yang tidak masuk akal
                if (beratBadan < 10 || beratBadan > 500) { // Contoh: min 10kg, max 500kg
                    bmiErrorDiv.textContent =
                        'Berat badan tidak masuk akal. Harap masukkan nilai antara 10 kg dan 500 kg.';
                    bmiErrorDiv.style.display = 'block';
                    return;
                }

                if (tinggiBadanCm < 50 || tinggiBadanCm > 300) { // Contoh: min 50cm, max 300cm
                    bmiErrorDiv.textContent =
                        'Tinggi badan tidak masuk akal. Harap masukkan nilai antara 50 cm dan 300 cm.';
                    bmiErrorDiv.style.display = 'block';
                    return;
                }

                const tinggiBadanM = tinggiBadanCm / 100;
                const bmi = beratBadan / (tinggiBadanM * tinggiBadanM);
                const roundedBmi = bmi.toFixed(2);

                let category = '';
                if (bmi < 18.5) {
                    category = 'Kurang Berat Badan';
                } else if (bmi >= 18.5 && bmi < 25) {
                    category = 'Berat Badan Normal';
                } else if (bmi >= 25 && bmi < 30) {
                    category = 'Kelebihan Berat Badan';
                } else {
                    category = 'Obesitas';
                }

                bmiValueSpan.textContent = roundedBmi;
                bmiCategorySpan.textContent = category;
                bmiResultDiv.style.display = 'block';
            }

            // Event listener untuk tombol Hitung BMI
            if (hitungBmiBtn) {
                hitungBmiBtn.addEventListener('click', calculateAndValidateBmi);
            }

            // Event listeners untuk input berat badan dan tinggi badan (keyup)
            if (beratBadanInput) {
                beratBadanInput.addEventListener('keyup', function() {
                    // Sembunyikan hasil dan error jika pengguna mulai mengetik lagi
                    bmiResultDiv.style.display = 'none';
                    bmiErrorDiv.style.display = 'none';
                });
            }
            if (tinggiBadanInput) {
                tinggiBadanInput.addEventListener('keyup', function() {
                    // Sembunyikan hasil dan error jika pengguna mulai mengetik lagi
                    bmiResultDiv.style.display = 'none';
                    bmiErrorDiv.style.display = 'none';
                });
            }
        });

        // Helper functions untuk mendapatkan nama dan deskripsi sesi
        @php
            $getSesiNama = function ($sesi) {
                return [
                    1 => 'Skrining Awal',
                    2 => 'Klasifikasi Risiko FRAX',
                    3 => 'Penilaian Asupan Nutrisi',
                    4 => 'Preferensi Makanan',
                ][$sesi] ?? 'Tidak Diketahui';
            };

            $getSesiDeskripsi = function ($sesi) {
                return [
                    1 => 'Menilai faktor risiko osteoporosis berdasarkan riwayat kesehatan dan gaya hidup',
                    2 => 'Menghitung risiko fraktur menggunakan FRAX tool berdasarkan data klinis',
                    3 => 'Mengevaluasi kecukupan asupan nutrisi penting untuk kesehatan tulang',
                    4 => 'Menentukan rekomendasi makanan berdasarkan preferensi dan kondisi kesehatan',
                ][$sesi] ?? '';
            };
        @endphp
    </script>
@endpush
