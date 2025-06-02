@extends('layouts.user')

@section('title', 'Riwayat Konsultasi | SPPOBN')
@section('title-menu', 'Riwayat Konsultasi')
@section('subtitle-menu', 'Daftar Konsultasi Anda')

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
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi bi-clock-history me-2"></i> Riwayat Konsultasi Anda</h4>
                        <div class="button-right-side">
                            <a href="{{ route('konsultasi.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle align-middle me-1"></i> Mulai Konsultasi Baru
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <x-alert />

                        @if ($konsultasi->isEmpty())
                            <div class="alert alert-warning" role="alert">
                                <h4 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Belum ada
                                    konsultasi!
                                </h4>
                                <p>Anda belum pernah melakukan konsultasi. Silakan <a
                                        href="{{ route('konsultasi.create') }}" class="alert-link">mulai konsultasi
                                        baru</a>.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID Konsultasi</th>
                                            <th scope="col">Tanggal Mulai</th>
                                            <th scope="col">Status</th>
                                            <th scope="col" class="text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($konsultasi as $item)
                                            <tr>
                                                <td>#{{ $item->id }}</td>
                                                <td>{{ $item->created_at->format('d M Y, H:i') }}</td>
                                                <td>
                                                    @if ($item->status === 'selesai')
                                                        <span class="badge bg-success">Selesai</span>
                                                    @elseif ($item->status === 'sedang_berjalan')
                                                        <span class="badge bg-info">Sedang Berjalan</span>
                                                    @else
                                                        <span
                                                            class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $item->status)) }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    @if ($item->status === 'selesai')
                                                        <a href="{{ route('konsultasi.result', $item->id) }}"
                                                            class="btn btn-sm btn-success me-2" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" title="Lihat Hasil">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    @else
                                                        <a href="{{ route('konsultasi.question', $item->id) }}"
                                                            class="btn btn-sm btn-info me-2" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" title="Lanjutkan Konsultasi">
                                                            <i class="bi bi-play-circle"></i>
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('konsultasi.show', $item->id) }}"
                                                        class="btn btn-sm btn-secondary" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="Detail Konsultasi">
                                                        <i class="bi bi-info-circle"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                {{ $konsultasi->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts-bottom')
    <script>
        // Initialize tooltips if Bootstrap JS is loaded
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
@endpush

@push('css-top')
@endpush
