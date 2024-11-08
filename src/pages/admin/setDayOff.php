<?php
session_start();

if (!isset($_SESSION['token'])) {
    header('Location: login.php');
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../../unauthorized.php');
    session_destroy();
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
    <title>Set Libur</title>
    <link href="css/output.css" rel="stylesheet">
    <link rel="stylesheet" href="src/pages/css/dataAbsensi.css">
    <link href="src/pages/css/font/poppins-font.css" rel="stylesheet">
    <link href="src/pages/css/global/generalStyling.css" rel="stylesheet">
    <link href="src/pages/css/global/tableFormat.css" rel="stylesheet">
    <link rel="stylesheet" href="src/pages/admin/css/setDayOff.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="flex flex-col md:flex-row lg:flex-row h-screen">
        <?php include('src/pages/navbar/sidenav.php') ?>
        <div id="content" class="min-h-screen inline-flex flex-col flex-1 bg-mainBgColor ml-56">
            <?php include('src/pages/navbar/topnav.php') ?>
            <main class="flex-1 p-6 bg-mainBgColor mainContent">
                <h1 class="text-lg sm:text-xl md:text-3xl border-b border-gray-500 py-2 font-Poppins font-semibold">Input Hari Libur</h1>

                <div class="bg-white shadow-md rounded-lg p-6 mb-6 mt-6">
                    <div class="mb-4 bg-purpleNavbar px-4 py-3 rounded-lg">
                        <h3 class="text-xl text-white font-bold font-Poppins">Pengaturan Hari Libur</h3>
                    </div>
                    <form id="holiday-form">
                        <div class="flex gap-4">
                            <div class="flex flex-col w-full gap-4">
                                <div class="w-full flex flex-col md:flex-row gap-4">
                                    <div class="w-full">
                                        <label for="tanggal_mulai" class="block text-lg font-Poppins font-semibold text-gray-700">Tanggal Mulai</label>
                                        <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="mt-1 block w-full px-4 py-2 border-2 border-gray-200 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                                    </div>

                                    <div class="w-full">
                                        <label for="tanggal_akhir" class="block text-lg font-Poppins font-semibold text-gray-700">Tanggal Akhir</label>
                                        <input type="date" id="tanggal_akhir" name="tanggal_akhir" class="mt-1 block w-full px-4 py-2 border-2 border-gray-200 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                                    </div>
                                </div>

                                <div>
                                    <div class="w-full">
                                        <label for="nama_hari_libur" class="block text-lg font-Poppins font-semibold text-gray-700">Nama Hari Libur</label>
                                        <input type="text" id="nama_hari_libur" name="nama_hari_libur" class="mt-1 block w-full px-4 py-2 border-2 border-gray-200 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit" class="bg-purpleNavbar text-white px-6 py-2 rounded-lg hover:bg-purpleNavbarHover transition duration-200">Simpan</button>
                        </div>
                    </form>
                </div>

                <div class="tableOverflow mt-6 shadow-customTable rounded-lg">
                    <table class="bg-white border w-full">
                        <thead>
                            <tr class="bg-purpleNavbar text-white rounded-t-lg">
                                <th class="px-6 py-4 font-Poppins font-medium uppercase tracking-wider rounded-tl-lg">No</th>
                                <th class="px-6 py-4 font-Poppins font-medium uppercase tracking-wider">Nama Hari Libur</th>
                                <th class="px-6 py-4 font-Poppins font-medium uppercase tracking-wider">Tanggal Mulai</th>
                                <th class="px-6 py-4 font-Poppins font-medium uppercase tracking-wider">Tanggal Akhir</th>
                                <th class="px-6 py-4 font-Poppins font-medium uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="holiday-table-body" class="divide-y divide-gray-200"></tbody>
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
            let totalHolidays = 0;

            function loadHolidays(page) {
                $.ajax({
                    url: 'api/dayoff/get',
                    type: 'GET',
                    data: {
                        start: page * 5
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'unauthorized') {
                            window.location.href = 'unauthorized';
                            return;
                        }

                        totalHolidays = response.total;
                        let holidayTableBody = $('#holiday-table-body');
                        holidayTableBody.empty();

                        if (response.holidays.length === 0 && currentPage > 0) {
                            currentPage--;
                            loadHolidays(currentPage);
                        } else if (response.holidays.length === 0) {
                            holidayTableBody.append('<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>');
                        } else {
                            let counter = page * 5 + 1;
                            response.holidays.forEach(function(holiday) {
                                holidayTableBody.append(`
                            <tr class="bg-gray-100">
                                <td class="px-6 py-2 text-center">${counter++}</td>
                                <td class="px-6 py-2 text-center">${holiday.nama_libur}</td>
                                <td class="px-6 py-2 text-center">${holiday.tanggal_mulai ? holiday.tanggal_mulai.split('-').reverse().join('-') : '-'}</td>
                                <td class="px-6 py-2 text-center">${holiday.tanggal_akhir ? holiday.tanggal_akhir.split('-').reverse().join('-') : '-'}</td>
                                <td class="px-6 py-2 text-center">
                                    <a href="dayoff/edit?id=${holiday.id}">
                                        <button class="bg-purpleNavbar text-white px-3 py-2 rounded-xl hover:bg-purpleNavbarHover transition"><i class="fa-solid fa-pen-to-square"></i></button>
                                    </a>
                                    <a>
                                         <button class="delete-button bg-red-400 text-white px-3 py-2 rounded-xl hover:bg-red-500 transition" data-id="${holiday.id}"><i class="fa-solid fa-trash"></i></button>
                                    </a>
                                </td>
                            </tr>
                        `);
                            });
                        }

                        $('#prev-page').prop('disabled', currentPage === 0);
                        $('#next-page').prop('disabled', (currentPage + 1) * 5 >= totalHolidays);

                        updatePaginationButtons();
                    },
                    error: function() {
                        Swal.fire('Error!', 'Terjadi kesalahan saat memuat data', 'error');
                    }
                });
            }

            function updatePaginationButtons() {
                const totalPages = Math.ceil(totalHolidays / 5);
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

            $(document).on('click', '.delete-button', function() {
                const id = $(this).data('id');
                deleteHoliday(id);

                function deleteHoliday(id) {
                    Swal.fire({
                        title: 'Konfirmasi',
                        text: "Apakah Anda yakin ingin menghapus hari libur ini?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: 'api/dayoff/delete',
                                type: 'POST',
                                data: {
                                    id: id
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if (response.status === 'success') {
                                        Swal.fire('Berhasil!', response.message, 'success');
                                        loadHolidays(currentPage);
                                    } else {
                                        Swal.fire('Gagal!', response.message, 'error');
                                    }
                                },
                                error: function(jqXHR, textStatus, errorThrown) {
                                    Swal.fire('Error!', 'Terjadi kesalahan saat menghapus hari libur', 'error');
                                }
                            });
                        }
                    });
                }
            });

            $('#prev-page').on('click', function() {
                if (currentPage > 0) {
                    currentPage--;
                    loadHolidays(currentPage);
                }
            });

            $('#next-page').on('click', function() {
                if ((currentPage + 1) * 5 < totalHolidays) {
                    currentPage++;
                    loadHolidays(currentPage);
                }
            });

            $(document).on('click', '.pagination-button', function() {
                currentPage = parseInt($(this).data('page'));
                loadHolidays(currentPage);
                updatePaginationButtons();
            });

            $('#holiday-form').on('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Konfirmasi',
                    text: "Apakah Anda yakin ingin menambahkan hari libur ini?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, tambahkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = {
                            nama_hari_libur: $('#nama_hari_libur').val(),
                            tanggal_mulai: $('#tanggal_mulai').val(),
                            tanggal_akhir: $('#tanggal_akhir').val()
                        };

                        $.ajax({
                            url: 'api/dayoff/post',
                            type: 'POST',
                            data: formData,
                            dataType: 'json',
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire('Berhasil!', response.message, 'success');
                                    $('#holiday-form')[0].reset();
                                    loadHolidays(0);
                                } else {
                                    Swal.fire('Gagal!', response.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error!', 'Terjadi kesalahan saat menambahkan hari libur', 'error');
                            }
                        });
                    }
                });

                loadHolidays(currentPage);
                updatePaginationButtons();
            });

            loadHolidays(currentPage);


        });
    </script>
    <?php include('src/pages/navbar/profileInfo.php') ?>
</body>

</html>