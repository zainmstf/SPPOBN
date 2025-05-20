@extends('layouts.admin')

@section('title', 'Tambah Materi Nutrisi | Manajemen Konten Edukasi')
@section('title-menu', 'Manajemen Konten Edukasi')
@section('subtitle-menu', 'Menambahkan Materi Nutrisi Baru')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.konten-edukasi.index') }}">Daftar Materi</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah Materi Nutrisi</li>
@endsection

@section('content')
    <x-page-header>
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="page-title">
                <i class="bi-plus-circle-fill"></i> Tambah Materi Nutrisi
            </h2>
            <div class="header-actions">
                <a href="{{ route('admin.konten-edukasi.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left align-middle mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </x-page-header>
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <x-alert />
                        <form action="{{ route('admin.konten-edukasi.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label for="judul" class="form-label">Judul Materi</label>
                                <input type="text" class="form-control @error('judul') is-invalid @enderror"
                                    id="judul" name="judul" value="{{ old('judul') }}" required>
                                @error('judul')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="jenis" class="form-label">Jenis Materi</label>
                                <select class="form-select @error('jenis') is-invalid @enderror" id="jenis"
                                    name="jenis" required>
                                    <option value="">Pilih Jenis</option>
                                    <option value="artikel" {{ old('jenis') == 'artikel' ? 'selected' : '' }}>Artikel
                                    </option>
                                    <option value="video" {{ old('jenis') == 'video' ? 'selected' : '' }}>Video</option>
                                    <option value="infografis" {{ old('jenis') == 'infografis' ? 'selected' : '' }}>
                                        Infografis
                                    </option>
                                </select>
                                @error('jenis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="kategori" class="form-label">Kategori:</label>
                                <select name="kategori" id="kategori" class="form-select" required>
                                    <option value="" selected disabled>Pilih Kategori</option>
                                    <option value="osteoporosis_dasar"
                                        {{ old('kategori') == 'osteoporosis_dasar' ? 'selected' : '' }}>Osteoporosis Dasar
                                    </option>
                                    <option value="nutrisi_tulang"
                                        {{ old('kategori') == 'nutrisi_tulang' ? 'selected' : '' }}>Nutrisi Tulang</option>
                                    <option value="pencegahan" {{ old('kategori') == 'pencegahan' ? 'selected' : '' }}>
                                        Pencegahan</option>
                                    <option value="pengobatan" {{ old('kategori') == 'pengobatan' ? 'selected' : '' }}>
                                        Pengobatan</option>
                                </select>
                                @error('kategori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <div id="quill-editor-create" style="height: 400px;"></div>
                                <input type="hidden" name="deskripsi" id="deskripsi-content-create"
                                    value="{{ old('deskripsi') }}">
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3" id="file-pendukung">
                                <label for="path" class="form-label">File Pendukung (Opsional)</label>
                                <input type="file" class="form-control @error('path') is-invalid @enderror"
                                    id="path" name="path">
                                @error('path')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Format file yang didukung (jika ada). Max 2
                                    Mb</small>
                            </div>

                            <div class="mb-3">
                                <label for="thumbnail" class="form-label">Thumbnail:</label>
                                <input type="file" name="thumbnail" id="thumbnail" class="form-control" accept="image/*">
                                @error('thumbnail')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">File harus berupa gambar (jpeg, png, jpg, gif, svg) dan tidak
                                    lebih dari 2MB.</small>
                                <small class="text-muted">Untuk video dari youtube boleh dikosongkan.</small>
                            </div>

                            <div class="mb-3">
                                <label for="isActive" class="form-label">Status Aktif</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="isActive" name="isActive"
                                        value="1" {{ old('isActive', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isActive">Aktif</label>
                                </div>
                                @error('isActive')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.konten-edukasi.index') }}" class="btn btn-secondary me-2">
                                    <i class="bi-arrow-left-circle"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi-save"></i> Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts-bottom')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script>
        var quillCreate = new Quill('#quill-editor-create', {
            theme: 'snow'
        });

        const descriptionInputCreate = document.getElementById('deskripsi-content-create');
        const jenisSelect = document.getElementById('jenis');
        const filePendukungDiv = document.getElementById('file-pendukung'); // Menggunakan ID yang benar
        const urlPendukungDiv = document.createElement('div');
        urlPendukungDiv.classList.add('mb-3');
        urlPendukungDiv.innerHTML = `
        <label for="url" class="form-label">URL Video</label>
        <input type="url" class="form-control @error('url') is-invalid @enderror"
            id="url" name="url" value="{{ old('url') }}">
        @error('url')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">Masukkan URL video (contoh: link YouTube embed -> https://www.youtube.com/embed/TTOEdIdu1bU?si=0hWVv4C5RJXjLkye)</small>
    `;
        const infografisDiv = document.createElement('div');
        infografisDiv.classList.add('mb-3');
        infografisDiv.innerHTML = `
        <label for="path_infografis" class="form-label">File Infografis </label>
        <input type="file" class="form-control @error('path_infografis') is-invalid @enderror"
            id="path_infografis" name="path_infografis">
        @error('path_infografis')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">Format file yang didukung: PDF. Max 2MB</small>
    `;

        quillCreate.on('text-change', function() {
            descriptionInputCreate.value = quillCreate.root.innerHTML;
        });

        jenisSelect.addEventListener('change', function() {
            if (this.value === 'video') {
                filePendukungDiv.style.display = 'none';
                // Pastikan elemen infografisDiv sudah dihapus sebelum menambahkan urlPendukungDiv
                if (infografisDiv.parentNode) {
                    infografisDiv.parentNode.removeChild(infografisDiv);
                }
                if (!urlPendukungDiv.parentNode) { // Hanya tambahkan jika belum ada
                    filePendukungDiv.parentNode.insertBefore(urlPendukungDiv, filePendukungDiv
                        .nextSibling); // Tampilkan input URL
                }

            } else if (this.value === 'infografis') {
                filePendukungDiv.style.display = 'none';
                // Pastikan elemen urlPendukungDiv sudah dihapus sebelum menambahkan infografisDiv
                if (urlPendukungDiv.parentNode) {
                    urlPendukungDiv.parentNode.removeChild(urlPendukungDiv);
                }
                if (!infografisDiv.parentNode) {
                    filePendukungDiv.parentNode.insertBefore(infografisDiv, filePendukungDiv.nextSibling);
                }
            } else {
                filePendukungDiv.style.display = 'block';
                // Pastikan kedua elemen sudah dihapus
                if (urlPendukungDiv.parentNode) {
                    urlPendukungDiv.parentNode.removeChild(urlPendukungDiv); // Hapus input URL jika ada
                }
                if (infografisDiv.parentNode) {
                    infografisDiv.parentNode.removeChild(infografisDiv);
                }
            }
        });

        // Inisialisasi tampilan saat halaman pertama kali dimuat (untuk menangani old('jenis'))
        if (jenisSelect.value === 'video') {
            filePendukungDiv.style.display = 'none';
            if (!urlPendukungDiv.parentNode) {
                filePendukungDiv.parentNode.insertBefore(urlPendukungDiv, filePendukungDiv.nextSibling);
            }
        } else if (jenisSelect.value === 'infografis') {
            filePendukungDiv.style.display = 'none';
            if (!infografisDiv.parentNode) {
                filePendukungDiv.parentNode.insertBefore(infografisDiv, filePendukungDiv.nextSibling);
            }
        }
    </script>
@endpush

@push('css-top')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
@endpush
