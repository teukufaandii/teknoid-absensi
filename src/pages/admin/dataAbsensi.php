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
                    <table class="bg-white border">
                        <thead>
                            <tr class="bg-purpleNavbar text-white rounded-t-lg">
                                <th class="px-6 py-4 font-medium uppercase tracking-wider rounded-tl-lg">No</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">Nomor Induk</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">Nama Lengkap</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">Jabatan</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">Sakit</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">Izin</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">Alpha</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">Cuti</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider rounded-tr-lg">Aksi</th>
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
    <script>
        $('#addButton').on('click', function() {
            Swal.fire({
                title: 'Generate Detail Absen',
                text: 'Pilih opsi untuk generate detail absensi:',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Per Bulan',
                cancelButtonText: 'Per Minggu',
                html: '<button id="customCancel" class="swal2-cancel swal2-styled">Cancel</button>',
                allowOutsideClick: false
            }).then((result) => {
                let option;
                if (result.isConfirmed) {
                    option = 'bulanan';
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    option = 'mingguan';
                } else {
                    return;
                }

                if (option) {
                    document.getElementById('loadingSpinner').classList.remove('hidden');

                    $.ajax({
                        url: 'api/users/generate-absensi-details',
                        type: 'GET',
                        data: {
                            option: option
                        },
                        dataType: 'json',
                        success: function(response) {
                            document.getElementById('loadingSpinner').classList.add('hidden');

                            if (response.status === 'success') {
                                showToast('success', response.message);
                            } else {
                                showToast('error', response.message);
                            }
                        },
                        error: function() {
                            document.getElementById('loadingSpinner').classList.add('hidden');
                            showToast('error', 'Terjadi kesalahan saat memproses permintaan.');
                        }
                    });
                }
            });



            $(document).on('click', '#customCancel', function() {
                Swal.close();
            });
        });

        $('#addButton').on('click', function() {
            Swal.fire({
                title: 'Generate Detail Absen',
                text: 'Pilih opsi untuk generate detail absensi:',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Per Bulan',
                cancelButtonText: 'Per Minggu',
                html: '<button id="customCancel" class="swal2-cancel swal2-styled">Cancel</button>',
                allowOutsideClick: false
            }).then((result) => {
                let option;
                if (result.isConfirmed) {
                    option = 'bulanan';
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    option = 'mingguan';
                } else {
                    return;
                }

                if (option) {
                    document.getElementById('loadingSpinner').classList.remove('hidden');

                    $.ajax({
                        url: 'api/users/generate-absensi-details',
                        type: 'GET',
                        data: {
                            option: option
                        },
                        dataType: 'json',
                        success: function(response) {
                            document.getElementById('loadingSpinner').classList.add('hidden');

                            if (response.status === 'success') {
                                showToast('success', response.message);
                            } else {
                                showToast('error', response.message);
                            }
                        },
                        error: function() {
                            document.getElementById('loadingSpinner').classList.add('hidden');
                            showToast('error', 'Terjadi kesalahan saat memproses permintaan.');
                        }
                    });
                }
            });

            $(document).on('click', '#customCancel', function() {
                Swal.close();
            });
        });

        $(document).ready(function() {
            let currentPage = 0;
            let searchTerm = '';
            let totalDataAbsensi = 0;

            function loadDataAbsensi(page, search = '') {
                $('#loading').removeClass('hidden');
                $.ajax({
                    url: 'api/users/get-absensi',
                    type: 'GET',
                    data: {
                        start: page * 5,
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
                        currentPage = page;
                        renderData(response.data_absensi);
                        updatePaginationButtons();
                    },
                    error: function() {
                        $('#loading').addClass('hidden');
                        Swal.fire('Error!', 'Terjadi kesalahan saat memuat data', 'error');
                    }
                });
            }

            function renderData(data) {
                const tableBody = $('#absensi-table-body');
                tableBody.empty();

                if (data.length === 0) {
                    tableBody.append('<tr><td colspan="8" class="text-center">Tidak ada data</td></tr>');
                    return;
                }

                data.forEach((data_absensi, index) => {
                    tableBody.append(`
                <tr class="bg-gray-100">
                    <td class="px-6 py-2 text-center">${index + 1 + currentPage * 5}</td>
                    <td class="px-6 py-2 text-center">${data_absensi.noinduk}</td>
                    <td class="px-6 py-2 text-center">${data_absensi.nama}</td>
                    <td class="px-6 py-2 text-center">${data_absensi.jabatan}</td>
                    <td class="px-6 py-2 text-center">${data_absensi.sakit}</td>
                    <td class="px-6 py-2 text-center">${data_absensi.izin}</td>
                    <td class="px-6 py-2 text-center">${data_absensi.alpha}</td>
                    <td class="px-6 py-2 text-center">${data_absensi.cuti}</td>
                    <td class="px-6 py-2 text-center">
                        <a href="absensi/edit?id_pg=${data_absensi.id_pg}">
                        <button class="bg-purpleNavbar text-white px-3 py-2 rounded-xl hover:bg-purpleNavbarHover transition">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        </a>
                    </td>
                </tr>
            `);
                });
            }

            function updatePaginationButtons() {
                const totalPages = Math.ceil(totalDataAbsensi / 5);
                const paginationContainer = $('#pagination-container');

                // Clear previous buttons and add new ones
                paginationContainer.find('.pagination-button').remove();

                // Create dynamic pagination buttons
                for (let i = 0; i < totalPages; i++) {
                    const button = $(`<button class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition shadow-xl drop-shadow-xl pagination-button" data-page="${i}">${i + 1}</button>`);
                    button.addClass(i === currentPage ? 'active-button' : 'inactive-button');
                    button.insertBefore('#next-page'); // Insert before "Next" button
                }

                // Enable/Disable navigation buttons
                $('#first-page').prop('disabled', currentPage === 0);
                $('#prev-page').prop('disabled', currentPage === 0);
                $('#next-page').prop('disabled', currentPage >= totalPages - 1);
                $('#last-page').prop('disabled', currentPage >= totalPages - 1);
            }

            $('#first-page').on('click', function() {
                if (currentPage > 0) {
                    loadDataAbsensi(0, searchTerm);
                }
            });

            $('#last-page').on('click', function() {
                const totalPages = Math.ceil(totalDataAbsensi / 5);
                if (currentPage < totalPages - 1) {
                    loadDataAbsensi(totalPages - 1, searchTerm);
                }
            });

            $('#prev-page').on('click', function() {
                if (currentPage > 0) {
                    loadDataAbsensi(--currentPage, searchTerm);
                }
            });

            $('#next-page').on('click', function() {
                if ((currentPage + 1) * 5 < totalDataAbsensi) {
                    loadDataAbsensi(++currentPage, searchTerm);
                }
            });

            $(document).on('click', '.pagination-button', function() {
                const page = parseInt($(this).data('page'));
                if (page !== currentPage) {
                    loadDataAbsensi(page, searchTerm);
                }
            });

            $('#searchInput').on('keyup', function() {
                searchTerm = $(this).val();
                loadDataAbsensi(0, searchTerm);
            });

            loadDataAbsensi(currentPage);
        });

        function showToast(type, message) {
            const successToast = document.getElementById('toast-success');
            const errorToast = document.getElementById('toast-error');

            if (type === 'success') {
                document.getElementById('success-message').innerText = message;
                successToast.classList.remove('hidden');
                successToast.classList.add('block');
                setTimeout(() => {
                    successToast.classList.remove('block');
                    successToast.classList.add('hidden');
                }, 3000);
            } else if (type === 'error') {
                document.getElementById('error-message').innerText = message;
                errorToast.classList.remove('hidden');
                errorToast.classList.add('block');
                setTimeout(() => {
                    errorToast.classList.remove('block');
                    errorToast.classList.add('hidden');
                }, 3000);
            }
        }

        $.ajax({
            url: 'generateDetailAbsen.php',
            type: 'GET',
            data: {
                option: selectedOption
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    showToast('success', response.message);
                } else {
                    showToast('error', response.message);
                }
            },
            error: function() {
                showToast('error', 'Terjadi kesalahan saat memproses permintaan.');
            }
        });
    </script>

    <script>
        // Variabel untuk popup dan tombol
        const downloadButton = document.getElementById('downloadButton');
        const downloadPopup = document.getElementById('downloadPopup');
        const closePopup = document.getElementById('closePopup');
        const rolePopup = document.getElementById('rolePopup');
        const dosenTetapButton = document.getElementById('dosenTetapButton');
        const karyawanButton = document.getElementById('karyawanButton');
        const closeRolePopup = document.getElementById('closeRolePopup');

        // Dosen Tetap Sections
        const dosenTetapSection = document.getElementById('dosenTetapSection');
        const harianButtonDosenTetap = document.getElementById('harianButtonDosenTetap');
        const mingguanButtonDosenTetap = document.getElementById('mingguanButtonDosenTetap');
        const bulananButtonDosenTetap = document.getElementById('bulananButtonDosenTetap');
        const mingguanDatesDosenTetap = document.getElementById('mingguanDatesDosenTetap');
        const bulananDatesDosenTetap = document.getElementById('bulananDatesDosenTetap');
        const downloadMingguanDosenTetap = document.getElementById('downloadMingguanDosenTetap');
        const downloadBulananDosenTetap = document.getElementById('downloadBulananDosenTetap');

        // Karyawan Sections
        const karyawanSection = document.getElementById('karyawanSection');
        const harianButtonKaryawan = document.getElementById('harianButtonKaryawan');
        const mingguanButtonKaryawan = document.getElementById('mingguanButtonKaryawan');
        const bulananButtonKaryawan = document.getElementById('bulananButtonKaryawan');
        const mingguanDatesKaryawan = document.getElementById('mingguanDatesKaryawan');
        const bulananDatesKaryawan = document.getElementById('bulananDatesKaryawan');
        const downloadMingguanKaryawan = document.getElementById('downloadMingguanKaryawan');
        const downloadBulananKaryawan = document.getElementById('downloadBulananKaryawan');

        // Menyembunyikan popups saat halaman pertama kali dimuat
        downloadPopup.classList.add('hidden');
        rolePopup.classList.add('hidden');
        dosenTetapSection.classList.add('hidden');
        karyawanSection.classList.add('hidden');

        // Menampilkan Role Popup ketika tombol Download di-klik
        downloadButton.addEventListener('click', () => {
            rolePopup.classList.remove('hidden'); // Tampilkan role selection popup
        });

        // Menutup Role Popup ketika tombol Close di-klik
        closeRolePopup.addEventListener('click', () => {
            rolePopup.classList.add('hidden'); // Sembunyikan role selection popup
        });

        // Ketika "Dosen Tetap" dipilih
        dosenTetapButton.addEventListener('click', () => {
            rolePopup.classList.add('hidden'); // Menyembunyikan role popup
            dosenTetapSection.classList.remove('hidden'); // Menampilkan dosen tetap section
            karyawanSection.classList.add('hidden'); // Menyembunyikan karyawan section
            downloadPopup.classList.remove('hidden'); // Menampilkan download popup

            // Menampilkan pilihan Harian, Mingguan, dan Bulanan untuk Dosen Tetap
            harianButtonDosenTetap.classList.remove('hidden');
            mingguanButtonDosenTetap.classList.remove('hidden');
            bulananButtonDosenTetap.classList.remove('hidden');
        });

        // Ketika "Karyawan" dipilih
        karyawanButton.addEventListener('click', () => {
            rolePopup.classList.add('hidden'); // Menyembunyikan role popup
            karyawanSection.classList.remove('hidden'); // Menampilkan karyawan section
            dosenTetapSection.classList.add('hidden'); // Menyembunyikan dosen tetap section
            downloadPopup.classList.remove('hidden'); // Menampilkan download popup

            // Menampilkan pilihan Harian, Mingguan, dan Bulanan untuk Karyawan
            harianButtonKaryawan.classList.remove('hidden');
            mingguanButtonKaryawan.classList.remove('hidden');
            bulananButtonKaryawan.classList.remove('hidden');
        });

        // Menutup download popup
        closePopup.addEventListener('click', () => {
            downloadPopup.classList.add('hidden'); // Menyembunyikan download popup
        });

        function showPopup(message) {
            const popup = document.getElementById("popup");
            const popupMessage = document.getElementById("popup-message");

            if (popup && popupMessage) {
                popupMessage.textContent = message;

                popup.classList.remove("translate-x-[120%]", "opacity-0");
                popup.classList.add("translate-x-0", "opacity-100");

                setTimeout(() => closeNotificationPopup(), 3000);
            }
        }

        function closeNotificationPopup() {
            const popup = document.getElementById("popup");

            popup.classList.remove("translate-x-0", "opacity-100");
            popup.classList.add("translate-x-[120%]", "opacity-0");
        }

        // Handle Harian filter for Dosen Tetap (Dummy API)
        function toggleHarian(jabatan) {
            if (jabatan === 'dosenTetap') {
                window.location.href = 'api/user/download-dosen?filter=harian&jabatan=dosenTetap';
            } else if (jabatan === 'Karyawan') {
                window.location.href = 'api/user/download-karyawan?filter=harian&jabatan=Karyawan';
            }
        }

        // Fungsi untuk menghitung selisih tanggal
        function getDateDifference(start, end) {
            const startDate = new Date(start);
            const endDate = new Date(end);
            const diffTime = Math.abs(endDate - startDate);
            return Math.ceil(diffTime / (1000 * 60 * 60 * 24)); // Menghitung perbedaan dalam hari
        }


        function toggleMingguan(jabatan) {
            const jabatanPrefix = jabatan === 'dosenTetap' ? 'DosenTetap' : 'Karyawan';
            const mingguanDatesElement = jabatan === 'dosenTetap' ? mingguanDatesDosenTetap : mingguanDatesKaryawan;

            mingguanDatesElement.classList.remove('hidden'); // Tampilkan form tanggal mingguan

            if (jabatan === 'dosenTetap') {
                downloadMingguanDosenTetap.onclick = function() {
                    const start = document.getElementById(`startMingguan${jabatanPrefix}`);
                    const end = document.getElementById(`endMingguan${jabatanPrefix}`);

                    if (start && end) {
                        const startValue = start.value;
                        const endValue = end.value;

                        if (startValue && endValue) {
                            const diffDays = getDateDifference(startValue, endValue);

                            if (diffDays > 7) {
                                alert("Rentang tanggal untuk mingguan tidak boleh lebih dari 7 hari.");
                                return;
                            }

                            // Dummy API for Dosen Tetap (Redirecting to a dummy URL)
                            window.location.href = `api/user/download-dosen?filter=mingguan&start=${startValue}&end=${endValue}&jabatan=${jabatanPrefix}`;
                        } else {
                            alert("Harap isi kedua tanggal untuk filter mingguan.");
                        }
                    } else {
                        alert("Elemen input tanggal tidak ditemukan.");
                    }
                };
            } else {
                downloadMingguanKaryawan.onclick = function() {
                    const start = document.getElementById(`startMingguan${jabatanPrefix}`);
                    const end = document.getElementById(`endMingguan${jabatanPrefix}`);

                    if (start && end) {
                        const startValue = start.value;
                        const endValue = end.value;

                        if (startValue && endValue) {
                            const diffDays = getDateDifference(startValue, endValue);

                            if (diffDays > 7) {
                                showPopup("Rentang tanggal untuk mingguan tidak boleh lebih dari 7 hari.");
                                return;
                            }

                            // Actual API for Karyawan
                            window.location.href = `api/user/download-karyawan?filter=mingguan&start=${startValue}&end=${endValue}&jabatan=${jabatanPrefix}`;
                        } else {
                            showPopup("Harap isi kedua tanggal untuk filter mingguan.");
                        }
                    } else {
                        showPopup("Elemen input tanggal tidak ditemukan.");
                    }
                };
            }
        }
        // Handle Bulanan filter for Dosen Tetap (Dummy API)
        function toggleBulanan(jabatan) {
            const jabatanPrefix = jabatan === 'dosenTetap' ? 'DosenTetap' : 'Karyawan';
            const bulananDatesElement = jabatan === 'dosenTetap' ? bulananDatesDosenTetap : bulananDatesKaryawan;

            console.log(jabatanPrefix);

            // Menampilkan form tanggal bulanan
            bulananDatesElement.classList.remove('hidden');

            if (jabatan === 'dosenTetap') {
                downloadBulananDosenTetap.onclick = function() {
                    const startElement = document.getElementById(`startBulanan${jabatanPrefix}`);
                    const endElement = document.getElementById(`endBulanan${jabatanPrefix}`);

                    if (startElement && endElement) {
                        const start = startElement.value;
                        const end = endElement.value;

                        if (start && end) {
                            const diffDays = getDateDifference(start, end);

                            if (diffDays > 30) {
                                showPopup("Rentang tanggal untuk bulanan tidak boleh lebih dari 30 hari.");
                                return;
                            }

                            // Redirect ke Dummy API untuk Dosen Tetap
                            window.location.href = `api/user/download-dosen?filter=bulanan&start=${start}&end=${end}&jabatan=${jabatanPrefix}`;
                        } else {
                            showPopup("Harap isi kedua tanggal untuk filter bulanan.");
                        }
                    } else {
                        showPopup("Elemen tanggal tidak ditemukan.");
                    }
                };
            } else {
                downloadBulananKaryawan.onclick = function() {
                    console.log("Download Button Karyawan diklik");
                    const startElement = document.getElementById(`startBulanan${jabatanPrefix}`);
                    const endElement = document.getElementById(`endBulanan${jabatanPrefix}`);

                    if (startElement && endElement) {
                        const start = startElement.value;
                        const end = endElement.value;

                        if (start && end) {
                            const diffDays = getDateDifference(start, end);

                            if (diffDays > 30) {
                                showPopup("Rentang tanggal untuk bulanan tidak boleh lebih dari 30 hari.");
                                return;
                            }

                            // Redirect ke API yang sesuai untuk Karyawan
                            window.location.href = `api/user/download-karyawan?filter=bulanan&start=${start}&end=${end}&jabatan=${jabatanPrefix}`;
                        } else {
                            showPopup("Harap isi kedua tanggal untuk filter bulanan.");
                        }
                    } else {
                        showPopup("Elemen tanggal tidak ditemukan.");
                    }
                };
            }
        }

        // Event Listeners for Mingguan and Bulanan buttons
        mingguanButtonDosenTetap.addEventListener('click', () => toggleMingguan('dosenTetap'));
        bulananButtonDosenTetap.addEventListener('click', () => toggleBulanan('dosenTetap'));

        mingguanButtonKaryawan.addEventListener('click', () => toggleMingguan('karyawan'));
        bulananButtonKaryawan.addEventListener('click', () => toggleBulanan('karyawan'));
    </script>

    <?php include('src/pages/navbar/profileInfo.php') ?>

</body>

</html>