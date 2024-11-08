<?php
session_start();

if (!isset($_SESSION['token'])) {
  header('Location: login');
  exit();
}

// Cek session akses admin
if ($_SESSION['role'] !== 'admin') {
  header('Location: ../unauthorized');
  exit();
}

$username = htmlspecialchars($_SESSION['name']);
$role = $_SESSION['role'];
$id = $_SESSION['user_id'];
$token = $_SESSION['token'];

$detailId = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;
$id_pg = isset($_GET['id_pg']) ? htmlspecialchars($_GET['id_pg']) : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Rekap</title>
  <link href="../../css/output.css" rel="stylesheet">
  <link href="../../src/pages/css/font/poppins-font.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <div class="flex flex-col md:flex-row lg:flex-row h-screen">
    <!-- Side Navigation -->
    <?php include('src/pages/navbar/sidenav.php') ?>

    <div id="content" class="min-h-screen inline-flex flex-col flex-1 bg-mainBgColor ml-56">
      <!-- Top Navigation -->
      <?php include('src/pages/navbar/topnav.php') ?>

      <!-- Main Content -->
      <main class="flex-1 p-6 bg-mainBgColor mainContent">
        <div class="flex justify-between items-center border-b border-gray-500">
          <h1 class="text-lg sm:text-xl md:text-3xl py-2 font-Poppins font-semibold">Edit Data Absensi</h1>
          <a href="../../absensi/edit?id_pg=<?php echo $id_pg; ?>">
            <button class="bg-purpleNavbar text-white px-4 py-2 rounded-lg hover:bg-purpleNavbarHover transition duration-200">Kembali</button>
          </a>
        </div>
        <div class="max-w-full mx-auto py-6">
          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Tanggal</label>
            <input
              type="date"
              class="w-full border-2 border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar cursor-not-allowed"
              value=""
              name="tanggal"
              readonly />
          </div>

          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Keterangan</label>
            <div class="flex items-center justify-between space-x-8 sm:justify-start">
              <label class="flex items-center text-gray-600">
                <input
                  type="radio"
                  name="keterangan"
                  value="sakit"
                  class="hidden" />
                <span class="w-3.5 h-3.5 inline-block mr-2 border-2 border-purpleNavbar rounded-full flex-shrink-0 transition"></span>
                Sakit
              </label>
              <label class="flex items-center text-gray-600">
                <input
                  type="radio"
                  name="keterangan"
                  value="izin"
                  class="hidden" />
                <span class="w-3.5 h-3.5 inline-block mr-2 border-2 border-purpleNavbar rounded-full flex-shrink-0 transition"></span>
                Izin
              </label>
              <label class="flex items-center text-gray-600">
                <input
                  type="radio"
                  name="keterangan"
                  value="cuti"
                  class="hidden" />
                <span class="w-3.5 h-3.5 inline-block mr-2 border-2 border-purpleNavbar rounded-full flex-shrink-0 transition"></span>
                Cuti
              </label>
              <label class="flex items-center text-gray-600">
                <input
                  type="radio"
                  name="keterangan"
                  value="hadir"
                  class="hidden" />
                <span class="w-3.5 h-3.5 inline-block mr-2 border-2 border-purpleNavbar rounded-full flex-shrink-0 transition"></span>
                Hadir
              </label>
              <label class="flex items-center text-gray-600">
                <input
                  type="radio"
                  name="keterangan"
                  value="alpha"
                  class="hidden" />
                <span class="w-3.5 h-3.5 inline-block mr-2 border-2 border-purpleNavbar rounded-full flex-shrink-0 transition"></span>
                Alpha
              </label>

            </div>
          </div>

          <div class="flex justify-between mt-6">
            <button class="bg-purpleNavbar text-white px-6 py-2 rounded-lg hover:bg-purpleNavbarHover transition duration-200" onclick="saveData()">Simpan</button>
          </div>
        </div>
      </main>
    </div>
  </div>

  <?php include('src/pages/navbar/profileInfo.php') ?>
</body>

<script>
  function saveData() {
    const userId = <?php echo json_encode($id_pg); ?>;
    const detailId = <?php echo json_encode($detailId); ?>;
    const data = {
      tanggal: document.querySelector('input[name="tanggal"]').value,
      keterangan: document.querySelector('input[name="keterangan"]:checked').value,
    };

    // Show confirmation popup
    Swal.fire({
      title: 'Konfirmasi',
      text: "Apakah Anda yakin ingin mengupdate data absensi?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Ya, update!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        fetch('../../api/details/update', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              id: detailId,
              user_id: userId,
              data: data
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire({
                icon: 'success',
                title: 'Data updated successfully!',
                showConfirmButton: false,
                timer: 1500
              }).then(() => {
                // Redirect after 1.5 seconds
                setTimeout(() => {
                  window.location.href = '/teknoid-absensi/absensi/edit?id_pg=' + userId;
                }, 1500);
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Failed to update data',
                text: data.message || 'Please try again.'
              });
            }
          })
          .catch(error => {
            console.error('Error:', error);
            Swal.fire({
              icon: 'error',
              title: 'An error occurred',
              text: 'Please try again later.'
            });
          });
      }
    });
  }

  function fetchData() {
    const userId = <?php echo json_encode($id_pg); ?>;
    const detailId = <?php echo json_encode($detailId); ?>;

    fetch(`../../api/users/fetch-preview-detail?id_pg=${userId}&id=${detailId}&start=0&limit=1`)
      .then(response => response.json())
      .then(data => {
        console.log(data); 
        if (data.status === 'success' && data.preview_data_absensi.length > 0) {
          const attendanceData = data.preview_data_absensi.find(item => item.id == detailId);
          if (attendanceData) {
            document.querySelector('input[name="tanggal"]').value = attendanceData.tanggal;
            document.querySelectorAll('input[name="keterangan"]').forEach(radio => {
              if (radio.value === attendanceData.keterangan) {
                radio.checked = true;
              }
            });
          } else {
            console.error('No matching data found for the provided detailId.');
          }
        } else {
          console.error('Data not found or no attendance data available.');
        }
      })
      .catch(error => console.error('Error fetching data:', error));
  }

  document.addEventListener('DOMContentLoaded', fetchData);
</script>


</html>