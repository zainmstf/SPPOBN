<!-- File: resources/views/profile/show.blade.php -->

@extends('layouts.user')

@section('title', 'Informasi Pengguna | SPPOBN')
@section('title-menu', 'Informasi Pengguna')
@section('subtitle-menu', 'Lihat Informasi Pengguna Anda.')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Informasi Pengguna</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Detail Informasi Pengguna</h4>
                </div>
                <div class="card-body">
                    <x-alert />

                    <form action="{{ route('profile.update') }}" method="post">
                        @csrf
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username"
                                value="{{ $user->username }}" required disabled>
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama"
                                value="{{ $user->nama_lengkap }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ $user->email }}" required disabled>
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" required>{{ $user->alamat }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="no_telp" class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control" id="no_telp" name="no_telp"
                                value="{{ $user->no_telepon }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="{{ route('profile.pengaturan') }}" class="btn btn-secondary">Pengaturan Akun</a>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
