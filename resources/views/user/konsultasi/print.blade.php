@extends('layouts.print')

@section('title', 'Laporan Hasil Konsultasi Osteoporosis - ' . $konsultasi->id)

@section('content')
    <div class="print-container">
        <!-- Header dengan Border -->
        <div class="print-header mb-4 text-center border-bottom pb-3">
            <div class="header-logo mb-3">
                <img src="{{ asset('storage/img/logo/logo-dashboard.png') }}" alt="Logo" width="120">
            </div>
            <h1 class="report-title mb-1">LAPORAN HASIL KONSULTASI</h1>
            <h2 class="report-subtitle mb-2">Sistem Pakar Penanganan Osteoporosis Berbasis Nutrisi</h2>

            <div class="report-meta d-flex justify-content-center gap-4">
                <div class="meta-item">
                    <span class="meta-label">ID Konsultasi</span>
                    <span class="meta-value">#{{ $konsultasi->id }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Tanggal</span>
                    <span class="meta-value">{{ $konsultasi->completed_at->format('d F Y H:i') }}</span>
                </div>
            </div>
        </div>

        <!-- Informasi Pasien dengan Card -->
        <div class="print-section mb-4">
            <div class="section-header bg-primary text-white p-2 rounded-top">
                <h4 class="section-title mb-0">
                    <i class="fas fa-user-circle me-2"></i>INFORMASI PASIEN
                </h4>
            </div>
            <div class="section-body border p-3 rounded-bottom">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <label class="info-label">Nama Lengkap</label>
                            <p class="info-value">{{ $konsultasi->user->nama_lengkap }}</p>
                        </div>
                        <div class="info-item mb-3">
                            <label class="info-label">Email</label>
                            <p class="info-value">{{ $konsultasi->user->email }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <label class="info-label">No. Telepon</label>
                            <p class="info-value">{{ $konsultasi->user->no_telepon ?? '-' }}</p>
                        </div>
                        <div class="info-item">
                            <label class="info-label">Alamat</label>
                            <p class="info-value">{{ $konsultasi->user->alamat ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Hasil Konsultasi -->
        <div class="print-section mb-4">
            <div
                class="result-status p-3 rounded 
                @if (!empty($detailSolusi)) bg-success text-white
                @else bg-info text-dark @endif">
                <div class="d-flex align-items-center">
                    <div class="status-icon me-3">
                        @if (!empty($detailSolusi))
                            <i class="fas fa-clipboard-check fa-2x"></i>
                        @else
                            <i class="fas fa-check-circle fa-2x"></i>
                        @endif
                    </div>
                    <div>
                        <h4 class="status-title mb-1">
                            @if (!empty($detailSolusi))
                                REKOMENDASI PENANGANAN OSTEOPOROSIS
                            @else
                                RISIKO OSTEOPOROSIS RENDAH
                            @endif
                        </h4>
                        <p class="status-message mb-0">
                            @if (!empty($detailSolusi))
                                Berdasarkan hasil konsultasi, ditemukan indikasi risiko osteoporosis yang memerlukan
                                penanganan khusus.
                            @else
                                Berdasarkan jawaban Anda, tidak ditemukan indikasi risiko osteoporosis yang signifikan.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rekomendasi (jika ada) -->
        @if (!empty($detailSolusi))
            <div class="mb-4">
                <div class="section-header bg-success text-white p-2 rounded-top">
                    <h4 class="section-title mb-0">
                        <i class="fas fa-lightbulb me-2"></i>REKOMENDASI PENANGANAN
                    </h4>
                </div>
                <div class="section-body border p-0 rounded-bottom">
                    @foreach ($detailSolusi as $index => $solusi)
                        <div class="solution-item p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="d-flex">
                                <div class="solution-number bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                    style="width: 30px; height: 30px; min-width: 30px;">
                                    {{ $index + 1 }}
                                </div>
                                <div>
                                    <h5 class="solution-title text-success mb-2">{{ $solusi['nama'] }}</h5>
                                    <p class="solution-description mb-2">{{ $solusi['deskripsi'] }}</p>
                                    @if (!empty($solusi['peringatan']))
                                        <div class="alert alert-warning p-2 mb-0">
                                            <div class="d-flex align-items-start">
                                                <i class="fas fa-exclamation-triangle mt-1 me-2"></i>
                                                <div>
                                                    <strong>PERHATIAN:</strong> {{ $solusi['peringatan'] }}
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
        @endif

        <!-- Rekomendasi Nutrisi (jika ada) -->
        @if (!empty($rekomendasiNutrisi) && count($rekomendasiNutrisi) > 0)
            <div class="mb-4">
                <div class="section-header bg-success text-white p-2 rounded-top">
                    <h4 class="section-title mb-0">
                        <i class="fas fa-utensils me-2"></i>REKOMENDASI NUTRISI
                    </h4>
                </div>
                <div class="section-body border p-3 rounded-bottom">
                    @foreach ($rekomendasiNutrisi as $nutrisi => $recommendations)
                        <div class="nutrisi-group mb-4">
                            <h5 class="nutrisi-title bg-light p-2 rounded">
                                <i class="fas fa-circle text-success me-2" style="font-size: 0.5rem;"></i>
                                {{ strtoupper($nutrisi) }}
                            </h5>

                            @foreach ($recommendations as $recommendation)
                                <div class="recommendation-item mb-3 p-3 border rounded">
                                    @if ($recommendation->kontraindikasi)
                                        <div class="alert alert-warning p-2 mb-3">
                                            <div class="d-flex align-items-start">
                                                <i class="fas fa-exclamation-triangle mt-1 me-2"></i>
                                                <div>
                                                    <strong>KONTRAINDIKASI:</strong> {{ $recommendation->kontraindikasi }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($recommendation->alternatif)
                                        <div class="alert alert-info p-2 mb-3">
                                            <div class="d-flex align-items-start">
                                                <i class="fas fa-info-circle mt-1 me-2"></i>
                                                <div>
                                                    <strong>ALTERNATIF:</strong> {{ $recommendation->alternatif }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <h6 class="recommendation-for text-success mb-3">
                                        <i class="fas fa-arrow-right me-2"></i>
                                        Untuk Solusi: {{ $recommendation->solusi->nama }}
                                    </h6>

                                    <div class="sumber-nutrisi">
                                        <h6 class="sumber-title mb-2">
                                            <i class="fas fa-list-ul me-2"></i>
                                            Sumber Nutrisi:
                                        </h6>
                                        <div class="row">
                                            @foreach ($recommendation->sumberNutrisi as $sumber)
                                                <div class="col-md-6 mb-3">
                                                    <div class="sumber-item border rounded p-3 h-100">
                                                        <div class="d-flex">
                                                            @if ($sumber->image)
                                                                <img src="{{ asset('storage/' . $sumber->image) }}"
                                                                    alt="{{ $sumber->nama_sumber }}" class="rounded me-3"
                                                                    width="60" height="60">
                                                            @endif
                                                            <div>
                                                                <h6 class="sumber-name mb-1">{{ $sumber->nama_sumber }}
                                                                </h6>
                                                                <div class="sumber-meta text-muted">
                                                                    <div class="meta-item">
                                                                        <i class="fas fa-tag me-1"></i>
                                                                        <small>Jenis:
                                                                            {{ ucfirst($sumber->jenis_sumber) }}</small>
                                                                    </div>
                                                                    <div class="meta-item">
                                                                        <i class="fas fa-weight me-1"></i>
                                                                        <small>Takaran: {{ $sumber->takaran }}</small>
                                                                    </div>
                                                                    @if ($sumber->catatan)
                                                                        <div class="meta-item">
                                                                            <i class="fas fa-sticky-note me-1"></i>
                                                                            <small>Catatan: {{ $sumber->catatan }}</small>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Saran Umum -->
        <div class="print-section mb-4">
            <div class="section-header bg-primary text-white p-2 rounded-top">
                <h4 class="section-title mb-0">
                    <i class="fas fa-heart me-2"></i>SARAN UMUM UNTUK KESEHATAN TULANG
                </h4>
            </div>
            <div class="section-body border p-3 rounded-bottom">
                <div class="row">
                    <div class="col-md-6">
                        <div class="saran-item d-flex mb-3">
                            <div class="saran-icon text-primary me-3">
                                <i class="fas fa-utensils fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="saran-title">Nutrisi Seimbang</h6>
                                <p class="saran-desc mb-0">Konsumsi makanan kaya kalsium, vitamin D, dan protein untuk
                                    kesehatan tulang.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="saran-item d-flex mb-3">
                            <div class="saran-icon text-primary me-3">
                                <i class="fas fa-running fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="saran-title">Aktivitas Fisik</h6>
                                <p class="saran-desc mb-0">Lakukan olahraga teratur seperti berjalan, jogging, atau angkat
                                    beban ringan.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="saran-item d-flex mb-3">
                            <div class="saran-icon text-primary me-3">
                                <i class="fas fa-smoking-ban fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="saran-title">Hindari Kebiasaan Buruk</h6>
                                <p class="saran-desc mb-0">Jauhi rokok dan batasi konsumsi alkohol untuk menjaga kepadatan
                                    tulang.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="saran-item d-flex">
                            <div class="saran-icon text-primary me-3">
                                <i class="fas fa-user-md fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="saran-title">Pemeriksaan Rutin</h6>
                                <p class="saran-desc mb-0">Lakukan pemeriksaan kepadatan tulang secara berkala sesuai
                                    anjuran dokter.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Disclaimer dan Footer -->
        <div class="print-footer mt-4">
            <div class="disclaimer alert alert-warning p-3 mb-4">
                <div class="d-flex">
                    <div class="disclaimer-icon me-3">
                        <i class="fas fa-exclamation-triangle fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="disclaimer-title mb-2">PENTING</h5>
                        <p class="disclaimer-text mb-0">
                            Hasil konsultasi ini bersifat informatif dan tidak menggantikan konsultasi medis profesional.
                            Disarankan untuk berkonsultasi dengan dokter atau ahli gizi untuk diagnosis dan penanganan yang
                            tepat.
                        </p>
                    </div>
                </div>
            </div>

            <div class="footer-meta text-center text-muted pt-3 border-top">
                <p class="mb-1">
                    <strong>Sistem Pakar Penanganan Osteoporosis Berbasis Nutrisi</strong>
                </p>
                <p class="mb-0">
                    Dicetak pada {{ now()->format('d F Y H:i') }} • Dokumen ini sah tanpa tanda tangan
                </p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Print-specific styles */
        @page {
            size: A4;
            margin: 15mm;
        }

        body {
            font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #333;
            background: none;
            padding: 0;
            margin: 0;
        }

        .print-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header Styles */
        .print-header {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #2c7be5;
        }

        .report-title {
            font-size: 20pt;
            font-weight: 700;
            color: #2c7be5;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .report-subtitle {
            font-size: 14pt;
            font-weight: 500;
            color: #6c757d;
            margin-bottom: 15px;
        }

        .report-meta {
            font-size: 10pt;
        }

        .meta-item {
            display: flex;
            flex-direction: column;
        }

        .meta-label {
            font-weight: 600;
            color: #6c757d;
        }

        .meta-value {
            font-weight: 700;
            color: #2c7be5;
        }

        /* Section Styles */
        .print-section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .section-header {
            font-size: 12pt;
            font-weight: 600;
        }

        .section-title {
            font-size: 12pt;
            font-weight: 600;
            margin: 0;
        }

        .section-body {
            background-color: #fff;
        }

        /* Info Item Styles */
        .info-item {
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 10pt;
            margin-bottom: 2px;
        }

        .info-value {
            font-weight: 500;
            margin: 0;
            padding-left: 10px;
        }

        /* Result Status */
        .result-status {
            border-radius: 5px;
            padding: 15px;
        }

        .status-icon {
            font-size: 24px;
        }

        .status-title {
            font-size: 14pt;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .status-message {
            font-size: 11pt;
        }

        /* Solution Styles */
        .solution-item {
            padding: 15px;
        }

        .solution-number {
            font-weight: 700;
            font-size: 12pt;
        }

        .solution-title {
            font-size: 12pt;
            font-weight: 700;
            color: #2c7be5;
        }

        .solution-description {
            font-size: 11pt;
            color: #495057;
        }

        /* Nutrisi Styles */
        .nutrisi-title {
            font-size: 11pt;
            font-weight: 700;
            color: #495057;
        }

        .recommendation-item {
            background-color: #f8f9fa;
        }

        .recommendation-for {
            font-size: 11pt;
            font-weight: 600;
        }

        .sumber-nutrisi {
            margin-top: 15px;
        }

        .sumber-title {
            font-size: 11pt;
            font-weight: 600;
            color: #6c757d;
        }

        .sumber-item {
            background-color: #fff;
            transition: all 0.2s;
        }

        .sumber-item:hover {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .sumber-name {
            font-size: 11pt;
            font-weight: 600;
            color: #2c7be5;
        }

        .sumber-meta {
            font-size: 10pt;
        }

        .meta-item {
            margin-bottom: 3px;
        }

        /* Saran Styles */
        .saran-item {
            padding: 5px 0;
        }

        .saran-icon {
            font-size: 16px;
        }

        .saran-title {
            font-size: 11pt;
            font-weight: 600;
            color: #2c7be5;
            margin-bottom: 3px;
        }

        .saran-desc {
            font-size: 10pt;
            color: #6c757d;
            margin: 0;
        }

        /* Disclaimer Styles */
        .disclaimer {
            border-left: 4px solid #ffc107;
        }

        .disclaimer-icon {
            color: #ffc107;
        }

        .disclaimer-title {
            font-size: 12pt;
            font-weight: 700;
            color: #856404;
            margin-bottom: 5px;
        }

        .disclaimer-text {
            font-size: 10pt;
            color: #856404;
            margin: 0;
        }

        /* Footer Styles */
        .footer-meta {
            font-size: 9pt;
        }

        /* Alert Styles */
        .alert {
            border-radius: 5px;
            font-size: 10pt;
            margin-bottom: 15px;
        }

        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffeeba;
            color: #856404;
        }

        .alert-info {
            background-color: #e7f5fe;
            border-color: #b8e2fb;
            color: #0c5460;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #f8f9fa !important;
            font-weight: 600;
            font-size: 10pt;
            text-align: left;
        }

        td {
            font-size: 10pt;
        }

        /* Utility Classes */
        .rounded {
            border-radius: 5px !important;
        }

        .border {
            border: 1px solid #e9ecef !important;
        }

        /* Print Specific Adjustments */
        @media print {
            body {
                font-size: 10pt;
            }

            .print-container {
                padding: 10px;
            }

            .print-header {
                margin-bottom: 15px;
                padding-bottom: 10px;
            }

            .report-title {
                font-size: 16pt;
            }

            .report-subtitle {
                font-size: 12pt;
            }

            .no-print {
                display: none !important;
            }

            .page-break {
                page-break-before: always;
            }

            .solution-item,
            {
            page-break-inside: avoid;
        }

        .print-footer {
            margin-top: 20px;
            padding-top: 10px;
        }
        }
    </style>
@endpush
