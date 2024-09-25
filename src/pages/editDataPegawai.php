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
    <title>Dashboard</title>
    <link href="../../css/output.css" rel="stylesheet">
    <link href="../../css/font/poppins-font.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body>
    <div class="flex flex-row">
      <!-- Side Navigation -->
      <?php include('navbar/sidenav.php') ?>

      <div class="inline-flex flex-col flex-1 bg-mainBgColor ml-56">
          <!-- Top Navigation -->
          <?php include('navbar/topnav.php') ?>

          <!-- Main Content -->
          <main class="inline-flex flex-1 p-6 bg-mainBgColor">
              <h1 class="text-3xl border-b border-black py-2 font-Poppins font-semibold"> Edit Data Pegawai </h1>
              <div class="max-w-lg mx-auto p-6">
                <div class="mb-4">
                  <label class="block text-gray-700 font-semibold mb-2">Nomor Kartu</label>
                  <input
                    type="text"
                    placeholder="Masukkan Nomor kartu"
                    class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:border-indigo-500"
                    defaultValue="123456789"
                  />
                </div>

                <div class="mb-4">
                  <label class="block text-gray-700 font-semibold mb-2">Nama Lengkap</label>
                  <input
                    type="text"
                    placeholder="Masukkan Nama Lengkap"
                    class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:border-indigo-500"
                    defaultValue="Jane Doe"
                  />
                </div>

                <div class="mb-4">
                  <label class="block text-gray-700 font-semibold mb-2">NIDN</label>
                  <input
                    type="text"
                    placeholder="Masukkan Nama Lengkap"
                    class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:border-indigo-500"
                  />
                </div>

                <div class="mb-4">
                  <label class="block text-gray-700 font-semibold mb-2">Jenis Kelamin</label>
                  <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                      <input
                        type="radio"
                        name="gender"
                        value="Laki-laki"
                        class="text-indigo-500"
                        checked
                      />
                      <span class="ml-2 mr-4 text-gray-700">Laki-laki</span>
                    </label>
                    <label class="flex items-center">
                      <input
                        type="radio"
                        name="gender"
                        value="Perempuan"
                        class="text-indigo-500"
                      />
                      <span class="ml-2 mr-4 text-gray-700">Perempuan</span>
                    </label>
                  </div>
                </div>

                <label class="block text-gray-700 font-semibold mb-2">Tempat, Tanggal Lahir</label>

                <div class="mb-4 grid" style="grid-template: 'menu main'; gap: 6px;">
                  <div style="grid-area: menu;">
                      <input
                      type="text"
                      placeholder="Masukkan Nama Tempat Lahir"
                      class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:border-indigo-500"
                      />
                  </div>
                  <div style="grid-area: main;">
                      <div class="relative">
                      <input
                          type="date"
                          placeholder="Masukkan Tanggal Lahir"
                          class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:border-indigo-500"
                      />
                      </div>
                  </div>
                </div>


                <div class="mb-4">
                  <label class="block text-gray-700 font-semibold mb-2">Jabatan</label>
                  <div class="relative w-full">
                      <select class="appearance-none w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:border-indigo-500">
                          <option hidden>Pilih Jabatan</option>
                          <option>Jabatan 1</option>
                          <option>Jabatan 2</option>
                      </select>

                      <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                          </svg>
                      </div>
                  </div>
                </div>

                <div class="flex justify-between mt-6">
                    <button class="bg-red-400 text-white px-6 py-2 rounded-lg hover:bg-red-600 transition duration-200">Hapus</button>
                    <button class="bg-purpleNavbar text-white px-6 py-2 rounded-lg hover:bg-indigo-600 transition duration-200">Simpan</button>
                </div>

              </div>
          </main>
      </div>
    </div>
</body>

</html>