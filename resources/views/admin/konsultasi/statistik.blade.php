@extends('layouts.admin')

@section('title', 'Statistik Konsultasi')
@section('title-menu', 'Statistik Konsultasi')
@section('subtitle-menu', 'Menampilkan Data Statistik Konsultasi')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Statistik Konsultasi</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="row">
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon purple">
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
                                <div class="stats-icon blue">
                                    <i class="bi-people-fill mt-1"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Jumlah Pengguna Konsultasi</h6>
                                <h6 class="font-extrabold mb-0">{{ $jumlahPenggunaKonsultasi }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-6 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon green">
                                    <i class="bi-book-fill mt-1"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Rata-Rata Konsultasi Per Pengguna</h6>
                                <h6 class="font-extrabold mb-0"> {{ number_format($rataRataKonsultasiPerPengguna, 2) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi bi-bar-chart-line"></i> Statistik Konsultasi</h4>
                    </div>
                    <div class="card-body">
                        <x-alert />

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Jumlah Konsultasi 7 Hari Terakhir</h5>
                                        <div id="chart-konsultasi-perbulan"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Status Konsultasi</h5>
                                        <div id="chart-status-konsultasi"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Distribusi Waktu Konsultasi</h5>
                                        <div id="chart-distribusi-waktu"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Rekomendasi Nutrisi Paling Sering Diberikan</h5>
                                        <ul class="list-group">
                                            @forelse($RekomendasiPalingSeringDiajukan as $rekomendasi)
                                                <li class="list-group-item d-flex justify-content-between align-items-center"
                                                    data-bs-toggle="tooltip"
                                                    title="{{ $rekomendasi->kode . '-' . $rekomendasi->nama }}">
                                                    {{ strip_tags(Str::limit($rekomendasi->deskripsi, 100)) }}
                                                    <span
                                                        class="badge bg-primary rounded-pill">{{ $rekomendasi->total }}</span>
                                                </li>
                                            @empty
                                                <li class="list-group-item">Tidak ada rekomendasi yang sering diajukan.</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Distribusi Rating</h5>
                                        <div id="chart-distribusi-rating"></div>
                                    </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/4.3.0/apexcharts.min.js"></script>
    <script>
        var data = @json($konsultasiPerbulan);
        var categories = data.map(function(item) {
            // Ubah string tanggal ke objek Date
            var dateObj = new Date(item.tanggal);

            // Format: 25 Feb 2025
            return dateObj.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        });

        var totals = data.map(function(item) {
            return item.total;
        });
        var options = {
            chart: {
                type: 'area',
                height: 350
            },
            series: [{
                name: 'Jumlah Konsultasi',
                data: totals
            }],
            xaxis: {
                categories: categories
            }
        };

        // Render chart
        var chart = new ApexCharts(document.querySelector("#chart-konsultasi-perbulan"), options);
        chart.render();

        // Status Konsultasi Chart
        var statusKonsultasiOptions = {
            series: @json($statusKonsultasi->pluck('total')->toArray()),
            chart: {
                type: 'donut',
                height: 350
            },
            labels: @json($statusKonsultasi->pluck('status')->toArray()),
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            colors: ['#2E93fA', '#66DA26', '#FF9800'],
            legend: {
                position: 'right'
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " Konsultasi"
                    }
                }
            }
        };
        var statusKonsultasiChart = new ApexCharts(document.querySelector("#chart-status-konsultasi"),
            statusKonsultasiOptions);
        statusKonsultasiChart.render();

        // Distribusi Waktu Konsultasi Chart
        var distribusiWaktuOptions = {
            series: [{
                name: 'Jumlah Konsultasi',
                data: @json($distribusiWaktuKonsultasi->pluck('total')->toArray())
            }],
            chart: {
                type: 'bar',
                height: 350
            },
            xaxis: {
                categories: @json(
                    $distribusiWaktuKonsultasi->pluck('jam')->map(function ($jam) {
                        return sprintf('%02d:00', $jam);
                    })),
                title: {
                    text: 'Jam'
                }
            },
            yaxis: {
                title: {
                    text: 'Jumlah Konsultasi'
                }
            },
            colors: ['#775DD0'],
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " Konsultasi"
                    }
                }
            }
        };
        var distribusiWaktuChart = new ApexCharts(document.querySelector("#chart-distribusi-waktu"),
            distribusiWaktuOptions);
        distribusiWaktuChart.render();

        // Distribusi Rating Chart
        var distribusiRatingOptions = {
            series: [{
                name: 'Jumlah Umpan Balik',
                data: @json($distribusiRating->pluck('total')->toArray())
            }],
            chart: {
                type: 'bar',
                height: 350
            },
            xaxis: {
                categories: @json($distribusiRating->pluck('rating')->toArray()),
                title: {
                    text: 'Rating'
                }
            },
            yaxis: {
                title: {
                    text: 'Jumlah Umpan Balik'
                }
            },
            colors: ['#AD455C'],
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " Umpan Balik"
                    }
                }
            }
        };
        var distribusiRatingChart = new ApexCharts(document.querySelector("#chart-distribusi-rating"),
            distribusiRatingOptions);
        distribusiRatingChart.render();

        // Helper function untuk format angka dengan leading zero
        function sprintf(format) {
            for (var i = 1; i < arguments.length; i++) {
                format = format.replace(/%s/, arguments[i]);
            }
            return format;
        }
    </script>
    <script>
        // Aktifkan tooltip untuk elemen yang memiliki data-bs-toggle="tooltip"
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
@endpush
@push('css-top')
    <style>
        .tooltip-inner {
            max-width: 400px;
            /* Atur sesuai kebutuhan, default-nya 200px */
            white-space: pre-wrap;
            /* Agar teks bisa multi-baris */
            text-align: left;
            /* Opsional, agar teks rata kiri */
        }
    </style>
@endpush
