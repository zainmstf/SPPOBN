@extends('layouts.admin')

@section('title', 'Manajemen Umpan Balik')
@section('title-menu', 'Data Umpan Balik Pengguna')
@section('subtitle-menu', 'Menampilkan dan Mengelola Umpan Balik dari Pengguna')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Umpan Balik</li>
@endsection

@section('content')
    <x-page-header />

    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi bi-chat-square-text"></i> Data Umpan Balik</h4>
                    </div>
                    <div class="card-body">
                        <x-alert />
                        <div class="table-responsive">
                            <table id="feedbackTable" class="table table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Pengguna</th>
                                        <th>Konsultasi</th>
                                        <th>Komentar</th>
                                        <th>Rating</th>
                                        <th>Tanggal Dibuat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($feedbacks as $feedback)
                                        <tr>
                                            <td>{{ $feedback->id }}</td>
                                            <td>{{ $feedback->user->nama_lengkap ?? 'Anonim' }}</td>
                                            <td>{{ $feedback->konsultasi_id ? '#' . $feedback->konsultasi_id : 'Bukan Dari Konsultasi' }}
                                            </td>
                                            <td>{{ Str::limit($feedback->pesan, 50) }}</td>
                                            <td>{{ $feedback->rating }}</td>
                                            <td>{{ $feedback->created_at }}</td>
                                            <td>
                                                <a href="{{ route('admin.feedback.show', $feedback->id) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class=" bi-eye"></i> Detail
                                                </a>
                                                <form action="{{ route('admin.feedback.destroy', $feedback->id) }}"
                                                    method="POST" style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus umpan balik ini?')">
                                                        <i class=" bi-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts-bottom')
    <script src="https://cdn.datatables.net/v/bs5/dt-1.13.10/datatables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#feedbackTable').DataTable({
                language: {
                    processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i> <span class="sr-only">Loading...</span>'
                },
                order: [
                    [5, 'desc']
                ]
            });
        });
    </script>
@endpush

@push('css-top')
    <link href="https://cdn.datatables.net/v/bs5/dt-1.13.10/datatables.min.css" rel="stylesheet">
@endpush
