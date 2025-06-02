@extends('layouts.user')

@section('title', 'Mulai Konsultasi | SPPOBN')
@section('title-menu', 'Mulai Konsultasi Baru')
@section('subtitle-menu', 'Panduan Konsultasi Osteoporosis')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('konsultasi.index') }}">Riwayat Konsultasi</a></li>
    <li class="breadcrumb-item active" aria-current="page">Mulai Baru</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6"> {{-- Adjusted column size for better centering --}}
                <div class="card">
                    <div class="card-header text-center">
                        <div class="d-flex justify-content-center mb-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width: 80px; height: 80px;">
                                <i class="bi bi-question-lg text-primary" style="font-size: 3rem;"></i>
                            </div>
                        </div>
                        <h4 class="mb-0 text-gray-800">Mulai Konsultasi Baru</h4>
                        <p class="text-muted">Dapatkan rekomendasi nutrisi untuk pencegahan osteoporosis.</p>
                    </div>
                    <div class="card-body text-center">
                        <x-alert />
                        <p class="mb-4 text-lg text-gray-700">
                            Konsultasi ini akan memandu Anda melalui serangkaian pertanyaan untuk mengidentifikasi faktor
                            risiko dan kebutuhan nutrisi Anda. Jawablah dengan jujur untuk hasil yang akurat.
                        </p>

                        <form action="{{ route('konsultasi.store') }}" method="POST" class="mt-4">
                            @csrf
                            <button type="submit"
                                class="btn btn-success btn-lg w-100 d-flex align-items-center justify-content-center">
                                <i class="bi bi-play-circle me-2"></i> Mulai Sekarang
                            </button>
                        </form>

                        <div class="mt-4">
                            <a href="{{ route('konsultasi.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i> Kembali ke Riwayat Konsultasi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts-bottom')
@endpush

@push('css-top')
@endpush
