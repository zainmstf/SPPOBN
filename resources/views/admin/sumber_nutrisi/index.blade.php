@extends('layouts.admin')

@section('title', 'Sumber Nutrisi')
@section('title-menu', 'Daftar Sumber Nutrisi')
@section('subtitle-menu', 'Menampilkan Daftar Sumber Nutrisi')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Sumber Nutrisi</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi-circle-square"></i> Daftar Sumber Nutrisi</h4>
                        <div class="button-right-side">
                            <a href="{{ route('admin.sumber-nutrisi.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle align-middle mr-1"></i> Tambah Sumber Nutrisi
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <x-alert />
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="sumberNutrisiTable">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">ID</th>
                                        <th style="width: 5%;">Nutrisi </th>
                                        <th style="width: 10%;">Jenis Sumber</th>
                                        <th style="width: 35%;">Nama Sumber</th>
                                        <th style="width: 10%;">Takaran</th>
                                        <th style="width: 20%;">Catatan</th>
                                        <th style="width: 20%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($sumberNutrisi as $sumber)
                                        <tr>
                                            <td>{{ $sumber->id }}</td>
                                            <td>{{ $sumber->rekomendasiNutrisi->nutrisi }}</td>
                                            <td>{{ ucwords($sumber->jenis_sumber) }}</td>
                                            <td>{{ ucwords($sumber->nama_sumber) }}</td>
                                            <td>{{ $sumber->takaran }}</td>
                                            <td>{{ $sumber->catatan }}</td>
                                            <td>
                                                <a href="{{ route('admin.sumber-nutrisi.edit', $sumber->id) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="bi-pencil-fill"></i> Edit
                                                </a>
                                                <form action="{{ route('admin.sumber-nutrisi.destroy', $sumber->id) }}"
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
                                            <td colspan="7" class="text-center">Tidak ada data sumber nutrisi.</td>
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
            $('#sumberNutrisiTable').DataTable({
                responsive: true, // Menjadikan tabel responsif
            });
        });
    </script>
@endpush

@push('css-top')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush
