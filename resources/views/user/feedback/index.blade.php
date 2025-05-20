@extends('layouts.user')
@section('title', 'Umpan Balik | SPPOBN')
@section('title-menu', 'Umpan Balik')
@section('subtitle-menu', 'Berikan umpan balik Anda. Agar sistem lebih baik lagi.')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Umpan Balik</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Formulir Umpan Balik</h4>
                </div>
                <div class="card-body">
                    <x-alert />
                    <form class="row g-3 align-items-center w-lg-75 mx-auto" action="{{ route('feedback.store') }}"
                        method="POST">
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
                                    <svg class="star-icon" data-rating="3" xmlns="http://www.w3.org/2000/svg"
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
                            <div class="input-group-icon">
                                <textarea class="form-control form-little-squirrel-control" name="pesan" id="pesanInput" placeholder="Masukkan Pesan"
                                    rows="6"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-12 d-flex justify-content-between">
                            <button class="btn btn-primary orange-gradient-btn fs--1" type="submit">
                                Kirim Pesan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
@push('scripts-bottom')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const stars = document.querySelectorAll(".star-rating svg");
            const ratingInput = document.getElementById("ratingInput");

            stars.forEach((star) => {
                star.setAttribute("role", "button");
                star.setAttribute("tabindex", "0");
                star.addEventListener("click", function() {
                    const ratingValue = parseInt(this.dataset.rating);
                    ratingInput.value = ratingValue;

                    stars.forEach((s) => {
                        const starRating = parseInt(s.dataset.rating);
                        if (starRating <= ratingValue) {
                            s.setAttribute("fill", "currentColor");
                        } else {
                            s.setAttribute("fill", "none");
                        }
                    });
                });
            });
        });
    </script>
@endpush
