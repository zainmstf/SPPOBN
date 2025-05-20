@extends('layouts.admin')

@section('title', 'Edit Solusi | SPPOBN')
@section('title-menu', 'Edit Solusi')
@section('subtitle-menu', 'Ubah Data Solusi')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.basisPengetahuan.solusi.index') }}">Daftar Solusi</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Solusi</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="bi-pencil-fill"></i> Edit Solusi</h4>
                    </div>
                    <div class="card-body">
                        <x-alert />
                        <form action="{{ route('admin.basisPengetahuan.solusi.update', $solusi->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group mb-3">
                                <label for="kode">Kode Solusi</label>
                                <input type="text" id="kode" name="kode"
                                    class="form-control @error('kode') is-invalid @enderror"
                                    value="{{ old('kode', $solusi->kode) }}" required>
                                @error('kode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="nama">Nama Solusi</label>
                                <input type="text" id="nama" name="nama"
                                    class="form-control @error('nama') is-invalid @enderror"
                                    value="{{ old('nama', $solusi->nama) }}" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <div id="deskripsi" style="height: 400px;">
                                    {!! old('deskripsi', $solusi->deskripsi) !!}
                                </div>
                                <input type="hidden" name="deskripsi" id="deskripsi-content-create"
                                    value="{{ old('deskripsi', $solusi->deskripsi) }}">
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="peringatan_konsultasi" class="form-label">Peringatan Konsultasi</label>
                                <textarea name="peringatan_konsultasi" id="peringatan_konsultasi"
                                    class="form-control @error('peringatan_konsultasi') is-invalid @enderror" rows="3">{{ old('peringatan_konsultasi', $solusi->peringatan_konsultasi) }}</textarea>
                                @error('peringatan_konsultasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Is Default --}}
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" name="is_default" id="is_default"
                                    value="1" {{ old('is_default', $solusi->is_default) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_default">Jadikan sebagai solusi default</label>
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.basisPengetahuan.solusi.index') }}"
                                    class="btn btn-secondary me-2">
                                    <i class="bi-arrow-left-circle"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi-save"></i> Perbarui
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
