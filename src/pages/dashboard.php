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

<body class="flex flex-row h-screen">
    <!-- Side Navigation -->
    <?php include('navbar/sidenav.php') ?>

    <div class="inline-flex flex-col flex-1">
        <!-- Top Navigation -->
        <?php include('navbar/topnav.php') ?>

        <!-- Main Content -->
        <main class="flex-1 h-full p-6 bg-mainBgColor">
            <h1 class="text-3xl border-b py-2 font-Poppins font-semibold"> Dashboard </h1>
            <div class="grid grid-cols-4 gap-4 mt-4">
                <div class=" bg-gradient-to-r from-dashboardBoxPurple to-dashboardBoxBlue p-4 pb-10 rounded-lg shadow">
                    <h2 class="text-lg font-medium mb-2 border-b-2 pb-1 border-white text-white"> Pengguna </h2>
                    <p class="text-white">Konten Pengguna</p>
                </div>
                <div class=" bg-gradient-to-r from-dashboardBoxPurple to-dashboardBoxBlue p-4 pb-10 rounded-lg shadow">
                    <h2 class="text-lg font-medium mb-2 border-b-2 border-white text-white pb-1">Dashboard</h2>
                    <p class="text-white">Konten Dashboard</p>
                </div>
                <div class=" bg-gradient-to-r from-dashboardBoxPurple to-dashboardBoxBlue p-4 pb-10 rounded-lg shadow">
                    <h2 class="text-lg font-medium mb-2 border-b-2 border-white text-white pb-1">Dashboard</h2>
                    <p class="text-white">Konten Dashboard</p>
                </div>
                <div class=" bg-gradient-to-r from-dashboardBoxPurple to-dashboardBoxBlue p-4 pb-10 rounded-lg shadow">
                    <h2 class="text-lg font-medium mb-2 border-b-2 border-white text-white pb-1">Dashboard</h2>
                    <p class="text-white">Konten Dashboard</p>
                </div>
            </div>
        </main>
    </div>
</body>

</html>