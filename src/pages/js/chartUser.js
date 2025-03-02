document.addEventListener("DOMContentLoaded", function () {
  fetch("api/user/get-details")
    .then((response) => response.json())
    .then((data) => {
      if (data.error) {
        console.error(data.error);
        return;
      }

      const labels = [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "Mei",
        "Jun",
        "Jul",
        "Agu",
        "Sep",
        "Okt",
        "Nov",
        "Des",
      ];

      const getDataByKey = (key) =>
        labels.map((_, i) => data.chart[i + 1]?.[key] || 0);

      const hadirData = getDataByKey("total_hadir");
      const sakitData = getDataByKey("total_sakit");
      const izinData = getDataByKey("total_izin");
      const alphaData = getDataByKey("total_alpha");

      const ctx = document.getElementById("absenceChartUser").getContext("2d");
      new Chart(ctx, {
        type: "bar",
        data: {
          labels: labels,
          datasets: [
            {
              label: "Masuk",
              data: hadirData,
              backgroundColor: "rgba(255, 99, 132, 0.2)",
              borderColor: "rgb(255, 99, 132)",
              borderWidth: 1,
            },
            {
              label: "Sakit",
              data: sakitData,
              backgroundColor: "rgba(255, 159, 64, 0.2)",
              borderColor: "rgb(255, 159, 64)",
              borderWidth: 1,
            },
            {
              label: "Izin",
              data: izinData,
              backgroundColor: "rgba(75, 192, 192, 0.2)",
              borderColor: "rgb(75, 192, 192)",
              borderWidth: 1,
            },
            {
              label: "Alpha",
              data: alphaData,
              backgroundColor: "rgba(54, 162, 235, 0.2)",
              borderColor: "rgb(75, 192, 192)",
              borderWidth: 1,
            },
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
              max: 30,
              ticks: {
                stepSize: 5,
              },
              title: {
                display: true,
                text: "Jumlah Kehadiran",
                font: {
                  size: 14,
                  weight: "bold",
                },
              },
            },
          },
        },
      });
    })
    .catch((error) => console.error("Error fetching chart data:", error));
});
