@extends('layouts.admin')

@section('title', 'Edit Fakta | SPPOBN')
@section('title-menu', 'Edit Fakta')
@section('subtitle-menu', 'Form Edit Fakta')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.basisPengetahuan.fakta.index') }}">Fakta</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="bi bi-pencil-square"></i> Edit Fakta</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.basisPengetahuan.fakta.update', $fakta->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- Kode Fakta --}}
                            <div class="mb-3">
                                <label for="kode" class="form-label">Kode Fakta</label>
                                <input type="text" name="kode" id="kode"
                                    class="form-control @error('kode') is-invalid @enderror"
                                    value="{{ old('kode', $fakta->kode) }}" required>
                                @error('kode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small>Format Penulisan Kode :
                                    <ul>
                                        <li>F[nomor] : Fakta</li>
                                        <li>FA[nomor] : Fakta Antara</li>
                                    </ul>
                                    Kode Fakta harus sesuai jika tidak, akan error !
                                </small>
                            </div>

                            {{-- Pertanyaan --}}
                            <div class="mb-3">
                                <label for="pertanyaan" class="form-label">Pertanyaan</label>
                                <textarea name="pertanyaan" id="pertanyaan" class="form-control @error('pertanyaan') is-invalid @enderror"
                                    rows="2">{{ old('pertanyaan', $fakta->pertanyaan) }}</textarea>
                                @error('pertanyaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Deskripsi --}}
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea name="deskripsi" id="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="2"
                                    required>{{ old('deskripsi', $fakta->deskripsi) }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Kategori --}}
                            <div class="mb-3">
                                <label for="kategori" class="form-label">Kategori</label>
                                <select name="kategori" id="kategori"
                                    class="form-select @error('kategori') is-invalid @enderror" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="skrining_awal"
                                        {{ old('kategori', $fakta->kategori) == 'skrining_awal' ? 'selected' : '' }}>Sesi 1
                                        -
                                        Skrining Awal
                                    </option>
                                    <option value="risiko_fraktur"
                                        {{ old('kategori', $fakta->kategori) == 'risiko_fraktur' ? 'selected' : '' }}>Sesi
                                        2 = Klasifikasi
                                        Risiko
                                        Fraktur
                                    </option>
                                    <option value="asupan_nutrisi"
                                        {{ old('kategori', $fakta->kategori) == 'asupan_nutrisi' ? 'selected' : '' }}>Sesi
                                        3 - Asupan Nutrisi
                                    </option>
                                    <option value="preferensi_makanan"
                                        {{ old('kategori', $fakta->kategori) == 'preferensi_makanan' ? 'selected' : '' }}>
                                        Sesi 4 - Preferensi
                                        Makanan
                                    </option>
                                </select>
                                @error('kategori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Is Askable --}}
                            <div class="mb-3 form-check">
                                <input class="form-check-input" type="checkbox" name="is_askable" id="is_askable"
                                    value="1" {{ old('is_askable', $fakta->is_askable) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_askable">Dapat ditanyakan (pertanyaan)</label>
                                <small class="form-text text-muted">Jika tidak dicentang, fakta ini adalah fakta antara dan
                                    tidak akan menjadi
                                    pertanyaan.</small>
                            </div>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.basisPengetahuan.fakta.index') }}" class="btn btn-secondary me-2">
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
