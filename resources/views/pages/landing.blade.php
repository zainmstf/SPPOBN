@extends('layouts.guest')
@section('title', 'Landing Page | SPPOBN')
@section('content')
<!-- Nav Content-->
<nav class="navbar navbar-expand-lg navbar-light fixed-top py-5 d-block" data-navbar-on-scroll="data-navbar-on-scroll">
    <div class="container">
        <a class="navbar-brand" href="#"><img src="{{ asset('storage/img/logo/logo-landing.png') }}" height="75"
                alt="logo" /></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"> </span>
        </button>
        <div class="collapse navbar-collapse border-top border-lg-0 mt-4 mt-lg-0" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto pt-2 pt-lg-0 font-base align-items-lg-center align-items-start">
                <li class="nav-item px-3 px-xl-4">
                    <a class="nav-link fw-medium" aria-current="page" href="#page-top">Beranda</a>
                </li>
                <li class="nav-item px-3 px-xl-4">
                    <a class="nav-link fw-medium" aria-current="page" href="#service">Fitur</a>
                </li>
                <li class="nav-item px-3 px-xl-4">
                    <a class="nav-link fw-medium" aria-current="page" href="#education">Edukasi</a>
                </li>
                <li class="nav-item px-3 px-xl-4">
                    <a class="nav-link fw-medium" aria-current="page" href="#helper">Bantuan</a>
                </li>
                <li class="nav-item px-3 px-xl-4">
                    <a class="nav-link fw-medium" aria-current="page" href="#testimonial">Testimoni</a>
                </li>
                <li class="nav-item px-3 px-xl-4">
                    @auth
                    <form method="GET" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger orange-gradient-btn fs--1">Logout</button>
                    </form>
                    @else
                    <a class="btn btn-danger orange-gradient-btn fs--1" aria-current="page"
                        href="{{ route('login') }}">Login</a>
                    @endauth
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- Content -->
<section style="padding-top: 7rem" id="page-top">
    <div class="bg-holder" height="50">
    </div>

    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-5 col-lg-6 order-0 order-md-1 text-end">
                <img class="pt-7 pt-md-0 hero-img" src="{{ asset('storage/img/hero/hero-img.png') }}" alt="hero-header"
                    style="filter: drop-shadow(3px 3px 5px rgba(34, 34, 34, 0.3));" />
            </div>
            <div class="col-md-7 col-lg-6 text-md-start text-center py-6">
                <h1 class="hero-title">
                    Lawan Osteoporosis dengan Nutrisi Alami.
                </h1>
                <p class="mb-4 fw-medium">
                    Sistem pakar penanganan osteoporosis berbasis nutrisi (SPPOBN) akan membantu Anda menemukan
                    rekomendasi
                    nutrisi yang tepat untuk menjaga kesehatan tulang Anda.
                </p>
                <div class="text-center text-md-start">
                    <a class="btn btn-primary btn-lg me-md-4 mb-3 mb-md-0 border-0 primary-btn-shadow"
                        href="{{ route('login') }}" role="button">
                        Mulai Konsultasi Sekarang
                    </a>
                    <div class="w-100 d-block d-md-none"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Feature -->
