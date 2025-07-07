$(document).ready(function () {
  let currentPage = 0;
  let searchTerm = "";
  let totalDataPegawai = 0;
  const dataPerPage = 10;

  function loadDataPegawai(page, search = "") {
    $("#loading").removeClass("hidden");
    $.ajax({
      url: "api/users/get-pegawai",
      type: "GET",
      data: {
        start: page * dataPerPage,
        search: search,
      },
      dataType: "json",
      success: function (response) {
        $("#loading").addClass("hidden");
        if (response.status === "unauthorized") {
          window.location.href = "unauthorized";
          return;
        }

        totalDataPegawai = response.total;
        currentPage = page;
        renderData(response.data_pegawai);
        updatePaginationButtons();
      },
      error: function () {
        $("#loading").addClass("hidden");
        Swal.fire("Error!", "Terjadi kesalahan saat memuat data", "error");
      },
    });
  }

  function renderData(data) {
    const tableBody = $("#pegawai-table-body");
    tableBody.empty();

    if (data.length === 0) {
      tableBody.append(
        '<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>'
      );
      return;
    }

    data.forEach((data_pegawai, index) => {
      tableBody.append(`
        <tr class="bg-gray-100">
            <td class="px-6 py-2 text-center">${
              index + 1 + currentPage * dataPerPage
            }</td>
            <td class="px-6 py-2 text-center">${data_pegawai.noinduk}</td>
            <td class="px-6 py-2 text-center">${data_pegawai.nama}</td>
            <td class="px-6 py-2 text-center">${data_pegawai.jabatan}</td>
            <td class="px-6 py-2 text-center">${data_pegawai.role}</td>
            <td class="px-6 py-2 text-center">
                <a href="pegawai/edit?id_pg=${data_pegawai.id_pg}">
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
      $("#tablePegawai th").click(function () {
        let currentSort = $(this).attr("data-order") || "desc";
        let newSort = currentSort === "asc" ? "desc" : "asc";

        $(this).attr("data-order", newSort);
        updateSortIcons(this, newSort);
      });

      new Tablesort(document.getElementById("tablePegawai"));
    });
  }

  function updatePaginationButtons() {
    const totalPages = Math.ceil(totalDataPegawai / dataPerPage);
    const paginationContainer = $("#pagination-container");

    paginationContainer.find(".pagination-button").remove();

    for (let i = 0; i < totalPages; i++) {
      const button = $(
        `<button class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition shadow-xl drop-shadow-xl pagination-button" data-page="${i}">${
          i + 1
        }</button>`
      );
      button.addClass(i === currentPage ? "active-button" : "inactive-button");
      button.insertBefore("#next-page");
    }

    $("#first-page").prop("disabled", currentPage === 0);
    $("#prev-page").prop("disabled", currentPage === 0);
    $("#next-page").prop("disabled", currentPage >= totalPages - 1);
    $("#last-page").prop("disabled", currentPage >= totalPages - 1);
  }

  $("#first-page").on("click", function () {
    if (currentPage > 0) {
      loadDataPegawai(0, searchTerm);
    }
  });

  $("#last-page").on("click", function () {
    const totalPages = Math.ceil(totalDataPegawai / dataPerPage);
    if (currentPage < totalPages - 1) {
      loadDataPegawai(totalPages - 1, searchTerm);
    }
  });

  $("#prev-page").on("click", function () {
    if (currentPage > 0) {
      loadDataPegawai(--currentPage, searchTerm);
    }
  });

  $("#next-page").on("click", function () {
    if ((currentPage + 1) * dataPerPage < totalDataPegawai) {
      loadDataPegawai(++currentPage, searchTerm);
    }
  });

  $(document).on("click", ".pagination-button", function () {
    const page = parseInt($(this).data("page"));
    if (page !== currentPage) {
      loadDataPegawai(page, searchTerm);
    }
  });

  $("#searchInput").on("keyup", function () {
    searchTerm = $(this).val();
    loadDataPegawai(0, searchTerm);
  });

  loadDataPegawai(currentPage);

  // Delete button functionality
  $(document).on("click", ".delete-button", function () {
    const id_pg = $(this).data("id");
    deletedata_pegawai(id_pg);
  });

  function deletedata_pegawai(id_pg) {
    Swal.fire({
      title: "Konfirmasi",
      text: "Apakah Anda yakin ingin menghapus data pegawai ini?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Ya, hapus!",
      cancelButtonText: "Batal",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "api/users/delete-user",
          type: "POST",
          data: {
            id_pg: id_pg,
          },
          dataType: "json",
          success: function (response) {
            if (response.status === "success") {
              Swal.fire("Berhasil!", response.message, "success").then(() => {
                loadDataPegawai(currentPage, searchTerm); // Refresh data after deletion
              });
            } else {
              Swal.fire("Gagal!", response.message, "error");
            }
          },
          error: function () {
            Swal.fire(
              "Error!",
              "Terjadi kesalahan saat menghapus data pegawai",
              "error"
            );
          },
        });
      }
    });
  }
});
