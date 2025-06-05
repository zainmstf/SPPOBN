@extends('layouts.admin')
@section('title', 'Edukasi Nutrisi Lansia | SPPOBN')
@section('title-menu', 'Edukasi Nutrisi Lansia')
@section('subtitle-menu',
    'Temukan berbagai informasi penting tentang nutrisi yang tepat
    untuk lansia.')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edukasi Nutrisi Lansia</li>
@endsection

@section('content')
    <x-page-header />
    <x-alert />
    <div class="page-content">
        <div class="mb-3">
            <a href="{{ route('admin.konten-edukasi.index') }}"
                class="btn btn-outline-primary {{ is_null($jenis) ? 'active' : '' }}">Semua</a>
            <a href="{{ route('admin.konten-edukasi.index', ['jenis' => 'artikel']) }}"
                class="btn btn-outline-primary {{ $jenis == 'artikel' ? 'active' : '' }}">Artikel</a>
            <a href="{{ route('admin.konten-edukasi.index', ['jenis' => 'video']) }}"
                class="btn btn-outline-primary {{ $jenis == 'video' ? 'active' : '' }}">Video</a>
            <a href="{{ route('admin.konten-edukasi.index', ['jenis' => 'infografis']) }}"
                class="btn btn-outline-primary {{ $jenis == 'infografis' ? 'active' : '' }}">Infografis</a>
        </div>

        <div class="row">
            @forelse ($kontenEdukasi as $konten)
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        @if ($konten->jenis == 'video')
                            <div class="video-thumbnail pointer" data-bs-toggle="modal" data-bs-target="#edukasiModal"
                                data-konten-id="{{ $konten->id }}">
                                @php
                                    $videoId = '';
                                    if (preg_match('/embed\/([a-zA-Z0-9_-]+)/', $konten->path, $matches)) {
                                        $videoId = $matches[1];
                                    }
                                @endphp
                                <img src="{{ $videoId ? 'https://img.youtube.com/vi/' . $videoId . '/maxresdefault.jpg' : asset('assets/images/placeholder-video.jpg') }}"
                                    class="card-img-top" alt="{{ $konten->judul }}">
                            </div>
                        @else
                            <div class="artikel-thumbnail pointer" data-bs-toggle="modal" data-bs-target="#edukasiModal"
                                data-konten-id="{{ $konten->id }}">
                                @if ($konten->thumbnail || $konten->path)
                                    @php
                                        $imageSrc = $konten->thumbnail
                                            ? asset('storage/' . $konten->thumbnail)
                                            : asset('storage/' . $konten->path);
                                    @endphp
                                    <img src="{{ $imageSrc }}" class="card-img-top" alt="{{ $konten->judul }}">
                                @else
                                    <div class="card-body" style="height: 272px">
                                        <i class="bi bi-file-text fs-1 text-primary"></i>
                                    </div>
                                @endif
                            </div>
                        @endif
                        <div class="card-body pointer" data-bs-toggle="modal" data-bs-target="#edukasiModal"
                            data-konten-id="{{ $konten->id }}">
                            <h5 class="card-title">{{ $konten->judul }}</h5>
                            <p class="card-text">{{ Str::limit(strip_tags($konten->deskripsi), 100) }}</p>
                        </div>
                        <div class="d-flex justify-content-end me-3 mb-2 gap-2">
                            <a href="{{ route('admin.konten-edukasi.edit', $konten->id) }}"
                                class="btn btn-sm btn-outline-warning">
                                <i class="bi-pencil"></i> Edit
                            </a>
                            <form action="{{ route('admin.konten-edukasi.destroy', $konten->id) }}" method="POST"
                                class="d-inline delete-form" data-konten-title="{{ $konten->judul }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete">
                                    <i class="bi-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <small class="text-muted">
                                <span class="me-2"><i class="bi-tag-fill"></i> {{ ucfirst($konten->jenis) }}</span>
                            </small>
                            <small class="text-muted">
                                <span><i class="bi-calendar-event-fill"></i>
                                    {{ $konten->created_at->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                            </small>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        Belum ada konten edukasi yang tersedia.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
    @if ($kontenEdukasi->hasPages())
        <div class="d-flex justify-content-end mt-4">
            {{ $kontenEdukasi->links('components.paginate') }}
        </div>
    @endif

    <x-modal-edukasi />
@endsection

@push('scripts-bottom')
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script type="module">
        import {
            setupEdukasiModal
        } from '{{ asset('storage/js/components/modal-edukasi.js') }}';
        setupEdukasiModal();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle delete confirmation with SweetAlert
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    const form = this.closest('.delete-form');
                    const kontenTitle = form.dataset.kontenTitle;

                    Swal.fire({
                        title: 'Konfirmasi Hapus',
                        text: `Apakah Anda yakin ingin menghapus konten "${kontenTitle}"?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading state
                            Swal.fire({
                                title: 'Menghapus...',
                                text: 'Sedang memproses permintaan Anda',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            // Submit the form
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
