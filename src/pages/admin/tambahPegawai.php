<?php
session_start();

if (!isset($_SESSION['token'])) {
  header('Location: login.php');
  exit();
}

// Cek session akses admin
if ($_SESSION['role'] !== 'admin') {
  header('Location: ../../unauthorized.php'); // Ganti dengan halaman yang sesuai
  exit();
}

$username = htmlspecialchars($_SESSION['name']);
$role = $_SESSION['role'];
$id = $_SESSION['user_id'];
$token = $_SESSION['token'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Pegawai</title>
  <link href="../../../css/output.css" rel="stylesheet">
  <link href="../css/font/poppins-font.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <div class="flex flex-col md:flex-row lg:flex-row h-screen">
    <!-- Side Navigation -->
    <?php include('../navbar/sidenav.php') ?>

    <div id="content" class="inline-flex flex-col flex-1 bg-mainBgColor ml-56">
      <!-- Top Navigation -->
      <?php include('../navbar/topnav.php') ?>

      <!-- Main Content -->
      <main class="flex-1 p-6 bg-mainBgColor md:mt-0 mainContent">
        <h1 class="text-lg sm:text-xl md:text-3xl border-b border-gray-500 py-2 font-Poppins font-semibold">Tambah Data Pegawai</h1>
        <div class="w-full mx-auto py-6">
          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Nomor Kartu</label>
            <input
              type="text"
              name="nomorKartu"
              placeholder="Masukkan Nomor kartu"
              class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar"
              defaultValue="123456789" required />
          </div>

          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Nama Lengkap</label>
            <input
              type="text"
              name="namaLengkap"
              placeholder="Masukkan Nama Lengkap"
              class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar" required />
          </div>

          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Email</label>
            <input
              type="email"
              name="email"
              placeholder="Masukkan Email"
              class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar" required />
          </div>

          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Password</label>
            <input
              type="text"
              name="password"
              placeholder="Masukkan Password"
              class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar" required />
          </div>

          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">NIDN</label>
            <input
              type="text"
              name="noInduk"
              placeholder="Masukkan NIDN"
              class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar" required />
          </div>

          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Jenis Kelamin</label>
            <div class="flex items-center space-x-6">
              <label class="flex items-center text-gray-600">
                <input
                  type="radio"
                  name="gender"
                  value="Laki-laki"
                  class="hidden"
                  checked required />
                <span class="w-3.5 h-3.5 inline-block mr-2 border-2 border-purpleNavbar rounded-full flex-shrink-0 transition"></span>
                Laki-Laki
              </label>
              <label class="flex items-center text-gray-600">
                <input
                  type="radio"
                  name="gender"
                  value="Perempuan"
                  class="hidden" required />
                <span class="w-3.5 h-3.5 inline-block mr-2 border-2 border-purpleNavbar rounded-full flex-shrink-0 transition"></span>
                Perempuan
              </label>
            </div>
          </div>

          <label class="block text-gray-700 font-semibold mb-2">Tempat, Tanggal Lahir</label>

          <div class="mb-4 grid" style="grid-template-columns: 1fr 1fr; gap: 6px; grid-template-areas: 'menu main';">
            <div style="grid-area: menu;">
              <input
                type="text"
                name="tempatLahir"
                placeholder="Masukkan Nama Tempat Lahir"
                class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar" required />
            </div>
            <div style="grid-area: main;">
              <div class="relative">
                <input
                  type="date"
                  name="tanggalLahir"
                  placeholder="Masukkan Tanggal Lahir"
                  class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar" required />
              </div>
            </div>
          </div>

          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Jabatan</label>
            <div class="relative w-full">
              <select name="jabatan" class="appearance-none w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar">
                <option hidden>Pilih Jabatan</option>
                <option value="Dosen Tetap">Dosen Tetap</option>
                <option value="Dosen Tidak Tetap">Dosen Tidak Tetap</option>
              </select>

              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>

          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Hak Akses</label>
            <div class="relative w-full">
              <select name="role" class="appearance-none w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar">
                <option hidden>Pilih Hak Akses</option>
                <option value="admin">Admin</option>
                <option value="user">User</option>
              </select>

              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>

          <div class="flex justify-between mt-6">
            <button class="bg-red-400 text-white px-6 py-2 rounded-lg hover:bg-red-500 transition duration-200" onclick="window.location.href='dataPegawai.php'">Batal</button>
            <button class="bg-purpleNavbar text-white px-6 py-2 rounded-lg hover:bg-purpleNavbarHover transition duration-200" onclick="saveData()">Simpan</button>
          </div>

        </div>
      </main>
    </div>
  </div>

  <?php include('../navbar/profileInfo.php') ?>
</body>

<script>
  function saveData() {
    const data = {
      nomorKartu: document.querySelector('input[name="nomorKartu"]').value,
      namaLengkap: document.querySelector('input[name="namaLengkap"]').value,
      email: document.querySelector('input[name="email"]').value,
      password: document.querySelector('input[name="password"]').value,
      noInduk: document.querySelector('input[name="noInduk"]').value,
      tempatLahir: document.querySelector('input[name="tempatLahir"]').value,
      tanggalLahir: document.querySelector('input[name="tanggalLahir"]').value,
      jenis_kelamin: document.querySelector('input[name="gender"]:checked').value,
      jabatan: document.querySelector('select[name="jabatan"]').value,
      role: document.querySelector('select[name="role"]').value
    };

    // Konfirmasi sebelum menyimpan data
    Swal.fire({
      title: 'Konfirmasi',
      text: "Apakah Anda yakin ingin menambahkan data pegawai baru?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Ya, tambahkan!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        // Lakukan fetch ke backend untuk menambahkan data
        fetch('../../db/routes/addDataPegawai.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire({
                icon: 'success',
                title: 'Data berhasil ditambahkan!',
                showConfirmButton: false,
                timer: 1500
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Gagal menambahkan data',
                text: data.message || 'Coba lagi.'
              });
            }
          })
          .catch(error => {
            console.error('Error:', error);
            Swal.fire({
              icon: 'error',
              title: 'Terjadi kesalahan',
              text: 'Coba lagi nanti.'
            });
          });
      }
    });
  }
</script>

</html>