<section class="pt-5 pt-md-9" id="service">
    <div class="container">
        <div class="position-absolute z-index--1 end-0 d-none d-lg-block">
            <img src="{{ asset('storage/img/category/shape.svg') }}" style="max-width: 200px" alt="service" />
        </div>
        <div class="mb-7 text-center">
            <h5 class="text-secondary">CATEGORY</h5>
            <h3 class="fs-xl-10 fs-lg-8 fs-7 fw-bold font-cursive text-capitalize">
                Fitur Sistem
            </h3>
            <p class="mt-3">
                Jelajahi berbagai fitur yang dirancang untuk membantu Anda mengelola dan meningkatkan kesehatan tulang.
            </p>
        </div>
        <div class="row">
            <div class="col-lg-3 col-sm-6 mb-6">
                <div class="card service-card shadow-hover rounded-3 text-center align-items-center">
                    <div class="card-body p-xxl-5 p-4">
                        <img src="{{ asset('storage/img/category/icon1.png') }}" width="75" alt="Service"
                            class="pb-3" />
                        <h4 class="mb-3">Rekomendasi Nutrisi</h4>
                        <p class="mb-0 fw-medium">
                            Dapatkan rekomendasi nutrisi, sesuai dengan kebutuhan Anda.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-6">
                <div class="card service-card shadow-hover rounded-3 text-center align-items-center">
                    <div class="card-body p-xxl-5 p-4">
                        <img src="{{ asset('storage/img/category/icon2.png') }}" width="75" alt="Service"
                            class="pb-3" />
                        <h4 class="mb-3">Konsultasi Mudah & Cepat
                        </h4>
                        <p class="mb-0 fw-medium">
                            Tak perlu repot ke klinik! Konsultasikan masalah osteoporosis Anda secara online, mudah,
                            dan cepat
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-6">
                <div class="card service-card shadow-hover rounded-3 text-center align-items-center">
                    <div class="card-body p-xxl-5 p-4">
                        <img src="{{ asset('storage/img/category/icon3.png') }}" width="75" alt="Service"
                            class="pb-3" />
                        <h4 class="mb-3">Informasi Akurat & Terpercaya
                        </h4>
                        <p class="mb-0 fw-medium">
                            Sistem pakar ini dibuat dari sumber penelitian ilmiah terkini dan rekomendasi ahli gizi
                            terpercaya.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-6">
                <div class="card service-card shadow-hover rounded-3 text-center align-items-center">
                    <div class="card-body p-xxl-5 p-4">
                        <img src="{{ asset('storage/img/category/icon4.png') }}" width="75" alt="Service"
                            class="pb-3" />
                        <h4 class="mb-3">Pantau Riwayat & Raih Hasil</h4>
                        <p class="mb-0 fw-medium">
                            Pantau riwayat konsultasi kesehatan tulang Anda dari waktu ke waktu!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Education -->
