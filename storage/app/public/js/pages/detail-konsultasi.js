$(document).ready(function () {
    $("#jawaban-table").DataTable({
        pageLength: 10,
        lengthMenu: [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, "Semua"],
        ],
        autoWidth: false,
        paging: true,
        info: true,
        searching: true,
        ordering: true,
        columnDefs: [
            {
                width: "10%",
                targets: 0,
            }, // No
            {
                width: "80%",
                targets: 1,
            }, // Pertanyaan
            {
                width: "10%",
                targets: 2,
            }, // Jawaban
        ],
        drawCallback: function (settings) {
            var pagination = $(this)
                .closest(".dataTables_wrapper")
                .find(".dataTables_paginate");
            pagination.toggle(this.api().page.info().pages > 1);
        },
    });
});
