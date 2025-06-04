@extends('layouts.user')

@section('title', 'Sumber Nutrisi | SPPOBN')
@section('title-menu', 'Sumber Nutrisi')
@section('subtitle-menu',
    'Temukan berbagai sumber nutrisi penting dalam bentuk makanan maupun suplemen untuk mendukung
    kesehatan lansia.')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Sumber Nutrisi</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="mb-3">
            <a href="{{ route('edukasi.daftarMakanan') }}"
                class="btn btn-outline-primary {{ is_null(request('jenis')) ? 'active' : '' }}">Semua</a>
            <a href="{{ route('edukasi.daftarMakanan.byJenis', ['jenis' => 'makanan']) }}"
                class="btn btn-outline-primary {{ request('jenis') == 'makanan' ? 'active' : '' }}">Makanan</a>
            <a href="{{ route('edukasi.daftarMakanan.byJenis', ['jenis' => 'suplemen']) }}"
                class="btn btn-outline-primary {{ request('jenis') == 'suplemen' ? 'active' : '' }}">Suplemen</a>
        </div>

        <div class="row">
            @forelse ($sumberNutrisi as $item)
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        @if ($item->image)
                            <img src="{{ asset('storage/' . $item->image) }}" class="card-img-top"
                                alt="{{ $item->nama_sumber }}">
                        @else
                            <div class="placeholder-image"
                                style="height: 200px; background-color: #f0f0f0; display: flex; justify-content: center; align-items: center;">
                                <span class="text-muted">Tidak ada gambar</span>
                            </div>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $item->nama_sumber }}</h5>
                            <p class="card-text">
                                <strong>Nutrisi:</strong> {{ $item->rekomendasiNutrisi->nutrisi ?? '-' }} <br>
                                <strong>Takaran:</strong> {{ $item->takaran ?? '-' }} <br>
                                @if ($item->catatan)
                                    <strong>Catatan:</strong> {{ Str::limit(strip_tags($item->catatan), 100) }} <br>
                                @endif
                                @if ($item->rekomendasiNutrisi && $item->rekomendasiNutrisi->kontraindikasi)
                                    <strong>Kontraindikasi:</strong>
                                    {{ Str::limit(strip_tags($item->rekomendasiNutrisi->kontraindikasi), 100) }} <br>
                                @endif
                                @if ($item->rekomendasiNutrisi && $item->rekomendasiNutrisi->alternatif)
                                    <strong>Alternatif:</strong>
                                    {{ Str::limit(strip_tags($item->rekomendasiNutrisi->alternatif), 100) }}
                                @endif
                            </p>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <small class="text-muted">
                                <i class="bi-tag-fill me-1"></i> {{ ucfirst($item->jenis_sumber) }}
                            </small>
                            <small class="text-muted">
                                <i class="bi-calendar-event-fill me-1"></i>
                                {{ \Carbon\Carbon::parse($item->created_at)->locale('id')->isoFormat('D MMM Y') }}
                            </small>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-md-12">
                    <p>Tidak ada sumber nutrisi.</p>
                </div>
            @endforelse
        </div>

        @if ($sumberNutrisi->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $sumberNutrisi->links('components.paginate') }}
            </div>
        @endif
    </div>
@endsection
