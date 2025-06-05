// dashboard-admin.js

document.addEventListener("DOMContentLoaded", function () {
    // Grafik Perkembangan 7 Hari Terakhir
    function fetchChartData() {
        fetch("chart-data-dashboard")
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
                    name: "Pending",
                    data: chartData.pending,
                },
                {
                    name: "Ongoing",
                    data: chartData.ongoing,
                },
                {
                    name: "Completed",
                    data: chartData.completed,
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
            document.querySelector("#chart-konsultasi"),
            areaOptions
        );

        konsultasiChart.render();
    }
    fetchChartData();

    // Grafik Rata-rata Rating Umpan Balik per Tanggal
    const allCategories = JSON.parse(
        document.getElementById("grafikUmpanBalikData").getAttribute("data-categories")
    );
    const allSeriesData = JSON.parse(
        document.getElementById("grafikUmpanBalikData").getAttribute("data-series")
    );

    // Hitung 7 hari terakhir
    const last7Days = allCategories.slice(-7);
    const last7DaysData = allSeriesData.slice(-7);

    const optionsUmpanBalik = {
        chart: {
            type: "line",
            height: 350,
            animations: {
                enabled: true,
                easing: "easeinout",
                speed: 800,
                animateGradually: {
                    enabled: true,
                    delay: 150,
                },
                dynamicAnimation: {
                    enabled: true,
                    speed: 350,
                },
            },
            zoom: {
                enabled: true,
                type: "x",
                autoScaleYaxis: true,
            },
        },
        series: [
            {
                name: "Rata-rata Rating",
                data: last7DaysData.map(function (value) {
                    // Format angka rata-rata rating
                    if (Number.isInteger(value)) {
                        return value;
                    } else {
                        return parseFloat(value.toFixed(1));
                    }
                }),
                color: "#2ecc71",
            },
        ],
        xaxis: {
            categories: last7Days,
            type: "category",
            labels: {
                rotate: -45,
                rotateAlways: false,
                formatter: function (value) {
                    if (value && typeof value === "string") {
                        const date = new Date(value);
                        if (!isNaN(date)) {
                            const day = date.getDate();
                            const month = date.toLocaleString("default", {
                                month: "short",
                            });
                            const year = date.getFullYear();
                            return `${day} ${month} ${year}`; // Format tanggal menjadi "dd MMM YYYY"
                        }
                    }
                    return value;
                },
                style: {
                    fontSize: "11px",
                },
            },
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            curve: "smooth",
            width: 3,
            colors: ["#2ecc71"],
            gradient: {
                enabled: true,
                shade: "light",
                gradientToColors: ["#3498db"],
                shadeIntensity: 1,
                type: "horizontal",
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 100, 100, 100],
            },
        },
        tooltip: {
            x: {
                format: "dd MMM YYYY",
            },
        },
        yaxis: {
            min: 0,
            max: 5,
            tickAmount: 5,
        },
        markers: {
            size: 5,
            hover: {
                size: 7,
            },
        },
    };

    const chartUmpanBalik = new ApexCharts(
        document.querySelector("#chart-rating-umpan-balik"),
        optionsUmpanBalik
    );
    chartUmpanBalik.render();
});
