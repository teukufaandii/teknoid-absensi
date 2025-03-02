document.addEventListener("DOMContentLoaded", function () {
    fetch("api/details/get-chart-admin")
        .then((response) => response.json())
        .then((data) => {
            if (data.error) {
                console.error(data.error);
                return;
            }

            const labels = [
                "Jan", "Feb", "Mar", "Apr", "Mei", "Jun",
                "Jul", "Agu", "Sep", "Okt", "Nov", "Des"
            ];

            const alphaData = labels.map((_, i) => data.chart[i + 1]?.alpha || 0);
            const sakitData = labels.map((_, i) => data.chart[i + 1]?.sakit || 0);
            const hadirData = labels.map((_, i) => data.chart[i + 1]?.hadir || 0);
            const telatData = labels.map((_, i) => data.chart[i + 1]?.telat || 0);

            const ctx = document.getElementById("absenceChartAdmin").getContext("2d");
            new Chart(ctx, {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: "Alpha",
                            data: alphaData,
                            backgroundColor: "rgba(255, 99, 132, 0.2)",
                            borderColor: "rgb(255, 99, 132)",
                            borderWidth: 1,
                        },
                        {
                            label: "Hadir",
                            data: hadirData,
                            backgroundColor: "rgba(255, 159, 64, 0.2)",
                            borderColor: "rgb(255, 159, 64)",
                            borderWidth: 1,
                        },
                        {
                            label: "Telat",
                            data: telatData,
                            backgroundColor: "rgba(75, 192, 192, 0.2)",
                            borderColor: "rgb(75, 192, 192)",
                            borderWidth: 1,
                        },
                        {
                            label: "Sakit",
                            data: sakitData,
                            backgroundColor: "rgba(54, 162, 235, 0.2)",
                            borderColor: "rgb(54, 162, 235)",
                            borderWidth: 1,
                        }
                    ],
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: "Statistik Kehadiran Tahun Ini",
                            font: {
                                size: 18,
                                weight: "bold",
                            },
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 5000,
                            ticks: { stepSize: 500 },
                            title: {
                                display: true,
                                text: "Jumlah Kehadiran",
                                font: { size: 14, weight: "bold" },
                            },
                        },
                    },
                },
            });
        })
        .catch((error) => console.error("Error fetching chart data:", error));
});
