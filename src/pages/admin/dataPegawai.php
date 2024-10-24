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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
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
    let searchTerm = ''; // Variabel untuk menyimpan kata kunci pencarian

    function loadDataAbsensi(page, search = '') {
      $.ajax({
        url: '../../db/routes/fetchDataPegawai.php',
        type: 'GET',
        data: {
          start: page * 5,
          search: search // Kirim kata kunci pencarian ke backend
        },
        dataType: 'json',
        success: function(response) {
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
                                         <button class="delete-button bg-red-400 text-white px-3 py-2 rounded-xl hover:bg-red-500 transition" data-id="${data_pegawai.id_pg}"><i class="fa-solid fa-trash"></i></button>
                                    </a>
                                </td>
                            </tr>
                        `);
            });
          }

          $('#prev-page').prop('disabled', currentPage === 0);
          $('#next-page').prop('disabled', (currentPage + 1) * 5 >= totalDataAbsensi);

          updatePaginationButtons();
        },
        error: function() {
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