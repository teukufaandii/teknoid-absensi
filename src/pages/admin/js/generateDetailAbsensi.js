function showToast(type, message) {
  const successToast = document.getElementById("toast-success");
  const errorToast = document.getElementById("toast-error");

  if (type === "success") {
    document.getElementById("success-message").innerText = message;
    successToast.classList.remove("hidden");
    successToast.classList.add("block");
    setTimeout(() => {
      successToast.classList.remove("block");
      successToast.classList.add("hidden");
      location.reload(); 
    }, 3000);
  } else if (type === "error") {
    document.getElementById("error-message").innerText = message;
    errorToast.classList.remove("hidden");
    errorToast.classList.add("block");
    setTimeout(() => {
      errorToast.classList.remove("block");
      errorToast.classList.add("hidden");
    }, 3000);
  }
}

$("#addButton").on("click", function () {
  Swal.fire({
    title: "Generate Detail Absen",
    text: "Pilih opsi untuk generate detail absensi:",
    icon: "info",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Per Bulan",
    cancelButtonText: "Per Minggu",
    html: '<button id="customCancel" class="swal2-cancel swal2-styled">Cancel</button>',
    allowOutsideClick: false,
  }).then((result) => {
    let option;
    if (result.isConfirmed) {
      option = "bulanan";
    } else if (result.dismiss === Swal.DismissReason.cancel) {
      option = "mingguan";
    } else {
      return;
    }

    if (option) {
      document.getElementById("loadingSpinner").classList.remove("hidden");

      $.ajax({
        url: "api/users/generate-absensi-details",
        type: "GET",
        data: {
          option: option,
        },
        dataType: "json",
        success: function (response) {
          document.getElementById("loadingSpinner").classList.add("hidden");

          if (response.status === "success") {
            showToast("success", response.message);
          } else {
            showToast("error", response.message);
          }
        },
        error: function () {
          document.getElementById("loadingSpinner").classList.add("hidden");
          showToast("error", "Terjadi kesalahan saat memproses permintaan.");
        },
      });
    }
  });

  $(document).on("click", "#customCancel", function () {
    Swal.close();
  });
});
