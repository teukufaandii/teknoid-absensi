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

// Pengaturan baris
$start = 0;
$rows_per_page = 10;

// Total nomor baris
$records = mysqli_query($conn, "SELECT * FROM tb_pengguna");
$nr_of_rows = $records->num_rows;

// Kalkulasi nomor per halaman
$pages = ceil($nr_of_rows / $rows_per_page);

// Start point
if (isset($_GET['page-nr'])) {
    $page = $_GET['page-nr'] - 1;
    $start = $page * $rows_per_page;
}

// Tabel db pengguna
$stmt = $conn->prepare("SELECT * FROM tb_pengguna LIMIT $start, $rows_per_page");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi</title>
    <link href="../../css/output.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/global/tableFormat.css">
    <link rel="stylesheet" href="./css/dataAbsensi.css">
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
            <main class="flex-1 h-auto p-4 sm:p-6 lg:p-8 bg-mainBgColor mainContent">
            <h1 class="text-lg sm:text-xl md:text-3xl border-b border-gray-500 py-2 font-Poppins font-semibold">Data Absensi</h1>
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

                <button id="downloadButton" class="mt-4 bg-purpleNavbar text-white px-8 py-2 rounded-xl hover:bg-purpleNavbarHover transition">
                    Download
                </button>

                <!-- Popup Download -->
                <div id="downloadPopup" class="hidden fixed inset-0 flex justify-center items-center bg-gray-900 bg-opacity-50 z-[9999]">
                    <div class="bg-white w-[430px] h-[430px] p-6 rounded-lg shadow-lg flex flex-col justify-between z-[10000]">
                        <h2 class="text-xl font-semibold mb-4 text-center">Filter Download</h2>
                        <div class="flex justify-between space-x-3">
                            <button id="harianButton" class="flex-1 bg-purpleNavbar text-white px-2 py-4 rounded-lg hover:bg-purpleNavbarHover transition">
                                <i class="fa-solid fa-calendar-days"></i> Harian
                            </button>
                            <button id="mingguanButton" class="flex-1 bg-purpleNavbar text-white px-2 py-4 rounded-lg hover:bg-purpleNavbarHover transition">
                                <i class="fa-solid fa-calendar-week"></i> Mingguan
                            </button>
                            <button id="bulananButton" class="flex-1 bg-purpleNavbar text-white px-2 py-4 rounded-lg hover:bg-purpleNavbarHover transition">
                                <i class="fa-solid fa-calendar"></i> Bulanan 
                            </button>
                        </div>
                        <button id="closePopup" class="mt-4 bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                            Close
                        </button>
                    </div>
                </div>

                                                            
                <div class="tableOverflow mt-6 shadow-customTable rounded-lg">
                    <table class="bg-white border">
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
                        <tr class="bg-gray-100">
                          <td class="px-6 py-2 text-center <?php echo $is_last_row ? 'rounded-bl-lg' : ''; ?>">1</td>
                          <td class="px-6 py-2 text-center">215123123123</td>
                          <td class="px-6 py-2 text-center">Adam Ilham Sulaiman</td>
                          <td class="px-6 py-2 text-center">Anomali</td>
                          <td class="px-6 py-2 text-center <?php echo $is_last_row ? 'rounded-br-lg' : ''; ?>">
                              <a href="previewDataAbsensi.php">
                                  <button class="bg-purpleNavbar text-white px-8 py-2 rounded-xl hover:bg-purpleNavbarHover transition">Lihat</button>
                              <a>
                          </td>
                        </tr>
                        <tr>
                          <td class="px-6 py-2 text-center">2</td>
                          <td class="px-6 py-2 text-center">215123123123</td>
                          <td class="px-6 py-2 text-center">Adam Ilham Sulaiman</td>
                          <td class="px-6 py-2 text-center">Anomali</td>
                          <td class="px-6 py-2 text-center">
                              <a href="previewDataAbsensi.php">
                                  <button class="bg-purpleNavbar text-white px-8 py-2 rounded-xl hover:bg-purpleNavbarHover transition">Lihat</button>
                              <a>
                          </td>
                        </tr>
                        <tr class="bg-gray-100">
                          <td class="px-6 py-2 text-center">3</td>
                          <td class="px-6 py-2 text-center">215123123123</td>
                          <td class="px-6 py-2 text-center">Adam Ilham Sulaiman</td>
                          <td class="px-6 py-2 text-center">Anomali</td>
                          <td class="px-6 py-2 text-center">
                              <a href="previewDataAbsensi.php">
                                  <button class="bg-purpleNavbar text-white px-8 py-2 rounded-xl hover:bg-purpleNavbarHover transition">Lihat</button>
                              <a>
                          </td>
                        </tr>
                        <tr>
                          <td class="px-6 py-2 text-center">4</td>
                          <td class="px-6 py-2 text-center">215123123123</td>
                          <td class="px-6 py-2 text-center">Adam Ilham Sulaiman</td>
                          <td class="px-6 py-2 text-center">Anomali</td>
                          <td class="px-6 py-2 text-center">
                              <a href="previewDataAbsensi.php">
                                  <button class="bg-purpleNavbar text-white px-8 py-2 rounded-xl hover:bg-purpleNavbarHover transition">Lihat</button>
                              <a>
                          </td>
                        </tr>
                        <tr>
                          <td class="px-6 py-2 text-center">5</td>
                          <td class="px-6 py-2 text-center">215123123123</td>
                          <td class="px-6 py-2 text-center">Adam Ilham Sulaiman</td>
                          <td class="px-6 py-2 text-center">Anomali</td>
                          <td class="px-6 py-2 text-center">
                              <a href="previewDataAbsensi.php">
                                  <button class="bg-purpleNavbar text-white px-8 py-2 rounded-xl hover:bg-purpleNavbarHover transition">Lihat</button>
                              <a>
                          </td>
                        </tr>
                        <tr>
                          <td class="px-6 py-2 text-center">6</td>
                          <td class="px-6 py-2 text-center">215123123123</td>
                          <td class="px-6 py-2 text-center">Adam Ilham Sulaiman</td>
                          <td class="px-6 py-2 text-center">Anomali</td>
                          <td class="px-6 py-2 text-center">
                              <a href="previewDataAbsensi.php">
                                  <button class="bg-purpleNavbar text-white px-8 py-2 rounded-xl hover:bg-purpleNavbarHover transition">Lihat</button>
                              <a>
                          </td>
                        </tr>
                        <tr>
                          <td class="px-6 py-2 text-center">7</td>
                          <td class="px-6 py-2 text-center">215123123123</td>
                          <td class="px-6 py-2 text-center">Adam Ilham Sulaiman</td>
                          <td class="px-6 py-2 text-center">Anomali</td>
                          <td class="px-6 py-2 text-center">
                              <a href="previewDataAbsensi.php">
                                  <button class="bg-purpleNavbar text-white px-8 py-2 rounded-xl hover:bg-purpleNavbarHover transition">Lihat</button>
                              <a>
                          </td>
                        </tr>
                        <tr>
                          <td class="px-6 py-2 text-center">8</td>
                          <td class="px-6 py-2 text-center">215123123123</td>
                          <td class="px-6 py-2 text-center">Adam Ilham Sulaiman</td>
                          <td class="px-6 py-2 text-center">Anomali</td>
                          <td class="px-6 py-2 text-center">
                              <a href="previewDataAbsensi.php">
                                  <button class="bg-purpleNavbar text-white px-8 py-2 rounded-xl hover:bg-purpleNavbarHover transition">Lihat</button>
                              <a>
                          </td>
                        </tr>
                        <tr>
                          <td class="px-6 py-2 text-center">9</td>
                          <td class="px-6 py-2 text-center">215123123123</td>
                          <td class="px-6 py-2 text-center">Adam Ilham Sulaiman</td>
                          <td class="px-6 py-2 text-center">Anomali</td>
                          <td class="px-6 py-2 text-center">
                              <a href="previewDataAbsensi.php">
                                  <button class="bg-purpleNavbar text-white px-8 py-2 rounded-xl hover:bg-purpleNavbarHover transition">Lihat</button>
                              <a>
                          </td>
                        </tr>
                        <tr>
                          <td class="px-6 py-2 text-center">10</td>
                          <td class="px-6 py-2 text-center">215123123123</td>
                          <td class="px-6 py-2 text-center">Adam Ilham Sulaiman</td>
                          <td class="px-6 py-2 text-center">Anomali</td>
                          <td class="px-6 py-2 text-center">
                              <a href="previewDataAbsensi.php">
                                  <button class="bg-purpleNavbar text-white px-8 py-2 rounded-xl hover:bg-purpleNavbarHover transition">Lihat</button>
                              <a>
                          </td>
                        </tr>
                        <tr>
                          <td class="px-6 py-2 text-center">11</td>
                          <td class="px-6 py-2 text-center">215123123123</td>
                          <td class="px-6 py-2 text-center">King Adam Mentor</td>
                          <td class="px-6 py-2 text-center">Anomali</td>
                          <td class="px-6 py-2 text-center">
                              <a href="previewDataAbsensi.php">
                                  <button class="bg-purpleNavbar text-white px-8 py-2 rounded-xl hover:bg-purpleNavbarHover transition">Lihat</button>
                              <a>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                </div>
                
                  <div class="flex justify-center items-center space-x-1 mt-4">
                    <!-- Previous Button -->
                    <button class="px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    
                    <!-- Page Numbers -->
                    <button class="px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition">1</button>
                    <button class="px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition">2</button>
                    <button class="px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition">3</button>

                    <!-- Dots -->
                    <button class="px-3 py-2 text-purple-600 rounded-md hover:text-white hover:bg-purpleNavbarHover transition">...</button>

                    <!-- Next Button -->
                    <button class="px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </main>
        </div>
    </div>
    <script>
    const downloadButton = document.getElementById('downloadButton');
    const downloadPopup = document.getElementById('downloadPopup');
    const closePopup = document.getElementById('closePopup');
    const harianButton = document.getElementById('harianButton');
    const mingguanButton = document.getElementById('mingguanButton');
    const bulananButton = document.getElementById('bulananButton');

    downloadButton.addEventListener('click', () => {
        downloadPopup.classList.remove('hidden'); // Tampilkan popup
    });

    closePopup.addEventListener('click', () => {
        downloadPopup.classList.add('hidden'); // Sembunyikan popup
    });

    window.addEventListener('click', (event) => {
        if (event.target === downloadPopup) {
            downloadPopup.classList.add('hidden');
        }
    });

    function downloadFilteredData(filter) {
        window.location.href = `downloadAbsensi.php?filter=${filter}`;
    }

    harianButton.addEventListener('click', () => {
        downloadFilteredData('harian');
    });

    mingguanButton.addEventListener('click', () => {
        downloadFilteredData('mingguan');
    });

    bulananButton.addEventListener('click', () => {
        downloadFilteredData('bulanan');
    });
    </script>

</body>

</html>