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
      hadirGradient.addColorStop(0, "rgba(75, 192, 192, 0.8)");
      hadirGradient.addColorStop(1, "rgba(75, 192, 192, 0.2)");

      const telatGradient = ctx.createLinearGradient(0, 0, 0, 400);
      telatGradient.addColorStop(0, "rgba(255, 206, 86, 0.8)");
      telatGradient.addColorStop(1, "rgba(255, 206, 86, 0.2)");

      const sakitGradient = ctx.createLinearGradient(0, 0, 0, 400);
      sakitGradient.addColorStop(0, "rgba(54, 162, 235, 0.8)");
      sakitGradient.addColorStop(1, "rgba(54, 162, 235, 0.2)");

      // Set chart font
      Chart.defaults.font.family =
        "'Poppins', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif";

      // Calculate dynamic max value
      const maxValue = Math.max(...hadirData, ...alphaData, ...telatData, ...sakitData);
      const dynamicMax = Math.ceil(maxValue / 1000) * 1000 + 500; // Round up with buffer

      new Chart(ctx, {
        type: "bar",
        data: {
          labels: labels,
          datasets: [
            {
              label: "Hadir",
              data: hadirData,
              backgroundColor: hadirGradient,
              borderColor: "rgb(75, 192, 192)",
              borderWidth: 2,
              borderRadius: 8,
              hoverBackgroundColor: "rgba(75, 192, 192, 0.9)",
              hoverBorderWidth: 3,
            },
            {
              label: "Alpha",
              data: alphaData,
              backgroundColor: alphaGradient,
              borderColor: "rgb(255, 99, 132)",
              borderWidth: 2,
              borderRadius: 8,
              hoverBackgroundColor: "rgba(255, 99, 132, 0.9)",
              hoverBorderWidth: 3,
            },
            {
              label: "Terlambat",
              data: telatData,
              backgroundColor: telatGradient,
              borderColor: "rgb(255, 206, 86)",
              borderWidth: 2,
              borderRadius: 8,
              hoverBackgroundColor: "rgba(255, 206, 86, 0.9)",
              hoverBorderWidth: 3,
            },
            {
              label: "Sakit",
              data: sakitData,
              backgroundColor: sakitGradient,
              borderColor: "rgb(54, 162, 235)",
              borderWidth: 2,
              borderRadius: 8,
              hoverBackgroundColor: "rgba(54, 162, 235, 0.9)",
              hoverBorderWidth: 3,
            }
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          interaction: {
            intersect: false,
            mode: 'index'
          },
          plugins: {
            legend: {
              position: "top",
              labels: {
                usePointStyle: true,
                padding: 20,
                font: {
                  size: 12,
                },
                boxWidth: 12,
                boxHeight: 12
              },
            },
            title: {
              display: true,
              text: "Statistik Kehadiran Karyawan - Tahun 2025",
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
              cornerRadius: 8,
              displayColors: true,
              callbacks: {
                label: function (context) {
                  let label = context.dataset.label || "";
                  if (label) {
                    label += ": ";
                  }
                  label += context.parsed.y.toLocaleString() + " orang";
                  return label;
                },
                footer: function(tooltipItems) {
                  let total = 0;
                  tooltipItems.forEach(item => {
                    total += item.parsed.y;
                  });
                  return 'Total: ' + total.toLocaleString() + ' orang';
                }
              },
            },
          },
          scales: {
            y: {
              beginAtZero: true,
              max: dynamicMax,
              grid: {
                color: "rgba(200, 200, 200, 0.15)",
                drawBorder: false,
              },
              ticks: {
                stepSize: Math.ceil(dynamicMax / 10),
                font: {
                  size: 12,
                },
                color: "#666",
                callback: function(value) {
                  return value.toLocaleString();
                }
              },
              title: {
                display: true,
                text: "Jumlah Karyawan",
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
                maxRotation: 45,
                minRotation: 0
              },
              title: {
                display: true,
                text: "Bulan",
                font: {
                  size: 14,
                  weight: "bold",
                },
                color: "#333",
                padding: {
                  top: 10,
                },
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
          elements: {
            bar: {
              borderRadius: 6,
              borderSkipped: false,
            }
          },
          // Responsive options
          onResize: function(chart, size) {
            if (size.width < 768) {
              chart.options.plugins.legend.position = 'bottom';
              chart.options.plugins.legend.labels.padding = 10;
              chart.options.scales.x.ticks.maxRotation = 45;
            } else {
              chart.options.plugins.legend.position = 'top';
              chart.options.plugins.legend.labels.padding = 20;
              chart.options.scales.x.ticks.maxRotation = 0;
            }
          }
        },
      });
    })
    .catch((error) => console.error("Error fetching chart data:", error));
});