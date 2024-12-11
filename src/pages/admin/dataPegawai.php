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
                <th class="px-6 py-4 font-medium uppercase tracking-wider rounded-tl-lg">No</th>
                <th class="px-6 py-4 font-medium uppercase tracking-wider">Nomor Induk</th>
                <th class="px-6 py-4 font-medium uppercase tracking-wider">Nama Lengkap</th>
                <th class="px-6 py-4 font-medium uppercase tracking-wider">Jabatan</th>
                <th class="px-6 py-4 font-medium uppercase tracking-wider">Role</th>
                <th class="px-6 py-4 font-medium uppercase tracking-wider rounded-tr-lg">Aksi</th>
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
</body>

<script>
  $(document).ready(function() {
    let currentPage = 0;
    let totalDataPegawai = 0;
    const dataPerPage = 5;
    let searchTerm = '';

    // Load data pegawai
    function loadDataPegawai(page, search = '') {
      $.ajax({
        url: 'api/users/get-pegawai',
        type: 'GET',
        data: {
          start: page * dataPerPage,
          search: search
        },
        dataType: 'json',
        success: function(response) {
          if (response.status === 'unauthorized') {
            window.location.href = 'unauthorized';
            return;
          }

          totalDataPegawai = response.total;
          const tableBody = $('#pegawai-table-body');
          tableBody.empty();

          if (response.data_pegawai.length === 0) {
            if (currentPage > 0) currentPage--;
            loadDataPegawai(currentPage, search);
          } else {
            let counter = page * dataPerPage + 1;
            response.data_pegawai.forEach(function(data_pegawai) {
              tableBody.append(`
                            <tr class="bg-gray-100">
                                <td class="px-6 py-2 text-center">${counter++}</td>
                                <td class="px-6 py-2 text-center">${data_pegawai.noinduk}</td>
                                <td class="px-6 py-2 text-center">${data_pegawai.nama}</td>
                                <td class="px-6 py-2 text-center">${data_pegawai.jabatan}</td>
                                <td class="px-6 py-2 text-center">${data_pegawai.role}</td>
                                <td class="px-6 py-2 text-center">
                                    <a href="pegawai/edit?id_pg=${data_pegawai.id_pg}">
                                        <button class="bg-purpleNavbar text-white px-3 py-2 rounded-xl hover:bg-purpleNavbarHover transition">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                    </a>
                                </td>
                            </tr>
                        `);
            });
          }

          updatePaginationButtons();
        },
        error: function() {
          Swal.fire('Error!', 'Terjadi kesalahan saat memuat data', 'error');
        },
      });
    }

    // Update pagination buttons
    function updatePaginationButtons() {
      const totalPages = Math.ceil(totalDataPegawai / dataPerPage);

      // Clear current pagination buttons
      $('#pagination-container .pagination-button').remove();

      // Define pagination range
      const maxButtons = 5;
      let startPage = Math.max(0, currentPage - Math.floor(maxButtons / 2));
      let endPage = Math.min(totalPages, startPage + maxButtons);

      if (endPage - startPage < maxButtons) {
        startPage = Math.max(0, endPage - maxButtons);
      }

      // Generate pagination buttons
      for (let i = startPage; i < endPage; i++) {
        const button = $(`<button class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition shadow-xl drop-shadow-xl pagination-button" data-page="${i}">${i + 1}</button>`);
        button.addClass(i === currentPage ? 'active-button' : 'inactive-button');
        button.insertBefore('#next-page'); // Insert before "Next" button
      }

      // Enable/Disable navigation buttons
      $('#first-page').prop('disabled', currentPage === 0);
      $('#prev-page').prop('disabled', currentPage === 0);
      $('#next-page').prop('disabled', currentPage >= totalPages - 1);
      $('#last-page').prop('disabled', currentPage >= totalPages - 1);
    }

    // Navigation button handlers
    $('#first-page').on('click', function() {
      if (currentPage > 0) {
        currentPage = 0;
        loadDataPegawai(currentPage, searchTerm);
      }
    });

    $('#last-page').on('click', function() {
      const totalPages = Math.ceil(totalDataPegawai / dataPerPage);
      if (currentPage < totalPages - 1) {
        currentPage = totalPages - 1;
        loadDataPegawai(currentPage, searchTerm);
      }
    });

    $('#prev-page').on('click', function() {
      if (currentPage > 0) {
        currentPage--;
        loadDataPegawai(currentPage, searchTerm);
      }
    });

    $('#next-page').on('click', function() {
      const totalPages = Math.ceil(totalDataPegawai / dataPerPage);
      if (currentPage < totalPages - 1) {
        currentPage++;
        loadDataPegawai(currentPage, searchTerm);
      }
    });

    // Pagination button click
    $(document).on('click', '.pagination-button', function() {
      currentPage = parseInt($(this).data('page'), 10);
      loadDataPegawai(currentPage, searchTerm);
    });

    // Search input handler
    $('#searchInput').on('keyup', function() {
      searchTerm = $(this).val();
      currentPage = 0;
      loadDataPegawai(currentPage, searchTerm);
    });

    // Initial data load
    loadDataPegawai(currentPage);

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
            url: 'api/users/delete-user',
            type: 'POST',
            data: {
              id_pg: id_pg
            },
            dataType: 'json',
            success: function(response) {
              if (response.status === 'success') {
                Swal.fire('Berhasil!', response.message, 'success').then(() => {
                  loadDataPegawai(currentPage, searchTerm); // Refresh data after deletion
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
  });

  //untuk fungsi search
  $(document).ready(function() {
    $("#searchInput").keyup(function() {
      var search = $(this).val();
      $.ajax({
        url: 'api/users/search-users',
        method: 'POST',
        data: {
          query: search
        },
        success: function(response) {
          $("#tablePegawai").html(response);
        }
      });
    });
  });
</script>

</html>