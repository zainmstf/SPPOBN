<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Title -->
    <title>@yield('title', 'SPPOBN | Sistem Pakar Penanganan Osteoporosisi Berbasis Nutrisi')</title>

    <!-- Font & Icon -->
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('storage/img/favicons/dashboard/favicon.ico') }}" type="image/x-icon" />

    <!-- Css Styling -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/perfect-scrollbar/1.5.6/css/perfect-scrollbar.css" />
    <style>
        #sidebar .sidebar-wrapper {
            overflow-y: auto;
            /* Aktifkan scrolling jika konten melebihi tinggi */
            scrollbar-width: none;
            /* Untuk Firefox */
            -ms-overflow-style: none;
            /* Untuk Internet Explorer dan Edge */
        }

        #sidebar .sidebar-wrapper::-webkit-scrollbar {
            display: none;
            /* Untuk Chrome, Safari, dan Opera */
        }
    </style>
    <link rel="stylesheet" href="{{ asset('storage/css/app.css') }}" />
    @stack('css-top')
</head>

<body>
    <div id="app">
        @include('layouts.partials.user.sidebar')
        <div id="main">
            @yield('content')
            @include('layouts.partials.user.footer')
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/perfect-scrollbar/1.5.6/perfect-scrollbar.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="{{ asset('storage/js/main.js') }}"></script>
    @stack('scripts-bottom')
</body>

</html>
