@extends('layouts.admin')

@section('title', 'Rekomendasi Nutrisi')
@section('title-menu', 'Daftar Rekomendasi Nutrisi')
@section('subtitle-menu', 'Menampilkan Daftar Rekomendasi Nutrisi')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Rekomendasi Nutrisi</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi-heart-pulse-fill"></i> Daftar Rekomendasi Nutrisi</h4>
                        <div class="button-right-side">
                            <a href="{{ route('admin.rekomendasi-nutrisi.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle align-middle mr-1"></i> Tambah Rekomendasi Nutrisi
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <x-alert />
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="rekomendasiNutrisiTable">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">ID</th>
                                        <th style="width: 5%;">Solusi</th>
                                        <th style="width: 10%;">Nutrisi</th>
                                        <th style="width: 32,5%;">Kontraindikasi</th>
                                        <th style="width: 32,5%;">Alternatif</th>
                                        <th style="width: 15%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($rekomendasiNutrisi as $rekomendasi)
                                        <tr>
                                            <td>{{ $rekomendasi->id }}</td>
                                            <td>{{ $rekomendasi->solusi->kode }}</td>
                                            <td>{{ $rekomendasi->nutrisi }}</td>
                                            <td>{{ $rekomendasi->kontraindikasi ?? '-' }}</td>
                                            <td>{{ $rekomendasi->alternatif ?? '-' }}</td>
                                            <td>
                                                <a href="{{ route('admin.rekomendasi-nutrisi.edit', $rekomendasi->id) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="bi-pencil-fill"></i> Edit
                                                </a>
                                                <form
                                                    action="{{ route('admin.rekomendasi-nutrisi.destroy', $rekomendasi->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                        <i class="bi-trash-fill"></i> Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Tidak ada data rekomendasi nutrisi.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts-bottom')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#rekomendasiNutrisiTable').DataTable({
                responsive: true, // Menjadikan tabel responsif
            });
        });
    </script>
@endpush

@push('css-top')
    <style>
        /* стили */
    </style>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush
