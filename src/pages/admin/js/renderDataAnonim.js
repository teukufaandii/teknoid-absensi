$(document).ready(function () {
  let currentPage = 0;
  let totalDataAnonim = 0;
  let searchTerm = "";

  // Function to load "anonim" data
  function loadDataAnonim(page, search = "") {
    $.ajax({
      url: "api/anonim/get",
      type: "GET",
      data: {
        start: page * 5,
        search: search,
      },
      dataType: "json",
      success: function (response) {
        if (response.status === "unauthorized") {
          window.location.href = "unauthorized";
          return;
        }

        totalDataAnonim = response.total; // Set total data
        currentPage = page; // Update current page
        let DataAnonimTableBody = $("#anonim-table-body");
        DataAnonimTableBody.empty();

        if (response.data_anonim.length === 0 && currentPage > 0) {
          currentPage--; // Go to previous page if no data
          loadDataAnonim(currentPage, search);
        } else if (response.data_anonim.length === 0) {
          DataAnonimTableBody.append(
            '<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>'
          );
        } else {
          let counter = page * 5 + 1;
          response.data_anonim.forEach(function (data_anonim) {
            DataAnonimTableBody.append(`
                            <tr class="bg-gray-100">
                                <td class="px-6 py-2 text-center">${counter++}</td>
                                <td class="px-6 py-2 text-center" style="display: none;">${
                                  data_anonim.id
                                }</td>
                                <td class="px-6 py-2 text-center">${
                                  data_anonim.nomor_kartu
                                }</td>
                                <td class="px-6 py-2 text-center">${
                                  data_anonim.jam
                                }</td>
                                <td class="px-6 py-2 text-center">${
                                  data_anonim.tanggal
                                    ? data_anonim.tanggal
                                        .split("-")
                                        .reverse()
                                        .join("-")
                                    : "-"
                                } </td>
                                <td class="px-6 py-2 text-center">
                                    <button class="edit-button bg-purpleNavbar text-white px-3 py-2 rounded-xl hover:bg-purpleNavbarHover transition" data-nomor-kartu="${
                                      data_anonim.nomor_kartu
                                    }">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                    <button class="delete-button bg-red-400 text-white px-3 py-2 rounded-xl hover:bg-red-500 transition" data-id="${
                                      data_anonim.id
                                    }">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
          });
        }
        updatePaginationButtons();
      },
      error: function () {
        $("#loading").addClass("hidden");
        Swal.fire("Error!", "Terjadi kesalahan saat memuat data", "error");
      },
    });
  }

  // Function to update pagination buttons
  function updatePaginationButtons() {
    const totalPages = Math.ceil(totalDataAnonim / 5);
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

    // Enable/Disable Prev/Next buttons
    $("#prev-page").prop("disabled", currentPage === 0);
    $("#next-page").prop("disabled", currentPage >= totalPages - 1);
  }

  // Event listener for previous button
  $("#prev-page").on("click", function () {
    if (currentPage > 0) {
      currentPage--;
      loadDataAnonim(currentPage, searchTerm);
    }
  });

  // Event listener for next button
  $("#next-page").on("click", function () {
    if ((currentPage + 1) * 5 < totalDataAnonim) {
      currentPage++;
      loadDataAnonim(currentPage, searchTerm);
    }
  });

  // Event listener for pagination button
  $(document).on("click", ".pagination-button", function () {
    const page = parseInt($(this).data("page"));
    if (page !== currentPage) {
      loadDataAnonim(page, searchTerm);
    }
  });

  // Event listener for search input
  $("#searchInput").on("keyup", function () {
    searchTerm = $(this).val();
    currentPage = 0; // Reset to the first page on search
    loadDataAnonim(currentPage, searchTerm);
  });

  // Initial data load
  loadDataAnonim(currentPage, searchTerm);
});

$(document).on("click", ".edit-button", function () {
  const nomorKartu = $(this).data("nomor-kartu");
  window.location.href = `/teknoid-absensi/pegawai/add?nomor_kartu=${nomorKartu}`;
});

$(document).on("click", ".delete-button", function () {
  const id = $(this).data("id");
  deletedata_anonim(id);
});

function deletedata_anonim(id) {
  Swal.fire({
    title: "Konfirmasi",
    text: "Apakah Anda yakin ingin menghapus data anonim ini?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Ya, hapus!",
    cancelButtonText: "Batal",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "api/anonim/delete",
        type: "POST",
        data: {
          id: id,
        },
        dataType: "json",
        success: function (response) {
          if (response.status === "success") {
            Swal.fire("Berhasil!", response.message, "success").then(() => {
              setTimeout(() => {
                location.reload();
              }, 100);
            });
          } else {
            Swal.fire("Gagal!", response.message, "error");
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          Swal.fire(
            "Error!",
            "Terjadi kesalahan saat menghapus data anonim",
            "error"
          ); // Error message
        },
      });
    }
  });
}
