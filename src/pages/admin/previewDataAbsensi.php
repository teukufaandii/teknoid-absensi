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
                    <h1 class="text-lg sm:text-xl md:text-3xl py-2 font-Poppins font-semibold">Preview Data Absensi <?php echo $nama_pg ?></h1>
                    <a href="../absensi">
                        <button class="bg-purpleNavbar text-white px-4 py-2 rounded-lg hover:bg-purpleNavbarHover transition duration-200">Kembali</button>
                    </a>
                </div>

                <div class="flex justify-start mt-5">
                    <button id="downloadButton" class="bg-purpleNavbar text-white px-4 py-2  rounded-xl text-base font-medium hover:bg-purpleNavbarHover transition">
                        Download
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
    <script>
        $(document).ready(function() {
            let currentPage = 0;
            let totalDataAbsensi = 0;
            let searchTerm = '';
            let id_pg = "<?php echo $_GET['id_pg']; ?>";

            // Function to load "absensi" data
            function loadDataAbsensi(page, search = '') {
                $('#loading').removeClass('hidden');
                $.ajax({
                    url: '../api/users/fetch-preview-detail',
                    type: 'GET',
                    data: {
                        id_pg: id_pg,
                        start: page * 10,
                        search: search
                    },
                    dataType: 'json',
                    success: function(response) {
                        $('#loading').addClass('hidden');
                        if (response.status === 'unauthorized') {
                            window.location.href = 'unauthorized';
                            return;
                        }

                        totalDataAbsensi = response.total;
                        renderData(response.preview_data_absensi, page);
                        updatePaginationButtons();
                    },
                    error: function() {
                        $('#loading').addClass('hidden');
                        Swal.fire('Error!', 'Terjadi kesalahan saat memuat data', 'error');
                    }
                });
            }

            // Function to render data into the table
            function renderData(data, page) {
                const tableBody = $('#preview-absensi-table-body');
                tableBody.empty();

                if (data.length === 0 && page > 0) {
                    currentPage--; // Go to previous page if no data found
                    loadDataAbsensi(currentPage, searchTerm);
                } else if (data.length === 0) {
                    tableBody.append('<tr><td colspan="8" class="text-center">Tidak ada data untuk <?php echo $nama_pg ?></td></tr>');
                } else {
                    let counter = page * 10 + 1;
                    data.forEach((item) => {
                        tableBody.append(`
                    <tr class="bg-gray-100">
                        <td class="px-6 py-2 text-center">${counter++}</td>
                        <td class="px-6 py-2 text-center">${item.nama}</td>
                        <td class="px-6 py-2 text-center">${item.tanggal ? item.tanggal.split('-').reverse().join('-') : '-'}</td>
                        <td class="px-6 py-2 text-center">${item.scan_masuk}</td>
                        <td class="px-6 py-2 text-center">${item.scan_keluar}</td>
                        <td class="px-6 py-2 text-center">${item.durasi}</td>
                        <td class="px-6 py-2 text-center">${item.keterangan}</td>
                        <td class="px-6 py-2 text-center">
                            <a href="../absensi/edit/preview?id_pg=${item.id_pg}&id=${item.id}">
                                <button class="bg-purpleNavbar text-white px-3 py-2 rounded-xl hover:bg-purpleNavbarHover transition"><i class="fa-solid fa-pen-to-square"></i></button>
                            </a>
                        </td>
                    </tr>
                `);
                    });
                }
            }

            function updatePaginationButtons() {
                const totalPages = Math.ceil(totalDataAbsensi / 10);

                // Define the range of pages to display
                const maxButtonsToShow = 5; // Maximum number of buttons to display
                let startPage = Math.max(0, currentPage - Math.floor(maxButtonsToShow / 2));
                let endPage = Math.min(totalPages, startPage + maxButtonsToShow);

                if (endPage - startPage < maxButtonsToShow) {
                    startPage = Math.max(0, endPage - maxButtonsToShow);
                }

                // Clear existing pagination buttons
                $('#pagination-container .pagination-button').remove();

                // Create pagination buttons dynamically
                for (let i = startPage; i < endPage; i++) {
                    const button = $(`<button class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition shadow-xl drop-shadow-xl pagination-button" data-page="${i}">${i + 1}</button>`);
                    button.addClass(i === currentPage ? 'active-button' : 'inactive-button');
                    button.insertBefore('#next-page'); // Insert before "Next" button
                }

                // Enable/Disable First, Prev, Next, and Last buttons based on the current page
                $('#first-page').prop('disabled', currentPage === 0);
                $('#prev-page').prop('disabled', currentPage === 0);
                $('#next-page').prop('disabled', currentPage >= totalPages - 1);
                $('#last-page').prop('disabled', currentPage >= totalPages - 1);
            }

            $('#first-page').on('click', function() {
                if (currentPage > 0) {
                    currentPage = 0;
                    loadDataAbsensi(currentPage, searchTerm);
                    updatePaginationButtons();
                }
            });

            $('#last-page').on('click', function() {
                const totalPages = Math.ceil(totalDataAbsensi / 10);
                if (currentPage < totalPages - 1) {
                    currentPage = totalPages - 1;
                    loadDataAbsensi(currentPage, searchTerm);
                    updatePaginationButtons();
                }
            });

            // Event listener for "Previous" button
            $('#prev-page').on('click', function() {
                if (currentPage > 0) {
                    loadDataAbsensi(--currentPage, searchTerm);
                }
            });

            // Event listener for "Next" button
            $('#next-page').on('click', function() {
                if ((currentPage + 1) * 10 < totalDataAbsensi) {
                    loadDataAbsensi(++currentPage, searchTerm);
                }
            });

            $(document).on('click', '.pagination-button', function() {
                currentPage = parseInt($(this).data('page'));
                loadDataAbsensi(currentPage, searchTerm);
                updatePaginationButtons();
            });

            // Event listener for search input
            $('#searchInput').on('keyup', function() {
                searchTerm = $(this).val();
                currentPage = 0; // Reset to first page on search
                loadDataAbsensi(currentPage, searchTerm);
            });

            // Initial data load
            loadDataAbsensi(currentPage);
        });
    </script>

    <script>
        // Variables for popup and buttons
        const id_pg = "<?= isset($_GET['id_pg']) ? $_GET['id_pg'] : '' ?>";
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
            window.location.href = `../api/user/download-data-user?filter=harian&id_pg=${id_pg}`;
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

                    window.location.href = `../api/user/download-data-user?filter=mingguan&start=${start}&end=${end}&id_pg=${id_pg}`;
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

                    window.location.href = `../api/user/download-data-user?filter=bulanan&start=${start}&end=${end}&id_pg=${id_pg}`;
                } else {
                    alert("Harap isi kedua tanggal untuk filter bulanan.");
                }
            };
        }

        downloadMingguan.addEventListener('click', () => {
            const start = document.getElementById('startMingguan').value;
            const end = document.getElementById('endMingguan').value;

            if (start && end) {
                window.location.href = `../api/user/download-data-user?filter=mingguan&start=${start}&end=${end}&id_pg=${id_pg}`;
            } else {
                alert('Please select both start and end dates for weekly download');
            }
        });

        downloadBulanan.addEventListener('click', () => {
            const start = document.getElementById('startBulanan').value;
            const end = document.getElementById('endBulanan').value;

            if (start && end) {
                window.location.href = `../api/user/download-data-user?filter=bulanan&start=${start}&end=${end}&id_pg=${id_pg}`;
            } else {
                alert('Please select both start and end dates for monthly download');
            }
        });
    </script>

    <?php include('src/pages/navbar/profileInfo.php') ?>

</body>

</html>