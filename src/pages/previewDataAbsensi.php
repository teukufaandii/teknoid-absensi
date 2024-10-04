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


// Mengambil data pengguna
$conn = mysqli_connect("localhost", "root", "", "db_absensi");
if ($conn-> connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Absensi</title>
    <link href="../../css/output.css" rel="stylesheet">
    <link href="./css/font/poppins-font.css" rel="stylesheet">
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
              <h1 class="text-lg sm:text-xl md:text-3xl border-b border-gray-500 py-2 font-Poppins font-semibold">Preview Data Absensi <?php echo $username ?></h1>
                              <?php
                                  $conn = mysqli_connect("localhost", "root", "", "db_absensi");
                                  if ($conn-> connect_error) {
                                  }
                                  
                                  // pengaturan baris
                                  $start = 0;
                                  $rows_per_page = 10;

                                  // total nomor baris
                                  $records = mysqli_query($conn, "SELECT * FROM tb_pengguna");
                                  $nr_of_rows = $records->num_rows;

                                  // kalkulasi nomor per halaman
                                  $pages = ceil($nr_of_rows / $rows_per_page);

                                  // start point
                                  if(isset($_GET['page-nr'])){
                                      $page = $_GET['page-nr'] - 1;
                                      $start = $page * $rows_per_page;
                                  }

                                  // tabel db suratmasuk
                                  $stmt=$conn->prepare("SELECT * FROM  tb_pengguna LIMIT $start, $rows_per_page");
                                  $stmt->execute();
                                  $result = $stmt->get_result();
                              ?>
                                                          
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
                          <th class="px-6 py-4 font-medium uppercase tracking-wider rounded-tr-lg">Aksi</th>
                        </tr>
                      </thead>
                      <tbody class="divide-y divide-gray-200">
                        <tr class="bg-gray-100">
                          <td class="px-6 py-2 text-center <?php echo $is_last_row ? 'rounded-bl-lg' : ''; ?>">1</td>
                          <td class="px-6 py-2 text-center">215123123123</td>
                          <td class="px-6 py-2 text-center">Adam Ilham Sulaiman</td>
                          <td class="px-6 py-2 text-center">Anomali</td>
                          <td class="px-6 py-2 text-center">Anomali</td>
                          <td class="px-6 py-2 text-center">Anomali</td>
                          <td class="px-6 py-2 text-center <?php echo $is_last_row ? 'rounded-br-lg' : ''; ?>">
                              <a href="editDataAbsensi.php">
                                  <button class="bg-purpleNavbar text-white px-8 py-2 rounded-xl hover:bg-purpleNavbarHover transition">Edit</button>
                              <a>
                          </td>
                        </tr>
                        <tr>
                          <td class="px-6 py-2 text-center">2</td>
                          <td class="px-6 py-2 text-center">215123123123</td>
                          <td class="px-6 py-2 text-center">Adam Ilham Sulaiman</td>
                          <td class="px-6 py-2 text-center">Anomali</td>
                          <td class="px-6 py-2 text-center">Anomali</td>
                          <td class="px-6 py-2 text-center">Anomali</td>
                          <td class="px-6 py-2 text-center">
                              <a href="editDataAbsensi.php">
                                  <button class="bg-purpleNavbar text-white px-8 py-2 rounded-xl hover:bg-purpleNavbarHover transition">Edit</button>
                              <a>
                          </td>
                        </tr>
                        <tr class="bg-gray-100">
                          <td class="px-6 py-2 text-center">3</td>
                          <td class="px-6 py-2 text-center">215123123123</td>
                          <td class="px-6 py-2 text-center">Adam Ilham Sulaiman</td>
                          <td class="px-6 py-2 text-center">Anomali</td>
                          <td class="px-6 py-2 text-center">Anomali</td>
                          <td class="px-6 py-2 text-center">Anomali</td>
                          <td class="px-6 py-2 text-center">
                              <a href="editDataAbsensi.php">
                                  <button class="bg-purpleNavbar text-white px-8 py-2 rounded-xl hover:bg-purpleNavbarHover transition">Edit</button>
                              <a>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
              </div>
          </main>
      </div>
    </div>
</body>

</html>