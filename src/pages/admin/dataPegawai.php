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

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Pegawai</title>
  <link href="css/output.css" rel="stylesheet">
  <link href="src/pages/css/font/poppins-font.css" rel="stylesheet">
  <!-- ajax live search -->
  <!-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script> -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tablesort/5.2.1/tablesort.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <style>
    .active-button {
      background-color: #8C85FF;
      color: white;
    }

    .inactive-button {
      background-color: #e2e8f0;
      color: #8C85FF;
    }

    .loader {
      border: 8px solid #f3f3f3;
      border-top: 8px solid #3498db;
      border-radius: 50%;
      width: 30px;
      height: 30px;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }
  </style>
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
        <h1 class="text-lg sm:text-xl md:text-3xl border-b border-gray-500 py-2 font-Poppins font-semibold">Data Pegawai</h1>
        <!-- Search Bar & Button Tambah -->
        <div class="flex justify-between items-center mt-5">
          <a href="pegawai/add">
            <button class="bg-purpleNavbar text-white px-4 py-2 rounded-xl text-base font-medium hover:bg-purpleNavbarHover transition">
              Tambah <i class="fa-solid fa-circle-plus"></i>
            </button>
          </a>
          <div class="relative">
            <form method="GET">
              <input
                type="text"
                id="searchInput"
                placeholder="Search here..."
                class="w-60 px-4 py-2 border rounded-xl shadow-md focus:outline-none focus:ring-2 focus:ring-purpleNavbar text-sm" />
              <i class="fa fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </form>
          </div>
        </div>

        <div id="loading" class="hidden text-center mt-4">
          <p>Loading...</p>
          <div class="loader"></div>
        </div>

        <div class="tableOverflow mt-6 shadow-customTable rounded-lg">
          <table class="min-w-full bg-white border" id="tablePegawai">
            <thead>
              <tr class="bg-purpleNavbar text-white rounded-t-lg">
                <th class="px-6 py-4 font-medium uppercase tracking-wider rounded-tl-lg">
                  <div class="sort-wrapper">
                    No
                    <div class="sort-icons">
                      <i class="fas fa-sort-up sort-icon"></i>
                      <i class="fas fa-sort-down sort-icon"></i>
                    </div>
                  </div>
                </th>
                <th class="px-6 py-4 font-medium uppercase tracking-wider">
                  <div class="sort-wrapper">
                    Nomor Induk
                    <div class="sort-icons">
                      <i class="fas fa-sort-up sort-icon"></i>
                      <i class="fas fa-sort-down sort-icon"></i>
                    </div>
                  </div>
                </th>
                <th class="px-6 py-4 font-medium uppercase tracking-wider">
                  <div class="sort-wrapper">
                    Nama Lengkap
                    <div class="sort-icons">
                      <i class="fas fa-sort-up sort-icon"></i>
                      <i class="fas fa-sort-down sort-icon"></i>
                    </div>
                  </div>
                </th>
                <th class="px-6 py-4 font-medium uppercase tracking-wider">
                  <div class="sort-wrapper">
                    Jabatan
                    <div class="sort-icons">
                      <i class="fas fa-sort-up sort-icon"></i>
                      <i class="fas fa-sort-down sort-icon"></i>
                    </div>
                  </div>
                </th>
                <th class="px-6 py-4 font-medium uppercase tracking-wider">
                  <div class="sort-wrapper">
                    Role
                    <div class="sort-icons">
                      <i class="fas fa-sort-up sort-icon"></i>
                      <i class="fas fa-sort-down sort-icon"></i>
                    </div>
                  </div>
                </th>
                <th class="px-6 py-4 font-medium uppercase tracking-wider rounded-tr-lg" style="pointer-events: none;">
                  Aksi
                </th>
              </tr>
            </thead>
            <tbody id="pegawai-table-body" class="divide-y divide-gray-200">
            </tbody>
          </table>
        </div>

        <div id="pagination-container" class="flex justify-center items-center space-x-1 mt-4">
          <button id="first-page" class="min-w-9 px-3 py-2 ring-2 ring-inset ring-purpleNavbar text-purpleNavbar rounded-md hover:ring-purpleNavbarHover hover:text-purpleNavbarHover transition shadow-xl drop-shadow-xl cursor-pointer" disabled>
            <i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left"></i>
          </button>
          <button id="prev-page" class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition shadow-xl drop-shadow-xl cursor-pointer" disabled>
            <i class="fas fa-chevron-left"></i>
          </button>
          <!-- Pagination buttons will be added here dynamically -->
          <button id="next-page" class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition shadow-xl drop-shadow-xl cursor-pointer">
            <i class="fas fa-chevron-right"></i>
          </button>
          <button id="last-page" class="min-w-9 px-3 py-2 ring-2 ring-inset ring-purpleNavbar text-purpleNavbar rounded-md hover:ring-purpleNavbarHover hover:text-purpleNavbarHover transition shadow-xl drop-shadow-xl cursor-pointer">
            <i class="fas fa-chevron-right"></i><i class="fas fa-chevron-right"></i>
          </button>
        </div>
      </main>
    </div>
  </div>
  <?php include('src/pages/navbar/profileInfo.php') ?>
  <script src="src/pages/admin/js/renderDataPegawai.js"></script>
</body>

</html>