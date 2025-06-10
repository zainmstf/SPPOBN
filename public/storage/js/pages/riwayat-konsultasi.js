$(document).ready(function () {
    // Initialize DataTables
    $("#ongoingTable").DataTable({
        responsive: true,
        order: [[2, "desc"]],
    });

    $("#pendingTable").DataTable({
        responsive: true,
        order: [[2, "desc"]],
    });

    $("#completedTable").DataTable({
        responsive: true,
        order: [[2, "desc"]],
    });

    $('button[data-bs-toggle="tab"]').on("shown.bs.tab", function (e) {
        $.fn.dataTable
            .tables({
                visible: true,
                api: true,
            })
            .columns.adjust();
    });
});
document.addEventListener("DOMContentLoaded", function () {
    const pendingLinks = document.querySelectorAll(".pending-link");

    pendingLinks.forEach((link) => {
        link.addEventListener("click", function (event) {
            event.preventDefault();

            if (confirm("Apakah Anda yakin ingin menunda konsultasi ini?")) {
                const action = this.dataset.action;
                const id = this.dataset.id;
                const riwayatUrl = this.dataset.riwayatUrl; // Ambil URL dari atribut data

                fetch(action, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                    body: JSON.stringify({
                        konsultasiID: id,
                    }),
                })
                    .then((response) => {
                        if (response.ok) {
                            window.location.href = riwayatUrl; // Gunakan URL dari atribut data
                        } else {
                            alert("Gagal menunda konsultasi.");
                        }
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        alert("Terjadi kesalahan.");
                    });
            }
        });
    });
});
