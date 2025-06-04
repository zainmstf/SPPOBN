@extends('layouts.user')
@section('title', 'Dashboard | SPPOBN')
@section('title-menu', 'Dashboard')
@section('subtitle-menu', 'Ringkasan Informasi Penting')

@section('content')
    <x-page-header />
    <div class="page-content">
        <section class="row">
            <div class="col-12 col-lg-9">
                <div class="row">
                    @php
                        $stats = [
                            [
                                'color' => 'purple',
                                'icon' => 'bi-chat-dots-fill',
                                'label' => 'Total Konsultasi',
                                'value' => $totalKonsultasi,
                            ],
                            [
                                'color' => 'blue',
                                'icon' => 'bi-play-btn-fill',
                                'label' => 'Total Konten Edu',
                                'value' => $totalKontenEdukasi,
                            ],
                            [
                                'color' => 'green',
                                'icon' => 'bi-egg-fried',
                                'label' => 'Daftar Makanan',
                                'value' => $totalSumberNutrisi,
                            ],
                            [
                                'color' => 'red',
                                'icon' => 'bi-clock-history',
                                'label' => 'Aktivitas Terakhir',
                                'value' => $lastActivity ? $lastActivity->diffForHumans() : 'Tidak ada aktivitas',
                            ],
                        ];
                    @endphp

                    @foreach ($stats as $stat)
                        <x-stat-card :color="$stat['color']" :icon="$stat['icon']" :label="$stat['label']" :value="$stat['value']" />
                    @endforeach
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Grafik Perkembangan Konsultasi 7 Hari Terakhir</h4>
                            </div>
                            <div class="card-body">
                                <div id="area"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Konten Edukasi Terbaru</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @forelse ($kontenEdukasiTerbaru as $konten)
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100">
                                                <x-konten-thumbnail :konten="$konten" />
                                                <div class="card-body pointer" data-bs-toggle="modal"
                                                    data-bs-target="#edukasiModal" data-konten-id="{{ $konten->id }}">
                                                    <h5 class="card-title">{{ $konten->judul }}</h5>
                                                    <p class="card-text">
                                                        {{ Str::limit(strip_tags($konten->deskripsi), 100) }}</p>
                                                </div>
                                                <div class="card-footer d-flex justify-content-between">
                                                    <small class="text-muted">
                                                        <span class="me-2"><i class="bi-tag-fill"></i>
                                                            {{ ucfirst($konten->jenis) }}</span>
                                                    </small>
                                                    <small class="text-muted">
                                                        <span><i class="bi-calendar-event-fill"></i>
                                                            {{ $konten->created_at->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <div class="alert alert-info">
                                                Belum ada konten edukasi yang tersedia.
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="card">
                    <div class="card-body py-4 px-5">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xl">
                                @php
                                    $randomNumber = rand(1, 8);
                                    $imagePath = asset('storage/img/profile/' . $randomNumber . '.jpg');
                                @endphp
                                <img src="{{ $imagePath }}" alt="Profile Picture" />
                            </div>
                            <div class="ms-3 name">
                                <h5 class="font-bold">{{ Auth::user()->nama_lengkap }}</h5>
                                <h6 class="text-muted mb-0">{{ '@' . Auth::user()->username }}</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4>Riwayat Konsultasi</h4>
                    </div>
                    <div class="card-content pb-4">
                        @foreach ($recentKonsultasi as $konsultasi)
                            <a class="recent-message d-flex px-4 py-3"
                                href="{{ route('konsultasi.question', $konsultasi->id) }}">
                                <div class="avatar avatar-lg">
                                    <div class="vertical-progress">
                                        <div class="progress-bar rounded bg-{{ $konsultasi->bgColor }}" role="progressbar"
                                            id="progressBar{{ $konsultasi->id }}"
                                            data-progress="{{ $konsultasi->progress }}"
                                            aria-valuenow="{{ $konsultasi->progress }}" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="name ms-4">
                                    <h5 class="mb-1">Konsultasi #{{ $konsultasi->id }}</h5>
                                    <h6 class="text-muted mb-0">{{ $konsultasi->created_at->format('d M Y') }}</h6>
                                    <span
                                        class="badge bg-{{ $konsultasi->status == 'selesai' ? 'success' : ($konsultasi->status == 'sedang_berjalan' ? 'primary' : 'secondary') }}">
                                        {{ ucwords(str_replace('_', ' ', $konsultasi->status)) }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                        <div class="px-4">
                            <a href="{{ route('konsultasi.index') }}"
                                class="btn btn-block btn-xl btn-light-primary font-bold mt-3">
                                Mulai Konsultasi Baru
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4>Hasil Konsultasi Terakhir</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @if ($lastKonsultasi)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div class="mb-3">
                                            @if (!empty($namaSolusiTerakhir))
                                                <ol>
                                                    @foreach ($namaSolusiTerakhir as $solusi)
                                                        <li>{{ $solusi }}</li>
                                                    @endforeach
                                                </ol>
                                            @else
                                                <p>Tidak ada solusi yang ditemukan.</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <a href="{{ route('konsultasi.result', $lastKonsultasi->id) }}"
                                            class="btn btn-light-primary font-bold me-2">Lihat Disini</a>
                                        <small class="text-muted">
                                            @if ($lastKonsultasi->completed_at)
                                                {{ \Carbon\Carbon::parse($lastKonsultasi->completed_at)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                                            @else
                                                Tanggal tidak tersedia
                                            @endif
                                        </small>
                                    </div>
                                </li>
                            @else
                                <li class="list-group-item text-center">Belum ada rekomendasi
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <section class="row">

        </section>
    </div>
    <x-modal-edukasi />
@endsection

@push('scripts-bottom')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/4.3.0/apexcharts.min.js"></script>
    <script src="{{ asset('storage/js/pages/dashboard.js') }}" type="module"></script>
@endpush
