<?php
session_start();

if (!isset($_SESSION['token'])) {
    header('Location: login.php');
    exit();
}

// Cek session akses admin
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'user') {
    header('Location: ../unauthorized');
    exit();
}

$username = htmlspecialchars($_SESSION['name']);
$role = $_SESSION['role'];
$id = $_SESSION['user_id'];
$token = $_SESSION['token'];


if (isset($_GET['id_pg']) && !empty($_GET['id_pg'])) {
    $id_pg = $_GET['id_pg'];
    require_once 'src/db/db_connect.php';

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
    $id_pg = null;
    $nama_pg = 'ID Pengguna tidak valid';
}

$months = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];
$currentMonth = date('n');
$years = date('Y');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Absensi</title>
    <link href="/teknoid-absensi/css/output.css" rel="stylesheet">
    <link href="/teknoid-absensi/src/pages/css/font/poppins-font.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
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
                <div class="flex justify-between items-center border-b border-gray-500">
                    <h1 class="text-lg sm:text-xl md:text-3xl py-2 font-Poppins font-semibold">Preview Data Absensi <?php echo $nama_pg ?> <?php echo $months[$currentMonth]; ?> <?php echo $years; ?> </h1>
                    <a href="../absensi">
                        <button class="bg-purpleNavbar text-white px-4 py-2 rounded-lg hover:bg-purpleNavbarHover transition duration-200">Kembali</button>
                    </a>
                </div>

                <div class="flex justify-start mt-5">
                    <button id="downloadButton" class="bg-purpleNavbar text-white px-4 py-2  rounded-xl text-base font-medium hover:bg-purpleNavbarHover transition">
                        <i class='bx bx-printer'></i> Print
                    </button>
                </div>

                <div id="loading" class="hidden text-center mt-4">
                    <p>Loading...</p>
                    <div class="loader"></div>
                </div>

                <!-- Popup Download -->
                <div id="downloadPopup" class="hidden fixed inset-0 flex justify-center items-center bg-gray-900 bg-opacity-50 z-[9999]">
                    <div class="bg-white w-[450px] p-6 rounded-lg shadow-lg flex flex-col justify-between z-[10000]">
                        <h2 class="text-xl font-semibold mb-4 text-center">Filter Download Data <?php echo $nama_pg ?> </h2>
                        <div class="space-y-4">
                            <!-- Harian Button -->
                            <button id="harianButton" class="w-full bg-purpleNavbar text-white px-4 py-3 rounded-lg hover:bg-purpleNavbarHover transition flex justify-center items-center space-x-2"
                                onclick="toggleHarian()">
                                <i class="fa-solid fa-calendar-days"></i>
                                <span>Harian</span>
                            </button>

                            <!-- Bulanan Button with Date Range -->
                            <div class="relative w-full">
                                <button id="bulananButton" class="w-full bg-purpleNavbar text-white px-4 py-3 rounded-lg hover:bg-purpleNavbarHover transition flex justify-center items-center space-x-2"
                                    onclick="toggleBulanan()">
                                    <i class="fa-solid fa-calendar"></i>
                                    <span>Bulanan</span>
                                </button>

                                <div id="bulananDates" class="hidden mt-2 p-3 bg-white text-black rounded-lg shadow-lg flex flex-col space-y-3">
                                    <label for="startBulanan" class="text-sm">Start Date:</label>
                                    <input type="date" id="startBulanan" class="p-2 rounded border focus:ring focus:ring-purple-300">

                                    <label for="endBulanan" class="text-sm">End Date:</label>
                                    <input type="date" id="endBulanan" class="p-2 rounded border focus:ring focus:ring-purple-300">

                                    <button id="downloadBulanan" class="mt-3 w-full bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-blue-600 transition"
                                        onclick="downloadBulanan()">
                                        Download Bulanan
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Close Button -->
                        <button id="closePopup" class="mt-6 bg-red-400 text-white px-4 py-2 rounded-lg hover:bg-red-500 transition">
                            Close
                        </button>
                    </div>
                </div>

                <div class="tableOverflow mt-6 shadow-customTable rounded-lg">
                    <table class="bg-white border">
                        <thead>
                            <tr class="bg-purpleNavbar text-white rounded-t-lg">
                                <th class="px-6 py-4 font-medium uppercase tracking-wider rounded-tl-lg">No</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">Scan Masuk</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">Scan Keluar</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">Durasi (m)</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">Keterangan</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider rounded-tr-lg">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="preview-absensi-table-body" class="divide-y divide-gray-200">
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

    <script src="../src/pages/admin/js/renderDataPreview.js"></script>
    <script>
        const id_pg = "<?= isset($_GET['id_pg']) ? $_GET['id_pg'] : '' ?>";
    </script>
    <script src="../src/pages/admin/js/popUpPreview.js"></script>

    <?php include('src/pages/navbar/profileInfo.php') ?>

</body>

</html>