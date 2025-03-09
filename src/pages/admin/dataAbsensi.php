<?php
session_start();

if (!isset($_SESSION['token'])) {
    header('Location: login.php');
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

date_default_timezone_set('Asia/Jakarta');
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
    <title>Rekap Absensi</title>
    <link href="css/output.css" rel="stylesheet">
    <link href="src/pages/css/font/poppins-font.css" rel="stylesheet">
    <link href="src/pages/css/global/generalStyling.css" rel="stylesheet">
    <link href="src/pages/css/global/tableFormat.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tablesort/5.2.1/tablesort.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<style>
    .active-button {
        background-color: #8C85FF;
        color: white;
    }

    .inactive-button {
        background-color: #e2e8f0;
        color: #8C85FF;
    }

    #loadingSpinner .loader {
        border: 8px solid rgba(255, 255, 255, 0.3);
        border-top: 8px solid #ffffff;
        border-radius: 50%;
        width: 50px;
        height: 50px;
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

<body>
    <div class="flex flex-col md:flex-row lg:flex-row h-screen">
        <!-- Side Navigation -->
        <?php include('src/pages/navbar/sidenav.php') ?>

        <div id="content" class="min-h-screen inline-flex flex-col flex-1 bg-mainBgColor ml-56">
            <!-- Top Navigation -->
            <?php include('src/pages/navbar/topnav.php') ?>

            <!-- Main Content -->
            <main class="flex-1 p-6 bg-mainBgColor mainContent">

                <h1 class="text-lg sm:text-xl md:text-3xl border-b border-gray-500 py-2 font-Poppins font-semibold">Data Absensi <?php echo $months[$currentMonth]; ?> <?php echo $years; ?></h1>
                <div class="flex justify-between items-center mt-5">
                    <div class="flex justify-start items-center space-x-4">
                        <!-- Print Button -->
                        <button id="downloadButton" class="bg-purpleNavbar text-white px-4 py-2 rounded-xl text-base font-medium hover:bg-purpleNavbarHover transition">
                            <i class='bx bx-printer'></i> Print
                        </button>
                        <!-- Generate Detail Button -->
                        <button class="bg-purpleNavbar text-white px-4 py-2 rounded-xl text-base font-medium hover:bg-purpleNavbarHover transition" id="addButton">
                            Generate Detail Absen <i class="fa-solid fa-circle-plus"></i>
                        </button>
                    </div>
                    <!-- Search Input -->
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Search here..." class="w-60 px-4 py-2 border rounded-xl shadow-md focus:outline-none focus:ring-2 focus:ring-purpleNavbar text-sm" />
                        <i class="fa fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <!-- Loading Indicator -->
                <div id="loading" class="hidden text-center mt-4">
                    <p>Loading...</p>
                    <div class="loader"></div>
                </div>

                <!-- Role Selection Popup -->
                <div id="rolePopup" class="hidden fixed inset-0 flex justify-center items-center bg-gray-900 bg-opacity-50 z-[9999]">
                    <div class="bg-white w-[450px] p-6 rounded-lg shadow-lg flex flex-col justify-between z-[10000]">
                        <h2 class="text-xl font-semibold mb-4 text-center">Pilih Jabatan</h2>

                        <!-- Dosen Tetap Button -->
                        <button id="dosenTetapButton" class="w-full bg-purpleNavbar text-white px-4 py-3 rounded-lg hover:bg-purpleNavbarHover transition flex justify-center items-center space-x-2 my-5">
                            <i class="fa-solid fa-user-tie"></i>
                            <span>Dosen Tetap FEB & FTD</span>
                        </button>

                        <!-- Karyawan Button -->
                        <button id="karyawanButton" class="w-full bg-purpleNavbar text-white px-4 py-3 rounded-lg hover:bg-purpleNavbarHover transition flex justify-center items-center space-x-2">
                            <i class="fa-solid fa-users"></i>
                            <span>Karyawan, Pimpinan, Dosen Struktural</span>
                        </button>

                        <!-- Close Button -->
                        <button id="closeRolePopup" class="mt-6 bg-red-400 text-white px-4 py-2 rounded-lg hover:bg-red-500 transition">Close</button>
                    </div>
                </div>

                <!-- Popup Download -->
                <div id="downloadPopup" class="hidden fixed inset-0 flex justify-center items-center bg-gray-900 bg-opacity-50 z-[9999]">
                    <div class="bg-white w-[450px] p-6 rounded-lg shadow-lg flex flex-col justify-between z-[100]">
                        <h2 class="text-xl font-semibold mb-4 text-center">Pilih Waktu</h2>

                        <!-- Dosen Tetap Section -->
                        <div id="dosenTetapSection" class="hidden">
                            <!-- Harian Button -->
                            <button id="harianButtonDosenTetap" class="w-full bg-purpleNavbar text-white px-4 py-3 rounded-lg hover:bg-purpleNavbarHover transition flex justify-center items-center space-x-2 my-5" onclick="toggleHarian('dosenTetap')">
                                <i class="fa-solid fa-calendar-days"></i>
                                <span>Harian</span>
                            </button>

                            <!-- Mingguan Button with Date Range -->
                            <div class="relative w-full">
                                <button id="mingguanButtonDosenTetap" class="w-full bg-purpleNavbar text-white px-4 py-3 rounded-lg hover:bg-purpleNavbarHover transition flex justify-center items-center space-x-2 mb-5" onclick="toggleMingguan('dosenTetap')">
                                    <i class="fa-solid fa-calendar-week"></i>
                                    <span>Mingguan</span>
                                </button>

                                <div id="mingguanDatesDosenTetap" class="hidden mt-2 p-3 bg-white text-black rounded-lg shadow-lg flex flex-col space-y-3">
                                    <label for="startMingguanDosenTetap" class="text-sm">Start Date:</label>
                                    <input type="date" id="startMingguanDosenTetap" class="p-2 rounded border focus:ring focus:ring-purple-300">

                                    <label for="endMingguanDosenTetap" class="text-sm">End Date:</label>
                                    <input type="date" id="endMingguanDosenTetap" class="p-2 rounded border focus:ring focus:ring-purple-300">

                                    <button id="downloadMingguanDosenTetap" class="mt-3 w-full bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-blue-600 transition">
                                        Download Mingguan
                                    </button>
                                </div>
                            </div>

                            <!-- Bulanan Button with Date Range -->
                            <div class="relative w-full">
                                <button id="bulananButtonDosenTetap" class="w-full bg-purpleNavbar text-white px-4 py-3 rounded-lg hover:bg-purpleNavbarHover transition flex justify-center items-center space-x-2" onclick="toggleBulanan('dosenTetap')">
                                    <i class="fa-solid fa-calendar"></i>
                                    <span>Bulanan</span>
                                </button>

                                <div id="bulananDatesDosenTetap" class="hidden mt-2 p-3 bg-white text-black rounded-lg shadow-lg flex flex-col space-y-3">
                                    <label for="startBulananDosenTetap" class="text-sm">Start Date:</label>
                                    <input type="date" id="startBulananDosenTetap" class="p-2 rounded border focus:ring focus:ring-purple-300">

                                    <label for="endBulananDosenTetap" class="text-sm">End Date:</label>
                                    <input type="date" id="endBulananDosenTetap" class="p-2 rounded border focus:ring focus:ring-purple-300">

                                    <button id="downloadBulananDosenTetap" class="mt-3 w-full bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-blue-600 transition">
                                        Download Bulanan
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Karyawan Section -->
                        <div id="karyawanSection" class="hidden">
                            <!-- Harian Button -->
                            <button id="harianButtonKaryawan" class="w-full bg-purpleNavbar text-white px-4 py-3 rounded-lg hover:bg-purpleNavbarHover transition flex justify-center items-center space-x-2 my-5" onclick="toggleHarian('Karyawan')">
                                <i class="fa-solid fa-calendar-days"></i>
                                <span>Harian</span>
                            </button>

                            <!-- Mingguan Button with Date Range -->
                            <div class="relative w-full">
                                <button id="mingguanButtonKaryawan" class="w-full bg-purpleNavbar text-white px-4 py-3 rounded-lg hover:bg-purpleNavbarHover transition flex justify-center items-center space-x-2 mb-5" onclick="toggleMingguan('Karyawan')">
                                    <i class="fa-solid fa-calendar-week"></i>
                                    <span>Mingguan</span>
                                </button>

                                <div id="mingguanDatesKaryawan" class="hidden mt-2 p-3 bg-white text-black rounded-lg shadow-lg flex flex-col space-y-3">
                                    <label for="startMingguanKaryawan" class="text-sm">Start Date:</label>
                                    <input type="date" id="startMingguanKaryawan" class="p-2 rounded border focus:ring focus:ring-purple-300">

                                    <label for="endMingguanKaryawan" class="text-sm">End Date:</label>
                                    <input type="date" id="endMingguanKaryawan" class="p-2 rounded border focus:ring focus:ring-purple-300">

                                    <button id="downloadMingguanKaryawan" class="mt-3 w-full bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-blue-600 transition">
                                        Download Mingguan
                                    </button>
                                </div>
                            </div>

                            <!-- Bulanan Button with Date Range -->
                            <div class="relative w-full">
                                <button id="bulananButtonKaryawan" class="w-full bg-purpleNavbar text-white px-4 py-3 rounded-lg hover:bg-purpleNavbarHover transition flex justify-center items-center space-x-2" onclick="toggleBulanan('Karyawan')">
                                    <i class="fa-solid fa-calendar"></i>
                                    <span>Bulanan</span>
                                </button>

                                <div id="bulananDatesKaryawan" class="hidden mt-2 p-3 bg-white text-black rounded-lg shadow-lg flex flex-col space-y-3">
                                    <label for="startBulananKaryawan" class="text-sm">Start Date:</label>
                                    <input type="date" id="startBulananKaryawan" class="p-2 rounded border focus:ring focus:ring-purple-300">

                                    <label for="endBulananKaryawan" class="text-sm">End Date:</label>
                                    <input type="date" id="endBulananKaryawan" class="p-2 rounded border focus:ring focus:ring-purple-300">

                                    <button id="downloadBulananKaryawan" class="mt-3 w-full bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-blue-600 transition">
                                        Download Bulanan
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Close Button -->
                        <button id="closePopup" class="mt-6 bg-red-400 text-white px-4 py-2 rounded-lg hover:bg-red-500 transition">Close</button>
                    </div>
                </div>


                <div class="tableOverflow mt-6 shadow-customTable rounded-lg">
                    <table class="bg-white border" id="tableAbsensi">
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
                                        Sakit
                                        <div class="sort-icons">
                                            <i class="fas fa-sort-up sort-icon"></i>
                                            <i class="fas fa-sort-down sort-icon"></i>
                                        </div>
                                    </div>
                                </th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">
                                    <div class="sort-wrapper">
                                        Izin
                                        <div class="sort-icons">
                                            <i class="fas fa-sort-up sort-icon"></i>
                                            <i class="fas fa-sort-down sort-icon"></i>
                                        </div>
                                    </div>
                                </th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">
                                    <div class="sort-wrapper">
                                        Alpha
                                        <div class="sort-icons">
                                            <i class="fas fa-sort-up sort-icon"></i>
                                            <i class="fas fa-sort-down sort-icon"></i>
                                        </div>
                                    </div>
                                </th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">
                                    <div class="sort-wrapper">
                                        Cuti
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
                        <tbody id="absensi-table-body" class="divide-y divide-gray-200">
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

            <div id="loadingSpinner" class="hidden fixed inset-0 flex justify-center items-center bg-gray-900 bg-opacity-50 z-50">
                <div class="loader"></div>
            </div>

            <div id="toast-success" class="hidden fixed top-5 right-5 p-4 mb-4 w-80 max-w-xs bg-green-100 border-t-4 border-green-500 rounded-lg shadow-md text-green-800" role="alert">
                <div class="flex">
                    <div class="py-1">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="ml-3">
                        <p id="success-message" class="text-sm font-medium"></p>
                    </div>
                </div>
            </div>

            <div id="toast-error" class="hidden fixed top-5 right-5 p-4 mb-4 w-80 max-w-xs bg-red-100 border-t-4 border-red-500 rounded-lg shadow-md text-red-800" role="alert">
                <div class="flex">
                    <div class="py-1">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="ml-3">
                        <p id="error-message" class="text-sm font-medium"></p>
                    </div>
                </div>
            </div>

            <div id="popup" class="fixed top-5 right-5 bg-gradient-to-r from-purple-500 to-purple-700 text-white px-6 py-4 rounded-lg shadow-lg 
                transition-all duration-500 transform translate-x-[120%] opacity-0 z-[9999]">
                <span id="popup-message" class="font-semibold"></span>
                <button onclick="closeNotificationPopup()" class="ml-4 text-white font-bold">&times;</button>
            </div>
        </div>
    </div>
    <script src="src/pages/admin/js/renderDataAbsensi.js"></script>
    <script src="src/pages/admin/js/generateDetailAbsensi.js"></script>
    <script src="src/pages/admin/js/popUpAbsensi.js"></script>
    <?php include('src/pages/navbar/profileInfo.php') ?>

</body>

</html>