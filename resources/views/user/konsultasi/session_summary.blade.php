@extends('layouts.user')

@section('title', 'Ringkasan Sesi - Konsultasi Osteoporosis | SPPOBN')
@section('title-menu', 'Ringkasan ' . $infoSesiSekarang['nama'])
@section('subtitle-menu', $infoSesiSekarang['deskripsi'])

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Ringkasan Sesi</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="container-fluid px-4">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-sm font-weight-bold text-primary">Progress Konsultasi</span>
                                <span class="text-sm text-gray-600">{{ $hasilKonsultasi['progress'] }}%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-primary" role="progressbar"
                                    style="width: {{ $hasilKonsultasi['progress'] }}%"
                                    aria-valuenow="{{ $hasilKonsultasi['progress'] }}" aria-valuemin="0"
                                    aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Alert Messages for Different Scenarios --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-info-circle mr-2"></i>{{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            @if ($jawabanUser->count() > 0)
                                <div class="card mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">
                                            <i class="fas fa-question-circle mr-2"></i>
                                            Jawaban Anda di Sesi Ini
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th width="15%">Kode</th>
                                                        <th width="65%">Pertanyaan</th>
                                                        <th width="20%">Jawaban</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($jawabanUser as $jawaban)
                                                        <tr>
                                                            <td><code>{{ $jawaban['kode'] }}</code></td>
                                                            <td>{{ $jawaban['pertanyaan'] }}</td>
                                                            <td>
                                                                <span
                                                                    class="badge bg-{{ $jawaban['jawaban'] === 'ya' ? 'success' : 'secondary' }}">
                                                                    {{ ucfirst($jawaban['jawaban']) }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-4">
                            <div class="card mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-directions mr-2"></i>Pilihan Tindakan
                                    </h6>
                                </div>
                                <div class="card-body">
                                    {{-- Cek apakah ada fakta antara atau solusi yang ditemukan --}}
                                    @php
                                        $adaFaktaAntara = !empty($hasilKonsultasi['fakta_antara']);
                                        $adaSolusi = !empty($detailSolusi);
                                        $bisaLanjutLogis = $adaFaktaAntara || $adaSolusi;
                                        $sesiSekarang = $hasilKonsultasi['sesi'];

                                        // Pesan khusus berdasarkan kondisi
                                        $pesanKhusus = '';
                                        if (!$bisaLanjutLogis) {
                                            if ($sesiSekarang == 1) {
                                                $pesanKhusus =
                                                    'Berdasarkan jawaban Anda, tidak ditemukan faktor risiko osteoporosis yang signifikan. Konsultasi akan dihentikan dengan rekomendasi pencegahan umum.';
                                            } else {
                                                $pesanKhusus =
                                                    'Tidak ada aturan yang terpenuhi pada sesi ini. Konsultasi tidak dapat dilanjutkan ke sesi berikutnya.';
                                            }
                                        }
                                    @endphp

                                    @if ($bisaLanjutSesi && $bisaLanjutLogis && isset($infoSesiBerikutnya))
                                        {{-- Can Continue to Next Session --}}
                                        <div class="alert alert-info mb-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-arrow-right text-info mr-2"></i>
                                                <h6 class="alert-heading mb-0">Sesi Berikutnya Tersedia</h6>
                                            </div>
                                            <p class="mb-2"><strong>{{ $infoSesiBerikutnya['nama'] }}</strong></p>
                                            <p class="mb-3 text-sm">{{ $infoSesiBerikutnya['deskripsi'] }}</p>

                                            {{-- Special handling for FRAX session --}}
                                            @if ($infoSesiBerikutnya['nomor'] == 2)
                                                <button type="button" class="btn btn-info btn-sm btn-block mb-2"
                                                    data-bs-toggle="modal" data-bs-target="#edukasiModal"
                                                    data-konten-id="1">
                                                    <i class="fab fa-youtube mr-2"></i> Pelajari FRAX Tool (Video)
                                                </button>
                                            @endif

                                            <form action="{{ route('konsultasi.continue-session', $konsultasi->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-primary btn-block">
                                                    <i class="fas fa-play mr-2"></i>
                                                    Lanjut ke Sesi {{ $infoSesiBerikutnya['nomor'] }}
                                                </button>
                                            </form>
                                        </div>
                                    @elseif (!$bisaLanjutLogis)
                                        {{-- Cannot Continue - No Rules Triggered --}}
                                        @if ($sesiSekarang == 1 && !$adaFaktaAntara)
                                            {{-- No Risk Found in Session 1 --}}
                                            <div class="alert alert-success mb-3">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-shield-alt text-success mr-2"></i>
                                                    <h6 class="alert-heading mb-0">Risiko Rendah Terdeteksi</h6>
                                                </div>
                                                <p class="mb-3">{{ $pesanKhusus }}</p>
                                                <div class="btn-group-vertical w-100">
                                                    <button type="button" class="btn btn-success btn-block mb-2"
                                                        onclick="selesaikanKonsultasi()">
                                                        <i class="fas fa-check-circle mr-2"></i>
                                                        Selesaikan Konsultasi
                                                    </button>
                                                    <a href="{{ route('konsultasi.result', $konsultasi->id) }}"
                                                        class="btn btn-outline-success btn-block">
                                                        <i class="fas fa-file-medical mr-2"></i>
                                                        Lihat Rekomendasi
                                                    </a>
                                                </div>
                                            </div>
                                        @else
                                            {{-- Insufficient Findings in Other Sessions --}}
                                            <div class="alert alert-warning mb-3">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
                                                    <h6 class="alert-heading mb-0">Konsultasi Terhenti</h6>
                                                </div>
                                                <p class="mb-3">{{ $pesanKhusus }}</p>
                                                <div class="btn-group-vertical w-100">
                                                    <button type="button" class="btn btn-warning btn-block mb-2"
                                                        onclick="selesaikanKonsultasi()">
                                                        <i class="fas fa-stop-circle mr-2"></i>
                                                        Selesaikan Konsultasi
                                                    </button>
                                                    <a href="{{ route('konsultasi.result', $konsultasi->id) }}"
                                                        class="btn btn-outline-warning btn-block">
                                                        <i class="fas fa-eye mr-2"></i>
                                                        Lihat Hasil Saat Ini
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    @elseif ($hasilKonsultasi['sesi'] >= 4)
                                        {{-- Consultation Complete --}}
                                        <div class="alert alert-success mb-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-flag-checkered text-success mr-2"></i>
                                                <h6 class="alert-heading mb-0">Konsultasi Selesai</h6>
                                            </div>
                                            <p class="mb-3">Semua sesi telah diselesaikan. Anda dapat melihat hasil
                                                lengkap dan rekomendasi.</p>
                                            <a href="{{ route('konsultasi.result', $konsultasi->id) }}"
                                                class="btn btn-success btn-block">
                                                <i class="fas fa-chart-line mr-2"></i>
                                                Lihat Hasil Lengkap
                                            </a>
                                        </div>
                                    @else
                                        {{-- Default Case - Consultation Complete --}}
                                        <div class="alert alert-success mb-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-trophy text-success mr-2"></i>
                                                <h6 class="alert-heading mb-0">Konsultasi Selesai</h6>
                                            </div>
                                            <p class="mb-3">Terima kasih telah menyelesaikan konsultasi. Hasil dan
                                                rekomendasi telah tersedia.</p>
                                            <a href="{{ route('konsultasi.result', $konsultasi->id) }}"
                                                class="btn btn-success btn-block">
                                                <i class="fas fa-download mr-2"></i>
                                                Lihat & Unduh Hasil
                                            </a>
                                        </div>
                                    @endif

                                    {{-- Common Actions (hanya tampil jika konsultasi belum selesai) --}}
                                    @if ($konsultasi->status !== 'selesai')
                                        <div class="btn-group-vertical w-100 mt-3">
                                            <button type="button" class="btn btn-outline-warning btn-sm mb-2"
                                                onclick="simpanKonsultasi()">
                                                <i class="fas fa-save mr-2"></i>
                                                Simpan & Lanjutkan Nanti
                                            </button>
                                            <a href="{{ route('konsultasi.index') }}" class="btn btn-secondary btn-sm">
                                                <i class="fas fa-list mr-2"></i>
                                                Kembali ke Daftar
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Session Information Card --}}
                            <div class="card mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-dark">
                                        <i class="fas fa-info-circle mr-2"></i>Informasi Sesi
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="border-right">
                                                <div class="h5 font-weight-bold text-primary">
                                                    {{ count($hasilKonsultasi['fakta_tersedia']) }}
                                                </div>
                                                <div class="text-xs text-muted">Fakta Dasar</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="border-right">
                                                <div class="h5 font-weight-bold text-info">
                                                    {{ count($hasilKonsultasi['fakta_antara']) }}
                                                </div>
                                                <div class="text-xs text-muted">Fakta Antara</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="h5 font-weight-bold text-success">
                                                {{ count($detailSolusi) }}
                                            </div>
                                            <div class="text-xs text-muted">Solusi</div>
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="text-center">
                                        <small class="text-muted">
                                            Sesi {{ $hasilKonsultasi['sesi'] }} dari 4 •
                                            @if (!$bisaLanjutLogis)
                                                <span class="badge bg-warning text-dark">Tidak Ada Aturan Terpenuhi</span>
                                            @elseif ($bisaLanjutSesi && $bisaLanjutLogis)
                                                <span class="badge bg-info">Dapat Dilanjutkan</span>
                                            @else
                                                <span class="badge bg-success">Selesai</span>
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>

                            {{-- Solutions Found in This Session --}}
                            @if (!empty($detailSolusi))
                                <div class="card mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-success">
                                            <i class="fas fa-lightbulb mr-2"></i>
                                            Temuan di Sesi Ini
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @foreach ($detailSolusi as $solusi)
                                            <div class="alert alert-success alert-sm mb-2">
                                                <div class="d-flex align-items-start">
                                                    <i class="fas fa-check-circle text-success mr-2 mt-1"></i>
                                                    <div class="flex-grow-1">
                                                        <h6 class="alert-heading mb-1">{{ $solusi['nama'] }}</h6>
                                                        <p class="mb-2 text-sm">{{ $solusi['deskripsi'] }}</p>
                                                        @if (!empty($solusi['peringatan']))
                                                            <div class="alert alert-warning alert-sm mt-2 mb-0 py-1">
                                                                <small><strong>Perhatian:</strong>
                                                                    {{ $solusi['peringatan'] }}</small>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @elseif (empty($hasilKonsultasi['fakta_antara']) && $sesiSekarang > 1)
                                {{-- No intermediate facts found message --}}
                                <div class="card mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-warning">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            Status Inferensi
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-warning alert-sm mb-0">
                                            <div class="d-flex align-items-start">
                                                <i class="fas fa-exclamation-triangle text-warning mr-2 mt-1"></i>
                                                <div class="flex-grow-1">
                                                    <p class="mb-0 text-sm">
                                                        Tidak ditemukan fakta antara atau solusi baru pada sesi ini.
                                                        Sistem forward chaining tidak dapat melanjutkan proses inferensi.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Inference Trace --}}
                            @if (!empty($traceInferensi))
                                <div class="card mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-info">
                                            <i class="fas fa-code-branch mr-2"></i>
                                            Proses Inferensi
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="timeline">
                                            @foreach ($traceInferensi as $index => $trace)
                                                <div class="timeline-item mb-3">
                                                    <div
                                                        class="timeline-marker bg-{{ $trace['jenis'] === 'Solusi' ? 'success' : 'info' }}">
                                                    </div>
                                                    <div class="timeline-content">
                                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                                            <h6 class="mb-0">{{ $trace['aturan_kode'] }}</h6>
                                                            <small class="text-muted">#{{ $index + 1 }}</small>
                                                        </div>
                                                        <p class="text-sm text-muted mb-1">
                                                            {{ $trace['aturan_deskripsi'] }}</p>
                                                        <p class="mb-1 text-sm">
                                                            <strong>Premis:</strong>
                                                            <code class="text-xs">{{ $trace['premis'] }}</code>
                                                        </p>
                                                        <p class="mb-0 text-sm">
                                                            <strong>Hasil:</strong>
                                                            <span
                                                                class="badge bg-{{ $trace['jenis'] === 'Solusi' ? 'success' : 'info' }}">
                                                                {{ $trace['fakta_terbentuk'] }}
                                                            </span>
                                                            <small class="text-muted">({{ $trace['jenis'] }})</small>
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Educational Modal for FRAX --}}
    <div class="modal fade modal-video" id="edukasiModal" tabindex="-1" aria-labelledby="edukasiModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div id="edukasiModalBody" class="modal-body" data-storage-path="{{ asset('storage') }}"
                    data-placeholder-image="{{ asset('assets/images/placeholder-image.jpg') }}">
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe id="modalVideoIframe" class="embed-responsive-item rounded-iframe-video"
                            style="width:100%;" src="https://www.youtube.com/embed/6r02weQNPXU?si=NJ6VEKp7osfolcy8"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin" allowfullscreen="">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts-bottom')
    <script>
        // Handle educational modal
        const edukasiModal = document.getElementById('edukasiModal');
        const modalVideoIframe = document.getElementById('modalVideoIframe');

        if (edukasiModal && modalVideoIframe) {
            edukasiModal.addEventListener('hidden.bs.modal', function() {
                const videoSrc = modalVideoIframe.src;
                modalVideoIframe.src = videoSrc; // Reset to stop video
            });
        }

        // Save consultation function
        function simpanKonsultasi() {
            if (confirm('Apakah Anda yakin ingin menyimpan konsultasi ini untuk dilanjutkan nanti?')) {
                fetch('{{ route('konsultasi.pending') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            konsultasi_id: {{ $konsultasi->id }}
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Konsultasi berhasil disimpan!');
                            window.location.href = '{{ route('konsultasi.index') }}';
                        } else {
                            alert('Terjadi kesalahan saat menyimpan konsultasi');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menyimpan konsultasi');
                    });
            }
        }

        // Finish consultation function
        function selesaikanKonsultasi() {
            if (confirm('Apakah Anda yakin ingin menyelesaikan konsultasi ini?')) {
                fetch('{{ route('konsultasi.complete') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            konsultasi_id: {{ $konsultasi->id }}
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = '{{ route('konsultasi.result', $konsultasi->id) }}';
                        } else {
                            alert('Terjadi kesalahan saat menyelesaikan konsultasi');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menyelesaikan konsultasi');
                    });
            }
        }
    </script>
@endpush

@push('css-top')
    <style>
        .timeline {
            position: relative;
            padding-left: 1.5rem;
        }

        .timeline-item {
            position: relative;
        }

        .timeline-marker {
            position: absolute;
            left: -1.75rem;
            top: 0.25rem;
            width: 0.75rem;
            height: 0.75rem;
            border-radius: 50%;
        }

        .timeline-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: -1.375rem;
            top: 1rem;
            width: 2px;
            height: calc(100% - 1rem);
            background-color: #e3e6f0;
        }

        .alert-sm {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }

        .btn-group-vertical .btn:not(:last-child) {
            margin-bottom: 0.25rem;
        }

        .rounded-iframe-video {
            border-radius: 0.375rem;
        }

        .border-right {
            border-right: 1px solid #e3e6f0 !important;
        }

        @media (max-width: 576px) {
            .border-right {
                border-right: none !important;
                border-bottom: 1px solid #e3e6f0 !important;
                padding-bottom: 1rem;
                margin-bottom: 1rem;
            }
        }
    </style>
    @endphp
