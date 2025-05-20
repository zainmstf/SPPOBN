@extends('layouts.admin')
@section('title', 'Detail Aturan | SPPOBN')
@section('title-menu', 'Detail Aturan')
@section('subtitle-menu', 'Informasi lengkap mengenai aturan')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.basisPengetahuan.aturan.index') }}">Aturan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="fas fa-info-circle"></i> Detail Aturan</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 row">
                            <label for="kodeAturan" class="col-sm-2 col-form-label">Kode Aturan</label>
                            <div class="col-sm-10 d-flex align-items-center">
                                <span class="d-inline-block">:</span>
                                <input type="text" readonly class="form-control ms-2" id="kodeAturan"
                                    value="{{ $aturan->kodeAturan }}">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="deskripsi" class="col-sm-2 col-form-label">Deskripsi</label>
                            <div class="col-sm-10 d-flex align-items-center">
                                <span class="d-inline-block">:</span>
                                <textarea readonly class="form-control ms-2" id="deskripsi">{{ $aturan->deskripsi ?? '-' }}</textarea>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="rekomendasi" class="col-sm-2 col-form-label">Rekomendasi</label>
                            <div class="col-sm-10 d-flex align-items-center">
                                <span class="d-inline-block">:</span>
                                <input type="text" readonly class="form-control ms-2" id="rekomendasi"
                                    value="{{ $aturan->rekomendasi->kodeRekomendasi ?? 'N/A' }} - {{ $aturan->rekomendasi->teksRekomendasi ?? 'N/A' }}">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="jenisAturan" class="col-sm-2 col-form-label">Jenis Aturan</label>
                            <div class="col-sm-10 d-flex align-items-center">
                                <span class="d-inline-block">:</span>
                                <input type="text" class="form-control ms-2" id="jenisAturan"
                                    value="{{ ucwords(str_replace('_', ' ', $aturan->jenisAturan)) ?? '-' }}" readonly>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label for="prioritas" class="col-sm-2 col-form-label ">Prioritas <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="d-flex align-items-center">
                                    <span class="d-inline-block">:</span>
                                    <input type="number" class="form-control ms-2 @error('prioritas') is-invalid @enderror"
                                        id="prioritas" name="prioritas" value="{{ old('prioritas', $aturan->prioritas) }}"
                                        required min="1" readonly>
                                </div>
                                @error('prioritas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted ms-2">Pengaturan ulang urutan prioritas dapat dilakukan
                                    di halaman Daftar Aturan.</small>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label for="isActive" class="col-sm-2 col-form-label ">Aktif</label>
                            <div class="col-sm-10 d-flex align-items-center">
                                <span class="d-inline-block">:</span>
                                <div class="form-check form-switch ms-2">
                                    <input class="form-check-input" type="checkbox" id="isActive" name="isActive"
                                        {{ old('isActive', $aturan->isActive) ? 'checked' : '' }} disabled>
                                    <label class="form-check-label"
                                        for="isActive">{{ $aturan->isActive ? 'Aktif' : 'Tidak Aktif' }}</label>
                                </div>
                                @error('isActive')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="createdBy" class="col-sm-2 col-form-label">Dibuat Oleh</label>
                            <div class="col-sm-10 d-flex align-items-center">
                                <span class="d-inline-block">:</span>
                                <input type="text" readonly class="form-control ms-2" id="createdBy"
                                    value="{{ $aturan->user->username ?? 'N/A' }} ({{ $aturan->user->nama ?? 'N/A' }})">
                            </div>
                        </div>

                        @if ($aturan->aturanKondisi->isNotEmpty())
                            <div class="mt-4">
                                <h5>Kondisi Aturan:</h5>
                                <ul class="list-group">
                                    @foreach ($aturan->aturanKondisi as $index => $kondisi)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                {{ $index + 1 }}. Jika
                                                <span
                                                    class="font-weight-bold">{{ $kondisi->pertanyaan->kodePertanyaan }}</span>
                                                -
                                                {{ $kondisi->pertanyaan->teksPertanyaan }}
                                                @if ($kondisi->pertanyaan->kategori || $kondisi->pertanyaan->subKategori)
                                                    <div class="mt-1 text-muted">
                                                        Kategori: <span
                                                            class="font-italic">{{ ucwords(str_replace('_', ' ', $kondisi->pertanyaan->kategori)) ?? '-' }}</span>
                                                        @if ($kondisi->pertanyaan->subKategori)
                                                            <br>Sub Kategori: <span
                                                                class="font-italic">{{ ucwords(str_replace('_', ' ', $kondisi->pertanyaan->subKategori)) ?? '-' }}</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                            <span
                                                class="badge rounded-pill {{ $kondisi->expectedAnswer ? 'bg-success' : 'bg-danger' }}">
                                                {{ $kondisi->expectedAnswer ? 'Ya' : 'Tidak' }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <div class="mt-4">
                                <h5>Kondisi Aturan:</h5>
                                <p class="text-muted">Tidak ada kondisi yang terkait dengan aturan ini.</p>
                            </div>
                        @endif

                        <div class="mt-4 d-flex justify-content-end">
                            <a href="{{ route('admin.basisPengetahuan.aturan.edit', $aturan->aturanID) }}"
                                class="btn btn-warning">
                                <i class="bi bi-pencil align-middle mr-1"></i> Edit
                            </a>
                            <a href="{{ route('admin.basisPengetahuan.aturan.index') }}" class="btn btn-secondary ms-2">
                                <i class="bi bi-arrow-left align-middle mr-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
