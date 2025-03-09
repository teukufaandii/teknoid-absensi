$(document).ready(function () {
  let currentPage = 0;
  let totalDataAbsensi = 0;
  let searchTerm = "";

  // Function to load "absensi" data
  function loadDataAbsensi(page, search = "") {
    $("#loading").removeClass("hidden");
    $.ajax({
      url: "../api/users/fetch-preview-detail",
      type: "GET",
      data: {
        id_pg: id_pg,
        start: page * 10,
        search: search,
      },
      dataType: "json",
      success: function (response) {
        $("#loading").addClass("hidden");
        if (response.status === "unauthorized") {
          window.location.href = "unauthorized";
          return;
        }

        totalDataAbsensi = response.total;
        renderData(response.preview_data_absensi, page);
        updatePaginationButtons();
      },
      error: function () {
        $("#loading").addClass("hidden");
        Swal.fire("Error!", "Terjadi kesalahan saat memuat data", "error");
      },
    });
  }

  // Function to render data into the table
  function renderData(data, page) {
    const tableBody = $("#preview-absensi-table-body");
    tableBody.empty();

    if (data.length === 0 && page > 0) {
      currentPage--; // Go to previous page if no data found
      loadDataAbsensi(currentPage, searchTerm);
    } else if (data.length === 0) {
      tableBody.append(
        '<tr><td colspan="8" class="text-center">Tidak ada data untuk <?php echo $nama_pg ?></td></tr>'
      );
    } else {
      let counter = page * 10 + 1;
      data.forEach((item) => {
        tableBody.append(`
                    <tr class="bg-gray-100">
                        <td class="px-6 py-2 text-center">${counter++}</td>
                        <td class="px-6 py-2 text-center">${item.nama}</td>
                        <td class="px-6 py-2 text-center">${
                          item.tanggal
                            ? item.tanggal.split("-").reverse().join("-")
                            : "-"
                        }</td>
                        <td class="px-6 py-2 text-center">${
                          item.scan_masuk
                        }</td>
                        <td class="px-6 py-2 text-center">${
                          item.scan_keluar
                        }</td>
                        <td class="px-6 py-2 text-center">${item.durasi}</td>
                        <td class="px-6 py-2 text-center">${
                          item.keterangan
                        }</td>
                        <td class="px-6 py-2 text-center">
                            <a href="../absensi/edit/preview?id_pg=${
                              item.id_pg
                            }&id=${item.id}">
                                <button class="bg-purpleNavbar text-white px-3 py-2 rounded-xl hover:bg-purpleNavbarHover transition"><i class="fa-solid fa-pen-to-square"></i></button>
                            </a>
                        </td>
                    </tr>
                `);
      });
    }
  }

  function updatePaginationButtons() {
    const totalPages = Math.ceil(totalDataAbsensi / 10);

    // Define the range of pages to display
    const maxButtonsToShow = 5; // Maximum number of buttons to display
    let startPage = Math.max(0, currentPage - Math.floor(maxButtonsToShow / 2));
    let endPage = Math.min(totalPages, startPage + maxButtonsToShow);

    if (endPage - startPage < maxButtonsToShow) {
      startPage = Math.max(0, endPage - maxButtonsToShow);
    }

    // Clear existing pagination buttons
    $("#pagination-container .pagination-button").remove();

    // Create pagination buttons dynamically
    for (let i = startPage; i < endPage; i++) {
      const button = $(
        `<button class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition shadow-xl drop-shadow-xl pagination-button" data-page="${i}">${
          i + 1
        }</button>`
      );
      button.addClass(i === currentPage ? "active-button" : "inactive-button");
      button.insertBefore("#next-page"); // Insert before "Next" button
    }

    // Enable/Disable First, Prev, Next, and Last buttons based on the current page
    $("#first-page").prop("disabled", currentPage === 0);
    $("#prev-page").prop("disabled", currentPage === 0);
    $("#next-page").prop("disabled", currentPage >= totalPages - 1);
    $("#last-page").prop("disabled", currentPage >= totalPages - 1);
  }

  $("#first-page").on("click", function () {
    if (currentPage > 0) {
      currentPage = 0;
      loadDataAbsensi(currentPage, searchTerm);
      updatePaginationButtons();
    }
  });

  $("#last-page").on("click", function () {
    const totalPages = Math.ceil(totalDataAbsensi / 10);
    if (currentPage < totalPages - 1) {
      currentPage = totalPages - 1;
      loadDataAbsensi(currentPage, searchTerm);
      updatePaginationButtons();
    }
  });

  // Event listener for "Previous" button
  $("#prev-page").on("click", function () {
    if (currentPage > 0) {
      loadDataAbsensi(--currentPage, searchTerm);
    }
  });

  // Event listener for "Next" button
  $("#next-page").on("click", function () {
    if ((currentPage + 1) * 10 < totalDataAbsensi) {
      loadDataAbsensi(++currentPage, searchTerm);
    }
  });

  $(document).on("click", ".pagination-button", function () {
    currentPage = parseInt($(this).data("page"));
    loadDataAbsensi(currentPage, searchTerm);
    updatePaginationButtons();
  });

  // Event listener for search input
  $("#searchInput").on("keyup", function () {
    searchTerm = $(this).val();
    currentPage = 0; // Reset to first page on search
    loadDataAbsensi(currentPage, searchTerm);
  });

  // Initial data load
  loadDataAbsensi(currentPage);
});
