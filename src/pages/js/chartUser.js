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

      // Set up the context and gradients
      const ctx = document.getElementById("absenceChartUser").getContext("2d");

      // Create gradients for each dataset with better colors
      const hadirGradient = ctx.createLinearGradient(0, 0, 0, 400);
      hadirGradient.addColorStop(0, "rgba(46, 204, 113, 0.8)");
      hadirGradient.addColorStop(1, "rgba(46, 204, 113, 0.2)");

      const sakitGradient = ctx.createLinearGradient(0, 0, 0, 400);
      sakitGradient.addColorStop(0, "rgba(52, 152, 219, 0.8)");
      sakitGradient.addColorStop(1, "rgba(52, 152, 219, 0.2)");

      const izinGradient = ctx.createLinearGradient(0, 0, 0, 400);
      izinGradient.addColorStop(0, "rgba(241, 196, 15, 0.8)");
      izinGradient.addColorStop(1, "rgba(241, 196, 15, 0.2)");

      const alphaGradient = ctx.createLinearGradient(0, 0, 0, 400);
      alphaGradient.addColorStop(0, "rgba(231, 76, 60, 0.8)");
      alphaGradient.addColorStop(1, "rgba(231, 76, 60, 0.2)");

      // Set chart font
      Chart.defaults.font.family =
        "'Poppins', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif";

      // Calculate dynamic max value
      const maxValue = Math.max(...hadirData, ...sakitData, ...izinData, ...alphaData);
      const dynamicMax = Math.max(30, Math.ceil(maxValue / 5) * 5 + 5); // Minimum 30, with buffer

      new Chart(ctx, {
        type: "bar",
        data: {
          labels: labels,
          datasets: [
            {
              label: "Masuk",
              data: hadirData,
              backgroundColor: hadirGradient,
              borderColor: "rgb(46, 204, 113)",
              borderWidth: 2,
              borderRadius: 8,
              hoverBackgroundColor: "rgba(46, 204, 113, 0.9)",
              hoverBorderWidth: 3,
            },
            {
              label: "Sakit",
              data: sakitData,
              backgroundColor: sakitGradient,
              borderColor: "rgb(52, 152, 219)",
              borderWidth: 2,
              borderRadius: 8,
              hoverBackgroundColor: "rgba(52, 152, 219, 0.9)",
              hoverBorderWidth: 3,
            },
            {
              label: "Izin",
              data: izinData,
              backgroundColor: izinGradient,
              borderColor: "rgb(241, 196, 15)",
              borderWidth: 2,
              borderRadius: 8,
              hoverBackgroundColor: "rgba(241, 196, 15, 0.9)",
              hoverBorderWidth: 3,
            },
            {
              label: "Alpha",
              data: alphaData,
              backgroundColor: alphaGradient,
              borderColor: "rgb(231, 76, 60)",
              borderWidth: 2,
              borderRadius: 8,
              hoverBackgroundColor: "rgba(231, 76, 60, 0.9)",
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
              text: "Statistik Kehadiran Pribadi - Tahun 2025",
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
                  label += context.parsed.y + " hari";
                  return label;
                },
                footer: function(tooltipItems) {
                  let total = 0;
                  tooltipItems.forEach(item => {
                    total += item.parsed.y;
                  });
                  return 'Total: ' + total + ' hari';
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
                stepSize: 5,
                font: {
                  size: 12,
                },
                color: "#666",
                callback: function(value) {
                  return value;
                }
              },
              title: {
                display: true,
                text: "Jumlah Hari",
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