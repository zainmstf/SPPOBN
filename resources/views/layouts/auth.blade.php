<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Title -->
    <title>@yield('title', 'SPPOBN | Sistem Pakar Penanganan Osteoporosisi Berbasis Nutrisi')</title>

    <!-- Font & Icon -->
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" />
    <link rel="shortcut icon" href="{{ asset('storage/img/favicons/dashboard/favicon.ico') }}" type="image/x-icon" />


    <!-- Css Styling -->
    <link rel="stylesheet" href="{{ asset('storage/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/css/auth.css') }}">

</head>

<body>
    <!-- Content -->
    @yield('content')
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="{{ asset('storage/js/components/hide-alert.js') }}"></script>

</html>
