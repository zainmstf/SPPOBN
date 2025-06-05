@extends('layouts.admin')

@section('title', 'Tambah Aturan | SPPOBN')
@section('title-menu', 'Tambah Aturan')
@section('subtitle-menu', 'Manajemen Data Aturan')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.basisPengetahuan.aturan.index') }}">Daftar Aturan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah Aturan</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="bi-lightbulb-fill"></i> Tambah Aturan Baru</h4>
                    </div>
                    <div class="card-body">
                        <x-alert />
                        <form method="POST" action="{{ route('admin.basisPengetahuan.aturan.store') }}">
                            @csrf
                            <div class="form-group">
                                <label for="kode">Kode</label>
                                <input type="text" name="kode" id="kode" class="form-control" required>
                                <small>Format Penulisan Kode :
                                    <ul>
                                        <li>R0.</li>
                                        <li>R1.</li>
                                        <li>R2.</li>
                                        <li>R3.</li>
                                        <li>R4.</li>
                                    </ul>
                                    Kode Aturan harus sesuai dengan prefix sesi jika tidak, akan error !
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="deskripsi">Deskripsi</label>
                                <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="premis" class="form-label">Premis (Fakta)</label>
                                <select name="premis[]" id="premis" class="form-select" multiple>
                                    <option value="START">START</option>
                                    @foreach ($fakta as $item)
                                        <option value="{{ $item['kode'] }}">{{ $item['kode'] . '-' . $item['deskripsi'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="jenis_konklusi">Jenis Konklusi</label>
                                <select name="jenis_konklusi" id="jenis_konklusi" class="form-control" required>
                                    <option value="fakta">Fakta</option>
                                    <option value="solusi">Solusi</option>
                                </select>
                            </div>

                            <div class="form-group" id="konklusi-container">
                                <label for="konklusi">Konklusi</label>
                                <select name="konklusi" id="konklusi" class="form-control" required>
                                    <!-- Options will be loaded dynamically here -->
                                </select>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                    value="1" checked>
                                <label class="form-check-label" for="is_active">Aktifkan Aturan Ini</label>
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.basisPengetahuan.aturan.index') }}" class="btn btn-secondary me-2">
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
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const premisElement = document.getElementById('premis');
            const premisChoices = new Choices(premisElement, {
                removeItemButton: true,
                placeholder: true,
                placeholderValue: 'Pilih satu atau lebih premis',
                searchPlaceholderValue: 'Cari premis...',
            });

            const jenisKonklusi = document.getElementById('jenis_konklusi');
            const konklusiSelect = document.getElementById('konklusi');

            const faktaData = @json($fakta);
            const solusiData = @json($solusi);

            function updateKonklusiOptions() {
                console.log("Fungsi updateKonklusiOptions dipanggil.");

                const selected = jenisKonklusi.value;
                console.log("Jenis konklusi terpilih:", selected);

                konklusiSelect.innerHTML = ''; // Mengosongkan pilihan sebelumnya

                const source = selected === 'fakta' ? faktaData : solusiData;

                // Filter data jika yang dipilih adalah 'fakta'
                const filteredSource = source.filter(item => {
                    if (selected === 'fakta') {
                        // Hanya ambil item yang kodenya dimulai dengan 'FA'
                        return item.kode && item.kode.startsWith('FA');
                    }
                    // Untuk 'solusi' atau lainnya, kembalikan semua item
                    return true;
                });

                filteredSource.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.kode;
                    // Menggabungkan kode dan deskripsi untuk tampilan
                    option.textContent = item.kode + ' - ' + (item.deskripsi || item.nama);
                    konklusiSelect.appendChild(option);
                });

                console.log("Pilihan konklusi diperbarui.");
            }

            // Cek apakah listener berhasil ditambahkan
            jenisKonklusi.addEventListener('change', function() {
                console.log("Event 'change' terjadi pada jenisKonklusi.");
                updateKonklusiOptions();
            });

            // Populate saat awal
            updateKonklusiOptions();
        });
    </script>
@endpush
@push('css-top')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
@endpush
