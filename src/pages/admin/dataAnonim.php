<?php
session_start();

if (!isset($_SESSION['token'])) {
    header('Location: login');
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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Anonim</title>
    <link href="css/output.css" rel="stylesheet">
    <link href="src/pages/css/font/poppins-font.css" rel="stylesheet">
    <!-- ajax live search -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
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
                <h1 class="text-lg sm:text-xl md:text-3xl border-b border-gray-500 py-2 font-Poppins font-semibold">Data Anonim</h1>

                <!-- Search Bar & Button Tambah -->
                <div class="flex justify-end items-center mt-5">
                    <div class="relative">
                        <form method="GET">
                            <input
                                type="text"
                                id="searchInput"
                                placeholder="Search here..."
                                class="w-60 px-4 py-2 border rounded-xl shadow-md focus:outline-none focus:ring-2 focus:ring-purpleNavbar text-sm" />
                            <i class="fa fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </form>
                    </div>
                </div>

                <div class="tableOverflow mt-6 shadow-customTable rounded-lg">
                    <table class="min-w-full bg-white border" id="tableAnonim">
                        <thead>
                            <tr class="bg-purpleNavbar text-white rounded-t-lg">
                                <th class="px-6 py-4 font-medium uppercase tracking-wider rounded-tl-lg">No</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">Nomor Kartu</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">Jam</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-4 font-medium uppercase tracking-wider rounded-tr-lg">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="anonim-table-body" class="divide-y divide-gray-200">
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
    <?php include('src/pages/navbar/profileInfo.php') ?>
</body>



<script>
    $(document).ready(function() {
        let currentPage = 0;
        let totalDataAnonim = 0;
        let totalPages = 0;
        let searchTerm = '';

        // Fungsi untuk memuat data anonim
        function loadDataAnonim(page, search = '') {
            $.ajax({
                url: 'api/anonim/get',
                type: 'GET',
                data: {
                    start: page * 5,
                    search: search
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'unauthorized') {
                        window.location.href = 'unauthorized';
                        return;
                    }

                    totalDataAnonim = response.total; // Total data dari response
                    totalPages = Math.ceil(totalDataAnonim / 5); // Hitung total halaman
                    let DataAnonimTableBody = $('#anonim-table-body');
                    DataAnonimTableBody.empty();

                    if (response.data_anonim.length === 0 && currentPage > 0) {
                        currentPage--;
                        loadDataAnonim(currentPage, search);
                    } else if (response.data_anonim.length === 0) {
                        DataAnonimTableBody.append('<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>');
                    } else {
                        let counter = page * 5 + 1;
                        response.data_anonim.forEach(function(data_anonim) {
                            DataAnonimTableBody.append(`
                            <tr class="bg-gray-100">
                                <td class="px-6 py-2 text-center">${counter++}</td>
                                <td class="px-6 py-2 text-center" style="display: none;">${data_anonim.id}</td>
                                <td class="px-6 py-2 text-center">${data_anonim.nomor_kartu}</td>
                                <td class="px-6 py-2 text-center">${data_anonim.jam}</td>
                                <td class="px-6 py-2 text-center">${data_anonim.tanggal ? data_anonim.tanggal.split('-').reverse().join('-') : '-'} </td>
                                <td class="px-6 py-2 text-center">
                                    <button class="edit-button bg-purpleNavbar text-white px-3 py-2 rounded-xl hover:bg-purpleNavbarHover transition" data-nomor-kartu="${data_anonim.nomor_kartu}">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                    <button class="delete-button bg-red-400 text-white px-3 py-2 rounded-xl hover:bg-red-500 transition" data-id="${data_anonim.id}">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                        });
                    }

                    updatePaginationButtons();
                },
                error: function() {
                    $('#loading').addClass('hidden');
                    Swal.fire('Error!', 'Terjadi kesalahan saat memuat data', 'error');
                }
            });
        }

        // Fungsi untuk memperbarui tombol pagination
        function updatePaginationButtons() {
            const totalPages = Math.ceil(totalDataAnonim / 5);
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
            $('#next-page').prop('disabled', (currentPage + 1) * 5 >= totalDataAnonim);
        }

        // Event listener untuk previous pagination
        $('#prev-page').on('click', function() {
            if (currentPage > 0) {
                currentPage--;
                loadDataAnonim(currentPage, searchTerm);
            }
        });

        // Event listener untuk next pagination
        $('#next-page').on('click', function() {
            if ((currentPage + 1) * 5 < totalDataAnonim) {
                currentPage++;
                loadDataAnonim(currentPage, searchTerm);
            }
        });

        // Event listener untuk pagination button
        $(document).on('click', '.pagination-button', function() {
            currentPage = parseInt($(this).data('page'));
            loadDataAnonim(currentPage, searchTerm);
            updatePaginationButtons();
        });

        // Event listener untuk input pencarian
        $('#searchInput').on('keyup', function() {
            searchTerm = $(this).val();
            currentPage = 0; // Reset ke halaman pertama saat pencarian
            loadDataAnonim(currentPage, searchTerm);
        });

        // Load data pertama kali
        loadDataAnonim(currentPage, searchTerm);
    });


    //add user button

    $(document).on('click', '.edit-button', function() {
        const nomorKartu = $(this).data('nomor-kartu');
        window.location.href = `/teknoid-absensi/pegawai/add?nomor_kartu=${nomorKartu}`;
    });


    $(document).on('click', '.delete-button', function() {
        const id = $(this).data('id');
        deletedata_anonim(id);
    });


    function deletedata_anonim(id) {
        Swal.fire({
            title: 'Konfirmasi',
            text: "Apakah Anda yakin ingin menghapus data anonim ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'api/anonim/delete',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Berhasil!', response.message, 'success').then(() => {
                                setTimeout(() => {
                                    location.reload();
                                }, 100);
                            });
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data anonim', 'error'); // Error message
                    }
                });
            }
        });
    }

    //search function
    $(document).ready(function() {
        $("#searchInput").keyup(function() {
            var search = $(this).val();
            $.ajax({
                url: 'api/anonim/search',
                method: 'POST',
                data: {
                    query: search
                },
                success: function(response) {
                    $("#tableAnonim").html(response);
                }
            });
        });
    });
</script>

</html>