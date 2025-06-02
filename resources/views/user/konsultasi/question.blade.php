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

                        {{-- Informasi Sesi --}}
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
                        </div>

                        {{-- Pertanyaan --}}
                        @if ($pertanyaan)
                            <div class="question-container"
                                style="border: 1px solid #ddd; padding: 30px; border-radius: 15px; margin-bottom: 30px; background-color: #f8f9fa;">
                                <div class="text-center mb-3">
                                    <span class="badge bg-secondary fs-6">Kode: {{ $pertanyaan->kode }}</span>
                                </div>
                                <h3 class="text-center mb-4" style="font-weight: 600; line-height: 1.4;">
                                    {{ $pertanyaan->pertanyaan }}
                                </h3>

                                {{-- Kategori atau info tambahan jika ada --}}
                                @if ($pertanyaan->kategori)
                                    <div class="text-center mb-3">
                                        <span class="badge bg-light text-dark">
                                            Kategori: {{ ucwords(str_replace('_', ' ', $pertanyaan->kategori)) }}
                                        </span>
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
                                <a href="{{ route('konsultasi.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                                </a>
                                @if ($konsultasi->status !== 'completed')
                                    <a href="{{ route('konsultasi.restart', $konsultasi->id) }}"
                                        class="btn btn-outline-warning"
                                        onclick="return confirm('Yakin ingin mengulang konsultasi dari awal?')">
                                        <i class="bi bi-arrow-clockwise"></i> Ulang dari Awal
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts-bottom')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnYa = document.getElementById('btnYa');
            const btnTidak = document.getElementById('btnTidak');
            const jawabanInput = document.getElementById('jawabanInput');
            const formJawaban = document.getElementById('formJawaban');

            let isSubmitting = false;

            function handleAnswer(jawaban) {
                if (isSubmitting) {
                    console.log('Pengiriman sudah dalam proses...');
                    return;
                }

                isSubmitting = true;

                // Nonaktifkan kedua tombol
                btnYa.disabled = true;
                btnTidak.disabled = true;

                // Ubah tampilan tombol yang dipilih
                if (jawaban === 'ya') {
                    btnYa.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses...';
                    btnYa.classList.add('btn-success');
                } else {
                    btnTidak.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses...';
                    btnTidak.classList.add('btn-danger');
                }

                // Set nilai jawaban dan submit form
                jawabanInput.value = jawaban;

                // Delay sedikit untuk memberikan feedback visual
                setTimeout(() => {
                    formJawaban.submit();
                }, 300);
            }

            // Event listeners
            if (btnYa && btnTidak) {
                btnYa.addEventListener('click', () => handleAnswer('ya'));
                btnTidak.addEventListener('click', () => handleAnswer('tidak'));
            }

            // Mencegah double submit dengan keyboard
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !isSubmitting) {
                    e.preventDefault();
                }
            });
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
