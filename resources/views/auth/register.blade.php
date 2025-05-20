@extends('layouts.auth')

@section('title', 'Register | SPPOBN')
@section('content')
    <div id="auth">
        <div class="row h-100">
            <div class="col-lg-5 d-none d-lg-block">
                <div id="auth-right-register"></div>
            </div>
            <div class="col-lg-7 col-12">
                <div id="auth-left">
                    <div class="auth-logo">
                        <a href="#"><img src="{{ asset('storage/img/logo/logo-dashboard.png') }}" alt="Logo"></a>
                    </div>
                    <h1 class="auth-title">Daftar.</h1>
                    <p class="auth-subtitle mb-5">Masukkan data Anda untuk mendaftar.</p>
                    <x-alert />
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group position-relative has-icon-left mb-4">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control form-control-xl" placeholder="Username"
                                        name="username" id="username" value="{{ old('username') }}" required
                                        autocomplete="username" autofocus>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group position-relative has-icon-left mb-4">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control form-control-xl" placeholder="Email"
                                        name="email" id="email" value="{{ old('email') }}" required
                                        autocomplete="email">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group position-relative has-icon-left mb-4">
                                    <label for="nama_lengkap">Nama Lengkap</label>
                                    <input type="text" class="form-control form-control-xl" placeholder="Nama Lengkap"
                                        name="nama_lengkap" id="full_name" value="{{ old('nama_lengkap') }}" required
                                        autocomplete="name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group position-relative has-icon-left mb-4">
                                    <label for="no_telepon">Nomor Telepon</label>
                                    <input type="tel" class="form-control form-control-xl" placeholder="Nomor Telepon"
                                        name="no_telepon" id="phone_number" value="{{ old('no_telepon') }}" required
                                        autocomplete="tel">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group position-relative has-icon-left mb-4">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control form-control-xl" placeholder="Password"
                                        name="password" id="password" required autocomplete="new-password">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group position-relative has-icon-left mb-4">
                                    <label for="password_confirmation">Konfirmasi Password</label>
                                    <input type="password" class="form-control form-control-xl"
                                        placeholder="Konfirmasi Password" name="password_confirmation"
                                        id="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>
                        </div>

                        <div class="form-group position-relative has-icon-left mb-4">
                            <label for="alamat">Alamat</label>
                            <textarea class="form-control form-control-xl" placeholder="Alamat" name="alamat" id="address" required>{{ old('alamat') }}</textarea>
                        </div>
                        <button class="btn btn-primary btn-block btn-lg shadow-lg mt-5" type="submit">Daftar</button>
                    </form>
                    <div class="text-center mt-5 text-lg fs-4">
                        <p class="text-gray-600">Sudah punya akun? <a href="{{ route('login') }}" class="font-bold">Masuk
                                Sekarang</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
