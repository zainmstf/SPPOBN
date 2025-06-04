@extends('layouts.user')
@section('title', 'Laporan Konsultasi | SPPOBN')
@section('title-menu', 'Laporan Konsultasi')
@section('subtitle-menu', 'Data Riwayat Konsultasi Pengguna Sistem.')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Laporan Konsultasi</li>
@endsection

@section('content')
    <x-page-header />

    <div class="page-content">
        <section class="row">
            <div class="col-12 col-lg-12">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Laporan Konsultasi Pengguna</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="tabelKonsultasi" class="table table-striped table-bordered"
                                        style="width:100%">
                                        <thead>
                                            <tr>
                                                <th style="width:5%;">ID</th>
                                                <th style="width:10%;">Tanggal Konsultasi</th>
                                                <th style="width:10%;">Rentang Waktu</th>
                                                <th style="width:10%;">Durasi</th>
                                                <th style="width:5%;">Jumlah Fakta</th>
                                                <th style="width:25%;">Hasil Konsultasi</th>
                                                <th style="width:5%;">Status</th>
                                                <th style="width:15%;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($konsultasi as $index => $konsul)
                                                <tr>
                                                    <td>{{ $konsul->id }}</td>
                                                    <td>{{ $konsul->waktu_mulai }}</td>
                                                    <td>{{ $konsul->rentang_waktu }}</td>
                                                    <td>{{ $konsul->durasi_konsultasi }}</td>
                                                    <td>{{ $konsul->jumlah_fakta }}</td>
                                                    <td class="truncated-text">
                                                        {{-- Iterasi untuk menampilkan nama dan deskripsi solusi --}}
                                                        @forelse ($konsul->solusi_rekomendasi as $solusi)
                                                            <strong>{{ $solusi->nama }}</strong>:
                                                            {{ $solusi->deskripsi }}
                                                        @empty
                                                            Tidak ada solusi yang ditemukan.
                                                        @endforelse
                                                    </td>
                                                    <td>
                                                        @php
                                                            $status = $konsul->status;
                                                            $statusClass = match ($status) {
                                                                'selesai' => 'bg-success',
                                                                'sedang_berjalan' => 'bg-warning',
                                                                'belum_selesai' => 'bg-danger',
                                                                default => 'bg-secondary',
                                                            };
                                                        @endphp
                                                        <span class="badge {{ $statusClass }}">
                                                            {{ str_replace('_', ' ', ucwords($status)) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('riwayat.show', $konsul->id) }}"
                                                            class="btn btn-sm btn-info" target="_blank">
                                                            <i class="bi-eye"></i> Detail
                                                        </a>
                                                        @if ($status === 'selesai')
                                                            <a href="{{ route('konsultasi.print', $konsul->id) }}"
                                                                class="btn btn-sm btn-secondary" target="_blank">
                                                                <i class="bi-printer"></i> Cetak
                                                            </a>
                                                        @else
                                                            <a href="{{ route('konsultasi.lanjutkan', $konsul->id) }}"
                                                                class="btn btn-sm btn-primary">
                                                                <i class="bi-play-fill"></i> Lanjutkan
                                                            </a>
                                                        @endif
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
        </section>
    </div>
@endsection

