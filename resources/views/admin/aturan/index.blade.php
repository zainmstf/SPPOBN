@extends('layouts.admin')

@section('title', 'Daftar Aturan | SPPOBN')
@section('title-menu', 'Daftar Aturan')
@section('subtitle-menu', 'Manajemen Data Aturan')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Aturan</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi-lightbulb-fill"></i> Daftar Aturan</h4>
                        <div>
                            <a href="{{ route('admin.basisPengetahuan.aturan.create') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus-circle align-middle me-1"></i> Tambah Aturan
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <x-alert />
                        <div class="table-responsive">
                            <table id="aturanTable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">Kode</th>
                                        <th style="width: 20%;">Deskripsi</th>
                                        <th style="width: 20%;">Premis</th>
                                        <th style="width: 10%;">Konklusi</th>
                                        <th style="width: 10%;">Jenis Konklusi</th>
                                        <th style="width: 5%;">Status</th>
                                        <th style="width: 15%;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($aturan as $item)
                                        <tr>
                                            <td>{{ $item->kode }}</td>
                                            <td>{{ $item->deskripsi }}</td>
                                            <td>{!! nl2br(e($item->premis)) !!}</td>
                                            <td>{{ $item->konklusi }}</td>
                                            <td>{{ ucwords($item->jenis_konklusi) }}</td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <div class="form-check form-switch me-2">
                                                        <input class="form-check-input status-toggle" type="checkbox"
                                                            role="switch" id="flexSwitchCheck{{ $item->id }}"
                                                            {{ $item->is_active == 1 ? 'checked' : '' }}
                                                            data-id="{{ $item->id }}">
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex gap-2 justify-content-center">
                                                    <a href="{{ route('admin.basisPengetahuan.aturan.edit', $item->id) }}"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="bi-pencil align-middle mt-1"></i> Edit
                                                    </a>
                                                    <form
                                                        action="{{ route('admin.basisPengetahuan.aturan.destroy', $item->id) }}"
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
            $('#aturanTable').DataTable({
                responsive: true,
                scrollX: true
            });

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

            // Handle Bootstrap switch toggle change
            $('.status-toggle').on('change', function() {
                const toggle = $(this);
                const statusBadge = toggle.closest('td').find('.status-badge');
                const aturanId = toggle.data('id');
                const isActive = toggle.is(':checked') ? 1 : 0;

                // Disable toggle during request
                toggle.prop('disabled', true);

                // AJAX request to update status
                $.ajax({
                    url: '{{ route('admin.basisPengetahuan.aturan.toggleStatus', ':id') }}'
                        .replace(':id', aturanId),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        is_active: isActive
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update badge based on response
                            if (response.is_active ===
                                1) { // Jika dari server mengembalikan is_active = 1
                                statusBadge.removeClass('bg-secondary').addClass('bg-success')
                                    .text('Aktif'); // Tampilkan 'Aktif'
                                toastMessage = 'Aturan berhasil diaktifkan!'
                            } else { // Jika dari server mengembalikan is_active = 0
                                statusBadge.removeClass('bg-success').addClass('bg-secondary')
                                    .text('Tidak Aktif'); // Tampilkan 'Tidak Aktif'
                                toastMessage = 'Aturan berhasil dinonaktifkan!'
                            }

                            // Show success toast
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Berhasil!',
                                text: toastMessage,
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                        }
                    },
                    error: function(xhr) {
                        // Revert toggle state on error
                        toggle.prop('checked', !
                            isActive);

                        let errorMessage = 'Gagal memperbarui status aturan!';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        // Show error toast
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'Gagal!',
                            text: errorMessage,
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    },
                    complete: function() {
                        // Re-enable toggle
                        toggle.prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endpush

@push('css-top')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <style>
        #aturanTable {
            table-layout: fixed;
            width: 100%;
        }

        #aturanTable th,
        #aturanTable td {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Custom styles for form-switch */
        .form-check-input.status-toggle {
            cursor: pointer;
        }

        .form-check-input.status-toggle:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            min-width: 80px;
            text-align: center;
        }

        /* Loading overlay */
        .loading-overlay {
            position: relative;
        }

        .loading-overlay::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.7);
            z-index: 1;
        }
    </style>
@endpush
