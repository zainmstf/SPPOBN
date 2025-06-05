export function setupRiwayatJawabanModal() {
    const riwayatJawabanModal = document.getElementById("riwayatJawabanModal");
    const riwayatJawabanTable = $("#riwayatJawabanTable");
    const riwayatJawabanLoading = document.getElementById(
        "riwayatJawabanLoading"
    );
    const riwayatJawabanError = document.getElementById("riwayatJawabanError");
    let dataTable;
    let triggeringButton;

    riwayatJawabanModal.addEventListener("show.bs.modal", function (event) {
        triggeringButton = event.relatedTarget;
        const konsultasiID = triggeringButton.dataset.konsultasiId;

        riwayatJawabanTable.find("tbody").empty();
        riwayatJawabanLoading.style.display = "block";
        riwayatJawabanError.style.display = "none";

        fetch(`${konsultasiID}/riwayat-jawaban`)
            .then((response) => response.json())
            .then((data) => {
                riwayatJawabanLoading.style.display = "none";

                if ($.fn.DataTable.isDataTable("#riwayatJawabanTable")) {
                    dataTable.clear().rows.add(data).draw();
                    dataTable.columns.adjust().draw();
                } else {
                    dataTable = riwayatJawabanTable.DataTable({
                        data: data,
                        columns: [
                            { data: "kodePertanyaan" },
                            { data: "teksPertanyaan" },
                            { data: "jawaban" },
                            { data: "kategori" },
                            { data: "subKategori" },
                        ],
                        columnDefs: [
                            { className: "text-center", targets: [0, 2, 3, 4] },
                        ],
                        autoWidth: true,
                        responsive: true,
                        drawCallback: function () {
                            this.api().columns.adjust();
                        },
                    });
                }
            })
            .catch((error) => {
                console.error("Error fetching riwayat jawaban:", error);
                riwayatJawabanLoading.style.display = "none";
                riwayatJawabanError.style.display = "block";
            });
    });

    riwayatJawabanModal.addEventListener("hidden.bs.modal", function () {
        if ($.fn.DataTable.isDataTable("#riwayatJawabanTable")) {
            dataTable.destroy();
        }
        if (triggeringButton) {
            triggeringButton.focus();
            triggeringButton = null;
        }
    });
}
