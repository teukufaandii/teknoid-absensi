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
    <title>Edit Rekap</title>
    <link href="../../../css/output.css" rel="stylesheet">
    <link href="../css/font/poppins-font.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
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
            <h1 class="text-lg sm:text-xl md:text-3xl border-b border-gray-500 py-2 font-Poppins font-semibold">Edit Data Absensi</h1>
                <div class="max-w-full mx-auto py-6">
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Tanggal</label>
                    <input
                    type="date"
                    class="w-full border-2 border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar"
                    value=""
                    />
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Jabatan</label>
                    <input
                    type="text"
                    placeholder="Masukkan Jabatan"
                    class="w-full border-2 border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:border-purpleNavbar"
                    value=""
                    />
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Keterangan</label>
                    <div class="flex items-center justify-between space-x-8 sm:justify-start">
                        <label class="flex items-center text-gray-600">
                        <input
                            type="radio"
                            name="status"
                            value="Sakit"
                            class="hidden"
                            checked
                        />
                        <span class="w-3.5 h-3.5 inline-block mr-2 border-2 border-purpleNavbar rounded-full flex-shrink-0 transition"></span>
                        Sakit
                        </label>
                        <label class="flex items-center text-gray-600">
                        <input
                            type="radio"
                            name="status"
                            value="Izin"
                            class="hidden"
                        />
                        <span class="w-3.5 h-3.5 inline-block mr-2 border-2 border-purpleNavbar rounded-full flex-shrink-0 transition"></span>
                        Izin
                        </label>
                        <label class="flex items-center text-gray-600">
                        <input
                            type="radio"
                            name="status"
                            value="Cuti"
                            class="hidden"
                        />
                        <span class="w-3.5 h-3.5 inline-block mr-2 border-2 border-purpleNavbar rounded-full flex-shrink-0 transition"></span>
                        Cuti
                        </label>
                        <label class="flex items-center text-gray-600">
                        <input
                            type="radio"
                            name="status"
                            value="Alpha"
                            class="hidden"
                        />
                        <span class="w-3.5 h-3.5 inline-block mr-2 border-2 border-purpleNavbar rounded-full flex-shrink-0 transition"></span>
                        Alpha
                        </label>
                    </div>
                </div>

                <div class="flex justify-between mt-6">
                    <button class="bg-red-400 text-white px-6 py-2 rounded-lg hover:bg-red-500 transition duration-200">Hapus</button>
                    <button class="bg-purpleNavbar text-white px-6 py-2 rounded-lg hover:bg-purpleNavbarHover transition duration-200">Simpan</button>
                </div>
                </div>
            </main>
        </div>
    </div>
    
    <?php include('../navbar/profileInfo.php') ?>
</body>


</html>