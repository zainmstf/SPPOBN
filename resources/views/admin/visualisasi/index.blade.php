@extends('layouts.admin')

@section('title', 'Alur Pertanyaan | SPPOBN')
@section('title-menu', 'Visualisasi Alur Pertanyaan')
@section('subtitle-menu', 'Menampilkan Alur Pertanyaan')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Visualisasi Alur Pertanyaan</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi-diagram-3-fill"></i> Daftar Kategori Pertanyaan</h4>
                    </div>
                    <div class="card-body">
                        <x-alert />
                        <div class="row">
                            @foreach ($categories as $jenis => $category)
                                @php
                                    $icon = '';
                                    switch ($jenis) {
                                        case 'risiko_osteoporosis':
                                            $icon = 'bi-bandaid-fill text-primary';
                                            break;
                                        case 'asupan_nutrisi':
                                            $icon = 'bi-bag-heart-fill text-primary';
                                            break;
                                        case 'preferensi_makanan':
                                            $icon = 'bi-egg-fill text-primary';
                                            break;
                                        default:
                                            $icon = 'bi-graph-up text-primary'; // Icon default jika tidak ada yang cocok
                                            break;
                                    }
                                @endphp
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body d-flex flex-column">
                                            <i class="{{ $icon }} mb-2" style="font-size: 2em;"></i>
                                            <h3 class="h5 card-title">{{ ucwords(str_replace('_', ' ', $jenis)) }}</h3>
                                            <p class="card-text">Visualisasi alur pertanyaan untuk
                                                {{ ucwords(str_replace('_', ' ', $jenis)) }}</p>
                                            <div class="mt-auto">
                                                <a href="{{ route('admin.basisPengetahuan.visualisasi.show', $jenis) }}"
                                                    class="btn btn-primary">Lihat Visualisasi</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts-bottom')
@endpush
@push('css-top')
@endpush
