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
                                                <th style="width: 10%;">Username</th>
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
                                                    <td>{{ '@' . $konsultasi->user->username ?? 'N/A' }}</td>
                                                    <td>{{ $konsultasi->waktu_mulai }}</td>
                                                    <td>{{ $konsultasi->rentang_waktu }}</td>
                                                    <td>{{ $konsultasi->durasi_konsultasi }}</td>
                                                    <td class="truncated-text"
                                                        title="{{ $konsultasi->hasil_konsultasi_text }}">
                                                        {{ $konsultasi->hasil_konsultasi_text }}
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('admin.konsultasi.show', $konsultasi->id) }}"
                                                                class="btn btn-sm btn-info" target="_blank" title="Detail">
                                                                <i class="bi-eye"></i> Detail
                                                            </a>
                                                            <a href="{{ route('admin.konsultasi.print', $konsultasi->id) }}"
                                                                class="btn btn-sm btn-secondary" target="_blank"
                                                                title="Cetak">
                                                                <i class="bi-printer"></i> Cetak
                                                            </a>
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
                buttons: [{
                        extend: "print",
                        title: "Laporan Konsultasi Selesai",
                        text: '<i class="bi bi-printer"></i> Cetak',
                        className: "btn btn-primary btn-sm",
                        action: function(e, dt, node, config) {
                            var dataToPrint = [];
                            dt.rows({
                                search: 'applied',
                                page: 'all'
                            }).every(function(rowIdx) {
                                var data = this.data();
                                dataToPrint.push([
                                    data[0], // No
                                    data[1], // Konsultasi ID
                                    data[2], // Nama Pengguna
                                    data[3], // Tanggal Konsultasi
                                    data[4], // Rentang Waktu
                                    data[5], // Durasi
                                    $(data[6]).text() || data[
                                        6] // Hasil Konsultasi (clean text)
                                ]);
                            });

                            if (dataToPrint.length > 0) {
                                submitPrintForm(dataToPrint, 'print');
                            } else {
                                showAlert(
                                    'Tidak ada data untuk dicetak berdasarkan filter saat ini.');
                            }
                        }
                    },
                    {
                        extend: "pdfHtml5",
                        text: '<i class="bi bi-file-earmark-pdf"></i> Unduh PDF',
                        className: "btn btn-danger btn-sm",
                        action: function(e, dt, node, config) {
                            var dataToPrint = [];
                            dt.rows({
                                search: 'applied',
                                page: 'all'
                            }).every(function(rowIdx) {
                                var data = this.data();
                                dataToPrint.push([
                                    data[0], // No
                                    data[1], // Konsultasi ID
                                    data[2], // Nama Pengguna
                                    data[3], // Tanggal Konsultasi
                                    data[4], // Rentang Waktu
                                    data[5], // Durasi
                                    $(data[6]).text() || data[
                                        6] // Hasil Konsultasi (clean text)
                                ]);
                            });

                            if (dataToPrint.length > 0) {
                                submitPrintForm(dataToPrint, 'download');
                            } else {
                                showAlert('Tidak ada data untuk diunduh.');
                            }
                        }
                    },
                    {
                        extend: "excel",
                        text: '<i class="bi bi-file-earmark-excel"></i> Unduh Excel',
                        className: "btn btn-success btn-sm",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6] // Include all columns except action
                        },
                        title: 'Laporan Konsultasi Selesai'
                    }
                ],
                responsive: true,
                pageLength: 25,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                },
                initComplete: function() {
                    initializeDateFilter();
                }
            });

            function submitPrintForm(data, action) {
                try {
                    var jsonData = JSON.stringify(data);
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('admin.laporan.cetak_halaman') }}";
                    form.target = '_blank';

                    // CSRF token
                    var csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    form.appendChild(csrfInput);

                    // Data
                    var dataInput = document.createElement('input');
                    dataInput.type = 'hidden';
                    dataInput.name = 'data';
                    dataInput.value = jsonData;
                    form.appendChild(dataInput);

                    // Action
                    if (action) {
                        var actionInput = document.createElement('input');
                        actionInput.type = 'hidden';
                        actionInput.name = 'action';
                        actionInput.value = action;
                        form.appendChild(actionInput);
                    }

                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                } catch (error) {
                    console.error('Error submitting form:', error);
                    showAlert('Terjadi kesalahan saat memproses data.');
                }
            }

            function initializeDateFilter() {
                $(".date-filter-container").html(
                    '<div class="input-group input-group-sm">' +
                    '<input type="text" id="dateRangePicker" class="form-control" placeholder="Filter Tanggal">' +
                    '<button id="applyDateFilter" class="btn btn-primary">Terapkan</button>' +
                    '<button id="resetDateFilter" class="btn btn-outline-secondary">Reset</button>' +
                    '</div>'
                );

                $("#dateRangePicker").daterangepicker({
                    locale: {
                        format: "DD MMM YYYY",
                        applyLabel: "Pilih",
                        cancelLabel: "Batal",
                        fromLabel: "Dari",
                        toLabel: "Sampai",
                        customRangeLabel: "Kustom",
                        weekLabel: "M",
                        daysOfWeek: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
                        monthNames: ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
                            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
                        ],
                        firstDay: 1,
                    },
                    opens: "left",
                    autoUpdateInput: false,
                });

                $("#dateRangePicker").on("apply.daterangepicker", function(ev, picker) {
                    $(this).val(picker.startDate.format("DD MMM YYYY") + " - " + picker.endDate.format(
                        "DD MMM YYYY"));
                    table.draw();
                });

                $("#dateRangePicker").on("cancel.daterangepicker", function(ev, picker) {
                    $(this).val("");
                    table.draw();
                });

                $("#applyDateFilter, #resetDateFilter").on("click", function() {
                    if (this.id === 'resetDateFilter') {
                        $("#dateRangePicker").val("");
                    }
                    table.draw();
                });
            }

            function showAlert(message) {
                alert(message);
            }

            // Custom date range filter
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                var dateRangeVal = $("#dateRangePicker").val();
                if (!dateRangeVal) {
                    return true;
                }

                try {
                    var dateRange = dateRangeVal.split(" - ");
                    var startDate = moment(dateRange[0], "DD MMM YYYY");
                    var endDate = moment(dateRange[1], "DD MMM YYYY");
                    var rowDate = moment(data[3], "DD MMM YYYY HH:mm");

                    return rowDate.isBetween(startDate, endDate, 'day', '[]');
                } catch (error) {
                    console.error('Date filter error:', error);
                    return true;
                }
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
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 250px;
            cursor: help;
        }

        .btn-group .btn {
            margin-right: 2px;
        }

        .btn-group .btn:last-child {
            margin-right: 0;
        }

        #tabelKonsultasi tbody tr td:nth-child(5),
        #tabelKonsultasi tbody tr td:nth-child(6) {
            white-space: nowrap;
            font-family: monospace;
        }

        .input-group-sm .btn {
            font-size: 0.875rem;
        }

        .date-filter-container .input-group {
            min-width: 300px;
        }
    </style>
@endpush
