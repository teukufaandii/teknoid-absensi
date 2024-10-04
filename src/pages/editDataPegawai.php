<?php
session_start();

if (!isset($_SESSION['token'])) {
    header('Location: login.php');
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
    <title>Edit Pegawai</title>
    <link href="../../css/output.css" rel="stylesheet">
    <link href="./css/font/poppins-font.css" rel="stylesheet">
    <link href="./css/responsive/resp.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body>
  <div class="flex flex-col md:flex-row lg:flex-row h-screen">
      <!-- Side Navigation -->
      <?php include('navbar/sidenav.php') ?>

      <div id="content" class="min-h-screen inline-flex flex-col flex-1 bg-mainBgColor ml-56">
          <!-- Top Navigation -->
          <?php include('navbar/topnav.php') ?>

          <!-- Main Content -->
          <main class="flex-1 p-6 bg-mainBgColor mainContent">
          <h1 class="text-lg sm:text-xl md:text-3xl border-b border-gray-500 py-2 font-Poppins font-semibold"> Edit Data Pegawai </h1>
              <div class="w-full mx-auto py-6">
                <div class="mb-4">
                  <label class="block text-gray-700 font-semibold mb-2">Nomor Kartu</label>
                  <input
                    type="text"
                    placeholder="Masukkan Nomor Kartu"
                    class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar"
                    defaultValue="123456789"
                  />
                </div>

                <div class="mb-4">
                  <label class="block text-gray-700 font-semibold mb-2">Nama Lengkap</label>
                  <input
                    type="text"
                    placeholder="Masukkan Nama Lengkap"
                    class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar"
                    defaultValue="Jane Doe"
                  />
                </div>

                <div class="mb-4">
                  <label class="block text-gray-700 font-semibold mb-2">NIDN</label>
                  <input
                    type="text"
                    placeholder="Masukkan Nomor Induk"
                    class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar"
                  />
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
                        checked
                      />
                      <span class="w-3.5 h-3.5 inline-block mr-2 border-2 border-purpleNavbar rounded-full flex-shrink-0 transition"></span>
                      Laki-Laki
                    </label>
                    <label class="flex items-center text-gray-600">
                      <input
                        type="radio"
                        name="gender"
                        value="Perempuan"
                        class="hidden"
                      />
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
                      placeholder="Masukkan Nama Tempat Lahir"
                      class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar"
                    />
                  </div>
                  <div style="grid-area: main;">
                    <div class="relative">
                      <input
                        type="date"
                        placeholder="Masukkan Tanggal Lahir"
                        class="w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar"
                      />
                    </div>
                  </div>
                </div>

                <div class="mb-4">
                  <label class="block text-gray-700 font-semibold mb-2">Jabatan</label>
                  <div class="relative w-full">
                      <select class="appearance-none w-full border-2 border-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar">
                          <option hidden>Pilih Jabatan</option>
                          <option>Jabatan 1</option>
                          <option>Jabatan 2</option>
                      </select>

                      <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                          </svg>
                      </div>
                  </div>
                </div>

                <div class="flex justify-between mt-6">
                    <button class="bg-red-400 text-white px-6 py-2 rounded-lg hover:bg-red-500 transition duration-200">Hapus</button>
                    <button class="bg-purpleNavbar text-white px-6 py-2 rounded-lg hover:bg-purpleNavbarHover transition duration-200">Simpan</button>
                </div>

              </div>
          </main>
      </div>
    </div>
    
    <?php include('navbar/profileInfo.php') ?>
</body>


</html>