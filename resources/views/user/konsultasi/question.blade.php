@extends('layouts.user')

@section('title')
    Sesi Konsultasi {{ ucwords(str_replace('_', ' ', $konsultasi->sesi)) }} | SPPOBN
@endsection
@section('title-menu', 'Sesi Konsultasi')
@section('subtitle-menu', 'Jawab pertanyaan untuk mendapatkan rekomendasi')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Sesi Konsultasi</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi bi-question-circle-fill"></i> Pertanyaan</h4>
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
                        @if ($fakta)
                            <div style="border: 1px solid #ccc; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                                <p class="mb-2" style="font-size: 2em;">
                                    <strong>Pertanyaan :</strong>
                                </p>
                                <p class="mb-5 text-center"
                                    style="font-size: 2em; font-weight: bold; white-space: pre-line;">
                                    {{ $fakta->pertanyaan }}
                                </p>
                            </div>

                            <div class="d-flex justify-content-center mb-4 pt-3">
                                <button type="button" class="btn btn-success btn-lg me-5"
                                    style="font-size: 1.3em; padding: 15px 80px;">Ya</button>
                                <button type="button" class="btn btn-danger btn-lg"
                                    style="font-size: 1.3em; padding: 15px 80px;">Tidak</button>
                            </div>

                            <form id="formJawaban" method="POST" action="{{ route('konsultasi.answer', $konsultasi->id) }}"
                                style="display: none;">
                                @csrf
                                <input type="hidden" name="fakta_kode" value="{{ $fakta->kode }}">
                                <input type="hidden" name="jawaban" id="jawabanInput">
                            </form>
                        @else
                            <p class="text-center fs-3">Tidak ada pertanyaan untuk sesi ini.</p>
                            <div class="text-center">
                                <a href="{{ route('konsultasi.result', $konsultasi->id) }}" class="btn btn-success btn-lg"
                                    style="font-size: 1.3em; padding: 15px 30px;">Lihat
                                    Hasil</a>
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
        document.addEventListener('DOMContentLoaded', () => {
            const yaButton = document.querySelector('.btn-success');
            const tidakButton = document.querySelector('.btn-danger');
            const jawabanInput = document.getElementById('jawabanInput');
            const formJawaban = document.getElementById('formJawaban');
            let isSubmitting = false;

            if (yaButton && tidakButton && jawabanInput && formJawaban) {
                const handleAnswer = (jawaban) => {
                    if (isSubmitting) {
                        console.log('Pengiriman ganda dicegah.');
                        return;
                    }
                    isSubmitting = true;

                    // Menonaktifkan kedua tombol setelah salah satu diklik
                    yaButton.disabled = true;
                    tidakButton.disabled = true;

                    jawabanInput.value = jawaban;
                    formJawaban.submit();
                };

                yaButton.addEventListener('click', () => {
                    handleAnswer('ya');
                });

                tidakButton.addEventListener('click', () => {
                    handleAnswer('tidak');
                });
            }
        });
    </script>
@endpush
