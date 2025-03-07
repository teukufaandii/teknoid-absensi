// Variabel untuk popup dan tombol
const downloadButton = document.getElementById("downloadButton");
const downloadPopup = document.getElementById("downloadPopup");
const closePopup = document.getElementById("closePopup");
const rolePopup = document.getElementById("rolePopup");
const dosenTetapButton = document.getElementById("dosenTetapButton");
const karyawanButton = document.getElementById("karyawanButton");
const closeRolePopup = document.getElementById("closeRolePopup");

// Dosen Tetap Sections
const dosenTetapSection = document.getElementById("dosenTetapSection");
const harianButtonDosenTetap = document.getElementById(
  "harianButtonDosenTetap"
);
const mingguanButtonDosenTetap = document.getElementById(
  "mingguanButtonDosenTetap"
);
const bulananButtonDosenTetap = document.getElementById(
  "bulananButtonDosenTetap"
);
const mingguanDatesDosenTetap = document.getElementById(
  "mingguanDatesDosenTetap"
);
const bulananDatesDosenTetap = document.getElementById(
  "bulananDatesDosenTetap"
);
const downloadMingguanDosenTetap = document.getElementById(
  "downloadMingguanDosenTetap"
);
const downloadBulananDosenTetap = document.getElementById(
  "downloadBulananDosenTetap"
);

// Karyawan Sections
const karyawanSection = document.getElementById("karyawanSection");
const harianButtonKaryawan = document.getElementById("harianButtonKaryawan");
const mingguanButtonKaryawan = document.getElementById(
  "mingguanButtonKaryawan"
);
const bulananButtonKaryawan = document.getElementById("bulananButtonKaryawan");
const mingguanDatesKaryawan = document.getElementById("mingguanDatesKaryawan");
const bulananDatesKaryawan = document.getElementById("bulananDatesKaryawan");
const downloadMingguanKaryawan = document.getElementById(
  "downloadMingguanKaryawan"
);
const downloadBulananKaryawan = document.getElementById(
  "downloadBulananKaryawan"
);

// Menyembunyikan popups saat halaman pertama kali dimuat
downloadPopup.classList.add("hidden");
rolePopup.classList.add("hidden");
dosenTetapSection.classList.add("hidden");
karyawanSection.classList.add("hidden");

// Menampilkan Role Popup ketika tombol Download di-klik
downloadButton.addEventListener("click", () => {
  rolePopup.classList.remove("hidden"); // Tampilkan role selection popup
});

// Menutup Role Popup ketika tombol Close di-klik
closeRolePopup.addEventListener("click", () => {
  rolePopup.classList.add("hidden"); // Sembunyikan role selection popup
});

// Ketika "Dosen Tetap" dipilih
dosenTetapButton.addEventListener("click", () => {
  rolePopup.classList.add("hidden"); // Menyembunyikan role popup
  dosenTetapSection.classList.remove("hidden"); // Menampilkan dosen tetap section
  karyawanSection.classList.add("hidden"); // Menyembunyikan karyawan section
  downloadPopup.classList.remove("hidden"); // Menampilkan download popup

  // Menampilkan pilihan Harian, Mingguan, dan Bulanan untuk Dosen Tetap
  harianButtonDosenTetap.classList.remove("hidden");
  mingguanButtonDosenTetap.classList.remove("hidden");
  bulananButtonDosenTetap.classList.remove("hidden");
});

// Ketika "Karyawan" dipilih
karyawanButton.addEventListener("click", () => {
  rolePopup.classList.add("hidden"); // Menyembunyikan role popup
  karyawanSection.classList.remove("hidden"); // Menampilkan karyawan section
  dosenTetapSection.classList.add("hidden"); // Menyembunyikan dosen tetap section
  downloadPopup.classList.remove("hidden"); // Menampilkan download popup

  // Menampilkan pilihan Harian, Mingguan, dan Bulanan untuk Karyawan
  harianButtonKaryawan.classList.remove("hidden");
  mingguanButtonKaryawan.classList.remove("hidden");
  bulananButtonKaryawan.classList.remove("hidden");
});

// Menutup download popup
closePopup.addEventListener("click", () => {
  downloadPopup.classList.add("hidden"); // Menyembunyikan download popup
});

function showPopup(message) {
  const popup = document.getElementById("popup");
  const popupMessage = document.getElementById("popup-message");

  if (popup && popupMessage) {
    popupMessage.textContent = message;

    popup.classList.remove("translate-x-[120%]", "opacity-0");
    popup.classList.add("translate-x-0", "opacity-100");

    setTimeout(() => closeNotificationPopup(), 3000);
  }
}

function closeNotificationPopup() {
  const popup = document.getElementById("popup");

  popup.classList.remove("translate-x-0", "opacity-100");
  popup.classList.add("translate-x-[120%]", "opacity-0");
}

// Handle Harian filter for Dosen Tetap (Dummy API)
function toggleHarian(jabatan) {
  if (jabatan === "dosenTetap") {
    window.location.href =
      "api/user/download-dosen?filter=harian&jabatan=dosenTetap";
  } else if (jabatan === "Karyawan") {
    window.location.href =
      "api/user/download-karyawan?filter=harian&jabatan=Karyawan";
  }
}

// Fungsi untuk menghitung selisih tanggal
function getDateDifference(start, end) {
  const startDate = new Date(start);
  const endDate = new Date(end);
  const diffTime = Math.abs(endDate - startDate);
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24)); // Menghitung perbedaan dalam hari
}

