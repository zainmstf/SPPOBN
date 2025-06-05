@extends('layouts.admin')

@section('title', 'Tambah Solusi | SPPOBN')
@section('title-menu', 'Tambah Solusi')
@section('subtitle-menu', 'Formulir Tambah Solusi')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.basisPengetahuan.solusi.index') }}">Daftar Solusi</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah Solusi</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="bi-check-circle-fill"></i> Tambah Solusi Baru</h4>
                    </div>
                    <div class="card-body">
                        <x-alert />
                        <form action="{{ route('admin.basisPengetahuan.solusi.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="kode" class="form-label">Kode Solusi</label>
                                <input type="text" name="kode" id="kode"
                                    class="form-control @error('kode') is-invalid @enderror" value="{{ old('kode') }}"
                                    required>
                                @error('kode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small>Format Penulisan Kode :
                                    <ul>
                                        <li>S[nomor] : Solusi</li>

                                    </ul>
                                    Kode Solusi harus sesuai jika tidak, akan error !
                                </small>
                            </div>

                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Solusi</label>
                                <input type="text" name="nama" id="nama"
                                    class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}"
                                    required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <div id="deskripsi" style="height: 400px;"></div>
                                <input type="hidden" name="deskripsi" id="deskripsi-content-create"
                                    value="{{ old('deskripsi') }}">
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="peringatan_konsultasi" class="form-label">Peringatan Konsultasi</label>
                                <textarea name="peringatan_konsultasi" id="peringatan_konsultasi"
                                    class="form-control @error('peringatan_konsultasi') is-invalid @enderror" rows="3">{{ old('peringatan_konsultasi') }}</textarea>
                                @error('peringatan_konsultasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.basisPengetahuan.solusi.index') }}"
                                    class="btn btn-secondary me-2">
                                    <i class="bi-arrow-left-circle"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi-save"></i> Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts-bottom')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script>
        const descriptionInputCreate = document.getElementById('deskripsi-content-create');
        var quillCreate = new Quill('#deskripsi', {
            theme: 'snow'
        });
        descriptionInputCreate.value = quillCreate.root.innerHTML;
        quillCreate.on('text-change', function() {
            descriptionInputCreate.value = quillCreate.root.innerHTML;
        });
    </script>
@endpush

@push('css-top')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
@endpush
