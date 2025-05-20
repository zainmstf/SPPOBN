@extends(Auth::user()->role === 'admin' ? 'layouts.admin' : 'layouts.user')
@section('title', 'Detail Konsultasi | SPPOBN')
@section('title-menu', 'Detail Konsultasi')
@section('subtitle-menu', 'Informasi Lengkap Konsultasi')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('riwayat.index') }}">Riwayat Konsultasi</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Detail Konsultasi #{{ $konsultasi->id }}</h4>
                    </div>
                    <x-alert />
                    <div class="card-body">
                        <h5 class="mb-3">Informasi Pengguna</h5>
                        <dl class="row">
                            <dt class="col-sm-4">Nama</dt>
                            <dd class="col-sm-8">: {{ $konsultasi->user->nama_lengkap }}</dd>
                            <dt class="col-sm-4">Username</dt>
                            <dd class="col-sm-8">: {{ $konsultasi->user->username }}</dd>
                            <dt class="col-sm-4">Alamat</dt>
                            <dd class="col-sm-8">: {{ $konsultasi->user->alamat }}</dd>
                            <dt class="col-sm-4">Email</dt>
                            <dd class="col-sm-8">: {{ $konsultasi->user->email }}</dd>
                            <dt class="col-sm-4">No. Telepon</dt>
                            <dd class="col-sm-8">: {{ $konsultasi->user->no_telepon }}</dd>
                            <dt class="col-sm-4">Tanggal Dibuat</dt>
                            <dd class="col-sm-8">:
                                {{ Carbon\Carbon::parse($konsultasi->created_at)->isoFormat('DD MMMM YYYY HH:mm:ss') }}</dd>
                            <dt class="col-sm-4">Tanggal Selesai</dt>
                            <dd class="col-sm-8">:
                                {{ Carbon\Carbon::parse($konsultasi->completedAt)->isoFormat('DD MMMM YYYY HH:mm:ss') }}
                            </dd>
                        </dl>

                        <hr class="my-4">

                        <h5 class="mb-3">Riwayat Jawaban</h5>
                        <table id="table-jawaban" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 90%;">Pertanyaan</th>
                                    <th style="width: 5%;">Jawaban</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($detailKonsultasi as $index => $detail)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $detail->fakta->pertanyaan ?? '-' }}</td>
                                        <td>{{ ucfirst($detail->jawaban) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <hr class="my-4">
                        <div class="card-header">
                            <h4 class="mb-0"><i class="fas fa-check-circle"></i> Hasil Konsultasi</h4>
                        </div>
                        <div class="card-body">
                            @if ($solusiAkhir)
                                <div class="card mb-4 border-left-success shadow-sm">
                                    <div class="card-header bg-light">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h5 class="mb-1">Rekomendasi Konsultasi#{{ $konsultasi->id }}</h5>
                                                <small
                                                    class="text-muted">{{ $inferensiSolusi->aturan->deskripsi ?? 'Deskripsi rekomendasi' }}</small>
                                            </div>
                                            <div class="badge">
                                                <span class="badge bg-primary text-white">{{ $solusiAkhir->kode }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @if ($solusiAkhir->peringatan_konsultasi)
                                        <div class="alert alert-danger alert-dismissible nodismissable" role="alert">
                                            <strong>Peringatan:</strong> {{ $solusiAkhir->peringatan_konsultasi }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                    @endif
                                    <div class="card-body pt-2">
                                        <p class="card-text">
                                            {!! $solusiAkhir->deskripsi !!}
                                            @if ($solusiAkhir->rekomendasiNutrisi->isNotEmpty())
                                                <h6 class="mt-3">Sumber Nutrisi Yang Bisa Didapat</h6>
                                                <ul>
                                                    @foreach ($solusiAkhir->rekomendasiNutrisi as $rekomendasiNutrisi)
                                                        <li>{{ $rekomendasiNutrisi->nutrisi }}
                                                            @if ($rekomendasiNutrisi->sumberNutrisi->isNotEmpty())
                                                                Sumber:
                                                                @php
                                                                    $acakSumberNutrisi = $rekomendasiNutrisi->sumberNutrisi->shuffle();
                                                                @endphp

                                                                @foreach ($acakSumberNutrisi->take(3) as $sumber)
                                                                    {{ $sumber->nama_sumber }}{{ $loop->last ? '' : ', ' }}
                                                                @endforeach

                                                                @if ($rekomendasiNutrisi->sumberNutrisi->count() > 3)
                                                                    dan lainnya...
                                                                @endif
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                <small style="margin-top: 60px; font-size: 12px; color: #999;">
                                                    <p><span class="text-danger">*</span>
                                                        Untuk daftar sumber nutrisi lebih lengkap bisa di lihat di menu
                                                        <a href="{{ route('edukasi.daftarMakanan') }}">daftar makanan</a>
                                                    </p>
                                                </small>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="hasil_konsultasi">
                                    @php
                                        $hasil = explode("\n\n", $konsultasi->hasil_konsultasi);
                                    @endphp

                                    @foreach ($hasil as $bagian)
                                        @php
                                            $baris = explode("\n", $bagian);
                                        @endphp
                                        @if (count($baris) > 0)
                                            <div class="mb-3">
                                                <strong>{{ array_shift($baris) }}</strong>
                                                <ul class="mb-0">
                                                    @foreach ($baris as $item)
                                                        @if (trim($item) !== '')
                                                            <li>{{ $item }}</li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            <h5 class="mb-3 mt-4">Logika Inferensi:</h5>
                            @if ($inferensiLog->isNotEmpty())
                                <div class="accordion" id="inferensiAccordion">
                                    @foreach ($inferensiLog as $key => $log)
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading{{ $key }}">
                                                <button class="accordion-button {{ $key > 0 ? 'collapsed' : '' }}"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapse{{ $key }}"
                                                    aria-expanded="{{ $key == 0 ? 'true' : 'false' }}"
                                                    aria-controls="collapse{{ $key }}">
                                                    Aturan #{{ $key + 1 }}: Menyimpulkan:
                                                    {{ \App\Models\Fakta::where('kode', $log->fakta_terbentuk)->first()?->deskripsi ?? (\App\Models\Solusi::where('kode', $log->fakta_terbentuk)->first()?->nama ?? $log->fakta_terbentuk) }}
                                                </button>
                                            </h2>
                                            <div id="collapse{{ $key }}"
                                                class="accordion-collapse collapse {{ $key == 0 ? 'show' : '' }}"
                                                aria-labelledby="heading{{ $key }}"
                                                data-bs-parent="#inferensiAccordion">
                                                <div class="accordion-body">
                                                    <strong>Aturan:</strong>
                                                    {{ $log->aturan?->deskripsi ?? '-' }}<br>
                                                    <strong>Fakta Terpenuhi:</strong>
                                                    @if ($log->premis_terpenuhi)
                                                        <ul class="list-unstyled">
                                                            @foreach (explode(',', $log->premis_terpenuhi) as $premisKode)
                                                                <li>-
                                                                    {{ \App\Models\Fakta::where('kode', trim($premisKode))->first()?->deskripsi ?? trim($premisKode) }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        Tidak ada premis yang terpenuhi<br>
                                                    @endif
                                                    <strong>Kesimpulan:</strong>
                                                    @php
                                                        $faktaKonklusi = \App\Models\Fakta::where(
                                                            'kode',
                                                            $log->fakta_terbentuk,
                                                        )->first();
                                                    @endphp
                                                    {{ $faktaKonklusi?->deskripsi ?? $log->fakta_terbentuk }}
                                                    @if ($log->aturan?->jenis_konklusi === 'solusi' && $log->aturan?->solusi)
                                                        <br><strong>Solusi yang Dipicu:</strong>
                                                        {{ $log->aturan->solusi->nama }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-info">
                                    Tidak ada proses inferensi yang tercatat.
                                </div>
                            @endif
                        </div>

                        <div class="mt-4">
                            <a href="{{ Auth::user()->role === 'admin' ? route('admin.konsultasi.riwayat') : route('riwayat.index') }}"
                                class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i> Kembali ke Riwayat</a>
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                data-bs-target="#umpanBalikModal" data-konsultasi-id="{{ $konsultasi->id }}">
                                <i class="fas fa-star me-2"></i> Beri Umpan Balik
                            </button>
                            @if ($konsultasi->status === 'selesai')
                                <a href="{{ route('konsultasi.print', $konsultasi->id) }}" class="btn btn-secondary"
                                    target="_blank"><i class="fas fa-print me-2"></i> Cetak</a>
                                </a>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-modal-umpan-balik :konsultasi="$konsultasi" />
@endsection

@push('scripts-bottom')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script type="module">
        import {
            setupUmpanBalikModal
        } from '../../storage/js/components/star-rating.js';
        document.addEventListener("DOMContentLoaded", function() {
            setupUmpanBalikModal();
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#table-jawaban').DataTable({
                autoWidth: false,
                columnDefs: [{
                        width: '5%',
                        targets: 0
                    },
                    {
                        width: '90%',
                        targets: 1
                    },
                    {
                        width: '5%',
                        targets: 2
                    }
                ]
                // Anda bisa menambahkan opsi DataTables lainnya di sini
            });
        });
    </script>
@endpush

@push('css-top')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
@endpush
