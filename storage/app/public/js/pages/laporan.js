document.addEventListener("DOMContentLoaded", function () {
    // Extract data from hidden input elements
    var rowDataElements = document.querySelectorAll(".row-data");
    var tabelRekomendasiData = [];

    // Ambil data pengguna dari elemen tersembunyi (tambahkan ini)
    var userData = {
        nama:
            document.getElementById("userData").getAttribute("data-nama") ||
            "Nama Pasien",
        alamat:
            document.getElementById("userData").getAttribute("data-alamat") ||
            "Alamat Pasien",
        telepon:
            document.getElementById("userData").getAttribute("data-telepon") ||
            "No. Telepon",
    };

    rowDataElements.forEach(function (element) {
        tabelRekomendasiData.push({
            no: element.getAttribute("data-no"),
            hari_tanggal: element.getAttribute("data-tanggal"),
            rekomendasi_text: element.getAttribute("data-rekomendasi-text"),
        });
    });

    // Format untuk konversi tanggal
    var dateFormat = "DD MMMM YYYY";

    // Fungsi untuk mengkonversi format tanggal dari tabel
    function parseIndonesianDate(dateStr) {
        const parts = dateStr.split(", ");
        if (parts.length > 1) {
            return moment(parts[1], dateFormat);
        }
        return moment(dateStr, dateFormat);
    }

    // Inisialisasi DataTables dengan DOM custom untuk tombol cetak dan ekspor
    var table = $("#tabelRekomendasi").DataTable({
        dom: '<"row mb-3"<"col-md-6"B><"col-md-3 date-filter-container"><"col-md-3"f>>t<"row mt-3"<"col-md-6"i><"col-md-6"p>>',
        buttons: [
            {
                extend: "print",
                title: "",
                text: '<i class="bi bi-printer"></i> Cetak',
                className: "btn btn-primary",
                exportOptions: {
                    columns: [0, 1, 2],
                    format: {
                        body: function (data, row, column, node) {
                            if (column === 2) {
                                return data;
                            }
                            return data;
                        },
                    },
                },
                customize: function (win) {
                    $(win.document.body).css("font-size", "10pt");
                    $(win.document.body)
                        .find("table")
                        .addClass("compact")
                        .css("font-size", "inherit");

                    // Modifikasi header cetak: menambahkan info pengguna dan menghapus judul
                    $(win.document.body).prepend(
                        '<div style="text-align: center; margin-bottom: 20px;">' +
                            "<h3>Laporan Rekomendasi Kesehatan</h3>" +
                            "<p>SPPOBN - Sistem Pakar Penanganan Osteoporosis</p>" +
                            "<p>Tanggal Cetak: " +
                            new Date().toLocaleDateString("id-ID", {
                                weekday: "long",
                                year: "numeric",
                                month: "long",
                                day: "numeric",
                            }) +
                            "</p>" +
                            '<div style="text-align: left; margin: 15px 0; border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9;">' +
                            '<p style="margin: 5px 0;"><strong>Nama:</strong> ' +
                            userData.nama +
                            "</p>" +
                            '<p style="margin: 5px 0;"><strong>Alamat:</strong> ' +
                            userData.alamat +
                            "</p>" +
                            '<p style="margin: 5px 0;"><strong>No. Telepon:</strong> ' +
                            userData.telepon +
                            "</p>" +
                            "</div>" +
                            "</div>"
                    );

                    $(win.document.body)
                        .find("td")
                        .each(function () {
                            $(this).css("white-space", "pre-line");
                            var content = $(this).html();
                            if (content.includes("<ol>")) {
                                $(this).find("ol").css({
                                    "padding-left": "20px",
                                    "margin-top": "5px",
                                    "margin-bottom": "0",
                                });
                                $(this).find("li").css({
                                    padding: "2px 0",
                                    "margin-bottom": "8px",
                                });
                                $(this).find("b").css({
                                    display: "block",
                                    "margin-bottom": "8px",
                                    "margin-top": "12px",
                                    "font-weight": "bold",
                                    "font-size": "11pt",
                                });
                            }
                        });
                },
            },
            {
                extend: "pdfHtml5",
                text: '<i class="bi bi-file-earmark-pdf"></i> Unduh PDF',
                className: "btn btn-danger",
                exportOptions: {
                    columns: [0, 1, 2],
                },
                customize: function (doc) {
                    doc.pageOrientation = "landscape";
                    doc.styles.tableHeader.fontSize = 12;
                    doc.styles.tableHeader.alignment = "left";
                    doc.styles.tableBodyEven.alignment = "left";
                    doc.styles.tableBodyOdd.alignment = "left";
                    doc.styles.tableBodyEven.lineHeight = 1.5;
                    doc.styles.tableBodyOdd.lineHeight = 1.5;

                    // Modifikasi header PDF: tambahkan data pengguna
                    doc.content.splice(0, 1, {
                        margin: [0, 0, 0, 12],
                        alignment: "center",
                        stack: [
                            {
                                text: "Laporan Rekomendasi Kesehatan",
                                style: "header",
                                fontSize: 16,
                                bold: true,
                            },
                            {
                                text: "SPPOBN - Sistem Pakar Penanganan Osteoporosis",
                                fontSize: 12,
                            },
                            {
                                text:
                                    "Tanggal: " +
                                    new Date().toLocaleDateString("id-ID", {
                                        year: "numeric",
                                        month: "long",
                                        day: "numeric",
                                    }),
                                fontSize: 10,
                                margin: [0, 6, 0, 0],
                            },
                            // Tambahkan informasi pengguna dengan alignment kiri dan tabulasi
                            {
                                margin: [0, 15, 0, 10],
                                alignment: "left",
                                table: {
                                    widths: ["auto", "*"],
                                    body: [
                                        [
                                            {
                                                text: "Nama:",
                                                bold: true,
                                                margin: [0, 0, 10, 0],
                                            },
                                            {
                                                text: userData.nama,
                                                margin: [20, 0, 0, 0],
                                            }, // Tambahkan tabulasi
                                        ],
                                        [
                                            {
                                                text: "Alamat:",
                                                bold: true,
                                                margin: [0, 0, 10, 0],
                                            },
                                            {
                                                text: userData.alamat,
                                                margin: [20, 0, 0, 0],
                                            }, // Tambahkan tabulasi
                                        ],
                                        [
                                            {
                                                text: "No. Telepon:",
                                                bold: true,
                                                margin: [0, 0, 10, 0],
                                            },
                                            {
                                                text: userData.telepon,
                                                margin: [20, 0, 0, 0],
                                            }, // Tambahkan tabulasi
                                        ],
                                    ],
                                },
                                layout: {
                                    hLineWidth: function () {
                                        return 0;
                                    },
                                    vLineWidth: function () {
                                        return 0;
                                    },
                                    paddingLeft: function () {
                                        return 0;
                                    },
                                    paddingRight: function () {
                                        return 0;
                                    },
                                    paddingTop: function () {
                                        return 2;
                                    },
                                    paddingBottom: function () {
                                        return 2;
                                    },
                                },
                            },
                        ],
                    });

                    var dataRows = [];

                    function parseHtmlForPdf(text) {
                        // Split by double line breaks to separate sections
                        const sections = text.split("\n\n");
                        const content = [];

                        sections.forEach((section) => {
                            if (!section.trim()) return;

                            // Check if this is a header (jenisRekomendasi) ending with colon
                            if (section.includes(":")) {
                                const parts = section.split(":");
                                const header = parts[0] + ":";

                                content.push({
                                    text: header,
                                    bold: true,
                                    fontSize: 12,
                                    margin: [0, 10, 0, 5],
                                });

                                // Process the recommendation items that come after the colon
                                if (parts.length > 1 && parts[1].trim()) {
                                    const itemsText = parts[1].trim();
                                    const items = itemsText
                                        .split("\n")
                                        .filter((line) => line.trim());

                                    // Explicitly add numbers to each item
                                    items.forEach((item, index) => {
                                        content.push({
                                            text:
                                                index + 1 + ". " + item.trim(),
                                            margin: [15, 2, 0, 2],
                                        });
                                    });
                                }
                            } else {
                                // This is a list of recommendations without a header
                                const items = section
                                    .split("\n")
                                    .filter((line) => line.trim());

                                // Explicitly add numbers to each item
                                items.forEach((item, index) => {
                                    content.push({
                                        text: index + 1 + ". " + item.trim(),
                                        margin: [15, 2, 0, 2],
                                    });
                                });
                            }
                        });

                        return content;
                    }

                    tabelRekomendasiData.forEach(function (item) {
                        var recommendationContent = parseHtmlForPdf(
                            item.rekomendasi_text
                        );
                        dataRows.push([
                            { text: item.no },
                            { text: item.hari_tanggal },
                            {
                                stack: recommendationContent,
                                alignment: "left",
                            },
                        ]);
                    });

                    doc.content[1].table.body = [
                        [
                            { text: "No", style: "tableHeader" },
                            { text: "Hari & Tanggal", style: "tableHeader" },
                            { text: "Rekomendasi", style: "tableHeader" },
                        ],
                    ].concat(dataRows);

                    // Tambahkan border pada tabel rekomendasi
                    doc.content[1].table.layout = {
                        hLineWidth: function (i) {
                            return i === 0 ||
                                i === doc.content[1].table.body.length
                                ? 1
                                : 0.5; // Garis horizontal di atas, bawah, dan antar baris
                        },
                        vLineWidth: function (i) {
                            return i === 0 ||
                                i === doc.content[1].table.body.length
                                ? 1
                                : 0.5; // Garis vertikal di kiri, kanan, dan antar kolom
                        },
                        hLineColor: function (i) {
                            return i === 0 ||
                                i === doc.content[1].table.body.length
                                ? "black"
                                : "#aaa"; // Warna garis horizontal
                        },
                        vLineColor: function (i) {
                            return i === 0 ||
                                i === doc.content[1].table.body.length
                                ? "black"
                                : "#aaa"; // Warna garis vertikal
                        },
                        paddingLeft: function () {
                            return 5;
                        },
                        paddingRight: function () {
                            return 5;
                        },
                        paddingTop: function () {
                            return 5;
                        },
                        paddingBottom: function () {
                            return 5;
                        },
                    };

                    doc.styles.listItem = {
                        margin: [0, 3, 0, 3],
                    };
                },
            },
        ],
        responsive: true,
        initComplete: function () {
            // Tambahkan elemen rentang tanggal ke dalam wadah
            $(".date-filter-container").html(
                '<div class="input-group"><input type="text" id="dateRangePicker" class="form-control form-control-sm" placeholder="Pilih Rentang"><button id="applyDateFilter" class="btn btn-primary btn-sm">Terapkan</button><button id="resetDateFilter" class="btn btn-outline-secondary btn-sm">Reset</button></div>'
            );

            // Setup DateRangePicker
            $("#dateRangePicker").daterangepicker({
                locale: {
                    format: dateFormat,
                    applyLabel: "Pilih",
                    cancelLabel: "Batal",
                    fromLabel: "Dari",
                    toLabel: "Sampai",
                    customRangeLabel: "Kustom",
                    weekLabel: "M",
                    daysOfWeek: [
                        "Min",
                        "Sen",
                        "Sel",
                        "Rab",
                        "Kam",
                        "Jum",
                        "Sab",
                    ],
                    monthNames: [
                        "Januari",
                        "Februari",
                        "Maret",
                        "April",
                        "Mei",
                        "Juni",
                        "Juli",
                        "Agustus",
                        "September",
                        "Oktober",
                        "November",
                        "Desember",
                    ],
                    firstDay: 1,
                },
                opens: "left",
                autoUpdateInput: false,
            });

            // Update input value when date range is selected
            $("#dateRangePicker").on(
                "apply.daterangepicker",
                function (ev, picker) {
                    $(this).val(
                        picker.startDate.format(dateFormat) +
                            " - " +
                            picker.endDate.format(dateFormat)
                    );
                }
            );

            $("#dateRangePicker").on(
                "cancel.daterangepicker",
                function (ev, picker) {
                    $(this).val("");
                }
            );

            // Apply date filter when button is clicked
            $("#applyDateFilter").on("click", function () {
                table.draw();
            });

            // Reset date filter
            $("#resetDateFilter").on("click", function () {
                $("#dateRangePicker").val("");
                table.draw();
            });
        },
    });

    // Custom filter for date range
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        var dateRangeVal = $("#dateRangePicker").val();

        if (!dateRangeVal) {
            return true;
        }

        var dateRange = dateRangeVal.split(" - ");
        var startDate = moment(dateRange[0], dateFormat);
        var endDate = moment(dateRange[1], dateFormat);

        var dateStr = data[1];
        var rowDate = parseIndonesianDate(dateStr);

        if (rowDate.isBetween(startDate, endDate, null, "[]")) {
            return true;
        }
        return false;
    });
});
