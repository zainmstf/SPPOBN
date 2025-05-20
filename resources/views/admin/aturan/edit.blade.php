@extends('layouts.admin')

@section('title', 'Edit Aturan | SPPOBN')
@section('title-menu', 'Edit Aturan')
@section('subtitle-menu', 'Manajemen Data Aturan')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.basisPengetahuan.aturan.index') }}">Daftar Aturan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Aturan</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="bi-lightbulb-fill"></i> Edit Aturan</h4>
                    </div>
                    <div class="card-body">
                        <x-alert />
                        <form method="POST" action="{{ route('admin.basisPengetahuan.aturan.update', $aturan->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="kode">Kode</label>
                                <input type="text" name="kode" id="kode" class="form-control"
                                    value="{{ $aturan->kode }}" required>
                            </div>

                            <div class="form-group">
                                <label for="deskripsi">Deskripsi</label>
                                <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" required>{{ $aturan->deskripsi }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="premis" class="form-label">Premis (Fakta)</label>
                                <select name="premis[]" id="premis" class="form-select" multiple>
                                    @foreach ($fakta as $item)
                                        <option value="{{ $item['kode'] }}"
                                            {{ in_array($item['kode'], explode('^', $aturan->premis)) ? 'selected' : '' }}>
                                            {{ $item['kode'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="jenis_konklusi">Jenis Konklusi</label>
                                <select name="jenis_konklusi" id="jenis_konklusi" class="form-control" required>
                                    <option value="fakta" {{ $aturan->jenis_konklusi === 'fakta' ? 'selected' : '' }}>Fakta
                                    </option>
                                    <option value="solusi" {{ $aturan->jenis_konklusi === 'solusi' ? 'selected' : '' }}>
                                        Solusi</option>
                                </select>
                            </div>

                            <div class="form-group" id="konklusi-container">
                                <label for="konklusi">Konklusi</label>
                                <select name="konklusi" id="konklusi" class="form-control" required>
                                    <!-- Options loaded dynamically -->
                                </select>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                    value="1" {{ $aturan->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Aktifkan Aturan Ini</label>
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.basisPengetahuan.aturan.index') }}"
                                    class="btn btn-secondary me-2">
                                    <i class="bi-arrow-left-circle"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi-save"></i> Perbarui
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
            const konklusiLama = "{{ $aturan->konklusi }}";

            const faktaData = @json($fakta);
            const solusiData = @json($solusi);

            function updateKonklusiOptions() {
                const selected = jenisKonklusi.value;
                konklusiSelect.innerHTML = '';

                const source = selected === 'fakta' ? faktaData : solusiData;

                source.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.kode;
                    option.textContent = item.kode || item.kode;
                    if ((konklusiLama) === item.kode) {
                        option.selected = true;
                        console.log('true')
                    }
                    konklusiSelect.appendChild(option);
                });
            }

            jenisKonklusi.addEventListener('change', updateKonklusiOptions);
            updateKonklusiOptions();
        });
    </script>
@endpush

@push('css-top')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
@endpush
