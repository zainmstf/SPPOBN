@extends('layouts.admin')
@section('title', 'Riwayat Konsultasi | SPPOBN')
@section('title-menu', 'Riwayat Konsultasi')
@section('subtitle-menu', 'Lihat Riwayat Konsultasi User')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Riwayat Konsultasi</li>
@endsection


@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="fas fa-clock-rotate-left me-2"></i>Riwayat Konsultasi</h4>
                    </div>
                    <x-alert />
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="consultationTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="belum_selesai-tab" data-bs-toggle="tab"
                                    data-bs-target="#belum_selesai" type="button" role="tab"
                                    aria-controls="belum_selesai" aria-selected="true">
                                    Belum Selesai
                                    <span
                                        class="badge bg-warning text-dark">{{ $riwayatKonsultasi['belum_selesai']->count() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="sedang_berjalan-tab" data-bs-toggle="tab"
                                    data-bs-target="#sedang_berjalan" type="button" role="tab"
                                    aria-controls="sedang_berjalan" aria-selected="false">
                                    Sedang Berlangsung
                                    <span
                                        class="badge bg-primary">{{ $riwayatKonsultasi['sedang_berjalan']->count() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="selesai-tab" data-bs-toggle="tab" data-bs-target="#selesai"
                                    type="button" role="tab" aria-controls="selesai" aria-selected="false">
                                    Selesai
                                    <span class="badge bg-success">{{ $riwayatKonsultasi['selesai']->count() }}</span>
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content mt-4" id="consultationTabContent">
                            <div class="tab-pane fade show active" id="belum_selesai" role="tabpanel"
                                aria-labelledby="belum_selesai-tab">
                                <div class="table-responsive">
                                    <table id="belumSelesaiTable" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;">ID</th>
                                                <th style="width: 10%;">Nama</th>
                                                <th style="width: 15%;">Tanggal Dibuat</th>
                                                <th style="width: 15%;">Terakhir Diperbarui</th>
                                                <th style="width: 15%;">Sesi Aktif</th>
                                                <th style="width: 40%;">Pertanyaan Terakhir</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($riwayatKonsultasi['belum_selesai'] as $konsultasi)
                                                <tr>
                                                    <td>{{ $konsultasi->id }}</td>
                                                    <td>{{ '@' . $konsultasi->user->username }}</td>
                                                    <td>{{ Carbon\Carbon::parse($konsultasi->created_at)->locale('id')->isoFormat('DD MMM YYYY HH:mm') }}
                                                    </td>
                                                    <td>{{ Carbon\Carbon::parse($konsultasi->updated_at)->locale('id')->isoFormat('DD MMM YYYY HH:mm') }}
                                                    <td>{{ $konsultasi->sesi ? ucwords(str_replace('_', ' ', $konsultasi->sesi)) : 'Tidak ada sesi aktif' }}
                                                    </td>
                                                    <td>{{ $konsultasi->currentPertanyaan ? Str::limit($konsultasi->currentPertanyaan->pertanyaan, 50) : '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="sedang_berjalan" role="tabpanel"
                                aria-labelledby="sedang_berjalan-tab">
                                <div class="table-responsive">
                                    <table id="sedangBerlangsungTable" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;">ID</th>
                                                <th style="width: 10%;">Nama</th>
                                                <th style="width: 15%;">Tanggal Dibuat</th>
                                                <th style="width: 15%;">Terakhir Diperbarui</th>
                                                <th style="width: 15%;">Sesi Aktif</th>
                                                <th style="width: 40%;">Pertanyaan Terakhir</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($riwayatKonsultasi['sedang_berjalan'] as $konsultasi)
                                                <tr>
                                                    <td>{{ $konsultasi->id }}</td>
                                                    <td>{{ '@' . $konsultasi->user->username }}</td>
                                                    <td>{{ Carbon\Carbon::parse($konsultasi->created_at)->locale('id')->isoFormat('DD MMM YYYY HH:mm') }}
                                                    </td>
                                                    <td>{{ Carbon\Carbon::parse($konsultasi->updated_at)->locale('id')->isoFormat('DD MMM YYYY HH:mm') }}
                                                    <td>{{ $konsultasi->sesi ? ucwords(str_replace('_', ' ', $konsultasi->sesi)) : 'Tidak ada sesi aktif' }}
                                                    </td>
                                                    <td>{{ $konsultasi->currentPertanyaan ? Str::limit($konsultasi->currentPertanyaan->pertanyaan, 50) : '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="selesai" role="tabpanel" aria-labelledby="selesai-tab">
                                <div class="table-responsive">
                                    <table id="selesaiTable" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;">ID</th>
                                                <th style="width: 10%;">Username</th>
                                                <th style="width: 15%;">Tanggal Dibuat</th>
                                                <th style="width: 15%;">Tanggal Selesai</th>
                                                <th style="width: 40%;">Hasil Konsultasi</th>
                                                <th style="width: 15%;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($riwayatKonsultasi['selesai'] as $konsultasi)
                                                <tr>
                                                    <td>{{ $konsultasi->id }}</td>
                                                    <td>{{ '@' . $konsultasi->user->username }}</td>
                                                    <td>{{ Carbon\Carbon::parse($konsultasi->created_at)->locale('id')->isoFormat('DD MMM YYYY HH:mm') }}
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($konsultasi->completed_at)->format('d M Y, H:i') }}
                                                    </td>
                                                    <td>
                                                        @if (!empty($konsultasi->nama_solusi))
                                                            {{-- Join the solution names with a comma and space for display --}}
                                                            {{ implode(', ', $konsultasi->nama_solusi) }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.konsultasi.show', $konsultasi->id) }}"
                                                            class="btn btn-sm btn-info me-1">
                                                            <i class="bi-eye-fill"></i> Detail
                                                        </a>
                                                        <a href="{{ route('admin.konsultasi.print', $konsultasi->id) }}"
                                                            class="btn btn-sm btn-secondary" target="_blank">
                                                            <i class="bi-printer-fill"></i> Cetak
                                                        </a>
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
        </div>
    </div>
@endsection
@push('scripts-bottom')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#belumSelesaiTable').DataTable({
                responsive: true,
            });
            $('#sedangBerlangsungTable').DataTable();
            $('#selesaiTable').DataTable();

            $('button[data-bs-toggle="tab"]').on("shown.bs.tab", function(e) {
                $.fn.dataTable
                    .tables({
                        visible: true,
                        api: true,
                    })
                    .columns.adjust();
            });

            // Konfirmasi Tunda Konsultasi
            $('a[data-action="{{ route('konsultasi.pending') }}"]').click(function(e) {
                e.preventDefault();
                var konsultasiId = $(this).data('id');
                console.log(konsultasiId);
                var url = $(this).data('action');

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin menunda konsultasi ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Tunda!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                konsultasi_id: konsultasiId,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Berhasil!',
                                        'Konsultasi Anda telah ditunda.',
                                        'success'
                                    ).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire(
                                        'Gagal!',
                                        'Terjadi kesalahan saat menunda konsultasi.',
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'Error!',
                                    'Terjadi kesalahan: ' + error,
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
@push('css-top')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
@endpush
