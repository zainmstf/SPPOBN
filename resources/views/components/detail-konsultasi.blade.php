@extends(Auth::user()->role === 'admin' ? 'layouts.admin' : 'layouts.user')
@section('title', 'Detail Konsultasi | SPPOBN')
@section('title-menu', 'Detail Konsultasi')
@section('subtitle-menu', 'Informasi Lengkap Konsultasi')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.konsultasi.riwayat') }}">Riwayat Konsultasi</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Detail Konsultasi #{{ $konsultasi->konsultasiID }}</h4>
                    </div>
                    <div class="card-body">
                        <h5 class="mb-3">Informasi Pengguna</h5>
                        <dl class="row">
                            <dt class="col-sm-4">Nama</dt>
                            <dd class="col-sm-8">: {{ $konsultasi->user->nama }}</dd>
                            <dt class="col-sm-4">Username</dt>
                            <dd class="col-sm-8">: {{ $konsultasi->user->username }}</dd>
                            <dt class="col-sm-4">Alamat</dt>
                            <dd class="col-sm-8">: {{ $konsultasi->user->alamat }}</dd>
                            <dt class="col-sm-4">No. Telepon</dt>
                            <dd class="col-sm-8">: {{ $konsultasi->user->no_telp }}</dd>
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
                        @if ($konsultasi->konsultasiJawaban->isNotEmpty())
                            <ul class="nav nav-tabs" id="jawabanTabs" role="tablist">
                                @foreach (['risiko_osteoporosis', 'asupan_nutrisi', 'preferensi_makanan'] as $kategori)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link{{ $loop->first ? ' active' : '' }}"
                                            id="{{ $kategori }}-tab" data-bs-toggle="tab"
                                            data-bs-target="#{{ $kategori }}" type="button" role="tab"
                                            aria-controls="{{ $kategori }}"
                                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                            {{ ucwords(str_replace('_', ' ', $kategori)) }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="tab-content mt-4" id="jawabanTabContent">
                                @foreach (['risiko_osteoporosis', 'asupan_nutrisi', 'preferensi_makanan'] as $kategori)
                                    <div class="tab-pane fade{{ $loop->first ? ' show active' : '' }}"
                                        id="{{ $kategori }}" role="tabpanel"
                                        aria-labelledby="{{ $kategori }}-tab">
                                        <div class="table-responsive">
                                            <table id="{{ $kategori . 'Table' }}"
                                                class="table table-striped table-bordered" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>Kode Pertanyaan</th>
                                                        <th>Pertanyaan</th>
                                                        <th>Jawaban</th>
                                                        <th>Dijawab pada</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($konsultasi->konsultasiJawaban->filter(fn($item) => $item->pertanyaan->kategori === $kategori) as $jawaban)
                                                        <tr>
                                                            <td>{{ $jawaban->pertanyaan->kodePertanyaan }}</td>
                                                            <td>{!! nl2br(e($jawaban->pertanyaan->teksPertanyaan)) !!}</td>
                                                            <td>{{ $jawaban->formattedJawaban }}</td>
                                                            <td>{{ Carbon\Carbon::parse($jawaban->answeredAt)->isoFormat('DD MMMM YYYY HH:mm:ss') }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p>Belum ada jawaban yang diberikan.</p>
                        @endif

                        <hr class="my-4">
                        <div class="card-header">
                            <h4 class="mb-0"><i class="fas fa-check-circle"></i> Hasil Konsultasi</h4>
                        </div>
                        <div class="card-body">
                            <x-alert />
                            @if ($konsultasi->rekomendasies->isNotEmpty())
                                @foreach ($konsultasi->rekomendasies as $rekomendasi)
                                    @php $aturanData = $aturan[$rekomendasi->rekomendasiID] ?? null; @endphp
                                    <div
                                        class="card mb-4 {{ $aturanData ? 'border-left-success' : 'border-left-warning' }} shadow-sm">
                                        <div class="card-header bg-light">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h5 class="mb-1">
                                                        {{ ucwords(str_replace('_', ' ', $rekomendasi->jenisRekomendasi)) }}
                                                    </h5>
                                                    <small
                                                        class="text-muted">{{ $aturanData->deskripsi ?? 'Aturan tidak ditemukan - Rekomendasi Default' }}</small>
                                                </div>
                                                <div class="badge">
                                                    <span
                                                        class="badge {{ $aturanData ? 'bg-success' : 'bg-warning' }} text-white">{{ $aturanData->kodeAturan ?? 'Aturan Tidak Ada' }}</span>
                                                    <span class="text-black">→</span>
                                                    <span
                                                        class="badge bg-primary text-white">{{ $rekomendasi->kodeRekomendasi }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body pt-3">
                                            <p class="card-text">
                                            <ol>
                                                @foreach (explode("\n", $rekomendasi->teksRekomendasi) as $line)
                                                    @if (trim($line) !== '')
                                                        <li>{{ trim($line) }}</li>
                                                    @endif
                                                @endforeach
                                            </ol>
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-info">Tidak ada rekomendasi yang ditemukan untuk konsultasi ini.
                                </div>
                            @endif
                        </div>

                        <div class="mt-4">
                            <a href="{{ Auth::user()->role === 'admin' ? route('admin.konsultasi.riwayat') : route('konsultasi.riwayat') }}"
                                class="btn btn-secondary">Kembali ke Riwayat</a>
                            @if ($konsultasi->status === 'completed')
                                <a href="{{ Auth::user()->role === 'admin' ? route('admin.konsultasi.cetak', $konsultasi->konsultasiID) : route('konsultasi.cetak', $konsultasi->konsultasiID) }}"
                                    class="btn btn-info" target="_blank">Cetak</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts-bottom')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            const tableIds = ['risiko_osteoporosisTable', 'asupan_nutrisiTable', 'preferensi_makananTable'];
            const dataTables = {};

            tableIds.forEach(id => {
                dataTables[id] = $('#' + id).DataTable({
                    autoWidth: false, // penting untuk menghormati columnDefs
                    columnDefs: [{
                            targets: 0,
                            width: '5%'
                        },
                        {
                            targets: 1,
                            width: '65%'
                        },
                        {
                            targets: 2,
                            width: '5%'
                        },
                        {
                            targets: 3,
                            width: '25%'
                        }
                    ],
                    order: [
                        [3, 'asc']
                    ]
                });
            });

            // Event untuk tab Bootstrap 5
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                const targetId = $(e.target).data('bs-target').replace('#', '') + 'Table';
                if (dataTables[targetId]) {
                    // Delay 100ms supaya DOM benar-benar dirender sebelum adjust
                    setTimeout(() => {
                        dataTables[targetId].columns.adjust().draw();
                    }, 100);
                }
            });
        });
    </script>
@endpush

@push('css-top')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
@endpush
