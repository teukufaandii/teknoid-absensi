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
  <title>Data Pegawai</title>
  <link href="../../../css/output.css" rel="stylesheet">
  <link href="../css/font/poppins-font.css" rel="stylesheet">
  <!-- ajax live search -->
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <style>
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
    <?php include('../navbar/sidenav.php') ?>

    <div id="content" class="min-h-screen inline-flex flex-col flex-1 bg-mainBgColor ml-56">
      <!-- Top Navigation -->
      <?php include('../navbar/topnav.php') ?>

      <!-- Main Content -->
      <main class="flex-1 p-6 bg-mainBgColor mainContent">
        <h1 class="text-lg sm:text-xl md:text-3xl border-b border-gray-500 py-2 font-Poppins font-semibold">Data Pegawai</h1>

        <!-- Search Bar & Button Tambah -->
        <div class="flex justify-between items-center mt-5">
          <a href="tambahPegawai.php">
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
                <th class="px-6 py-4 font-medium uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 font-medium uppercase tracking-wider rounded-tr-lg">Aksi</th>
              </tr>
            </thead>
            <tbody id="pegawai-table-body" class="divide-y divide-gray-200">
            </tbody>
          </table>
        </div>

        <div class="flex justify-center items-center space-x-1 mt-4">
          <button id="prev-page" class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition shadow-xl drop-shadow-xl" disabled>
            <i class="fas fa-chevron-left"></i>
          </button>
          <button class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover hover:text-white transition shadow-xl drop-shadow-xl pagination-button" data-page="0">1</button>
          <button class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover hover:text-white transition shadow-xl drop-shadow-xl pagination-button" data-page="1">2</button>
          <button class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover hover:text-white transition shadow-xl drop-shadow-xl pagination-button" data-page="2">3</button>
          <button id="next-page" class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition shadow-xl drop-shadow-xl">
            <i class="fas fa-chevron-right"></i>
          </button>
        </div>

      </main>
    </div>
  </div>
  <?php include('../navbar/profileInfo.php') ?>
</body>

<script>
  $(document).ready(function() {
    let currentPage = 0;
    let totalDataAbsensi = 0;
    let searchTerm = '';

    function loadDataAbsensi(page, search = '') {
      $('#loading').removeClass('hidden');
      $.ajax({
        url: '../../db/routes/fetchDataPegawai.php',
        type: 'GET',
        data: {
          start: page * 5,
          search: search
        },
        dataType: 'json',
        success: function(response) {
          $('#loading').addClass('hidden');
          if (response.status === 'unauthorized') {
            window.location.href = '../../unauthorized.php';
            return;
          }

          totalDataAbsensi = response.total;
          let DataAbsensiTableBody = $('#pegawai-table-body');
          DataAbsensiTableBody.empty();

          if (response.data_pegawai.length === 0 && currentPage > 0) {
            currentPage--;
            loadDataAbsensi(currentPage, search);
          } else if (response.data_pegawai.length === 0) {
              DataAbsensiTableBody.append('<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>');
          } else {
              let counter = page * 5 + 1;
            response.data_pegawai.forEach(function(data_pegawai) {
              DataAbsensiTableBody.append(`
              <tr class="bg-gray-100">
              <td class="px-6 py-2 text-center">${counter++}</td>
              <td class="px-6 py-2 text-center">${data_pegawai.noinduk}</td>
              <td class="px-6 py-2 text-center">${data_pegawai.nama}</td>
              <td class="px-6 py-2 text-center">${data_pegawai.role}</td>
              <td class="px-6 py-2 text-center">
                <a href="./editDataPegawai.php?id_pg=${data_pegawai.id_pg}">
                  <button class="bg-purpleNavbar text-white px-3 py-2 rounded-xl hover:bg-purpleNavbarHover transition"><i class="fa-solid fa-pen-to-square"></i></button>
                </a>
                <a>
                  <button class="delete-button bg-red-400 text-white px-3 py-2 rounded-xl hover:bg-red-500 transition" data-id="${data_pegawai.id_pg}">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </a>
              </td>
            </tr>
          `);
            });
          }

          //$('#prev-page').prop('disabled', currentPage === 0);
          //$('#next-page').prop('disabled', (currentPage + 1) * 5 >= totalDataAbsensi);

          updatePaginationButtons();
        },
        error: function() {
          $('#loading').addClass('hidden');
          Swal.fire('Error!', 'Terjadi kesalahan saat memuat data', 'error');
        }
      });
    }

    function updatePaginationButtons() {
      const totalPages = Math.ceil(totalDataAbsensi / 5);
      const paginationButtons = $('.pagination-button');

      paginationButtons.hide();

      for (let i = 0; i < totalPages; i++) {
        const button = paginationButtons.eq(i);
        button.show().data('page', i).text(i + 1);
        if (i === currentPage) {
          button.removeClass('inactive-button').addClass('active-button');
        } else {
          button.removeClass('active-button').addClass('inactive-button');
        }
      }

      $('#prev-page').prop('disabled', currentPage === 0);
      $('#next-page').prop('disabled', (currentPage + 1) * 5 >= totalDataAbsensi);
    }

    $('#prev-page').on('click', function() {
      if (currentPage > 0) {
        currentPage--;
        loadDataAbsensi(currentPage, searchTerm);
      }
    });

    $('#next-page').on('click', function() {
      if ((currentPage + 1) * 5 < totalDataAbsensi) {
        currentPage++;
        loadDataAbsensi(currentPage, searchTerm);
      }
    });

    $(document).on('click', '.pagination-button', function() {
      currentPage = parseInt($(this).data('page'));
      loadDataAbsensi(currentPage, searchTerm);
      updatePaginationButtons();
    });

    // Event listener untuk input pencarian
    $('#searchInput').on('keyup', function() {
      searchTerm = $(this).val();
      currentPage = 0; // Reset ke halaman pertama saat pencarian
      loadDataAbsensi(currentPage, searchTerm);
    });

    loadDataAbsensi(currentPage);
  });


  //button delete
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
          url: '../../db/routes/deleteDataPegawai.php', // URL to your PHP file
          type: 'POST', // Method type
          data: {
            id_pg: id_pg
          },
          dataType: 'json', // Expected data type from server
          success: function(response) {
            console.log(response); // For debugging
            if (response.status === 'success') {
              Swal.fire('Berhasil!', response.message, 'success').then(() => {
                // Refresh the current page
                location.reload(); // Refresh the current page
              });
            } else {
              Swal.fire('Gagal!', response.message, 'error'); // Error message
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            console.error("Status: " + textStatus); // Log status
            console.error("Error: " + errorThrown); // Log error
            console.error("Response: " + jqXHR.responseText); // Log respons dari server
            Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data pegawai', 'error'); // Error message
          }
        });
      }
    });
  }


  //untuk fungsi search
  $(document).ready(function() {
    $("#searchInput").keyup(function() {
      var search = $(this).val();
      $.ajax({
        url: '../../db/routes/searchPegawai.php',
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