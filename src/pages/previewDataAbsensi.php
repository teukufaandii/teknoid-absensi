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


//mengambil data pengguna
?>

<!DOCTYPE html>





<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="../../css/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body>
    <div class="flex flex-row h-screen">
      <!-- Side Navigation -->
      <?php include('navbar/sidenav.php') ?>

      <div class="min-h-screen inline-flex flex-col flex-1 bg-mainBgColor ml-56">
          <!-- Top Navigation -->
          <?php include('navbar/topnav.php') ?>

          <!-- Main Content -->
          <main class="flex-1 h-full p-6 bg-mainBgColor">
              <h1 class="text-3xl border-b py-2 font-Poppins font-semibold">Preview Data Absensi <?php echo $username ?></h1>
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
                                                          
                                                          <div class="overflow-x-auto mt-4">
                    <table class="min-w-full bg-white border rounded-lg shadow-md">
                      <thead>
                        <tr class="bg-purpleNavbar text-white rounded-t-lg">
                          <th class="px-6 py-4 font-medium uppercase tracking-wider">No</th>
                          <th class="px-6 py-4 font-medium uppercase tracking-wider">Nomor Induk</th>
                          <th class="px-6 py-4 font-medium uppercase tracking-wider">Nama Lengkap</th>
                          <th class="px-6 py-4 font-medium uppercase tracking-wider">Status</th>
                          <th class="px-6 py-4 font-medium uppercase tracking-wider">Aksi</th>
                        </tr>
                      </thead>
                      <tbody class="divide-y divide-gray-200">
                        <tr class="bg-gray-100">
                          <td class="px-6 py-2 text-center">1</td>
                          <td class="px-6 py-2 text-center">215123123123</td>
                          <td class="px-6 py-2 text-center">Adam Ilham Sulaiman</td>
                          <td class="px-6 py-2 text-center">Anomali</td>
                          <td class="px-6 py-2 text-center">
                              <a href="editDataRekap.php">
                                  <button class="bg-purpleNavbar text-white px-8 py-2 rounded-full hover:bg-purpleNavbarHover transition">Lihat</button>
                              <a>
                          </td>
                        </tr>
                        <tr>
                          <td class="px-6 py-2 text-center">2</td>
                          <td class="px-6 py-2 text-center">215123123123</td>
                          <td class="px-6 py-2 text-center">Adam Ilham Sulaiman</td>
                          <td class="px-6 py-2 text-center">Anomali</td>
                          <td class="px-6 py-2 text-center">
                              <a href="editDataRekap.php">
                                  <button class="bg-purpleNavbar text-white px-8 py-2 rounded-full hover:bg-purpleNavbarHover transition">Lihat</button>
                              <a>
                          </td>
                        </tr>
                        <tr class="bg-gray-100">
                          <td class="px-6 py-2 text-center">3</td>
                          <td class="px-6 py-2 text-center">215123123123</td>
                          <td class="px-6 py-2 text-center">Adam Ilham Sulaiman</td>
                          <td class="px-6 py-2 text-center">Anomali</td>
                          <td class="px-6 py-2 text-center">
                              <a href="editDataRekap.php">
                                  <button class="bg-purpleNavbar text-white px-8 py-2 rounded-full hover:bg-purpleNavbarHover transition">Lihat</button>
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