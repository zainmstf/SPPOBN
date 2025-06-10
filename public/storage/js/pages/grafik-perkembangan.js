const chartData = JSON.parse(
    document.querySelector("#chart-data [data-chart]").dataset.chart
);

const ratingLabels = JSON.parse(
    document.querySelector("#chart-data [data-rating-labels]").dataset
        .ratingLabels
);
const ratingData = JSON.parse(
    document.querySelector("#chart-data [data-rating-data]").dataset.ratingData
);
const questionLabels = JSON.parse(
    document.querySelector("#chart-data [data-question-labels]").dataset
        .questionLabels
);
const questionData = JSON.parse(
    document.querySelector("#chart-data [data-question-data]").dataset
        .questionData
);

// Fungsi untuk membuat grafik menggunakan ApexCharts
function createChart(elementId, options) {
    const chart = new ApexCharts(
        document.querySelector(`#${elementId}`),
        options
    );
    chart.render();
}
console.log(chartData);
// Grafik Konsultasi (Area Chart) dengan Status
createChart("consultationChart", {
    series: [
        {
            name: "Belum Selesai",
            data: chartData.original.belum_selesai,
        },
        {
            name: "Sedang Berjalan",
            data: chartData.original.sedang_berjalan,
        },
        {
            name: "Selesai",
            data: chartData.original.selesai,
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
        categories: chartData.original.labels,
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
});

// Grafik Rating (Line Chart)
createChart("ratingChart", {
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
    },
    series: [
        {
            name: "Rata-rata Rating",
            data: ratingData,
            color: "#2ecc71",
        },
    ],

    xaxis: {
        categories: ratingLabels,
        type: "category",
        labels: {
            rotate: -45,
            rotateAlways: false,
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
            format: "dd MMM",
        },
    },
    yaxis: {
        min: 0,
        max: 5,
        tickAmount: 5,
    },
});

const MAX_LABEL_LENGTH = 80;

// Memotong label yang panjang
const trimmedLabels = questionLabels.map((label) =>
    label.length > MAX_LABEL_LENGTH
        ? label.substring(0, MAX_LABEL_LENGTH) + "…"
        : label
);

// Grafik Pertanyaan (Bar Chart)
createChart("questionChart", {
    chart: {
        type: "bar",
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
    },
    series: [
        {
            name: "Jumlah Jawaban",
            data: questionData,
            color: "#f39c12",
        },
    ],
    xaxis: {
        categories: trimmedLabels,
    },
    dataLabels: {
        enabled: true,
        style: {
            fontSize: "12px",
            fontWeight: "bold",
        },
    },
    fill: {
        type: "gradient",
        gradient: {
            shade: "light",
            type: "vertical",
            shadeIntensity: 0.25,
            gradientToColors: ["#ffc107"],
            inverseColors: true,
            opacityFrom: 1,
            opacityTo: 1,
            stops: [50, 100],
        },
    },

    plotOptions: {
        bar: {
            borderRadius: 4,
            columnWidth: "60%",
            hover: {
                opacity: 0.8,
            },
        },
    },
    markers: {
        colors: ["#ffc107"],
    },
});
