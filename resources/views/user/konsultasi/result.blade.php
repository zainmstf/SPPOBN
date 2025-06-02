@extends('layouts.user')

@section('title', 'Hasil Konsultasi - Konsultasi Osteoporosis | SPPOBN')
@section('title-menu', 'Hasil Konsultasi Osteoporosis')
@section('subtitle-menu', 'Laporan lengkap hasil konsultasi dan rekomendasi')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('konsultasi.index') }}">Konsultasi</a></li>
    <li class="breadcrumb-item active" aria-current="page">Hasil Konsultasi</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="container-fluid px-4">
            <div class="row">
                <div class="col-12">
                    {{-- Header Card with Progress --}}
                    <div class="card mb-4">
                        <div class="card-body">
                            <x-alert />
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center">
                                        <div class="result-status-icon me-3">
                                            @if (!empty($detailSolusi))
                                                <i class="fas fa-check-circle text-success fa-2x"></i>
                                            @elseif (isset($statusMessage) && $statusMessage['status'] === 'tidak_ada_solusi')
                                                <i class="fas fa-info-circle text-warning fa-2x"></i>
                                            @else
                                                <i class="fas fa-clipboard-check text-primary fa-2x"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <h5 class="mb-1">
                                                @if (!empty($detailSolusi))
                                                    Konsultasi Selesai dengan Rekomendasi
                                                @elseif (isset($statusMessage) && $statusMessage['status'] === 'tidak_ada_solusi')
                                                    Konsultasi Selesai - Risiko Rendah
                                                @else
                                                    Konsultasi Selesai
                                                @endif
                                            </h5>
                                            <p class="text-muted mb-0">
                                                Konsultasi ID: <code>{{ $konsultasi->id }}</code> •
                                                Diselesaikan:
                                                {{ $konsultasi->completed_at ? $konsultasi->completed_at->format('d M Y, H:i') : now()->format('d M Y, H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-md-right">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-sm font-weight-bold text-primary">Progress Konsultasi</span>
                                        {{-- Calculate display progress: 100% if status is 'selesai', otherwise use $hasilKonsultasi['progress'] --}}
                                        @php
                                            $displayProgress =
                                                $konsultasi->status === 'selesai' ? 100 : $hasilKonsultasi['progress'];
                                        @endphp
                                        <span class="text-sm text-gray-600">{{ $displayProgress }}%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar"
                                            style="width: {{ $displayProgress }}%" aria-valuenow="{{ $displayProgress }}"
                                            aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Main Content --}}
                        <div class="col-lg-8">
                            {{-- Status Message Card --}}
                            @if (isset($statusMessage))
                                <div class="card mb-4">
                                    <div class="card-header py-3">
                                        <h6
                                            class="m-0 font-weight-bold 
                                            @if ($statusMessage['status'] === 'tidak_ada_solusi') text-warning 
                                            @elseif(!empty($detailSolusi)) text-success 
                                            @else text-primary @endif">
                                            <i
                                                class="fas fa-{{ $statusMessage['status'] === 'tidak_ada_solusi' ? 'exclamation-triangle' : 'info-circle' }} me-2"></i>
                                            Status Konsultasi
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div
                                            class="alert alert-{{ $statusMessage['status'] === 'tidak_ada_solusi' ? 'warning' : 'success' }} mb-0">
                                            <p class="mb-0">{{ $statusMessage['message'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Solutions/Recommendations Card --}}
                            @if (!empty($detailSolusi))
                                <div class="card mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-success">
                                            <i class="fas fa-lightbulb me-2"></i>
                                            Rekomendasi Berdasarkan Konsultasi
                                        </h6>
                                    </div>
                                    <div class="card-body" style="max-height: 800px; overflow-y: auto;">
                                        @foreach ($detailSolusi as $index => $solusi)
                                            <div class="solution-item mb-4 {{ !$loop->last ? 'border-bottom pb-4' : '' }}">
                                                <div class="d-flex align-items-start">
                                                    <div class="solution-number me-3">
                                                        <span
                                                            class="badge bg-success badge-pill">{{ $index + 1 }}</span>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="solution-title mb-2">{{ $solusi['nama'] }}</h6>
                                                        <p class="solution-description mb-2">{{ $solusi['deskripsi'] }}</p>
                                                        @if (!empty($solusi['peringatan']))
                                                            <div class="alert alert-secondary alert-sm mt-2 mb-0">
                                                                <div class="d-flex align-items-start">
                                                                    <div>
                                                                        <strong>Perhatian:</strong>
                                                                        {{ $solusi['peringatan'] }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                {{-- No Solutions Found --}}
                                <div class="card mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">
                                            <i class="fas fa-shield-alt me-2"></i>
                                            Hasil Evaluasi
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-success mb-0">
                                            <div class="d-flex align-items-start">
                                                <i class="fas fa-check-circle text-success me-3 mt-1"></i>
                                                <div>
                                                    <h6 class="alert-heading mb-2">Risiko Osteoporosis Rendah</h6>
                                                    <p class="mb-2">Berdasarkan jawaban Anda, tidak ditemukan faktor
                                                        risiko osteoporosis yang signifikan. Tetap jaga kesehatan tulang
                                                        dengan:</p>
                                                    <ul class="mb-0">
                                                        <li>Konsumsi makanan kaya kalsium dan vitamin D</li>
                                                        <li>Lakukan olahraga teratur, terutama weight-bearing exercise</li>
                                                        <li>Hindari merokok dan konsumsi alkohol berlebihan</li>
                                                        <li>Lakukan pemeriksaan kesehatan rutin</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- User Answers Card --}}
                            @if ($jawabanUser->count() > 0)
                                <div class="card mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">
                                            <i class="fas fa-question-circle me-2"></i>
                                            Ringkasan Jawaban Anda
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table id="table-jawaban" class="table table-sm table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="12%">Kode</th>
                                                        <th width="58%">Pertanyaan</th>
                                                        <th width="15%">Jawaban</th>
                                                        <th width="15%">Kategori</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($jawabanUser as $jawaban)
                                                        <tr>
                                                            <td><code class="text-xs">{{ $jawaban['kode'] }}</code></td>
                                                            <td class="text-sm">{{ $jawaban['pertanyaan'] }}</td>
                                                            <td>
                                                                <span
                                                                    class="badge bg-{{ $jawaban['jawaban'] === 'ya' ? 'success' : 'secondary' }}">
                                                                    {{ ucfirst($jawaban['jawaban']) }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <small class="text-muted">
                                                                    {{ str_replace('_', ' ', ucwords($jawaban['kategori'] ?? 'Umum')) }}
                                                                </small>
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

                        {{-- Sidebar --}}
                        <div class="col-lg-4">
                            {{-- Action Buttons Card --}}
                            <div class="card mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-tools me-2"></i>Aksi
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="btn-group-vertical w-100">
                                        <a href="{{ route('konsultasi.export.pdf', $konsultasi->id) }}"
                                            class="btn btn-success btn-block mb-2">
                                            <i class="fas fa-download me-2"></i>
                                            Unduh PDF
                                        </a>
                                        <button type="button" class="btn btn-primary btn-block mb-2"
                                            onclick="window.print()">
                                            <i class="fas fa-print me-2"></i>
                                            Print Hasil
                                        </button>
                                        <a href="{{ route('konsultasi.create') }}"
                                            class="btn btn-outline-primary btn-block mb-2">
                                            <i class="fas fa-plus me-2"></i>
                                            Konsultasi Baru
                                        </a>
                                        <a href="{{ route('konsultasi.index') }}" class="btn btn-secondary btn-block">
                                            <i class="fas fa-list me-2"></i>
                                            Riwayat Konsultasi
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- Summary Statistics Card --}}
                            <div class="card mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-dark">
                                        <i class="fas fa-chart-bar me-2"></i>Ringkasan Statistik
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="border-right">
                                                <div class="h5 font-weight-bold text-primary">
                                                    {{ $hasilKonsultasi['sesi'] }}
                                                </div>
                                                <div class="text-xs text-muted">Sesi Selesai</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="border-right">
                                                <div class="h5 font-weight-bold text-info">
                                                    {{ count($hasilKonsultasi['fakta_tersedia']) }}
                                                </div>
                                                <div class="text-xs text-muted">Total Fakta</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="h5 font-weight-bold text-success">
                                                {{ count($detailSolusi) }}
                                            </div>
                                            <div class="text-xs text-muted">Rekomendasi</div>
                                        </div>
                                    </div>
                                    <hr class="my-3">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="border-right">
                                                <div class="h6 font-weight-bold text-warning">
                                                    {{ count($hasilKonsultasi['fakta_antara']) }}
                                                </div>
                                                <div class="text-xs text-muted">Fakta Antara</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="h6 font-weight-bold text-secondary">
                                                {{ count($traceInferensi) }}
                                            </div>
                                            <div class="text-xs text-muted">Aturan Aktif</div>
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="text-center">
                                        <small class="text-muted">
                                            Progress: {{ $displayProgress }}% •
                                            <span class="badge bg-success">Selesai</span>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            {{-- Consultation Info Card --}}
                            <div class="card mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-dark">
                                        <i class="fas fa-info-circle me-2"></i>Informasi Konsultasi
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="info-item mb-2">
                                        <small class="text-muted">ID Konsultasi:</small>
                                        <div class="font-weight-bold">#{{ $konsultasi->id }}</div>
                                    </div>
                                    <div class="info-item mb-2">
                                        <small class="text-muted">Tanggal Mulai:</small>
                                        <div class="font-weight-bold">{{ $konsultasi->created_at->format('d M Y, H:i') }}
                                        </div>
                                    </div>
                                    <div class="info-item mb-2">
                                        <small class="text-muted">Tanggal Selesai:</small>
                                        <div class="font-weight-bold">
                                            {{ $konsultasi->completed_at ? $konsultasi->completed_at->format('d M Y, H:i') : now()->format('d M Y, H:i') }}
                                        </div>
                                    </div>
                                    <div class="info-item mb-2">
                                        <small class="text-muted">Durasi:</small>
                                        <div class="font-weight-bold">
                                            @php
                                                $start = $konsultasi->created_at;
                                                $end = $konsultasi->completed_at ?? now();
                                                $duration = $start->diff($end);
                                            @endphp
                                            {{ $duration->h > 0 ? $duration->h . ' jam ' : '' }}{{ $duration->i }} menit
                                        </div>
                                    </div>
                                    <div class="info-item mb-0">
                                        <small class="text-muted">Status:</small>
                                        <div>
                                            <span class="badge bg-success">
                                                {{ ucfirst(str_replace('_', ' ', $konsultasi->status)) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Disclaimer Card --}}
                            <div class="card">
                                <div class="card-body">
                                    <div class="alert alert-danger alert-sm mb-0">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-info-circle text-white me-2 mt-1"></i>
                                            <div>
                                                <h6 class="alert-heading mb-1">Penting</h6>
                                                <p class="mb-0 text-xs">
                                                    Hasil konsultasi ini bersifat informatif dan tidak menggantikan
                                                    konsultasi medis profesional.
                                                    Konsultasikan dengan dokter untuk diagnosis dan penanganan yang tepat.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Inference Trace Card --}}
                            @if (!empty($traceInferensi))
                                <div class="card mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-info">
                                            <i class="fas fa-code-branch me-2"></i>
                                            Proses Inferensi Forward Chaining
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="timeline" style="max-height: 600px; overflow-y: auto;">
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
@endsection

