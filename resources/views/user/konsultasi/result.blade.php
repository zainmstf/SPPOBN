@extends('layouts.user')
@section('title', 'Hasil Konsultasi | SPPOBN')
@section('title-menu', 'Hasil Konsultasi Terakhir')
@section('subtitle-menu', 'Konsultasi Terakhir Menghasilkan Rekomendasi Di Bawah')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Hasil Konsultasi Terakhir</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="fas fa-check-circle"></i> Hasil Konsultasi Anda</h4>
                    </div>
                    <div class="card-body">
                        <x-alert />
                        @if ($solusiAkhir)
                            <div class="card mb-4 border-left-success shadow-sm">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5 class="mb-1">Rekomendasi Konsultasi#{{ $konsultasi->id }}</h5>
                                            <small
                                                class="text-muted">{{ $inferensiSolusi->aturan->deskripsi ?? 'Deskripsi rekomendasi' }}</small>
                                        </div>
                                        <div class="badge">
                                            <span class="badge bg-primary text-white">{{ $solusiAkhir->kode }}</span>
                                        </div>
                                    </div>
                                </div>
                                @if ($solusiAkhir->peringatan_konsultasi)
                                    <div class="alert alert-danger alert-dismissible nodismissable" role="alert">
                                        <strong>Peringatan:</strong> {{ $solusiAkhir->peringatan_konsultasi }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif
                                <div class="card-body pt-2">
                                    <p class="card-text">
                                        {!! $solusiAkhir->deskripsi !!}
                                        @if ($solusiAkhir->rekomendasiNutrisi->isNotEmpty())
                                            <p class="mt-3 fw-bold">Sumber Nutrisi Yang Bisa Didapat</p>
                                            <ul>
                                                @foreach ($solusiAkhir->rekomendasiNutrisi as $rekomendasiNutrisi)
                                                    <li>{{ $rekomendasiNutrisi->nutrisi }}
                                                        @if ($rekomendasiNutrisi->sumberNutrisi->isNotEmpty())
                                                            Sumber:
                                                            @php
                                                                $acakSumberNutrisi = $rekomendasiNutrisi->sumberNutrisi->shuffle();
                                                            @endphp

                                                            @foreach ($acakSumberNutrisi->take(3) as $sumber)
                                                                {{ $sumber->nama_sumber }}{{ $loop->last ? '' : ', ' }}
                                                            @endforeach

                                                            @if ($rekomendasiNutrisi->sumberNutrisi->count() > 3)
                                                                dan lainnya...
                                                            @endif
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <small style="margin-top: 60px; font-size: 12px; color: #999;">
                                                <p><span class="text-danger">*</span>
                                                    Untuk daftar sumber nutrisi lebih lengkap bisa di lihat di menu
                                                    <a href="{{ route('edukasi.daftarMakanan') }}">daftar makanan</a>
                                                </p>
                                            </small>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="hasil_konsultasi">
                                @php
                                    $hasil = explode("\n\n", $konsultasi->hasil_konsultasi);
                                @endphp

                                @foreach ($hasil as $bagian)
                                    @php
                                        $baris = explode("\n", $bagian);
                                    @endphp
                                    @if (count($baris) > 0)
                                        <div class="mb-3">
                                            <strong>{{ array_shift($baris) }}</strong>
                                            <ul class="mb-0">
                                                @foreach ($baris as $item)
                                                    @if (trim($item) !== '')
                                                        <li>{{ $item }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                        <div class="mt-4 text-center">
                            <a href="{{ route('dashboard') }}" class="btn btn-primary"><i
                                    class="fas fa-arrow-left me-2"></i>
                                Kembali ke Beranda</a>
                            <a href="{{ route('konsultasi.start') }}" class="btn btn-success"><i
                                    class="fas fa-redo me-2"></i> Konsultasi Lagi</a>
                            <a href="{{ route('riwayat.show', $konsultasi->id) }}" class="btn btn-info"><i
                                    class="fas fa-eye me-2"></i> Lihat Detail</a>
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                data-bs-target="#umpanBalikModal" data-konsultasi-id="{{ $konsultasi->id }}">
                                <i class="fas fa-star me-2"></i> Beri Umpan Balik
                            </button>
                            <a href="{{ route('konsultasi.print', $konsultasi->id) }}" class="btn btn-secondary"
                                target="_blank"><i class="fas fa-print me-2"></i> Cetak</a>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-modal-umpan-balik :konsultasi="$konsultasi" />
@endsection

@push('scripts-bottom')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('storage/js/pages/hasil-konsultasi.js') }}" type="module"></script>
@endpush

@push('css-top')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
@endpush