function toggleMingguan(jabatan) {
  const jabatanPrefix = jabatan === "dosenTetap" ? "DosenTetap" : "Karyawan";
  const mingguanDatesElement =
    jabatan === "dosenTetap" ? mingguanDatesDosenTetap : mingguanDatesKaryawan;

  mingguanDatesElement.classList.remove("hidden"); // Tampilkan form tanggal mingguan

  if (jabatan === "dosenTetap") {
    downloadMingguanDosenTetap.onclick = function () {
      const start = document.getElementById(`startMingguan${jabatanPrefix}`);
      const end = document.getElementById(`endMingguan${jabatanPrefix}`);

      if (start && end) {
        const startValue = start.value;
        const endValue = end.value;

        if (startValue && endValue) {
          const diffDays = getDateDifference(startValue, endValue);

          if (diffDays > 7) {
            alert(
              "Rentang tanggal untuk mingguan tidak boleh lebih dari 7 hari."
            );
            return;
          }

          // Dummy API for Dosen Tetap (Redirecting to a dummy URL)
          window.location.href = `api/user/download-dosen?filter=mingguan&start=${startValue}&end=${endValue}&jabatan=${jabatanPrefix}`;
        } else {
          alert("Harap isi kedua tanggal untuk filter mingguan.");
        }
      } else {
        alert("Elemen input tanggal tidak ditemukan.");
      }
    };
  } else {
    downloadMingguanKaryawan.onclick = function () {
      const start = document.getElementById(`startMingguan${jabatanPrefix}`);
      const end = document.getElementById(`endMingguan${jabatanPrefix}`);

      if (start && end) {
        const startValue = start.value;
        const endValue = end.value;

        if (startValue && endValue) {
          const diffDays = getDateDifference(startValue, endValue);

          if (diffDays > 7) {
            showPopup(
              "Rentang tanggal untuk mingguan tidak boleh lebih dari 7 hari."
            );
            return;
          }

          // Actual API for Karyawan
          window.location.href = `api/user/download-karyawan?filter=mingguan&start=${startValue}&end=${endValue}&jabatan=${jabatanPrefix}`;
        } else {
          showPopup("Harap isi kedua tanggal untuk filter mingguan.");
        }
      } else {
        showPopup("Elemen input tanggal tidak ditemukan.");
      }
    };
  }
}
// Handle Bulanan filter for Dosen Tetap (Dummy API)
function toggleBulanan(jabatan) {
  const jabatanPrefix = jabatan === "dosenTetap" ? "DosenTetap" : "Karyawan";
  const bulananDatesElement =
    jabatan === "dosenTetap" ? bulananDatesDosenTetap : bulananDatesKaryawan;

  console.log(jabatanPrefix);

  // Menampilkan form tanggal bulanan
  bulananDatesElement.classList.remove("hidden");

  if (jabatan === "dosenTetap") {
    downloadBulananDosenTetap.onclick = function () {
      const startElement = document.getElementById(
        `startBulanan${jabatanPrefix}`
      );
      const endElement = document.getElementById(`endBulanan${jabatanPrefix}`);

      if (startElement && endElement) {
        const start = startElement.value;
        const end = endElement.value;

        if (start && end) {
          const diffDays = getDateDifference(start, end);

          if (diffDays > 30) {
            showPopup(
              "Rentang tanggal untuk bulanan tidak boleh lebih dari 30 hari."
            );
            return;
          }

          // Redirect ke Dummy API untuk Dosen Tetap
          window.location.href = `api/user/download-dosen?filter=bulanan&start=${start}&end=${end}&jabatan=${jabatanPrefix}`;
        } else {
          showPopup("Harap isi kedua tanggal untuk filter bulanan.");
        }
      } else {
        showPopup("Elemen tanggal tidak ditemukan.");
      }
    };
  } else {
    downloadBulananKaryawan.onclick = function () {
      console.log("Download Button Karyawan diklik");
      const startElement = document.getElementById(
        `startBulanan${jabatanPrefix}`
      );
      const endElement = document.getElementById(`endBulanan${jabatanPrefix}`);

      if (startElement && endElement) {
        const start = startElement.value;
        const end = endElement.value;

        if (start && end) {
          const diffDays = getDateDifference(start, end);

          if (diffDays > 30) {
            showPopup(
              "Rentang tanggal untuk bulanan tidak boleh lebih dari 30 hari."
            );
            return;
          }

          // Redirect ke API yang sesuai untuk Karyawan
          window.location.href = `api/user/download-karyawan?filter=bulanan&start=${start}&end=${end}&jabatan=${jabatanPrefix}`;
        } else {
          showPopup("Harap isi kedua tanggal untuk filter bulanan.");
        }
      } else {
        showPopup("Elemen tanggal tidak ditemukan.");
      }
    };
  }
}

// Event Listeners for Mingguan and Bulanan buttons
mingguanButtonDosenTetap.addEventListener("click", () =>
  toggleMingguan("dosenTetap")
);
bulananButtonDosenTetap.addEventListener("click", () =>
  toggleBulanan("dosenTetap")
);

mingguanButtonKaryawan.addEventListener("click", () =>
  toggleMingguan("karyawan")
);
bulananButtonKaryawan.addEventListener("click", () =>
  toggleBulanan("karyawan")
);
