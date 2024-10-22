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

if (isset($_GET['id_pg']) && !empty($_GET['id_pg'])) {
    $id_pg = $_GET['id_pg'];
    include '../../db/db_connect.php';

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
    <link href="../../../css/output.css" rel="stylesheet">
    <link href="../css/font/poppins-font.css" rel="stylesheet">
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
                <div class="flex justify-between items-center border-b border-gray-500">
                    <h1 class="text-lg sm:text-xl md:text-3xl py-2 font-Poppins font-semibold">Preview Data Absensi <?php echo $nama_pg ?></h1>
                    <a href="dataAbsensi.php">
                        <button class="bg-purpleNavbar text-white px-4 py-2 rounded-lg hover:bg-purpleNavbarHover transition duration-200">Kembali</button>
                    </a>
                </div>
                <div class="tableOverflow mt-6 shadow-customTable rounded-lg">
                    <table class="bg-white border">
                        <thead>
                            <tr class="bg-purpleNavbar text-white rounded-t-lg">
                                <th class="px-6 py-4 font-medium uppercase tracking-wider rounded-tl-lg">Tanggal</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">jam Kerja</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">Scan Masuk</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">Scan Keluar</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">Durasi (m)</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">Keterangan</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider rounded-tr-lg">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="preview-absensi-table-body" class="divide-y divide-gray-200">
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
            let searchTerm = '';
            let id_pg = "<?php echo $_GET['id_pg']; ?>";

            function loadDataAbsensi(page, search = '') {
                $.ajax({
                    url: '../../db/routes/fetchPreviewData.php',
                    type: 'GET',
                    data: {
                        id_pg: id_pg,
                        start: page * 10,
                        search: search
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'unauthorized') {
                            window.location.href = '../../unauthorized.php';
                            return;
                        }

                        totalDataAbsensi = response.total;
                        let DataAbsensiTableBody = $('#preview-absensi-table-body');
                        DataAbsensiTableBody.empty();

                        if (response.preview_data_absensi.length === 0 && currentPage > 0) {
                            currentPage--;
                            loadDataAbsensi(currentPage, search);
                        } else if (response.preview_data_absensi.length === 0) {
                            DataAbsensiTableBody.append('<tr><td colspan="8" class="text-center">Tidak ada data untuk <?php echo $nama_pg ?></td></tr>');
                        } else {
                            let counter = page * 10 + 1;
                            response.preview_data_absensi.forEach(function(preview_data_absensi) {
                                DataAbsensiTableBody.append(`
                                <tr class="bg-gray-100">
                                    <td class="px-6 py-2 text-center">${counter++}</td>
                                    <td class="px-6 py-2 text-center">${preview_data_absensi.nama}</td>
                                    <td class="px-6 py-2 text-center">${preview_data_absensi.tanggal}</td>
                                    <td class="px-6 py-2 text-center">${preview_data_absensi.scan_masuk}</td>
                                    <td class="px-6 py-2 text-center">${preview_data_absensi.scan_keluar}</td>
                                    <td class="px-6 py-2 text-center">${preview_data_absensi.durasi}</td>
                                    <td class="px-6 py-2 text-center">${preview_data_absensi.keterangan}</td>
                                    <td class="px-6 py-2 text-center">
                                        <a href="./previewDataAbsensi.php?id_pg=${preview_data_absensi.id_pg}">
                                            <button class="bg-purpleNavbar text-white px-3 py-2 rounded-xl hover:bg-purpleNavbarHover transition"><i class="fa-solid fa-pen-to-square"></i></button>
                                        </a>
                                        <button class="delete-button bg-red-400 text-white px-3 py-2 rounded-xl hover:bg-red-500 transition" data-id="${preview_data_absensi.id_pg}"><i class="fa-solid fa-trash"></i></button>
                                    </td>
                                </tr>
                            `);
                            });
                        }

                        $('#prev-page').prop('disabled', currentPage === 0);
                        $('#next-page').prop('disabled', (currentPage + 1) * 10 >= totalDataAbsensi);

                        updatePaginationButtons();
                    }
                });
            }

            function updatePaginationButtons() {
                const totalPages = Math.ceil(totalDataAbsensi / 10); // Updated for 10 results per page
                const paginationButtons = $('.pagination-button');

                paginationButtons.hide();

                for (let i = 0; i < totalPages; i++) {
                    paginationButtons.eq(i).show().data('page', i).text(i + 1);
                    if (i === currentPage) {
                        paginationButtons.eq(i).addClass('active-button').removeClass('inactive-button');
                    } else {
                        paginationButtons.eq(i).addClass('inactive-button').removeClass('active-button');
                    }
                }

                $('#next-page').prop('disabled', currentPage >= totalPages - 1);
            }

            $('#prev-page').on('click', function() {
                if (currentPage > 0) {
                    currentPage--;
                    loadDataAbsensi(currentPage, searchTerm);
                }
            });

            $('#next-page').on('click', function() {
                if ((currentPage + 1) * 10 < totalDataAbsensi) {
                    currentPage++;
                    loadDataAbsensi(currentPage, searchTerm);
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

            // Load initial data
            loadDataAbsensi(currentPage);
        });
    </script>

    <?php include('../navbar/profileInfo.php') ?>

</body>

</html>