@extends('layouts.admin')

@section('title', 'Riwayat Aktivitas')
@section('title-menu', 'Riwayat Aktivitas Pengguna')
@section('subtitle-menu', 'Menampilkan aktivitas terbaru dari pengguna dan sistem')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Riwayat Aktivitas</li>
@endsection

@section('content')
    <x-page-header />

    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi bi-activity"></i> Riwayat Aktivitas Terbaru</h4>
                    </div>
                    <div class="card-body">
                        <x-alert />
                        <div class="table-responsive">
                            <table id="activityTable" class="table table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Deskripsi</th>
                                        <th>Tanggal</th>
                                        <th>Jenis Aktivitas</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($aktivitasPenggunaTerbaru as $aktivitas)
                                        <tr>
                                            <td>{{ $aktivitas->nama }}</td>
                                            <td>{{ $aktivitas->deskripsi }}</td>
                                            <td>{{ $aktivitas->created_at }}</td>
                                            <td>{{ $aktivitas->jenis_aktivitas }}</td>
                                            <td>{{ $aktivitas->status_aktivitas }}</td>
                                        </tr>
                                    @endforeach
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
    <script src="https://cdn.datatables.net/v/bs5/dt-1.13.10/datatables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#activityTable').DataTable({
                language: {
                    processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i> <span class="sr-only">Loading...</span>'
                },
                order: [
                    [2, 'desc']
                ] // Mengurutkan berdasarkan tanggal (kolom 2) secara descending
            });
        });
    </script>
@endpush

@push('css-top')
    <link href="https://cdn.datatables.net/v/bs5/dt-1.13.10/datatables.min.css" rel="stylesheet">
@endpush
