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

$id_libur = $_GET['id'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Libur</title>
    <link href="../../../css/output.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/dataAbsensi.css">
    <link href="../css/font/poppins-font.css" rel="stylesheet">
    <link href="../css/global/generalStyling.css" rel="stylesheet">
    <link href="../css/global/tableFormat.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/setDayOff.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="flex flex-col md:flex-row lg:flex-row h-screen">
        <?php include('../navbar/sidenav.php') ?>
        <div id="content" class="min-h-screen inline-flex flex-col flex-1 bg-mainBgColor ml-56">
            <?php include('../navbar/topnav.php') ?>
            <main class="flex-1 p-6 bg-mainBgColor mainContent">
                <div class="flex justify-between items-center border-b border-gray-500">
                    <h1 class="text-lg sm:text-xl md:text-3xl py-2 font-Poppins font-semibold">Edit Hari Libur</h1>
                    <a href="setDayOff.php">
                        <button class="bg-purpleNavbar text-white px-4 py-2 rounded-lg hover:bg-purpleNavbarHover transition duration-200">Kembali</button>
                    </a>
                </div>

                <div class="bg-white shadow-md rounded-lg p-6 mb-6 mt-6">
                    <form id="holiday-form">
                        <div class="flex gap-4">
                            <div class="flex flex-col w-full gap-4">
                                <div class="w-full flex flex-col md:flex-row gap-4">
                                    <div class="w-full">
                                        <label for="tanggal_mulai" class="block text-lg font-Poppins font-semibold text-gray-700">Tanggal Mulai</label>
                                        <input type="date" name="tanggal_mulai" class="mt-1 block w-full px-4 py-2 border-2 border-gray-200 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                                    </div>

                                    <div class="w-full">
                                        <label for="tanggal_akhir" class="block text-lg font-Poppins font-semibold text-gray-700">Tanggal Akhir</label>
                                        <input type="date" name="tanggal_akhir" class="mt-1 block w-full px-4 py-2 border-2 border-gray-200 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                                    </div>
                                </div>

                                <div>
                                    <div class="w-full">
                                        <label for="nama_hari_libur" class="block text-lg font-Poppins font-semibold text-gray-700">Nama Hari Libur</label>
                                        <input type="text" name="nama_hari_libur" class="mt-1 block w-full px-4 py-2 border-2 border-gray-200 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit" class="bg-purpleNavbar text-white px-6 py-2 rounded-lg hover:bg-purpleNavbarHover transition duration-200">Simpan</button>
                        </div>
                    </form>
                </div>

            </main>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            const id_libur = <?php echo json_encode($id_libur); ?>;

            $.ajax({
                url: '../../db/routes/get_current_dayoff.php',
                type: 'GET',
                data: {
                    id: id_libur
                },
                success: function(response) {
                    if (response.error) {
                        Swal.fire('Error', response.error, 'error');
                    } else {
                        $('input[name="tanggal_mulai"]').val(response.tanggal_mulai);
                        $('input[name="tanggal_akhir"]').val(response.tanggal_akhir);
                        $('input[name="nama_hari_libur"]').val(response.nama_hari_libur);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    Swal.fire('Error', 'Terjadi kesalahan saat memuat data.', 'error');
                }
            });

            $('#holiday-form').submit(function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Yakin ingin menyimpan perubahan?',
                    text: "Perubahan yang Anda buat akan disimpan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#675EFF',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, simpan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const updatedData = {
                            id_libur: id_libur,
                            tanggal_mulai: $('input[name="tanggal_mulai"]').val(),
                            tanggal_akhir: $('input[name="tanggal_akhir"]').val(),
                            nama_hari_libur: $('input[name="nama_hari_libur"]').val(),
                        };

                        $.ajax({
                            url: '../../db/routes/update_dayoff.php',
                            type: 'POST',
                            data: updatedData,
                            success: function(response) {
                                if (response.error) {
                                    Swal.fire('Error', response.error, 'error');
                                } else {
                                    Swal.fire('Berhasil!', 'Data Anda berhasil diperbarui.', 'success').then(() => {
                                        window.location.href = './editDayOff.php?id=' + id_libur;
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error(error);
                                Swal.fire('Error', 'Terjadi kesalahan saat memperbarui data.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>

    <?php include('../navbar/profileInfo.php') ?>
</body>

</html>