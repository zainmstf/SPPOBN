// Hitung Progress Riwayat Konsultasi
document.addEventListener("DOMContentLoaded", function () {
    const progressBars = document.querySelectorAll(".progress-bar");
    progressBars.forEach(function (progressBar) {
        const progress = progressBar.dataset.progress;
        progressBar.style.height = progress + "%";
    });
});

// Modal Edukasi
import { setupEdukasiModal } from "../components/modal-edukasi.js";
setupEdukasiModal();

// Chart Perkembangan 7 Hari Terakhir
document.addEventListener("DOMContentLoaded", function () {
    function fetchChartData() {
        fetch("api/chart-data-dashboard")
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then((chartData) => {
                renderChart(chartData);
            })
            .catch((error) => {
                console.error("Error fetching chart data:", error);
            });
    }

    function renderChart(chartData) {
        const areaOptions = {
            series: [
                {
                    name: "Belum Selesai",
                    data: chartData.belum_selesai,
                },
                {
                    name: "Sedang Berjalan",
                    data: chartData.sedang_berjalan,
                },
                {
                    name: "Selesai",
                    data: chartData.selesai,
                },
            ],
            chart: {
                height: 350,
                type: "area",
                toolbar: {
                    show: true,
                },
            },
            colors: ["#FFA500", "#1E90FF", "#32CD32"],
            dataLabels: {
                enabled: false,
            },
            stroke: {
                curve: "smooth",
                width: 2,
            },
            fill: {
                type: "gradient",
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                    stops: [0, 90, 100],
                },
            },
            xaxis: {
                categories: chartData.labels,
                type: "category",
                labels: {
                    rotateAlways: false,
                    style: {
                        fontSize: "12px",
                    },
                },
            },
            yaxis: {
                min: 0,
                forceNiceScale: true,
            },
            legend: {
                position: "bottom",
                horizontalAlign: "center",
            },
            tooltip: {
                shared: true,
                intersect: false,
            },
        };

        const konsultasiChart = new ApexCharts(
            document.querySelector("#area"),
            areaOptions
        );

        konsultasiChart.render();
    }
    fetchChartData();
});
