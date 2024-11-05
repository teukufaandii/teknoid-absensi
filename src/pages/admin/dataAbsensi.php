<?php
session_start();

if (!isset($_SESSION['token'])) {
    header('Location: login.php');
    exit();
}

// Cek session akses admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../../unauthorized.php');
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
    <link href="../css/font/poppins-font.css" rel="stylesheet">
    <link href="../css/global/generalStyling.css" rel="stylesheet">
    <link href="../css/global/tableFormat.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
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
        <?php include('../navbar/sidenav.php') ?>

        <div id="content" class="min-h-screen inline-flex flex-col flex-1 bg-mainBgColor ml-56">
            <!-- Top Navigation -->
            <?php include('../navbar/topnav.php') ?>

            <!-- Main Content -->
            <main class="flex-1 p-6 bg-mainBgColor mainContent">
                <h1 class="text-lg sm:text-xl md:text-3xl border-b border-gray-500 py-2 font-Poppins font-semibold">Data Absensi</h1>

                <div class="flex justify-between items-center mt-5">
                    <div class="flex justify-start items-center space-x-4">
                        <button id="downloadButton" class="bg-purpleNavbar text-white px-4 py-2  rounded-xl text-base font-medium hover:bg-purpleNavbarHover transition">
                            Download
                        </button>
                        <button class="bg-purpleNavbar text-white px-4 py-2 rounded-xl text-base font-medium hover:bg-purpleNavbarHover transition" id="addButton"> 
                            Generate Detail Absen <i class="fa-solid fa-circle-plus"></i>
                        </button>
                    </div>

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
                    <button id="prev-page" class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition shadow-xl drop-shadow-xl cursor-pointer" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover hover:text-white transition shadow-xl drop-shadow-xl pagination-button" data-page="0">1</button>
                    <button class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover hover:text-white transition shadow-xl drop-shadow-xl pagination-button" data-page="1">2</button>
                    <button class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover hover:text-white transition shadow-xl drop-shadow-xl pagination-button" data-page="2">3</button>
                    <button id="next-page" class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition shadow-xl drop-shadow-xl cursor-pointer">
                        <i class="fas fa-chevron-right"></i>
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
                        url: '../../db/routes/generateAbsenceDetails.php',
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
            let totalDataAbsensi = 0;
            let searchTerm = '';

            function loadDataAbsensi(page, search = '') {
                $.ajax({
                    url: '../../db/routes/getDataAbsensi.php',
                    type: 'GET',
                    data: {
                        start: page * 5,
                        search: search
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

            $('#searchInput').on('keyup', function() {
                searchTerm = $(this).val();
                currentPage = 0;
                loadDataAbsensi(currentPage, searchTerm);
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