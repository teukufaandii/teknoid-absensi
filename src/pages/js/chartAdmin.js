document.addEventListener("DOMContentLoaded", function () {
  fetch("api/details/get-chart-admin")
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

      const alphaData = labels.map((_, i) => data.chart[i + 1]?.alpha || 0);
      const sakitData = labels.map((_, i) => data.chart[i + 1]?.sakit || 0);
      const hadirData = labels.map((_, i) => data.chart[i + 1]?.hadir || 0);
      const telatData = labels.map((_, i) => data.chart[i + 1]?.telat || 0);

      // Set gradients for more attractive bars
      const ctx = document.getElementById("absenceChartAdmin").getContext("2d");

      // Create gradients
      const alphaGradient = ctx.createLinearGradient(0, 0, 0, 400);
      alphaGradient.addColorStop(0, "rgba(255, 99, 132, 0.8)");
      alphaGradient.addColorStop(1, "rgba(255, 99, 132, 0.2)");

      const hadirGradient = ctx.createLinearGradient(0, 0, 0, 400);
      hadirGradient.addColorStop(0, "rgba(75, 192, 92, 0.8)");
      hadirGradient.addColorStop(1, "rgba(75, 192, 92, 0.2)");

      const telatGradient = ctx.createLinearGradient(0, 0, 0, 400);
      telatGradient.addColorStop(0, "rgba(255, 159, 64, 0.8)");
      telatGradient.addColorStop(1, "rgba(255, 159, 64, 0.2)");

      const sakitGradient = ctx.createLinearGradient(0, 0, 0, 400);
      sakitGradient.addColorStop(0, "rgba(54, 162, 235, 0.8)");
      sakitGradient.addColorStop(1, "rgba(54, 162, 235, 0.2)");

      // Set chart font
      Chart.defaults.font.family =
        "'Poppins', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif";

      new Chart(ctx, {
        type: "bar",
        data: {
          labels: labels,
          datasets: [
            {
              label: "Alpha",
              data: alphaData,
              backgroundColor: alphaGradient,
              borderColor: "rgb(255, 99, 132)",
              borderWidth: 1,
              borderRadius: 6,
              hoverBackgroundColor: "rgba(255, 99, 132, 0.9)",
            },
            {
              label: "Hadir",
              data: hadirData,
              backgroundColor: hadirGradient,
              borderColor: "rgb(75, 192, 92)",
              borderWidth: 1,
              borderRadius: 6,
              hoverBackgroundColor: "rgba(75, 192, 92, 0.9)",
            },
            {
              label: "Telat",
              data: telatData,
              backgroundColor: telatGradient,
              borderColor: "rgb(255, 159, 64)",
              borderWidth: 1,
              borderRadius: 6,
              hoverBackgroundColor: "rgba(255, 159, 64, 0.9)",
            },
            {
              label: "Sakit",
              data: sakitData,
              backgroundColor: sakitGradient,
              borderColor: "rgb(54, 162, 235)",
              borderWidth: 1,
              borderRadius: 6,
              hoverBackgroundColor: "rgba(54, 162, 235, 0.9)",
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: "top",
              labels: {
                usePointStyle: true,
                padding: 20,
                font: {
                  size: 12,
                },
              },
            },
            title: {
              display: true,
              text: "Statistik Kehadiran Tahun 2025",
              font: {
                size: 20,
                weight: "bold",
              },
              padding: {
                top: 10,
                bottom: 30,
              },
              color: "#333",
            },
            tooltip: {
              backgroundColor: "rgba(0, 0, 0, 0.8)",
              padding: 12,
              titleFont: {
                size: 14,
              },
              bodyFont: {
                size: 13,
              },
              displayColors: false,
              callbacks: {
                label: function (context) {
                  let label = context.dataset.label || "";
                  if (label) {
                    label += ": ";
                  }
                  label += context.parsed.y;
                  return label;
                },
              },
            },
          },
          scales: {
            y: {
              beginAtZero: true,
              max: 5000,
              grid: {
                color: "rgba(200, 200, 200, 0.15)",
                drawBorder: false,
              },
              ticks: {
                stepSize: 500,
                font: {
                  size: 12,
                },
                color: "#666",
              },
              title: {
                display: true,
                text: "Jumlah Kehadiran",
                font: {
                  size: 14,
                  weight: "bold",
                },
                color: "#333",
                padding: {
                  bottom: 10,
                },
              },
            },
            x: {
              grid: {
                display: false,
                drawBorder: false,
              },
              ticks: {
                font: {
                  size: 12,
                },
                color: "#666",
              },
            },
          },
          animation: {
            duration: 1500,
            easing: "easeOutQuart",
          },
          layout: {
            padding: {
              top: 10,
              right: 20,
              bottom: 10,
              left: 10,
            },
          },
          barPercentage: 0.8,
          categoryPercentage: 0.9,
        },
      });
    })
    .catch((error) => console.error("Error fetching chart data:", error));
});
