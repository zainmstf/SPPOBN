@extends('layouts.admin')

@section('title', 'Daftar Aturan | SPPOBN')
@section('title-menu', 'Daftar Aturan')
@section('subtitle-menu', 'Manajemen Data Aturan')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Aturan</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi-lightbulb-fill"></i> Daftar Aturan</h4>
                        <div>
                            <button class="btn btn-sm btn-info me-2" data-bs-toggle="modal"
                                data-bs-target="#setDefaultSolusiAturanModal">
                                <i class="bi bi-gear-fill align-middle me-1"></i> Set Solusi Default
                            </button>
                            <button class="btn btn-sm btn-warning me-2" data-bs-toggle="modal"
                                data-bs-target="#setDefaultFaktaTurunanModal">
                                <i class="bi bi-gear-fill align-middle me-1"></i> Set Fakta Default
                            </button>
                            <a href="{{ route('admin.basisPengetahuan.aturan.create') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus-circle align-middle me-1"></i> Tambah Aturan
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <x-alert />
                        <div class="table-responsive">
                            <table id="aturanTable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">Kode</th>
                                        <th style="width: 20%;">Deskripsi</th>
                                        <th style="width: 20%;">Premis</th>
                                        <th style="width: 10%;">Konklusi</th>
                                        <th style="width: 10%;">Jenis Konklusi</th>
                                        <th style="width: 5%;">Status</th>
                                        <th style="width: 15%;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($aturan as $item)
                                        <tr>
                                            <td>{{ $item->kode }}</td>
                                            <td>{{ $item->deskripsi }}</td>
                                            <td>{!! nl2br(e($item->premis)) !!}</td>
                                            <td>{{ $item->konklusi }}</td>
                                            <td>{{ ucwords($item->jenis_konklusi) }}</td>
                                            <td>{{ $item->is_active ? 'Aktif' : 'Tidak Aktif' }}</td>
                                            <td class="text-center">
                                                <div class="d-flex gap-2 justify-content-center">
                                                    <a href="{{ route('admin.basisPengetahuan.aturan.edit', $item->id) }}"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="bi-pencil align-middle mt-1"></i> Edit
                                                    </a>
                                                    <form
                                                        action="{{ route('admin.basisPengetahuan.aturan.destroy', $item->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger btn-delete">
                                                            <i class="bi-trash align-middle mt-1"></i> Hapus
                                                        </button>
                                                    </form>
                                                </div>
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
    <!-- Modal Set Default Solusi -->
    <div class="modal fade" id="setDefaultSolusiAturanModal" tabindex="-1"
        aria-labelledby="setDefaultSolusiAturanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="setDefaultSolusiAturanModalLabel">Set Default Solusi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.basisPengetahuan.aturan.setDefaultSolusi') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="default_solusi_id">Pilih Solusi untuk diatur sebagai Default:</label>
                            <select class="form-control" id="default_solusi_id" name="default_solusi_id" required>
                                <option value="">-- Pilih Solusi --</option>
                                @foreach ($solusi as $sls)
                                    <option value="{{ $sls->id }}">{{ $sls->nama }} ({{ $sls->kode }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Solusi yang dipilih akan ditandai sebagai default di tabel
                                Solusi.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Set Default Solusi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Set Default Fakta -->
    <div class="modal fade" id="setDefaultFaktaTurunanModal" tabindex="-1"
        aria-labelledby="setDefaultFaktaTurunanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="setDefaultFaktaTurunanModalLabel">Set Default Fakta (Bukan Pertanyaan) per
                        Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.basisPengetahuan.aturan.setDefaultFakta') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="default_fakta_kategori">Pilih Kategori Fakta:</label>
                            <select class="form-control" id="default_fakta_kategori" name="kategori" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="risiko_osteoporosis">Risiko Osteoporosis</option>
                                <option value="asupan_nutrisi">Asupan Nutrisi</option>
                                <option value="preferensi_makanan">Preferensi Makanan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="default_fakta_id">Pilih Fakta Default (yang bukan pertanyaan):</label>
                            <select class="form-control" id="default_fakta_id" name="default_fakta_id" required>
                                <option value="">-- Pilih Fakta --</option>
                                @foreach ($fakta as $f)
                                    @if (!$f->is_askable)
                                        <option value="{{ $f->id }}" data-kategori="{{ $f->kategori }}">
                                            {{ $f->deskripsi }} ({{ $f->kode }}) -
                                            {{ ucwords(str_replace('_', ' ', $f->kategori)) }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Fakta yang dipilih (pada kategori ini dan bukan pertanyaan)
                                akan ditandai sebagai default di tabel Fakta.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Set Default Fakta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts-bottom')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#aturanTable').DataTable({
                responsive: true,
                scrollX: true
            });

            // SweetAlert Delete Confirmation
            $('.btn-delete').on('click', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Make sure Bootstrap 5 Modal works
            const solusiModal = document.getElementById('setDefaultSolusiAturanModal');
            if (solusiModal) {
                const bsModal = new bootstrap.Modal(solusiModal);
                $('.modal-solusi-btn').on('click', function() {
                    bsModal.show();
                });
            }

            const faktaModal = document.getElementById('setDefaultFaktaTurunanModal');
            if (faktaModal) {
                const bsFaktaModal = new bootstrap.Modal(faktaModal);
                $('.modal-fakta-btn').on('click', function() {
                    bsFaktaModal.show();
                });
            }

            // Filter fakta berdasarkan kategori pada modal set default fakta
            $('#default_fakta_kategori').on('change', function() {
                const kategori = $(this).val();
                $('#default_fakta_id').val(''); // Reset pilihan fakta jika kategori berubah

                // Filter options based on kategori
                $('#default_fakta_id option').each(function() {
                    if ($(this).val() === '') {
                        // Always show the placeholder option
                        $(this).show();
                    } else if ($(this).data('kategori') === kategori) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // Pada saat modal fakta ditampilkan
            $('#setDefaultFaktaTurunanModal').on('show.bs.modal', function() {
                // Reset selections
                $('#default_fakta_kategori').val('');
                $('#default_fakta_id').val('');

                // Show all options initially
                $('#default_fakta_id option').show();
            });
        });
    </script>
@endpush

@push('css-top')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <style>
        #aturanTable {
            table-layout: fixed;
            width: 100%;
        }

        #aturanTable th,
        #aturanTable td {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
@endpush
