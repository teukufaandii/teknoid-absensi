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

if (isset($_GET['id_pg']) && !empty($_GET['id_pg'])) {
  $id_pg = $_GET['id_pg']; 
include '../../db/db_connect.php';

$query = "SELECT nama FROM tb_pengguna WHERE id_pg = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $id_pg); 
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nama_pg = htmlspecialchars($row['nama']); 
} 

} else {
  $nama_pg = 'ID Pengguna tidak valid';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Absensi</title>
    <link href="../../../css/output.css" rel="stylesheet">
    <link href="../css/font/poppins-font.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
              <h1 class="text-lg sm:text-xl md:text-3xl border-b border-gray-500 py-2 font-Poppins font-semibold">Preview Data Absensi <?php echo $nama_pg ?></h1>                                                          
                  <div class="tableOverflow mt-6 shadow-customTable rounded-lg">
                  <table class="bg-white border">
            <thead>
              <tr class="bg-purpleNavbar text-white rounded-t-lg">
                <th class="px-6 py-4 font-medium uppercase tracking-wider rounded-tl-lg">Tanggal</th>
                <th class="px-6 py-4 font-medium uppercase tracking-wider">jam Kerja</th>
                <th class="px-6 py-4 font-medium uppercase tracking-wider">Scan Masuk</th>
                <th class="px-6 py-4 font-medium uppercase tracking-wider">Scan Keluar</th>
                <th class="px-6 py-4 font-medium uppercase tracking-wider">Durasi (m)</th>
                <th class="px-6 py-4 font-medium uppercase tracking-wider">Keterangan</th>
                <th class="px-6 py-4 font-medium uppercase tracking-wider">Nama</th>
                <th class="px-6 py-4 font-medium uppercase tracking-wider rounded-tr-lg">Aksi</th>
              </tr>
            </thead>
            <tbody id="absensi-data" class="divide-y divide-gray-200">
              
            </tbody>
          </table>
          
                  </div>
                  <div id="pagination" class="flex justify-center items-center space-x-1 mt-4">
    <!-- Pagination buttons will be rendered here -->
</div>

              </div>
              
          </main>
      </div>
    </div>
    <script>
    let id_pg = <?php echo json_encode($id_pg); ?>;
    let currentPage = 1; // Current page, starts from 1
    let totalPages = 1; // Placeholder, will be updated with actual page count from backend

    // Function to fetch data and pagination
    function fetchAbsensiData(page) {
    $.get('../../db/routes/fetchPreviewData.php', { id_pg: id_pg, 'page-nr': page })
        .done(function(data) {
            $('#absensi-data').html(data);
            renderPagination(page, totalPages);
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            console.error("Request failed: " + textStatus, errorThrown);
        });
}

    // Function to render pagination dynamically
    function renderPagination(currentPage, totalPages) {
        let paginationHtml = '';

        // Previous Button
        paginationHtml += `
            <button class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition shadow-xl drop-shadow-xl" 
                    ${currentPage === 1 ? 'disabled' : ''} 
                    onclick="changePage(${currentPage - 1})">
                <i class="fas fa-chevron-left"></i>
            </button>`;

        // Page Numbers
        for (let i = 1; i <= totalPages; i++) {
            paginationHtml += `
                <button class="min-w-9 px-3 py-2 ${i === currentPage ? 'bg-purpleNavbar text-white' : 'bg-white text-purpleNavbar'} 
                    rounded-md hover:bg-purpleNavbarHover transition shadow-xl drop-shadow-xl" 
                    onclick="changePage(${i})">
                    ${i}
                </button>`;
        }

        // Dots if there are many pages (optional)
        if (totalPages > 3) {
            paginationHtml += `
                <button class="min-w-9 px-3 py-2 bg-white text-purpleNavbar rounded-md hover:text-white hover:bg-purpleNavbarHover transition shadow-xl drop-shadow-xl">...</button>`;
        }

        // Next Button
        paginationHtml += `
            <button class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition shadow-xl drop-shadow-xl" 
                    ${currentPage === totalPages ? 'disabled' : ''} 
                    onclick="changePage(${currentPage + 1})">
                <i class="fas fa-chevron-right"></i>
            </button>`;

        // Inject pagination HTML
        $('#pagination').html(paginationHtml);
    }

    // Function to handle page change
    function changePage(page) {
        if (page >= 1 && page <= totalPages) {
            currentPage = page;
            fetchAbsensiData(page);
        }
    }

    // Initial fetch
    fetchAbsensiData(currentPage);
</script>

<?php include('../navbar/profileInfo.php') ?>

</body>

</html>