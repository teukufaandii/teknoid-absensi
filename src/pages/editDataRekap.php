<?php
session_start();

if (!isset($_SESSION['token'])) {
    header('Location: login.php');
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
    <title>Dashboard</title>
    <link href="../../css/output.css" rel="stylesheet">
    <link href="../../css/font/poppins-font.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body>
    <div class="flex flex-row h-screen">
        <!-- Side Navigation -->
        <?php include('navbar/sidenav.php') ?>

        <div class="min-h-screen inline-flex flex-col flex-1 bg-mainBgColor ml-56">
            <!-- Top Navigation -->
            <?php include('navbar/topnav.php') ?>

            <!-- Main Content -->
            <main class="inline-flex flex-1 p-6 bg-mainBgColor">
                <h1 class="text-3xl border-b py-2 font-Poppins font-semibold">Edit Data Rekap</h1>
                <div class="max-w-lg mx-auto p-6">
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Tanggal</label>
                    <input
                    type="date"
                    class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:border-indigo-500"
                    value="04-06-2024"
                    />
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Jabatan</label>
                    <input
                    type="text"
                    placeholder="Masukkan Jabatan"
                    class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:border-indigo-500"
                    value="Dosen Tetap FTD"
                    />
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Keterangan</label>
                    <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                        <input
                        type="radio"
                        name="status"
                        value="Sakit"
                        class="text-indigo-500"
                        checked
                        />
                        <span class="ml-2 mr-4 text-gray-700">Sakit</span>
                    </label>
                    <label class="flex items-center">
                        <input
                        type="radio"
                        name="status"
                        value="Izin"
                        class="text-indigo-500"
                        />
                        <span class="ml-2 mr-4 text-gray-700">Izin</span>
                    </label>
                    <label class="flex items-center">
                        <input
                        type="radio"
                        name="status"
                        value="Cuti"
                        class="text-indigo-500"
                        />
                        <span class="ml-2 mr-4 text-gray-700">Cuti</span>
                    </label>
                    <label class="flex items-center">
                        <input
                        type="radio"
                        name="status"
                        value="Alpha"
                        class="text-indigo-500"
                        />
                        <span class="ml-2 mr-4 text-gray-700">Alpha</span>
                    </label>
                    </div>
                </div>

                <div class="flex justify-between mt-6">
                    <button class="bg-red-400 text-white px-6 py-2 rounded-lg hover:bg-red-600 transition duration-200">Hapus</button>
                    <button class="bg-purpleNavbar text-white px-6 py-2 rounded-lg hover:bg-indigo-600 transition duration-200">Simpan</button>
                </div>
                </div>
            </main>
        </div>
    </div>
</body>

</html>