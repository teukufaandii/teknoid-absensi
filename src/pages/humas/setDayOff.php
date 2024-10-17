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
    <title>Set Libur</title>
    <link href="../../../css/output.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/dataAbsensi.css">
    <link href="../css/font/poppins-font.css" rel="stylesheet">
    <link href="../css/global/generalStyling.css" rel="stylesheet">
    <link href="../css/global/tableFormat.css" rel="stylesheet">
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
                <h1 class="text-lg sm:text-xl md:text-3xl border-b border-gray-500 py-2 font-Poppins font-semibold">Input Hari Libur</h1>
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
                }

                // tabel db suratmasuk
                $stmt = $conn->prepare("SELECT * FROM  tb_pengguna LIMIT $start, $rows_per_page");
                $stmt->execute();
                $result = $stmt->get_result();
                ?>

                <div class="bg-white shadow-md rounded-lg p-6 mb-6 mt-6">
                    <div class="mb-4 bg-purpleNavbar p-3">
                        <h3 class="text-xl text-white font-bold font-Poppins">Pengaturan Hari Libur</h2>
                    </div>
                    <!-- Form untuk pengaturan hari libur -->
                    <div class="flex gap-4">
                        <!-- Input untuk Tanggal -->
                        <div class="w-full">
                            <label for="tanggal" class="block text-lg font-Poppins font-semibold text-gray-700">Tanggal</label>
                            <input type="date" id="tanggal" name="tanggal" class="mt-1 block w-full px-4 py-2 border-2 border-gray-200 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        </div>

                        <!-- Input untuk Nama Hari Libur -->
                        <div class="w-full">
                            <label for="nama_hari_libur" class="block text-lg font-Poppins font-semibold text-gray-700">Nama Hari Libur</label>
                            <input type="text" id="nama_hari_libur" name="nama_hari_libur" class="mt-1 block w-full px-4 py-2 border-2 border-gray-200 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        </div>
                    </div>
                    <!-- TableOverflow -->
                    <div class="tableOverflow mt-6 shadow-customTable rounded-lg">
                        <table class="bg-white border w-full">
                            <thead>
                                <tr class="bg-purpleNavbar text-white rounded-t-lg">
                                    <th class="px-6 py-4 font-Poppins font-medium uppercase tracking-wider rounded-tl-lg">No</th>
                                    <th class="px-6 py-4 font-Poppins font-medium uppercase tracking-wider">Nama Hari Libur</th>
                                    <th class="px-6 py-4 font-Poppins font-medium uppercase tracking-wider">Tanggal</th>
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
                            $stmt = $conn->prepare("SELECT * FROM tb_dayoff LIMIT ?, ?");
                            $stmt->bind_param("ii", $start, $rows_per_page);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                $counter = $start + 1;
                                while ($row = $result->fetch_assoc()) {
                            ?>
                                    <tr class="bg-gray-100">
                                        <td class="px-6 py-2 text-center"><?php echo $counter++; ?></td>
                                        <td class="px-6 py-2 text-center"><?php echo htmlspecialchars($row["nama_libur"]); ?></td>
                                        <td class="px-6 py-2 text-center"><?php echo htmlspecialchars($row["tanggal"]); ?></td>
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
                    <div class="flex justify-end mt-6">
                        <button class="bg-purpleNavbar text-white px-6 py-2 rounded-lg hover:bg-purpleNavbarHover transition duration-200">Simpan</button>
                    </div>
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

</html>