@push('css-top')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <style>
        .result-status-icon {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .solution-item {
            transition: all 0.3s ease;
        }

        .solution-item:hover {
            background-color: #f8f9fa;
            border-radius: 0.25rem;
            padding: 1rem;
            margin: -1rem;
        }

        .solution-number .badge {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .solution-title {
            color: #28a745;
            font-weight: 600;
        }

        .solution-description {
            color: #6c757d;
            line-height: 1.5;
        }

        .timeline {
            position: relative;
            padding-left: 2rem;
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

        .border-right {
            border-right: 1px solid #e3e6f0 !important;
        }

        .info-item {
            border-bottom: 1px solid #f8f9fa;
            padding-bottom: 0.5rem;
        }

        .info-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        @media (max-width: 576px) {
            .border-right {
                border-right: none !important;
                border-bottom: 1px solid #e3e6f0 !important;
                padding-bottom: 1rem;
                margin-bottom: 1rem;
            }

            .solution-item:hover {
                margin: 0;
                padding: 1rem 0;
            }
        }

        @media print {

            .btn,
            .card-header,
            nav,
            .breadcrumb {
                display: none !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            .alert {
                border: 1px solid #ddd !important;
            }
        }
    </style>
@endpush

@push('scripts-bottom')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#table-jawaban').DataTable({
                autoWidth: false,
            });
        });
    </script>
    <script>
        // Auto-scroll to solutions if they exist
        document.addEventListener('DOMContentLoaded', function() {
            const solutions = document.querySelector('.solution-item');
            if (solutions && window.location.hash === '#solutions') {
                solutions.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });

        // Print functionality
        function printResult() {
            window.print();
        }

        // Smooth scroll for timeline items
        document.querySelectorAll('.timeline-item').forEach(function(item, index) {
            item.addEventListener('click', function() {
                this.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            });
        });
    </script>
@endpush
