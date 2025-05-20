@extends('layouts.admin')

@section('title', 'Tambah Sumber Nutrisi')
@section('title-menu', 'Tambah Sumber Nutrisi')
@section('subtitle-menu', 'Menambah Data Sumber Nutrisi Baru')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.sumber-nutrisi.index') }}">Sumber Nutrisi</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="bi-plus-circle"></i> Tambah Sumber Nutrisi</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.sumber-nutrisi.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="nutrisi" class="form-label">Nutrisi</label>
                                <select class="form-control @error('nutrisi') is-invalid @enderror" id="nutrisi"
                                    name="nutrisi" required>
                                    <option value="">Pilih Nutrisi</option>
                                    <option value="Kalsium" {{ old('nutrisi') == 'Kalsium' ? 'selected' : '' }}>Kalsium
                                    </option>
                                    <option value="Vitamin D" {{ old('nutrisi') == 'Vitamin D' ? 'selected' : '' }}>Vitamin
                                        D</option>
                                    <option value="Protein" {{ old('nutrisi') == 'Protein' ? 'selected' : '' }}>Protein
                                    </option>
                                    <option value="Magnesium" {{ old('nutrisi') == 'Magnesium' ? 'selected' : '' }}>
                                        Magnesium</option>
                                    <option value="Vitamin K" {{ old('nutrisi') == 'Vitamin K' ? 'selected' : '' }}>Vitamin
                                        K</option>
                                </select>
                                @error('nutrisi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="jenis_sumber" class="form-label">Jenis Sumber</label>
                                <select class="form-control @error('jenis_sumber') is-invalid @enderror" id="jenis_sumber"
                                    name="jenis_sumber" required>
                                    <option value="">Pilih Jenis Sumber</option>
                                    <option value="suplemen" {{ old('jenis_sumber') == 'suplemen' ? 'selected' : '' }}>
                                        Suplemen</option>
                                    <option value="makanan" {{ old('jenis_sumber') == 'makanan' ? 'selected' : '' }}>
                                        Makanan</option>
                                </select>
                                @error('jenis_sumber')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="nama_sumber" class="form-label">Nama Sumber</label>
                                <input type="text" class="form-control @error('nama_sumber') is-invalid @enderror"
                                    id="nama_sumber" name="nama_sumber" value="{{ old('nama_sumber') }}" required>
                                @error('nama_sumber')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Gambar Sumber Nutrisi</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror"
                                    id="image" name="image">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Format yang diperbolehkan: jpg, png, jpeg.</small>
                            </div>
                            <div class="mb-3">
                                <label for="takaran" class="form-label">Takaran</label>
                                <input type="text" class="form-control @error('takaran') is-invalid @enderror"
                                    id="takaran" name="takaran" value="{{ old('takaran') }}">
                                @error('takaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="catatan" class="form-label">Catatan</label>
                                <textarea class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" rows="3">{{ old('catatan') }}</textarea>
                                @error('catatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.sumber-nutrisi.index') }}" class="btn btn-secondary me-2">
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
@endpush

@push('css-top')
@endpush
