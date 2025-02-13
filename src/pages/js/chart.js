document.addEventListener("DOMContentLoaded", function () {
  fetch("api/user/get-details")
    .then((response) => response.json())
    .then((data) => {
      if (data.error) {
        console.error(data.error);
        return;
      }

      const labels = [
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
      ];

      const hadirData = labels.map(
        (_, i) => data.chart[i + 1]?.total_hadir || 0
      );
      const sakitData = labels.map(
        (_, i) => data.chart[i + 1]?.total_sakit || 0
      );
      const izinData = labels.map((_, i) => data.chart[i + 1]?.total_izin || 0);

      const ctx = document.getElementById("absenceChart").getContext("2d");
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
          ],
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
              max: 30,
              ticks: {
                stepSize: 5,
              },
            },
          },
        },
      });
    })
    .catch((error) => console.error("Error fetching chart data:", error));
});
