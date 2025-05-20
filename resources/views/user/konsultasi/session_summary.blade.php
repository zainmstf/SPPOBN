@extends('layouts.user')
@section('title', 'Riwayat Konsultasi | SPPOBN')
@section('title-menu', 'Riwayat Konsultasi')
@section('subtitle-menu', 'Lihat Hasil Konsultasi Sesi ini, Selesaikan Konsultasi Untuk Mendapatkan Rekomendasi')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Hasil Konsultasi Sementara</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi bi-question-circle-fill"></i> Ringkasan Sesi</h4>
                        <span>Sesi: {{ $judul }}</span>
                    </div>
                    <div class="card-body">
                        @if (isset($value))
                            <div class="progress mb-3">
                                <div class="progress-bar {{ $class }}" role="progressbar"
                                    aria-valuenow="{{ $value }}" aria-valuemin="0" aria-valuemax="100">
                                    Sesi {{ $sesi }}
                                </div>
                            </div>
                        @endif

                        <div style="border: 1px solid #ccc; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                            <p class="mb-2" style="font-size: 2em;">
                                <strong>Hasil Konsultasi Pada Sesi ini :</strong>
                            </p>
                            <p class="mb-5 text-center" style="font-size: 2em; font-weight: bold; white-space: pre-line;">
                                <?php
                                $originalString = $sessionMessage;
                                $lines = explode("\n", $originalString);
                                $results = [];
                                foreach ($lines as $line) {
                                    if (strpos($line, '- ') !== false) {
                                        $results[] = trim(substr($line, strpos($line, '- ') + 2));
                                    }
                                }
                                $processedString = implode(', ', $results);
                                ?>
                                {{ $processedString }}
                            </p>
                        </div>

                        <div class="d-flex justify-content-center mb-4 pt-3">
                            <a href="#" class="btn btn-lg me-5 btn-warning"
                                data-action="{{ route('konsultasi.pending') }}" data-id="{{ $konsultasi->id }}">
                                <i class="bi-pause-fill"></i> Tunda
                            </a>
                            <form action="{{ route('konsultasi.continue-session', $konsultasi->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-play-fill me-1"></i>
                                    Lanjutkan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts-bottom')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Konfirmasi Tunda Konsultasi
            $('a[data-action="{{ route('konsultasi.pending') }}"]').click(function(e) {
                e.preventDefault();
                var konsultasiId = $(this).data('id');
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
                                konsultasi_id: parseInt($(this).data('id')),
                                is_summary: true,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {

                                if (response.success) {
                                    Swal.fire(
                                        'Berhasil!',
                                        'Konsultasi Anda telah ditunda.',
                                        'success'
                                    ).then(() => {
                                        window.location.href =
                                            '{{ route('dashboard') }}';
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
