@extends('layouts.admin')
@section('title', 'Dashboard | SPPOBN')
@section('title-menu', 'Dashboard')
@section('subtitle-menu', 'Ringkasan Informasi Penting')
@section('content')
    <x-page-header />
    <div class="page-content">
        <section class="row">
            <div class="col-12 col-lg-9">
                <div class="row">
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon purple">
                                            <i class="bi-people-fill mt-1"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Total Pengguna</h6>
                                        <h6 class="font-extrabold mb-0">{{ $totalPengguna }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon blue">
                                            <i class="bi-chat-left-text-fill mt-1"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Total Konsultasi</h6>
                                        <h6 class="font-extrabold mb-0">{{ $totalKonsultasi }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon green">
                                            <i class="bi-book-fill mt-1"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Total Konten Edu</h6>
                                        <h6 class="font-extrabold mb-0">{{ $totalKontenEdukasi }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon red">
                                            <i class="bi-lightbulb-fill mt-1"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Basis Pengetahuan</h6>
                                        <h6 class="font-extrabold mb-0">
                                            {{ 'A. ' . $totalAturan . ' / F. ' . $totalPertanyaan . ' / S. ' . $totalRekomendasi }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Grafik Statistik Konsultasi 7 Hari Terakhir</h4>
                            </div>
                            <div class="card-body" style="height: 405px;">
                                <div id="chart-konsultasi"></div>
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
                    <div class="card-body">
                        @if ($konsultasiPending->isNotEmpty())
                            <div class="mb-3">
                                <h5>
                                    <i class="bi bi-question-circle-fill text-warning me-2"></i>
                                    Konsultasi Pending
                                    <span class="badge bg-warning">{{ $konsultasiPendingCount }}</span>
                                </h5>
                                <ul class="list-group">
                                    @foreach ($konsultasiPending as $konsultasi)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Konsultasi #{{ $konsultasi->id }} -
                                            {{ $konsultasi->created_at->diffForHumans() }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if ($umpanBalikRatingRendah->isNotEmpty())
                            <div>
                                <h5>
                                    <i class="bi bi-star-half text-danger me-2"></i>
                                    Rating Rendah
                                    <span class="badge bg-danger">{{ $umpanBalikRatingRendahCount }}</span>
                                </h5>
                                <ul class="list-group">
                                    @foreach ($umpanBalikRatingRendah as $umpanBalik)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Umpan Balik #{{ $umpanBalik->id }} -
                                            {{ $umpanBalik->created_at->diffForHumans() }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Akses Cepat ke Manajemen</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.konten-edukasi.create') }}"
                                class="btn btn-primary d-flex align-items-center">
                                <i class="bi-plus-circle me-2 mb-2"></i> Tambah Konten Edukasi
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary d-flex align-items-center">
                                <i class="bi-people me-2 mb-2"></i> Kelola Pengguna
                            </a>
                            <a href="{{ route('admin.laporan') }}" class="btn btn-info d-flex align-items-center">
                                <i class="bi-file-earmark-bar-graph me-2 mb-2"></i> Lihat Laporan Konsultasi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Grafik Rating Rata-Rata Umpan Balik 7 Hari Terakhir</h4>
                    </div>
                    <div class="card-body">
                        <div id="chart-rating-umpan-balik"></div>
                        <div id="grafikUmpanBalikData" style="display: none;"
                            data-series="{{ $grafikUmpanBalik['ratingDataJson'] }}"
                            data-categories="{{ $grafikUmpanBalik['ratingLabelsJson'] }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Aktivitas Pengguna Terbaru</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-lg">
                                <thead>
                                    <tr>
                                        <th>Pengguna</th>
                                        <th>Aktivitas</th>
                                        <th>Waktu</th>
                                        <th>Jenis Aktivitas</th>
                                        <th>Status Aktivitas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($aktivitasPenggunaTerbaru->isEmpty())
                                        <tr>
                                            <td colspan="5">Tidak ada aktivitas pengguna terbaru.</td>
                                        </tr>
                                    @else
                                        @foreach ($aktivitasPenggunaTerbaru as $aktivitas)
                                            <tr>
                                                <td class="col-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-md">
                                                            @php
                                                                $randomNumber = rand(1, 8);
                                                                $imagePath = asset(
                                                                    'storage/img/profile/' . $randomNumber . '.jpg',
                                                                );
                                                            @endphp
                                                            <img src="{{ $imagePath }}" alt="Profile Picture" />
                                                        </div>
                                                        <p class="font-bold ms-3 mb-0">{{ $aktivitas->nama }}</p>
                                                    </div>
                                                </td>
                                                <td class="col-auto">
                                                    <p class="mb-0">{{ $aktivitas->deskripsi }}</p>
                                                </td>
                                                <td class="col-auto">
                                                    <p class="mb-0">{{ $aktivitas->created_at->diffForHumans() }}</p>
                                                </td>
                                                <td class="col-auto">
                                                    <p class="mb-0">{{ $aktivitas->jenis_aktivitas }}</p>
                                                </td>
                                                <td class="col-auto">
                                                    <p class="mb-0">{{ $aktivitas->status_aktivitas }}</p>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/4.3.0/apexcharts.min.js"></script>
    <script src="{{ asset('storage/js/pages/dashboard-admin.js') }}"></script>
@endpush
