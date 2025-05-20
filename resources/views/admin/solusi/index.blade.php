@extends('layouts.admin')

@section('title', 'Daftar Solusi | SPPOBN')
@section('title-menu', 'Daftar Solusi')
@section('subtitle-menu', 'Manajemen Data Solusi')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Solusi</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi-check-circle-fill"></i> Daftar Solusi</h4>
                        <a href="{{ route('admin.basisPengetahuan.solusi.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle align-middle mr-1"></i> Tambah Solusi
                        </a>
                    </div>
                    <div class="card-body">
                        <x-alert />
                        <div class="table-responsive">
                            <table id="solusiTable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">Kode</th>
                                        <th style="width: 15%;">Nama</th>
                                        <th style="width: 20%;">Deskripsi</th>
                                        <th style="width: 25%;">Peringatan Konsultasi</th>
                                        <th style="width: 15%;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($solusis as $item)
                                        <tr>
                                            <td>{{ $item->kode }}</td>
                                            <td>{{ $item->nama }}</td> <!-- Kolom Nama -->
                                            <td>{{ Str::limit(strip_tags($item->deskripsi ?? '-'), 50) }}</td>

                                            <td>{{ Str::limit($item->peringatan_konsultasi ?? '-', 50) }}</td>
                                            <!-- Kolom Peringatan Konsultasi -->
                                            <td class="text-center">
                                                <div class="d-flex gap-2 justify-content-center">
                                                    <a href="{{ route('admin.basisPengetahuan.solusi.show', $item->id) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="bi-eye align-middle mt-1"></i> Detail
                                                    </a>
                                                    <a href="{{ route('admin.basisPengetahuan.solusi.edit', $item->id) }}"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="bi-pencil align-middle mt-1"></i> Edit
                                                    </a>
                                                    <form
                                                        action="{{ route('admin.basisPengetahuan.solusi.destroy', $item->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger btn-delete">
                                                            <i class="bi-trash align-middle mt-1"></i> Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#solusiTable').DataTable();

            // SweetAlert Delete Confirmation
            $('.btn-delete').on('click', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush

@push('css-top')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
@endpush
