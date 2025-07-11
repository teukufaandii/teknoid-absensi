<?php
session_start();

if (!isset($_SESSION['token'])) {
  header('Location: login');
  exit();
}

// Cek session akses admin
if ($_SESSION['role'] !== 'admin') {
  header('Location: unauthorized');
  exit();
}

$username = htmlspecialchars($_SESSION['name']);
$role = $_SESSION['role'];
$id = $_SESSION['user_id'];
$token = $_SESSION['token'];

$id_pg = isset($_GET['id_pg']) ? htmlspecialchars($_GET['id_pg']) : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Pegawai</title>
  <link href="/teknoid-absensi/css/output.css" rel="stylesheet">
  <link href="/teknoid-absensi/src/pages/css/font/poppins-font.css" rel="stylesheet">
  <link href="/teknoid-absensi/src/pages/css/responsive/resp.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
          <h1 class="text-lg sm:text-xl md:text-3xl py-2 font-Poppins font-semibold">Edit Data Pegawai</h1>
          <a href="../pegawai">
            <button class="bg-purpleNavbar text-white px-4 py-2 rounded-lg hover:bg-purpleNavbarHover transition duration-200">Kembali</button>
          </a>
        </div>
        <div class="w-full mx-auto py-6">
          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Nomor Kartu</label>
            <input type="text" name="nomor_kartu" placeholder="Masukkan Nomor Kartu"
              class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar"
              defaultValue="123456789" />
          </div>

          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Nama Lengkap</label>
            <input
              type="text"
              name="nama"
              placeholder="Masukkan Nama Lengkap"
              class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar"
              defaultValue="Jane Doe" />
          </div>

          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">NIDN</label>
            <input
              type="text"
              name="noinduk"
              placeholder="Masukkan Nomor Induk"
              class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar" />
          </div>

          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Jenis Kelamin</label>
            <div class="flex items-center space-x-6">
              <label class="flex items-center text-gray-600">
                <input
                  type="radio"
                  name="jenis_kelamin"
                  value="Laki-laki"
                  class="hidden"
                  checked />
                <span class="w-3.5 h-3.5 inline-block mr-2 border-2 border-purpleNavbar rounded-full flex-shrink-0 transition"></span>
                Laki-Laki
              </label>
              <label class="flex items-center text-gray-600">
                <input
                  type="radio"
                  name="jenis_kelamin"
                  value="Perempuan"
                  class="hidden" />
                <span class="w-3.5 h-3.5 inline-block mr-2 border-2 border-purpleNavbar rounded-full flex-shrink-0 transition"></span>
                Perempuan
              </label>
            </div>
          </div>

          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Jabatan</label>
            <div class="relative w-full">
              <select name="jabatan" class="appearance-none w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar" required>
                <option value="" disabled selected>Pilih Jabatan</option>
                <option value="Pimpinan">Pimpinan</option>
                <option value="Dosen Struktural">Dosen Struktural</option>
                <option value="Dosen Tetap FEB">Dosen Tetap FEB</option>
                <option value="Dosen Tetap FTD">Dosen Tetap FTD</option>
                <option value="Karyawan">Karyawan</option>
                <option value="Cleaning Service">Cleaning Service</option>
              </select>

              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>

          <div class="flex justify-between mt-6">
            <button class="delete-button bg-red-400 text-white px-6 py-2 rounded-lg hover:bg-red-500 transition duration-200" data-id="<?php echo $id_pg; ?>">Hapus</button>
            <button class="bg-purpleNavbar text-white px-6 py-2 rounded-lg hover:bg-purpleNavbarHover transition duration-200" onclick="saveData()">Simpan</button>
          </div>

        </div>
      </main>
    </div>
  </div>

  <?php include('src/pages/navbar/profileInfo.php') ?>
</body>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    fetchData();
  });

  function saveData() {
    const userId = <?php echo json_encode($id_pg); ?>;
    const data = {
      nomor_kartu: document.querySelector('input[name="nomor_kartu"]').value,
      nama: document.querySelector('input[name="nama"]').value,
      noinduk: document.querySelector('input[name="noinduk"]').value,
      jenis_kelamin: document.querySelector('input[name="jenis_kelamin"]:checked').value,
      jabatan: document.querySelector('select[name="jabatan"]').value
    };

    // Show confirmation popup
    Swal.fire({
      title: 'Konfirmasi',
      text: "Apakah Anda yakin ingin mengupdate data pegawai?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Ya, update!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        // Proceed with the fetch call to update data
        fetch('../api/users/update-data-pengguna', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              user_id: userId,
              data: data
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire({
                icon: 'success',
                title: 'Data Berhasil Diperbarui!',
                showConfirmButton: false,
                timer: 1500
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Gagal Memperbarui Data',
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

  // Delete button functionality
  $(document).on('click', '.delete-button', function() {
    const id_pg = $(this).data('id');
    deletedata_pegawai(id_pg);
  });

  function deletedata_pegawai(id_pg) {
    Swal.fire({
      title: 'Konfirmasi',
      text: "Apakah Anda yakin ingin menghapus data pegawai ini?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Ya, hapus!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: '../api/users/delete-user',
          type: 'POST',
          data: JSON.stringify({
            id_pg: id_pg
          }),
          dataType: 'json',
          success: function(response) {
            if (response.status === 'success') {
              Swal.fire('Berhasil!', response.message, 'success').then(() => {
                window.location.href = '/teknoid-absensi/pegawai'; // di production /teknoid-absensi ganti url asli
              });
            } else {
              Swal.fire('Gagal!', response.message, 'error');
            }
          },
          error: function() {
            Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data pegawai', 'error');
          }
        });
      }
    });
  }


  function fetchData() {
    const userId = <?php echo json_encode($id_pg); ?>;

    fetch('../api/users/get-data-pengguna', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          user_id: userId
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          document.querySelector('input[name="nomor_kartu"]').value = data.nomor_kartu;
          document.querySelector('input[name="nama"]').value = data.nama;
          document.querySelector('input[name="noinduk"]').value = data.noinduk;
          document.querySelector(`input[name="jenis_kelamin"][value="${data.jenis_kelamin}"]`).checked = true;
          document.querySelector('select[name="jabatan"]').value = data.jabatan;
        }
      })
      .catch(error => console.error('Error:', error));
  }
</script>


</html>