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
    <title>Pengajuan Cuti</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="../../../css/output.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/dataAbsensi.css">
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

            <!-- Pop Up -->
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
            <div class="bg-white p-8 rounded-lg shadow-lg w-80">
                <h3 class="text-center text-xl font-semibold mb-4">Sisa Cuti Anda 99999 Hari</h3>
                <p class="text-center mb-6">Ajukan surat cuti?</p>
                <div class="flex justify-center gap-4">
                    <button id="closeModal" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg">Tidak</button>
                    <button id="redirectToWebsite" class="bg-purpleNavbar hover:bg-purpleNavbarHover text-white font-bold py-2 px-4 rounded-lg">Ya</button>
                </div>
                </div>
            </div>
            </div>
        </div>

        <script>
            document.getElementById("closeModal").addEventListener("click", function() {
            //document.getElementById("modal").style.display = "none";
            window.location.href = "dashboard.php";
            });

            document.getElementById("redirectToWebsite").addEventListener("click", function() {
            window.location.href = "http://teknoid.itb-ad.ac.id/";
            });
        </script>

        <?php include('navbar/profileInfo.php') ?>

</body>

</html>