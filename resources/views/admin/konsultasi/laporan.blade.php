@extends('layouts.admin')
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
                                <h4>Laporan Konsultasi Selesai Pengguna</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="tabelKonsultasi" class="table table-striped table-bordered"
                                        style="width:100%">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;">No</th>
                                                <th style="width: 5%;">Konsultasi ID</th>
                                                <th style="width: 10%;">Nama Pengguna</th>
                                                <th style="width: 15%;">Tanggal Konsultasi</th>
                                                <th style="width: 10%;">Rentang Waktu</th>
                                                <th style="width: 10%;">Durasi</th>
                                                <th style="width: 15%;">Hasil Konsultasi</th>
                                                <th style="width: 15%;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($laporanKonsultasi as $key => $konsultasi)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ '#' . $konsultasi->id }}</td>
                                                    <td>{{ $konsultasi->user->username }}</td>

                                                    <td>{{ Carbon\Carbon::parse($konsultasi->created_at)->locale('id')->isoFormat('DD MMM YYYY HH:mm') }}
                                                    </td>
                                                    </td>
                                                    <td>
                                                        {{ Carbon\Carbon::parse($konsultasi->created_at)->locale('id')->isoFormat(' HH:mm:ss') }}
                                                        -
                                                        {{ Carbon\Carbon::parse($konsultasi->completed_at)->locale('id')->isoFormat(' HH:mm:ss') }}
                                                    </td>
                                                    <td>
                                                        {{ \Carbon\Carbon::parse($konsultasi->completed_at)->diff(\Carbon\Carbon::parse($konsultasi->created_at))->format('%H:%I:%S') }}
                                                    </td>
                                                    <td class="truncated-text">
                                                        {{ $konsultasi->hasil_konsultasi ?? 'Belum tersedia' }}
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.konsultasi.show', $konsultasi->id) }}"
                                                            class="btn btn-sm btn-info" target="_blank">
                                                            <i class="bi-eye"></i> Detail
                                                        </a>
                                                        <a href="{{ route('admin.konsultasi.print', $konsultasi->id) }}"
                                                            class="btn btn-sm btn-secondary" target="_blank">
                                                            <i class="bi-printer"></i> Cetak
                                                        </a>
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
                dom: '<"row mb-3"<"col-md-6"B><"col-md-3 date-filter-container"><"col-md-3"f>>tp', // Pastikan 'p' ada di dom
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
                                var jsonData = JSON.stringify(dataToPrint);
                                var form = document.createElement('form');
                                form.method = 'POST';
                                form.action = "{{ route('admin.laporan.cetak_halaman') }}";
                                form.target = '_blank'; // Buka di tab baru

                                // Tambahkan CSRF token
                                var csrf = document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content');
                                var csrfInput = document.createElement('input');
                                csrfInput.type = 'hidden';
                                csrfInput.name = '_token';
                                csrfInput.value = csrf;
                                form.appendChild(csrfInput);

                                // Tambahkan data JSON
                                var input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'data';
                                input.value = jsonData; // Gunakan JSON string tanpa encode
                                form.appendChild(input);

                                document.body.appendChild(form);
                                form.submit();
                                document.body.removeChild(form); // Bersihkan DOM
                            } else {
                                alert('Tidak ada data untuk dicetak berdasarkan filter saat ini.');
                            }
                        }
                    },

                    // Tombol PDF yang sudah diperbaiki
                    {
                        extend: "pdfHtml5",
                        text: '<i class="bi bi-file-earmark-pdf"></i> Unduh PDF',
                        className: "btn btn-danger",
                        action: function(e, dt, node, config) {
                            var dataToPrint = dt.rows({
                                search: 'applied',
                                page: 'all'
                            }).data().toArray();

                            console.log("Data PDF:", dataToPrint);

                            if (dataToPrint.length > 0) {
                                var jsonData = JSON.stringify(dataToPrint);
                                console.log("JSON stringified for PDF:", jsonData.substring(0,
                                    100) + "..."); // Tampilkan awal JSON

                                var form = document.createElement('form');
                                form.method = 'POST';
                                form.action = "{{ route('admin.laporan.cetak_halaman') }}";
                                form.target = '_blank'; // Buka di tab baru

                                // Tambahkan CSRF token
                                var csrf = document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content');
                                var csrfInput = document.createElement('input');
                                csrfInput.type = 'hidden';
                                csrfInput.name = '_token';
                                csrfInput.value = csrf;
                                form.appendChild(csrfInput);

                                // Tambahkan data JSON
                                var input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'data';
                                input.value = jsonData; // Gunakan JSON string tanpa encode
                                form.appendChild(input);

                                // Tambahkan action untuk PDF download
                                var actionInput = document.createElement('input');
                                actionInput.type = 'hidden';
                                actionInput.name = 'action';
                                actionInput.value = 'download';
                                form.appendChild(actionInput);

                                document.body.appendChild(form);
                                form.submit();
                                document.body.removeChild(form); // Bersihkan DOM
                            } else {
                                alert('Tidak ada data untuk diunduh.');
                            }
                        }
                    },
                    {
                        extend: "excel",
                        text: '<i class="bi bi-file-earmark-excel"></i> Unduh Excel',
                        className: "btn btn-success",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
                        }
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
                            format: "DD MMMM",
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
                            picker
                            .endDate
                            .format("DD MMM YYYY"));
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
                if (!dateRangeVal) {
                    return true;
                }

                var dateRange = dateRangeVal.split(" - ");
                var startDate = moment(dateRange[0], "DD MMMM");
                var endDate = moment(dateRange[1], "DD MMMM");

                // Get date from column 3 (Tanggal Konsultasi)
                var dateStr = data[3];
                var rowDate = moment(dateStr, "DD MMMM");

                if (rowDate.isBetween(startDate, endDate, null, "[]")) {
                    return true;
                }
                return false;
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

        #tabelKonsultasi tbody tr td:nth-child(6) {
            white-space: nowrap;
        }
    </style>
@endpush
