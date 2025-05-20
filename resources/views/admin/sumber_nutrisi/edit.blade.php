@extends('layouts.admin')

@section('title', 'Edit Sumber Nutrisi')
@section('title-menu', 'Edit Sumber Nutrisi')
@section('subtitle-menu', 'Mengedit Data Sumber Nutrisi')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.sumber-nutrisi.index') }}">Sumber Nutrisi</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="bi-pencil-square"></i> Edit Sumber Nutrisi</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.sumber-nutrisi.update', $sumberNutrisi->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="rekomendasi_nutrisi_id" class="form-label">Rekomendasi Nutrisi</label>
                                <select class="form-control" id="rekomendasi_nutrisi_id" name="rekomendasi_nutrisi_id"
                                    required>
                                    <option value="">Pilih Rekomendasi Nutrisi</option>
                                    @isset($rekomendasiNutrisi)
                                        @foreach ($rekomendasiNutrisi as $rekomendasi)
                                            <option value="{{ $rekomendasi->id }}"
                                                {{ $sumberNutrisi->rekomendasi_nutrisi_id == $rekomendasi->id ? 'selected' : '' }}>
                                                {{ $rekomendasi->nutrisi }}
                                            </option>
                                        @endforeach
                                    @endisset
                                </select>
                                @error('rekomendasi_nutrisi_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="jenis_sumber" class="form-label">Jenis Sumber</label>
                                <select class="form-control" id="jenis_sumber" name="jenis_sumber" required>
                                    <option value="suplemen"
                                        {{ $sumberNutrisi->jenis_sumber == 'suplemen' ? 'selected' : '' }}>Suplemen</option>
                                    <option value="makanan"
                                        {{ $sumberNutrisi->jenis_sumber == 'makanan' ? 'selected' : '' }}>Makanan</option>
                                </select>
                                @error('jenis_sumber')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="nama_sumber" class="form-label">Nama Sumber</label>
                                <input type="text" class="form-control" id="nama_sumber" name="nama_sumber"
                                    value="{{ $sumberNutrisi->nama_sumber }}" required>
                                @error('nama_sumber')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="takaran" class="form-label">Takaran</label>
                                <input type="text" class="form-control" id="takaran" name="takaran"
                                    value="{{ $sumberNutrisi->takaran }}">
                                @error('takaran')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="catatan" class="form-label">Catatan</label>
                                <textarea class="form-control" id="catatan" name="catatan" rows="3">{{ $sumberNutrisi->catatan }}</textarea>
                                @error('catatan')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.sumber-nutrisi.index') }}" class="btn btn-secondary me-2">
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
@endpush

@push('css-top')
@endpush
