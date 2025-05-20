<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Title -->
    <title>@yield('title', 'SPPOBN | Sistem Pakar Penanganan Osteoporosisi Berbasis Nutrisi')</title>

    <!-- Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&amp;family=Volkhov:wght@700&amp;display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" />


    <!-- Favicons -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('storage/img/favicons/favicon.ico') }}" />
    <meta name="theme-color" content="#ffffff" />

    <!--Stylesheets-->
    <link href="{{ asset('storage/css/landing.css') }}" rel="stylesheet" />
</head>

<body>
    <!-- Content -->
    @yield('content')

    <!-- Footer -->
    <div class="py-3 text-center">
        <img src="{{ asset('storage/img/logo/logo-dashboard.png') }}" alt="Logo" class="mb-2"
            style="max-width: 100px;">
        <p class="mb-0 text-secondary fs--1 fw-medium">
            © 2025 Made With &#9829;
        </p>
    </div>
    </main>

    <x-modal-helper />

    <!-- Javascript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/is_js/0.9.0/is.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script type="module">
        import {
            setupEdukasiModal
        } from '{{ asset('storage/js/components/modal-edukasi.js') }}';
        setupEdukasiModal();
    </script>
    <script src="{{ asset('storage/js/pages/landing.js') }}"></script>
</body>

</html>
