@extends('layouts.admin')

@section('title', 'Tambah Rekomendasi Nutrisi')
@section('title-menu', 'Tambah Rekomendasi Nutrisi')
@section('subtitle-menu', 'Menambah Data Rekomendasi Nutrisi Baru')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.rekomendasi-nutrisi.index') }}">Rekomendasi Nutrisi</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="bi-plus-circle"></i> Tambah Rekomendasi Nutrisi</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.rekomendasi-nutrisi.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="solusi_id" class="form-label">Solusi ID</label>
                                <select class="form-control" id="solusi_id" name="solusi_id" required>
                                    <option value="">Pilih Solusi</option>
                                    @isset($solusi)
                                        @foreach ($solusi as $s)
                                            <option value="{{ $s->id }}">{{$s->kode.'-'. $s->nama }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                                @error('solusi_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="nutrisi" class="form-label">Nutrisi</label>
                                <input type="text" class="form-control" id="nutrisi" name="nutrisi" required>
                                @error('nutrisi')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="kontraindikasi" class="form-label">Kontraindikasi</label>
                                <textarea class="form-control" id="kontraindikasi" name="kontraindikasi" rows="3"></textarea>
                                @error('kontraindikasi')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="alternatif" class="form-label">Alternatif</label>
                                <textarea class="form-control" id="alternatif" name="alternatif" rows="3"></textarea>
                                @error('alternatif')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.rekomendasi-nutrisi.index') }}" class="btn btn-secondary me-2">
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
