@extends('layouts.user')
@section('title', 'Grafik Perkembangan | SPPOBN')
@section('title-menu', 'Grafik Perkembangan')
@section('subtitle-menu', 'Lihat perkembangan konsultasi Anda.')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Grafik Perkembangan</li>
@endsection
@section('content')
    <x-page-header />
    <div id="chart-data" style="display: none;">
        <span data-chart="{{ $chartDataJson }}"></span>
        <span data-rating-labels="{{ $ratingData['ratingLabelsJson'] }}"></span>
        <span data-rating-data="{{ $ratingData['ratingDataJson'] }}"></span>
        <span data-question-labels="{{ $questionData['questionLabelsJson'] }}"></span>
        <span data-question-data="{{ $questionData['questionDataJson'] }}"></span>
    </div>
    <div class="page-content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Grafik Konsultasi Satu Bulan Terakhir</h4>
                    </div>
                    <div class="card-body">
                        <div id="consultationChart"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Grafik Pertanyaan yang Sering Dijawab</h4>
                            </div>
                            <div class="card-body">
                                <div id="questionChart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Grafik Rating Umpan Balik</h4>
                    </div>
                    <div class="card-body">
                        <div id="ratingChart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts-bottom')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/4.3.0/apexcharts.min.js"></script>
    <script src="{{ asset('storage/js/pages/grafik-perkembangan.js') }}"></script>
@endpush