<section class="pt-5" id="education">
    <div class="mb-7 text-center">
        <h5 class="text-secondary">Edukasi</h5>
        <h3 class="fs-xl-10 fs-lg-8 fs-7 fw-bold font-cursive text-capitalize">
            Apa Itu Osteoporosis?
        </h3>
        <p class="mt-3">
            Tulang rapuh? Itu Osteoporosis! Pelajari cara menjaga tulangmu tetap kuat dan sehat.
        </p>
    </div>
    <div class="container">
        <ul class="nav nav-tabs pb-4" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="videos-tab" data-bs-toggle="tab" data-bs-target="#videos"
                    type="button" role="tab" aria-controls="videos" aria-selected="true">Video</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="articles-tab" data-bs-toggle="tab" data-bs-target="#articles"
                    type="button" role="tab" aria-controls="articles" aria-selected="false">Artikel</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="infographics-tab" data-bs-toggle="tab" data-bs-target="#infografis"
                    type="button" role="tab" aria-controls="infografis"
                    aria-selected="false">Infografis</button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            {{-- Video Tab --}}
            <div class="tab-pane fade show active" id="videos" role="tabpanel" aria-labelledby="videos-tab">
                <div class="row">
                    @forelse ($kontenEdukasi['video'] ?? [] as $video)
                    <div class="col-md-4 mb-4">
                        <div class="card overflow-hidden shadow">
                            @php
                            $videoId = '';
                            if (preg_match('/embed\/([a-zA-Z0-9_-]+)/', $video->path, $matches)) {
                            $videoId = $matches[1];
                            }
                            @endphp
                            <div class="card-image-container video-thumbnail" data-bs-toggle="modal"
                                data-bs-target="#edukasiModal" data-konten-id="{{ $video->id }}">
                                <img src="{{ $videoId ? 'https://img.youtube.com/vi/' . $videoId . '/maxresdefault.jpg' : asset('assets/images/placeholder-video.jpg') }}"
                                    class="card-img-top" alt="{{ $video->judul }}">
                            </div>
                            <div class="card-body py-4 px-3" style="height: 200px">
                                <h4 class="text-secondary fw-medium">
                                    <a href="#!" class="link-900 text-decoration-none"
                                        data-bs-toggle="modal" data-bs-target="#edukasiModal"
                                        data-konten-id="{{ $video->id }}">
                                        {{ Str::limit($video->judul, 50) }}
                                    </a>
                                </h4>
                                <p class="fs-0 fw-medium">{{ Str::limit(strip_tags($video->deskripsi), 100) }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center">
                        <p class="text-muted">Belum ada video edukasi tersedia.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Artikel Tab --}}
            <div class="tab-pane fade" id="articles" role="tabpanel" aria-labelledby="articles-tab">
                <div class="row">
                    @forelse ($kontenEdukasi['artikel'] ?? [] as $article)
                    <div class="col-md-4 mb-4">
                        <div class="card overflow-hidden shadow">
                            <div class="card-image-container artikel-thumbnail pointer" data-bs-toggle="modal"
                                data-bs-target="#edukasiModal" data-konten-id="{{ $article->id }}">
                                @if ($article->path)
                                <img src="{{ asset('storage/' . $article->path) }}"
                                    class="card-img-top pointer" alt="{{ $article->judul }}">
                                @else
                                <div class="card-body d-flex align-items-center">
                                    <i class="bi bi-file-text text-primary me-3"
                                        style="font-size: 2.5rem;"></i>
                                </div>
                                @endif
                            </div>
                            <div class="card-body py-4 px-3" style="height: 200px">
                                <h4 class="text-secondary fw-medium">
                                    <a href="#" class="link-900 text-decoration-none"
                                        data-bs-toggle="modal" data-bs-target="#edukasiModal"
                                        data-konten-id="{{ $article->id }}">
                                        {{ $article->judul }}
                                    </a>
                                </h4>
                                <p class="fs-0 fw-medium">{{ Str::limit(strip_tags($article->deskripsi), 100) }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center">
                        <p class="text-muted">Belum ada artikel tersedia.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Infografis Tab --}}
            <div class="tab-pane fade" id="infografis" role="tabpanel" aria-labelledby="infografis-tab">
                <div class="row">
                    @php $infografis = $kontenEdukasi['infografis'] ?? []; @endphp
                    @forelse ($infografis as $info)
                    <div class="col-md-4 mb-4">
                        <div class="card overflow-hidden shadow">
                            <div class="card-image-container artikel-thumbnail pointer" data-bs-toggle="modal"
                                data-bs-target="#edukasiModal" data-konten-id="{{ $info->id }}">
                                @if ($info->path)
                                <img src="{{ asset('storage/' . $info->path) }}" class="card-img-top pointer"
                                    alt="{{ $info->judul }}">
                                @else
                                <div class="card-body text-center">
                                    <i class="bi bi-file-earmark-image fs-1 text-primary"></i>
                                </div>
                                @endif
                            </div>
                            <div class="card-body py-4 px-3" style="height: 200px">
                                <h4 class="text-secondary fw-medium">
                                    <a href="#" class="link-900 text-decoration-none"
                                        data-bs-toggle="modal" data-bs-target="#edukasiModal"
                                        data-konten-id="{{ $info->id }}">
                                        {{ $info->judul }}
                                    </a>
                                </h4>
                                <p class="fs-0 fw-medium">{{ Str::limit(strip_tags($info->deskripsi), 100) }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center">
                        <p class="text-muted">Belum ada infografis tersedia.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <x-modal-edukasi />
</section>

<!-- Section Helper -->
<section id="helper">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="mb-4 text-start">
                    <h5 class="text-secondary">Bantuan</h5>
                    <h3 class="fs-xl-10 fs-lg-8 fs-7 fw-bold font-cursive text-capitalize">
                        Cara Penggunaan Sistem
                    </h3>
                </div>
                <div class="d-flex align-items-start mb-5">
                    <div class="bg-primary me-sm-4 me-3 p-3" style="border-radius: 13px">
                        <img src="{{ asset('storage/img/steps/selection.svg') }}" width="22" alt="steps" />
                    </div>
                    <div class="flex-1">
                        <h5 class="text-secondary fw-bold fs-0">
                            Buka Website
                        </h5>
                        <p>
                            Ketikkan alamat website sistem pakar melalui perangkat yang Anda miliki, baik komputer
                            maupun smartphone
                        </p>
                    </div>
                </div>
                <div class="d-flex align-items-start mb-5">
                    <div class="bg-danger me-sm-4 me-3 p-3" style="border-radius: 13px">
                        <img src="{{ asset('storage/img/steps/water-sport.svg') }}" width="22" alt="steps" />
                    </div>
                    <div class="flex-1">
                        <h5 class="text-secondary fw-bold fs-0">Mulai Konsultasi</h5>
                        <p>
                            Klik “Mulai Konsultasi” untuk menuju login user atau lewat button login diatas.
                            Setelah berhasil login akan diarahkan ke dashboard user.
                        </p>
                    </div>
                </div>
                <div class="d-flex align-items-start mb-5">
                    <div class="bg-info me-sm-4 me-3 p-3" style="border-radius: 13px">
                        <img src="{{ asset('storage/img/steps/taxi.svg') }}" width="22" alt="steps" />
                    </div>
                    <div class="flex-1">
                        <h5 class="text-secondary fw-bold fs-0">
                            Pilih menu konsultasi untuk menuju halaman konsultasi
                        </h5>
                        <p>
                            Sistem akan menampilkan serangkaian pertanyaan terstruktur yang perlu Anda jawab, sistem
                            akan
                            menganalisis data dan menampilkan hasil rekomendasi
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 d-flex justify-content-center align-items-start">
                <div class="card position-relative" style="max-width: 370px">
                    <div class="position-absolute z-index--1 me-10 me-xxl-0" style="right: -160px; top: -210px">
                        <img src="{{ asset('storage/img/steps/bg.png') }}" style="max-width: 550px"
                            alt="shape" />
                    </div>
                    <div class="card-body p-3 shadow">
                        <a href="#!" data-bs-toggle="modal" data-bs-target="#helperModal"><img
                                class=" mb-4 mt-2 rounded-2 w-100" src="{{ asset('storage/img/icons/helper.jpg') }}"
                                alt="booking"></a>
                        <div>
                            <h5 class="fw-medium">Panduan Penggunaan Sistem</h5>
                            <p class="fs--1 mb-3 fw-medium">Klik gambar untuk temukan langkah-langkah mudah untuk
                                menggunakan
                                sistem. Mulai
                                dari registrasi hingga hasil rekomendasi nutrisi.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end of .container-->
</section>

<!-- Section Testimonials -->
<section id="testimonial">
    <div class="container">
        <div class="row">
            <div class="col-lg-5">
                <div class="mb-8 text-start">
                    <h5 class="text-secondary">Testimoni</h5>
                    <h3 class="fs-xl-10 fs-lg-8 fs-7 fw-bold font-cursive text-capitalize">
                        Apa yang orang katakan tentang sistem ini.
                    </h3>
                    <x-alert />
                </div>
            </div>
            <div class="col-lg-1"></div>
            <div class="col-lg-6">
                <div class="pe-7 ps-5 ps-lg-0">
                    @if ($feedback->isEmpty())
                    <p class="text-center">Belum ada umpan balik saat ini.</p>
                    @else
                    <div class="carousel slide carousel-fade position-static" id="testimonialIndicator"
                        data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            @foreach ($feedback as $index => $item)
                            <button class="{{ $index === 0 ? 'active' : '' }}" type="button"
                                data-bs-target="#testimonialIndicator" data-bs-slide-to="{{ $index }}"
                                aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                                aria-label="Testimonial {{ $index }}">
                            </button>
                            @endforeach
                        </div>
                        <div class="carousel-inner">
                            @foreach ($feedback as $testimoni)
                            <div class="carousel-item position-relative @if ($loop->first) active @endif"
                                data-bs-interval="5000">
                                <div class="card shadow" style="border-radius: 10px">
                                    <div class="position-absolute start-0 top-0 translate-middle">
                                        <img class="rounded-circle fit-cover"
                                            src="{{ $testimoni->user && $testimoni->user->foto ? asset('storage/img/testimonial/' . $testimoni->user->foto) : asset('storage/img/testimonial/author.png') }}"
                                            height="50" width="50" alt="Foto Pengguna" />
                                    </div>
                                    <div class="card-body p-4">
                                        <p class="fw-medium mb-4 scrollable-paragraph"
                                            style="max-height: 100px; overflow: hidden;">
                                            &quot;{{ Str::limit($testimoni->pesan ?? 'Tidak ada komentar yang diberikan.', 100, '...') }}&quot;
                                        </p>
                                        <div class="star-rating mb-2">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($testimoni->rating && $i <= $testimoni->rating)
                                                    <i class="fas fa-star"></i>
                                                    @else
                                                    <i class="far fa-star"></i>
                                                    @endif
                                                    @endfor
                                        </div>
                                        <h5 class="text-secondary">
                                            {{ $testimoni->user->nama_lengkap ?? 'Anonim' }}
                                        </h5>
                                        <p class="fw-medium fs--1 mb-0">
                                            {{ $testimoni->user->alamat ?? 'Alamat tidak tersedia' }}
                                        </p>
                                    </div>
                                    <div class="card shadow-sm position-absolute top-0 z-index--1 mb-3 w-100 h-100"
                                        style="border-radius: 10px; transform: translate(25px, 25px);"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="carousel-navigation d-flex flex-column flex-between-center position-absolute end-0 top-lg-50 bottom-0 translate-middle-y z-index-1 me-3 me-lg-0"
                            style="height: 60px; width: 20px">
                            <button class="carousel-control-prev position-static" type="button"
                                data-bs-target="#testimonialIndicator" data-bs-slide="prev">
                                <img src="{{ asset('storage/img/icons/up.svg') }}" width="16"
                                    alt="icon" />
                            </button>
                            <button class="carousel-control-next position-static" type="button"
                                data-bs-target="#testimonialIndicator" data-bs-slide="next">
                                <img src="{{ asset('storage/img/icons/down.svg') }}" width="16"
                                    alt="icon" />
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Section CTA -->

<div class="position-relative pt-9 pt-lg-8 pb-6 pb-lg-8 bg-secondary">
    <div class="container px-5">
        <h2 class="text-white display-1 lh-1 mb-5">
            Dapatkan panduan nutrisi,
            <br>
            segera!
        </h2>
        <a class="btn btn-outline-light py-3 px-4 rounded-pill " href="{{ route('login') }}">klik
            di
            sini!</a>
    </div>
</div>

<!-- Section Feedback -->
<section class="pt-6" id="sendFeedback">
    <div class="container">
        <div class="py-8 px-5 position-relative text-center"
            style="background-color: rgba(223, 215, 249, 0.199);border-radius: 129px 20px 20px 20px;">
            <div class="position-absolute start-100 top-0 translate-middle ms-md-n3 ms-n4 mt-3">
                <img src="{{ asset('storage/img/cta/send.png') }}" style="max-width: 70px" alt="send icon" />
            </div>
            <div class="position-absolute end-0 top-0 z-index--1">
                <img src="{{ asset('storage/img/cta/shape-bg2.png') }}" width="264" alt="cta shape" />
            </div>
            <div class="position-absolute start-0 bottom-0 ms-3 z-index--1 d-none d-sm-block">
                <img src="{{ asset('storage/img/cta/shape-bg1.png') }}" style="max-width: 340px" alt="cta shape" />
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <h2 class="text-secondary lh-1-7 mb-4">
                        Kami sangat menghargai umpan balik Anda untuk meningkatkan kualitas sistem kami
                    </h2>
                    <form class="row g-3 align-items-center w-lg-75 mx-auto"
                        action="{{ route('landing.feedback.store') }}" method="POST">
                        @csrf
                        <div class="col-sm-12">
                            <div class="input-group-icon">
                                <div class="star-rating mb-2 d-flex align-items-center justify-content-center">
                                    <svg class="star-icon" data-rating="1" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <polygon
                                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                    </svg>
                                    <svg class="star-icon" data-rating="2" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <polygon
                                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                    </svg>
                                    <svg class="star-icon" data-rating="3" xmlns="http://www.w3.000/svg"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <polygon
                                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                    </svg>
                                    <svg class="star-icon" data-rating="4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <polygon
                                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                    </svg>
                                    <svg class="star-icon" data-rating="5" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <polygon
                                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                    </svg>
                                    <input type="hidden" name="rating" id="ratingInput" value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <label for="pesanInput" class="form-label">Pesan / Komentar</label>
                            <textarea class="form-control form-little-squirrel-control" name="pesan" id="pesanInput"
                                placeholder="Masukkan komentar atau testimoni Anda..." rows="5">{{ old('pesan') }}</textarea>
                            @error('pesan')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-sm-12 d-flex justify-content-end">
                            <button class="btn btn-danger orange-gradient-btn fs--1" type="submit">
                                Kirim Umpan Balik
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection