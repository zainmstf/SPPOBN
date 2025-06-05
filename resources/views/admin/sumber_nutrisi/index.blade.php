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
                                                    method="POST" class="d-inline delete-form"
                                                    data-nama-sumber="{{ $sumber->nama_sumber }}"
                                                    data-jenis-sumber="{{ $sumber->jenis_sumber }}"
                                                    data-nutrisi="{{ $sumber->rekomendasiNutrisi->nutrisi }}"
                                                    data-takaran="{{ $sumber->takaran }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm btn-delete">
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
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#sumberNutrisiTable').DataTable({
                responsive: true, // Menjadikan tabel responsif
            });

            // Handle delete confirmation with SweetAlert
            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();

                const form = $(this).closest('.delete-form');
                const namaSumber = form.data('nama-sumber');
                const jenisSumber = form.data('jenis-sumber');
                const nutrisi = form.data('nutrisi');
                const takaran = form.data('takaran');

                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    html: `Apakah Anda yakin ingin menghapus sumber nutrisi?<br><br>
                           <div class="text-start">
                               <strong>Nama Sumber:</strong> ${namaSumber}<br>
                               <strong>Jenis Sumber:</strong> ${jenisSumber}<br>
                               <strong>Nutrisi:</strong> ${nutrisi}<br>
                               <strong>Takaran:</strong> ${takaran}
                           </div>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'swal-wide'
                    },
                    width: 600
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        Swal.fire({
                            title: 'Menghapus...',
                            text: 'Sedang memproses permintaan Anda',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit the form
                        form[0].submit();
                    }
                });
            });
        });
    </script>
@endpush

@push('css-top')
    <style>
        /* Custom styles for SweetAlert */
        .swal-wide {
            max-width: 600px !important;
        }

        /* DataTables responsive styles */
        .table-responsive {
            overflow-x: auto;
        }

        /* Responsive button styles */
        @media (max-width: 768px) {
            .btn-sm {
                font-size: 0.75rem;
                padding: 0.2rem 0.4rem;
            }

            .btn-sm i {
                font-size: 0.8rem;
            }
        }

        /* Table text alignment for better readability */
        #sumberNutrisiTable td {
            vertical-align: middle;
        }

        /* Custom styling for action buttons */
        .btn-warning,
        .btn-danger {
            margin: 0 2px;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush
