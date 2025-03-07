$(document).ready(function () {
  let currentPage = 0;
  let searchTerm = "";
  let totalDataAbsensi = 0;

  function loadDataAbsensi(page, search = "") {
    $("#loading").removeClass("hidden");
    $.ajax({
      url: "api/users/get-absensi",
      type: "GET",
      data: {
        start: page * 5,
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
        currentPage = page;
        renderData(response.data_absensi);
        updatePaginationButtons();
      },
      error: function () {
        $("#loading").addClass("hidden");
        Swal.fire("Error!", "Terjadi kesalahan saat memuat data", "error");
      },
    });
  }

  function renderData(data) {
    const tableBody = $("#absensi-table-body");
    tableBody.empty();

    if (data.length === 0) {
      tableBody.append(
        '<tr><td colspan="9" class="text-center">Tidak ada data</td></tr>'
      );
      return;
    }

    data.forEach((data_absensi, index) => {
      tableBody.append(`
                    <tr class="bg-gray-100">
                        <td class="px-6 py-2 text-center">${
                          index + 1 + currentPage * 5
                        }</td>
                        <td class="px-6 py-2 text-center">${
                          data_absensi.noinduk
                        }</td>
                        <td class="px-6 py-2 text-center">${
                          data_absensi.nama
                        }</td>
                        <td class="px-6 py-2 text-center">${
                          data_absensi.jabatan
                        }</td>
                        <td class="px-6 py-2 text-center">${
                          data_absensi.sakit
                        }</td>
                        <td class="px-6 py-2 text-center">${
                          data_absensi.izin
                        }</td>
                        <td class="px-6 py-2 text-center">${
                          data_absensi.alpha
                        }</td>
                        <td class="px-6 py-2 text-center">${
                          data_absensi.cuti
                        }</td>
                        <td class="px-6 py-2 text-center">
                            <a href="absensi/edit?id_pg=${data_absensi.id_pg}">
                            <button class="bg-purpleNavbar text-white px-3 py-2 rounded-xl hover:bg-purpleNavbarHover transition">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            </a>
                        </td>
                    </tr>
                    `);
    });

    function updateSortIcons(th, order) {
      $(".sort-icon").removeClass("active-sort");

      let sortUp = $(th).find(".fa-sort-up");
      let sortDown = $(th).find(".fa-sort-down");

      if (order === "asc") {
        sortUp.addClass("active-sort");
        sortDown.removeClass("active-sort");
      } else {
        sortDown.addClass("active-sort");
        sortUp.removeClass("active-sort");
      }
    }

    $(document).ready(function () {
      $("#tableAbsensi th").click(function () {
        let currentSort = $(this).attr("data-order") || "desc";
        let newSort = currentSort === "asc" ? "desc" : "asc";

        $(this).attr("data-order", newSort);
        updateSortIcons(this, newSort);
      });

      new Tablesort(document.getElementById("tableAbsensi"));
    });
  }

  function updatePaginationButtons() {
    const totalPages = Math.ceil(totalDataAbsensi / 5);
    const paginationContainer = $("#pagination-container");

    // Clear previous buttons and add new ones
    paginationContainer.find(".pagination-button").remove();

    // Create dynamic pagination buttons
    for (let i = 0; i < totalPages; i++) {
      const button = $(
        `<button class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition shadow-xl drop-shadow-xl pagination-button" data-page="${i}">${
          i + 1
        }</button>`
      );
      button.addClass(i === currentPage ? "active-button" : "inactive-button");
      button.insertBefore("#next-page"); // Insert before "Next" button
    }

    // Enable/Disable navigation buttons
    $("#first-page").prop("disabled", currentPage === 0);
    $("#prev-page").prop("disabled", currentPage === 0);
    $("#next-page").prop("disabled", currentPage >= totalPages - 1);
    $("#last-page").prop("disabled", currentPage >= totalPages - 1);
  }

  $("#first-page").on("click", function () {
    if (currentPage > 0) {
      loadDataAbsensi(0, searchTerm);
    }
  });

  $("#last-page").on("click", function () {
    const totalPages = Math.ceil(totalDataAbsensi / 5);
    if (currentPage < totalPages - 1) {
      loadDataAbsensi(totalPages - 1, searchTerm);
    }
  });

  $("#prev-page").on("click", function () {
    if (currentPage > 0) {
      loadDataAbsensi(--currentPage, searchTerm);
    }
  });

  $("#next-page").on("click", function () {
    if ((currentPage + 1) * 5 < totalDataAbsensi) {
      loadDataAbsensi(++currentPage, searchTerm);
    }
  });

  $(document).on("click", ".pagination-button", function () {
    const page = parseInt($(this).data("page"));
    if (page !== currentPage) {
      loadDataAbsensi(page, searchTerm);
    }
  });

  $("#searchInput").on("keyup", function () {
    searchTerm = $(this).val();
    loadDataAbsensi(0, searchTerm);
  });

  loadDataAbsensi(currentPage);
});
