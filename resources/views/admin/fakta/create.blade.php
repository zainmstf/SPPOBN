@extends('layouts.admin')

@section('title', 'Tambah Fakta | SPPOBN')
@section('title-menu', 'Tambah Fakta')
@section('subtitle-menu', 'Form Tambah Fakta')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.basisPengetahuan.fakta.index') }}">Fakta</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="bi bi-plus-circle-fill"></i> Tambah Fakta</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.basisPengetahuan.fakta.store') }}" method="POST">
                            @csrf

                            {{-- Kode Fakta --}}
                            <div class="mb-3">
                                <label for="kode" class="form-label">Kode Fakta</label>
                                <input type="text" name="kode" id="kode"
                                    class="form-control @error('kode') is-invalid @enderror" value="{{ old('kode') }}"
                                    required>
                                @error('kode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Pertanyaan --}}
                            <div class="mb-3">
                                <label for="pertanyaan" class="form-label">Pertanyaan</label>
                                <textarea name="pertanyaan" id="pertanyaan" class="form-control @error('pertanyaan') is-invalid @enderror"
                                    rows="2">{{ old('pertanyaan') }}</textarea>
                                @error('pertanyaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Deskripsi --}}
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea name="deskripsi" id="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="2"
                                    required>{{ old('deskripsi') }}</textarea>
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
                                    <option value="risiko_osteoporosis"
                                        {{ old('kategori') == 'risiko_osteoporosis' ? 'selected' : '' }}>Risiko Osteoporosis
                                    </option>
                                    <option value="asupan_nutrisi"
                                        {{ old('kategori') == 'asupan_nutrisi' ? 'selected' : '' }}>Asupan Nutrisi</option>
                                    <option value="preferensi_makanan"
                                        {{ old('kategori') == 'preferensi_makanan' ? 'selected' : '' }}>Preferensi Makanan
                                    </option>
                                </select>
                                @error('kategori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Is First --}}
                            <div class="mb-3 form-check">
                                <input class="form-check-input" type="checkbox" name="is_first" id="is_first"
                                    value="1" {{ old('is_first') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_first">Tandai sebagai fakta pertama dalam
                                    sesi</label>
                            </div>

                            {{-- Is Askable --}}
                            <div class="mb-3 form-check">
                                <input class="form-check-input" type="checkbox" name="is_askable" id="is_askable"
                                    value="1" {{ old('is_askable', 1) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_askable">Dapat ditanyakan (pertanyaan)</label>
                                <small class="form-text text-muted">Jika tidak dicentang, fakta ini tidak akan menjadi
                                    pertanyaan.</small>
                            </div>

                            {{-- Is Default --}}
                            <div class="mb-3 form-check">
                                <input class="form-check-input" type="checkbox" name="is_default" id="is_default"
                                    value="1" {{ old('is_default') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_default">Jadikan sebagai fakta default untuk
                                    kategori ini</label>
                                <small class="form-text text-muted">Fakta ini akan digunakan jika tidak ada aturan lain
                                    yang menghasilkan fakta untuk kategori ini.</small>
                            </div>

                            {{-- Tombol Aksi --}}
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.basisPengetahuan.fakta.index') }}" class="btn btn-secondary me-2">
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
