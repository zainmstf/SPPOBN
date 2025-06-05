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
                                                    method="POST" class="d-inline delete-form"
                                                    data-nutrisi-name="{{ $rekomendasi->nutrisi }}"
                                                    data-solusi-kode="{{ $rekomendasi->solusi->kode }}">
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
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#rekomendasiNutrisiTable').DataTable({
                responsive: true, // Menjadikan tabel responsif
            });

            // Handle delete confirmation with SweetAlert
            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();

                const form = $(this).closest('.delete-form');
                const nutrisiName = form.data('nutrisi-name');
                const solusiKode = form.data('solusi-kode');

                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    html: `Apakah Anda yakin ingin menghapus rekomendasi nutrisi?<br><br>
                           <strong>Nutrisi:</strong> ${nutrisiName}<br>
                           <strong>Solusi:</strong> ${solusiKode}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'swal-wide'
                    }
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
        /* Custom styles */
        .swal-wide {
            width: 600px !important;
        }

        /* DataTables responsive styles */
        .table-responsive {
            overflow-x: auto;
        }

        @media (max-width: 768px) {
            .btn-sm {
                font-size: 0.8rem;
                padding: 0.25rem 0.5rem;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush
