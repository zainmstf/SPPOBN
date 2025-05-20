@extends('layouts.auth')

@section('title', 'Login | SPPOBN')
@section('content')
    <div id="auth">
        <div class="row h-100">
            <div class="col-lg-5 col-12">
                <div id="auth-left">
                    <div class="auth-logo">
                        <a href="#"><img src="{{ asset('storage/img/logo/logo-dashboard.png') }}" alt="Logo"></a>
                    </div>
                    <h1 class="auth-title">Masuk.</h1>
                    <p class="auth-subtitle mb-5">Masuk dengan data Anda yang Anda masukkan saat pendaftaran.</p>
                    <x-alert />
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group position-relative has-icon-left mb-4">
                            <label for="username">Username</label>
                            <input type="text" class="form-control form-control-xl" placeholder="Username"
                                name="username" id="username" required autocomplete="username" autofocus>
                        </div>

                        <div class="form-group position-relative has-icon-left mb-4">
                            <label for="password">Password</label>
                            <input type="password" class="form-control form-control-xl" placeholder="Password"
                                name="password" id="password" required autocomplete="current-password">
                        </div>

                        <div class="form-check form-check-lg d-flex align-items-end">
                            <input class="form-check-input me-2" type="checkbox" name="remember" id="remember"
                                {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label text-gray-600" for="remember">
                                Ingat Saya
                            </label>
                        </div>

                        <button class="btn btn-primary btn-block btn-lg shadow-lg mt-5" type="submit">Masuk</button>
                    </form>
                    <div class="text-center mt-5 text-lg fs-4">
                        <p class="text-gray-600">Belum mempunyai akun ?
                            <a href="{{ route('register') }}" class="font-bold">Daftar Sekarang</a>.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 d-none d-lg-block">
                <div id="auth-right">
                </div>
            </div>
        </div>
    </div>
@endsection
