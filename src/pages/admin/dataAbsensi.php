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
    <title>Rekap Absensi</title>
    <link href="../../../css/output.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/dataAbsensi.css">
    <link href="../css/font/poppins-font.css" rel="stylesheet">
    <link href="../css/global/generalStyling.css" rel="stylesheet">
    <link href="../css/global/tableFormat.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/dataAbsensi.css">
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
                <h1 class="text-lg sm:text-xl md:text-3xl border-b border-gray-500 py-2 font-Poppins font-semibold">Data Absensi</h1>
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

                <div class="flex justify-between items-center mt-5">
                    <button id="downloadButton" class="bg-purpleNavbar text-white px-4 py-2  rounded-xl text-base font-medium hover:bg-purpleNavbarHover transition">
                        Download
                    </button>

                    <div class="relative">
                        <input
                            type="text"
                            id="searchInput"
                            placeholder="Search here..."
                            class="w-60 px-4 py-2 border rounded-xl shadow-md focus:outline-none focus:ring-2 focus:ring-purpleNavbar text-sm" />
                        <i class="fa fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <!-- Popup Download -->
                <div id="downloadPopup" class="hidden fixed inset-0 flex justify-center items-center bg-gray-900 bg-opacity-50 z-[9999]">
                    <div class="bg-white w-[450px] p-6 rounded-lg shadow-lg flex flex-col justify-between z-[10000]">
                        <h2 class="text-xl font-semibold mb-4 text-center">Filter Download</h2>

                        <div class="space-y-4">
                            <!-- Harian Button -->
                            <button id="harianButton" class="w-full bg-purpleNavbar text-white px-4 py-3 rounded-lg hover:bg-purpleNavbarHover transition flex justify-center items-center space-x-2" onclick="toggleHarian()">
                                <i class="fa-solid fa-calendar-days"></i>
                                <span>Harian</span>
                            </button>
                            <!-- Mingguan Button with Date Range -->
                            <div class="relative w-full">
                                <button id="mingguanButton" class="w-full bg-purpleNavbar text-white px-4 py-3 rounded-lg hover:bg-purpleNavbarHover transition flex justify-center items-center space-x-2" onclick="toggleMingguan()">
                                    <i class="fa-solid fa-calendar-week"></i>
                                    <span>Mingguan</span>
                                </button>

                                <div id="mingguanDates" class="hidden mt-2 p-3 bg-white text-black rounded-lg shadow-lg flex flex-col space-y-3">
                                    <label for="startMingguan" class="text-sm">Start Date:</label>
                                    <input type="date" id="startMingguan" class="p-2 rounded border focus:ring focus:ring-purple-300">

                                    <label for="endMingguan" class="text-sm">End Date:</label>
                                    <input type="date" id="endMingguan" class="p-2 rounded border focus:ring focus:ring-purple-300">

                                    <button id="downloadMingguan" class="mt-3 w-full bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-blue-600 transition" onclick="downloadMingguan()">
                                        Download Mingguan
                                    </button>
                                </div>
                            </div>
                            <!-- Bulanan Button with Date Range -->
                            <div class="relative w-full">
                                <button id="bulananButton" class="w-full bg-purpleNavbar text-white px-4 py-3 rounded-lg hover:bg-purpleNavbarHover transition flex justify-center items-center space-x-2" onclick="toggleBulanan()">
                                    <i class="fa-solid fa-calendar"></i>
                                    <span>Bulanan</span>
                                </button>

                                <div id="bulananDates" class="hidden mt-2 p-3 bg-white text-black rounded-lg shadow-lg flex flex-col space-y-3">
                                    <label for="startBulanan" class="text-sm">Start Date:</label>
                                    <input type="date" id="startBulanan" class="p-2 rounded border focus:ring focus:ring-purple-300">

                                    <label for="endBulanan" class="text-sm">End Date:</label>
                                    <input type="date" id="endBulanan" class="p-2 rounded border focus:ring focus:ring-purple-300">

                                    <button id="downloadBulanan" class="mt-3 w-full bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-blue-600 transition" onclick="downloadBulanan()">
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
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">Nomor Induk</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">Nama Lengkap</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider rounded-tr-lg">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="absensi-table-body" class="divide-y divide-gray-200">
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
    <script>
        $(document).ready(function() {
            let currentPage = 0;
            let totalDataAbsensi = 0;
            let searchTerm = ''; // Variabel untuk menyimpan kata kunci pencarian

            function loadDataAbsensi(page, search = '') {
                $.ajax({
                    url: '../../db/routes/getDataAbsensi.php',
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
                        let DataAbsensiTableBody = $('#absensi-table-body');
                        DataAbsensiTableBody.empty();

                        if (response.data_absensi.length === 0 && currentPage > 0) {
                            currentPage--;
                            loadDataAbsensi(currentPage, search);
                        } else if (response.data_absensi.length === 0) {
                            DataAbsensiTableBody.append('<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>');
                        } else {
                            let counter = page * 5 + 1;
                            response.data_absensi.forEach(function(data_absensi) {
                                DataAbsensiTableBody.append(`
                            <tr class="bg-gray-100">
                                <td class="px-6 py-2 text-center">${counter++}</td>
                                <td class="px-6 py-2 text-center">${data_absensi.noinduk}</td>
                                <td class="px-6 py-2 text-center">${data_absensi.nama}</td>
                                <td class="px-6 py-2 text-center">${data_absensi.role}</td>
                                <td class="px-6 py-2 text-center">
                                    <a href="./previewDataAbsensi.php?id_pg=${data_absensi.id_pg}">
                                        <button class="bg-purpleNavbar text-white px-3 py-2 rounded-xl hover:bg-purpleNavbarHover transition"><i class="fa-solid fa-pen-to-square"></i></button>
                                    </a>
                                    <a>
                                         <button class="delete-button bg-red-400 text-white px-3 py-2 rounded-xl hover:bg-red-500 transition" data-id="${data_absensi.id_pg}"><i class="fa-solid fa-trash"></i></button>
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
    </script>

    <script>
        // Variables for popup and buttons
        const downloadButton = document.getElementById('downloadButton');
        const downloadPopup = document.getElementById('downloadPopup');
        const closePopup = document.getElementById('closePopup');
        const harianButton = document.getElementById('harianButton');
        const mingguanButton = document.getElementById('mingguanButton');
        const bulananButton = document.getElementById('bulananButton');
        const mingguanDates = document.getElementById('mingguanDates');
        const bulananDates = document.getElementById('bulananDates');
        const downloadMingguan = document.getElementById('downloadMingguan');
        const downloadBulanan = document.getElementById('downloadBulanan');

        // Show the download popup
        downloadButton.addEventListener('click', () => {
            downloadPopup.classList.remove('hidden');
        });

        // Close the download popup
        closePopup.addEventListener('click', () => {
            downloadPopup.classList.add('hidden');
            mingguanDates.classList.add('hidden');
            bulananDates.classList.add('hidden');
            downloadMingguan.classList.add('hidden');
            downloadBulanan.classList.add('hidden');
        });

        function toggleHarian() {
            window.location.href = 'downloadAbsensi.php?filter=harian';
        }

        function getDateDifference(start, end) {
            const startDate = new Date(start);
            const endDate = new Date(end);
            const diffTime = Math.abs(endDate - startDate);
            return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        }

        function toggleMingguan() {
            const mingguanDates = document.getElementById('mingguanDates');
            const startMingguan = document.getElementById('startMingguan');
            const endMingguan = document.getElementById('endMingguan');
            const downloadMingguan = document.getElementById('downloadMingguan');

            mingguanDates.classList.toggle('hidden');

            downloadMingguan.onclick = function() {
                const start = startMingguan.value;
                const end = endMingguan.value;

                if (start && end) {
                    const diffDays = getDateDifference(start, end);

                    if (diffDays > 7) {
                        alert("Rentang tanggal untuk mingguan tidak boleh lebih dari 7 hari.");
                        return;
                    }

                    window.location.href = `downloadAbsensi.php?filter=mingguan&start=${start}&end=${end}`;
                } else {
                    alert("Harap isi kedua tanggal untuk filter mingguan.");
                }
            };
        }

        function toggleBulanan() {
            const bulananDates = document.getElementById('bulananDates');
            const startBulanan = document.getElementById('startBulanan');
            const endBulanan = document.getElementById('endBulanan');
            const downloadBulanan = document.getElementById('downloadBulanan');

            bulananDates.classList.toggle('hidden');

            downloadBulanan.onclick = function() {
                const start = startBulanan.value;
                const end = endBulanan.value;

                if (start && end) {
                    const diffDays = getDateDifference(start, end);

                    if (diffDays > 30) {
                        alert("Rentang tanggal untuk bulanan tidak boleh lebih dari 30 hari.");
                        return;
                    }

                    window.location.href = `downloadAbsensi.php?filter=bulanan&start=${start}&end=${end}`;
                } else {
                    alert("Harap isi kedua tanggal untuk filter bulanan.");
                }
            };
        }

        downloadMingguan.addEventListener('click', () => {
            const start = document.getElementById('startMingguan').value;
            const end = document.getElementById('endMingguan').value;

            if (start && end) {
                window.location.href = `downloadAbsensi.php?filter=mingguan&start=${start}&end=${end}`;
            } else {
                alert('Please select both start and end dates for weekly download');
            }
        });

        downloadBulanan.addEventListener('click', () => {
            const start = document.getElementById('startBulanan').value;
            const end = document.getElementById('endBulanan').value;

            if (start && end) {
                window.location.href = `downloadAbsensi.php?filter=bulanan&start=${start}&end=${end}`;
            } else {
                alert('Please select both start and end dates for monthly download');
            }
        });
    </script>

    <?php include('../navbar/profileInfo.php') ?>

</body>

</html>