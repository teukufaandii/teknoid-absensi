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
            <tbody class="divide-y divide-gray-200">
              <?php
              include '../../db/db_connect.php';

              // pengaturan baris
              $start = 0;
              $rows_per_page = 10;

              // total nomor baris
              $records = mysqli_query($conn, "SELECT * FROM tb_pengguna");
              $nr_of_rows = $records->num_rows;

              // kalkulasi nomor per halaman
              $pages = ceil($nr_of_rows / $rows_per_page);

              // start point
              if (isset($_GET['page-nr'])) {
                $page = $_GET['page-nr'] - 1;
                $start = $page * $rows_per_page;
              } else {
                $page = 0;
              }

              // ambil data dari tabel dengan batasan jumlah per halaman
              $stmt = $conn->prepare("SELECT * FROM tb_pengguna LIMIT ?, ?");
              $stmt->bind_param("ii", $start, $rows_per_page);
              $stmt->execute();
              $result = $stmt->get_result();

              if ($result->num_rows > 0) {
                $counter = $start + 1;
                while ($row = $result->fetch_assoc()) {
              ?>
                  <tr class="bg-gray-100">
                    <td class="px-6 py-2 text-center"><?php echo $counter++; ?></td>
                    <td class="px-6 py-2 text-center"><?php echo htmlspecialchars($row["noinduk"]); ?></td>
                    <td class="px-6 py-2 text-center"><?php echo htmlspecialchars($row["nama"]); ?></td>
                    <td class="px-6 py-2 text-center"><?php echo htmlspecialchars($row["role"]); ?></td>
                    <td class="px-6 py-2 text-center">
                      <a href="editDataPegawai.php?id_pg=<?php echo $row['id_pg']; ?>">
                        <button class="bg-purpleNavbar text-white px-8 py-2 rounded-xl hover:bg-purpleNavbarHover transition">Edit</button>
                      </a>
                    </td>
                  </tr>
              <?php
                }
              } else {
                echo "<tr><td colspan='5' class='text-center'>Tidak ada data</td></tr>";
              }

              $stmt->close();
              $conn->close();
              ?>
            </tbody>
          </table>
        </div>
        <div class="flex justify-center items-center space-x-1 mt-4">
          <!-- Previous Button -->
          <button class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition shadow-xl drop-shadow-xl">
            <i class="fas fa-chevron-left"></i>
          </button>

          <!-- Page Numbers -->
          <button class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition shadow-xl drop-shadow-xl">1</button>
          <button class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition shadow-xl drop-shadow-xl">2</button>
          <button class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition shadow-xl drop-shadow-xl">3</button>

          <!-- Dots -->
          <button class="min-w-9 px-3 py-2 bg-white text-purpleNavbar rounded-md hover:text-white hover:bg-purpleNavbarHover transition shadow-xl drop-shadow-xl">...</button>

          <!-- Next Button -->
          <button class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition shadow-xl drop-shadow-xl">
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