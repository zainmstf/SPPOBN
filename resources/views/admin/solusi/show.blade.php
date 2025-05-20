@extends('layouts.admin')

@section('title', 'Detail Solusi | SPPOBN')
@section('title-menu', 'Detail Solusi')
@section('subtitle-menu', 'Lihat Detail Solusi')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.basisPengetahuan.solusi.index') }}">Daftar Solusi</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail Solusi</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="bi-info-circle-fill"></i> Detail Solusi</h4>
                    </div>
                    <div class="card-body">
                        <x-alert />

                        <div class="form-group mb-3">
                            <h5>Kode Solusi</h5>
                            <p>{{ $solusi->kode }}</p>
                        </div>
                        <div class="form-group mb-3">
                            <h5>Nama Solusi</h5>
                            <p>{{ $solusi->nama }}</p>
                        </div>
                        <div class="form-group mb-3">
                            <h5>Peringatan Konsultasi</h5>
                            <p>{{ $solusi->peringatan_konsultasi ?? 'Tidak ada' }}</p>
                        </div>
                        <div class="form-group mb-3">
                            <h5>Deskripsi</h5>
                            <p>{!! $solusi->deskripsi !!}</p>
                        </div>
                        <div class="form-group mb-3">
                            <h5>Status Default</h5>
                            <p>{{ $solusi->is_default ? 'Ya' : 'Tidak' }}</p>
                        </div>

                        @if ($solusi->rekomendasiNutrisi->isNotEmpty())
                            <div class="form-group mb-3">
                                <h5>Rekomendasi Nutrisi</h5>
                                <ul>
                                    @foreach ($solusi->rekomendasiNutrisi as $rekomendasi)
                                        <li>{{ $rekomendasi->nutrisi }}
                                            @if ($rekomendasi->sumberNutrisi->isNotEmpty())
                                                Sumber:
                                                @php
                                                    $acakSumberNutrisi = $rekomendasi->sumberNutrisi->shuffle();
                                                @endphp

                                                @foreach ($acakSumberNutrisi->take(3) as $sumber)
                                                    {{ $sumber->nama_sumber }}{{ $loop->last ? '' : ', ' }}
                                                @endforeach

                                                @if ($rekomendasi->sumberNutrisi->count() > 3)
                                                    dan lainnya...
                                                @endif
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                                <small style="font-size: 12px; color: #999;">
                                    <p><span class="text-danger">*</span>
                                        Untuk daftar sumber nutrisi lebih lengkap bisa di lihat di menu
                                        <a href="{{ route('edukasi.daftarMakanan') }}">daftar makanan</a>
                                    </p>
                                </small>
                            </div>
                        @else
                            <div class="form-group mb-3">
                                <p>Tidak ada rekomendasi nutrisi untuk solusi ini.</p>
                            </div>
                        @endif

                        <a href="{{ route('admin.basisPengetahuan.solusi.index') }}" class="btn btn-secondary">Kembali ke
                            Daftar Solusi</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
