@extends('layouts.admin')

@section('title', 'Detail Umpan Balik')
@section('title-menu', 'Detail Umpan Balik Pengguna')
@section('subtitle-menu', 'Menampilkan Detail Umpan Balik')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.feedback.index') }}">Umpan Balik</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail</li>
@endsection

@section('content')
    <x-page-header />

    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="bi bi-chat-square-text"></i> Detail Umpan Balik</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="form-label">Pengguna:</h6>
                            <p id="pengguna" class="form-control-plaintext">
                                {{ $feedback->user->nama_lengkap ?? 'Anonim' }}
                            </p>
                        </div>

                        <div class="mb-3">
                            <h6 class="form-label">Konsultasi:</h6>
                            <p id="konsultasi" class="form-control-plaintext">
                                {{ $feedback->konsultasi_id ? '#' . $feedback->konsultasi_id : 'Bukan Dari Konsultasi' }}
                            </p>
                        </div>

                        <div class="mb-3">
                            <h6 class="form-label">Komentar:</h6>
                            <p id="komentar" class="form-control-plaintext">
                                {{ $feedback->pesan }}
                            </p>
                        </div>

                        <div class="mb-3">
                            <h6 class="form-label">Rating:</h6>
                            <p id="rating" class="form-control-plaintext">
                                {{ $feedback->rating }} / 5
                            </p>
                        </div>

                        <div class="mb-3">
                            <h6 class="form-label">Tanggal Dibuat:</h6>
                            <p id="tanggal" class="form-control-plaintext">
                                {{ \Carbon\Carbon::parse($feedback->created_at)->locale('id')->isoFormat('DD MMMM YYYY HH:mm') }}
                            </p>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('admin.feedback.index') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Umpan Balik
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