@push('scripts-bottom')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/id.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var table = $("#tabelKonsultasi").DataTable({
                dom: '<"row mb-3"<"col-md-6"B><"col-md-3 date-filter-container"><"col-md-3"f>>tp',
                autoWidth: false,
                buttons: [{
                        extend: "print",
                        title: "",
                        text: '<i class="bi bi-printer"></i> Cetak',
                        className: "btn btn-primary",
                        action: function(e, dt, node, config) {
                            var dataToPrint = dt.rows({
                                search: 'applied',
                                page: 'all'
                            }).data().toArray();

                            if (dataToPrint.length > 0) {
                                var form = $('<form>', {
                                    method: 'POST',
                                    action: "{{ route('laporan.cetak_halaman') }}",
                                    target: '_blank'
                                });

                                // Tambahkan CSRF Token Laravel
                                var token = '{{ csrf_token() }}';
                                form.append($('<input>', {
                                    type: 'hidden',
                                    name: '_token',
                                    value: token
                                }));

                                // Kirim data sebagai input hidden
                                form.append($('<input>', {
                                    type: 'hidden',
                                    name: 'data',
                                    value: JSON.stringify(dataToPrint)
                                }));

                                // Tambahkan form ke body, submit, lalu hapus
                                form.appendTo('body').submit().remove();
                            } else {
                                alert('Tidak ada data untuk dicetak berdasarkan filter saat ini.');
                            }
                        }
                    },
                    {
                        extend: "pdfHtml5",
                        text: '<i class="bi bi-file-earmark-pdf"></i> Unduh PDF',
                        className: "btn btn-danger",
                        action: function(e, dt, node, config) {
                            var dataToPrint = dt.rows({
                                search: 'applied',
                                page: 'all'
                            }).data().toArray();

                            if (dataToPrint.length > 0) {
                                var form = $('<form>', {
                                    method: 'POST',
                                    action: "{{ route('laporan.cetak_halaman') }}",
                                    target: '_blank'
                                });

                                // Tambahkan CSRF Token Laravel
                                var token = '{{ csrf_token() }}';
                                form.append($('<input>', {
                                    type: 'hidden',
                                    name: '_token',
                                    value: token
                                }));

                                // Kirim data sebagai input hidden
                                form.append($('<input>', {
                                    type: 'hidden',
                                    name: 'data',
                                    value: JSON.stringify(dataToPrint)
                                }));

                                // Tambahkan flag download PDF
                                form.append($('<input>', {
                                    type: 'hidden',
                                    name: 'action',
                                    value: 'download'
                                }));

                                // Tambahkan form ke body, submit, lalu hapus
                                form.appendTo('body').submit().remove();
                            } else {
                                alert('Tidak ada data untuk diunduh berdasarkan filter saat ini.');
                            }
                        }
                    },
                    {
                        extend: "excelHtml5",
                        text: '<i class="bi bi-file-earmark-excel"></i> <span class="d-none d-sm-inline">Unduh Excel</span>',
                        className: "btn btn-success btn-sm",
                        titleAttr: "Export ke Excel",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6],
                            format: {
                                body: function(data, row, column, node) {
                                    return data.replace(/<[^>]*>?/gm, '')
                                        .trim();
                                }
                            }
                        },
                        customizeData: function(data) {
                            // Format Rentang Waktu di kolom ke-2
                            data.body.forEach(function(row) {
                                row[2] = row[2].replace(/\s*[\r\n]+\s*/g, ' ').trim();
                            });
                        },
                        filename: "laporan_konsultasi_" + new Date().toISOString().slice(0, 10),
                        title: null
                    }
                ],
                responsive: true,
                initComplete: function() {

                    $(".date-filter-container").html(
                        '<div class="input-group"><input type="text" id="dateRangePicker" class="form-control form-control-sm" placeholder="Pilih Rentang"><button id="applyDateFilter" class="btn btn-primary btn-sm">Terapkan</button><button id="resetDateFilter" class="btn btn-outline-secondary btn-sm">Reset</button></div>'
                    );

                    // Initialize date range picker 
                    $("#dateRangePicker").daterangepicker({
                        locale: {
                            format: "DD MMMM YYYY",
                            applyLabel: "Pilih",
                            cancelLabel: "Batal",
                            fromLabel: "Dari",
                            toLabel: "Sampai",
                            customRangeLabel: "Kustom",
                            weekLabel: "M",
                            daysOfWeek: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
                            monthNames: ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
                                "Juli", "Agustus", "September", "Oktober", "November",
                                "Desember"
                            ],
                            firstDay: 1,
                        },
                        opens: "left",
                        autoUpdateInput: false,
                    });

                    $("#dateRangePicker").on("apply.daterangepicker", function(ev, picker) {
                        $(this).val(picker.startDate.format("DD MMM YYYY") + " - " +
                            picker.endDate.format("DD MMM YYYY"));
                        table.draw();
                    });

                    $("#dateRangePicker").on("cancel.daterangepicker", function(ev, picker) {
                        $(this).val("");
                        table.draw();
                    });

                    $("#applyDateFilter").on("click", function() {
                        table.draw();
                    });

                    $("#resetDateFilter").on("click", function() {
                        $("#dateRangePicker").val("");
                        table.draw();
                    });
                },
            });

            // Custom date range filter function
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                var dateRangeVal = $("#dateRangePicker").val();

                // Jika tidak ada filter tanggal, tampilkan semua data
                if (!dateRangeVal) {
                    return true;
                }

                var dateRange = dateRangeVal.split(" - ");
                var startDate = moment(dateRange[0], "DD MMM YYYY");
                var endDate = moment(dateRange[1], "DD MMM YYYY");

                // Ambil tanggal dari kolom ke-1 (Tanggal Konsultasi) - index 1
                var dateStr = data[1]; // Menggunakan index 1 untuk kolom Tanggal Konsultasi

                // Parse tanggal dari format "dd MMM YYYY"
                var rowDate = moment(dateStr, "DD MMM YYYY");

                // Pastikan tanggal berada dalam rentang yang dipilih (inklusive)
                return rowDate.isBetween(startDate, endDate, null, "[]");
            });
        });
    </script>
@endpush

@push('css-top')
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .truncated-text {
            white-space: nowrap;
            /* Mencegah teks pindah ke baris baru */
            overflow: hidden;
            /* Menyembunyikan teks yang melampaui batas */
            text-overflow: ellipsis;
            /* Menampilkan elipsis (...) untuk teks yang terpotong */
            max-width: 300px;
            /* Atur lebar maksimum yang diinginkan */
        }

        .recommendations ol {
            padding-left: 25px;
        }

        .recommendations b {
            display: block;
            margin-bottom: 10px;
            margin-top: 15px;
        }

        .recommendations li {
            margin-bottom: 5px;
        }

        td.deskripsi {
            white-space: nowrap;
            /* Agar teks tidak membungkus ke baris baru */
            overflow: hidden;
            /* Menyembunyikan teks yang melebihi batas kolom */
            text-overflow: ellipsis;
            /* Menambahkan tanda "..." jika teks terpotong */
            max-width: 250px;
            /* Sesuaikan dengan lebar kolom yang diinginkan */
        }
    </style>
@endpush
