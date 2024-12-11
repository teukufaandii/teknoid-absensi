<?php
session_start();

if (!isset($_SESSION['token'])) {
    header('Location: login.php');
    exit();
}

// Cek session akses admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../unauthorized'); // Ganti dengan halaman yang sesuai
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

                <div id="loading" class="hidden text-center mt-4">
                    <p>Loading...</p>
                    <div class="loader"></div>
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
                    <button id="prev-page" class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition shadow-xl drop-shadow-xl cursor-pointer" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <!-- Pagination buttons will be added here dynamically -->
                    <button id="next-page" class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition shadow-xl drop-shadow-xl cursor-pointer">
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
            let searchTerm = '';
            let id_pg = "<?php echo $_GET['id_pg']; ?>";

            // Function to load "absensi" data
            function loadDataAbsensi(page, search = '') {
                $('#loading').removeClass('hidden');
                $.ajax({
                    url: '/teknoid-absensi/api/users/fetch-preview-detail',
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

            // Function to update pagination buttons
            function updatePaginationButtons() {
                const totalPages = Math.ceil(totalDataAbsensi / 10);
                const paginationContainer = $('#pagination-container');

                // Clear existing pagination buttons
                paginationContainer.find('.pagination-button').remove();

                // Create pagination buttons dynamically
                for (let i = 0; i < totalPages; i++) {
                    const button = $(`<button class="min-w-9 px-3 py-2 bg-purpleNavbar text-white rounded-md hover:bg-purpleNavbarHover transition shadow-xl drop-shadow-xl pagination-button" data-page="${i}">${i + 1}</button>`);
                    button.addClass(i === currentPage ? 'active-button' : 'inactive-button');
                    button.insertBefore('#next-page'); // Insert before "Next" button
                }

                // Enable/Disable Prev/Next buttons based on the current page
                $('#prev-page').prop('disabled', currentPage === 0);
                $('#next-page').prop('disabled', currentPage >= totalPages - 1);
            }

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

    <?php include('src/pages/navbar/profileInfo.php') ?>

</body>

</html>