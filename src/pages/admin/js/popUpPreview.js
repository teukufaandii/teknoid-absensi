// Variables for popup and buttons
const downloadButton = document.getElementById("downloadButton");
const downloadPopup = document.getElementById("downloadPopup");
const closePopup = document.getElementById("closePopup");
const harianButton = document.getElementById("harianButton");
const mingguanButton = document.getElementById("mingguanButton");
const bulananButton = document.getElementById("bulananButton");
const mingguanDates = document.getElementById("mingguanDates");
const bulananDates = document.getElementById("bulananDates");
const downloadMingguan = document.getElementById("downloadMingguan");
const downloadBulanan = document.getElementById("downloadBulanan");

// Show the download popup
downloadButton.addEventListener("click", () => {
  downloadPopup.classList.remove("hidden");
});

// Close the download popup
closePopup.addEventListener("click", () => {
  downloadPopup.classList.add("hidden");
  mingguanDates.classList.add("hidden");
  bulananDates.classList.add("hidden");
  downloadMingguan.classList.add("hidden");
  downloadBulanan.classList.add("hidden");
});

function toggleHarian() {
  window.location.href = `../api/user/download-data-user?filter=harian&id_pg=${id_pg}`;
}

function getDateDifference(start, end) {
  const startDate = new Date(start);
  const endDate = new Date(end);
  const diffTime = Math.abs(endDate - startDate);
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
}

function toggleMingguan() {
  const mingguanDates = document.getElementById("mingguanDates");
  const startMingguan = document.getElementById("startMingguan");
  const endMingguan = document.getElementById("endMingguan");
  const downloadMingguan = document.getElementById("downloadMingguan");

  mingguanDates.classList.toggle("hidden");

  downloadMingguan.onclick = function () {
    const start = startMingguan.value;
    const end = endMingguan.value;

    if (start && end) {
      const diffDays = getDateDifference(start, end);

      if (diffDays > 7) {
        alert("Rentang tanggal untuk mingguan tidak boleh lebih dari 7 hari.");
        return;
      }

      window.location.href = `../api/user/download-data-user?filter=mingguan&start=${start}&end=${end}&id_pg=${id_pg}`;
    } else {
      alert("Harap isi kedua tanggal untuk filter mingguan.");
    }
  };
}

function toggleBulanan() {
  const bulananDates = document.getElementById("bulananDates");
  const startBulanan = document.getElementById("startBulanan");
  const endBulanan = document.getElementById("endBulanan");
  const downloadBulanan = document.getElementById("downloadBulanan");

  bulananDates.classList.toggle("hidden");

  downloadBulanan.onclick = function () {
    const start = startBulanan.value;
    const end = endBulanan.value;

    if (start && end) {
      const diffDays = getDateDifference(start, end);

      if (diffDays > 30) {
        alert("Rentang tanggal untuk bulanan tidak boleh lebih dari 30 hari.");
        return;
      }

      window.location.href = `../api/user/download-data-user?filter=bulanan&start=${start}&end=${end}&id_pg=${id_pg}`;
    } else {
      alert("Harap isi kedua tanggal untuk filter bulanan.");
    }
  };
}

downloadMingguan.addEventListener("click", () => {
  const start = document.getElementById("startMingguan").value;
  const end = document.getElementById("endMingguan").value;

  if (start && end) {
    window.location.href = `../api/user/download-data-user?filter=mingguan&start=${start}&end=${end}&id_pg=${id_pg}`;
  } else {
    alert("Please select both start and end dates for weekly download");
  }
});

downloadBulanan.addEventListener("click", () => {
  const start = document.getElementById("startBulanan").value;
  const end = document.getElementById("endBulanan").value;

  if (start && end) {
    window.location.href = `../api/user/download-data-user?filter=bulanan&start=${start}&end=${end}&id_pg=${id_pg}`;
  } else {
    alert("Please select both start and end dates for monthly download");
  }
});
