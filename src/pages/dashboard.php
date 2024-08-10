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
    <title>Document</title>
    <link href="../../css/output.css" rel="stylesheet">
</head>

<body class="flex flex-col h-screen">
    <!-- Top Navigation -->
    <?php include('navbar/topnav.php') ?>

    <div class="flex flex-1">
        <!-- Side Navigation -->
        <?php include('navbar/sidenav.php') ?>

        <!-- Main Content -->
        <main class="flex-1 p-4 bg-gray-100">
            <div class="grid grid-cols-4 gap-4">
                <div class="bg-gray-200 p-4 rounded-lg shadow">
                    <h2 class="text-lg font-semibold mb-2">Pengguna</h2>
                    <p class="text-gray-600">Konten Pengguna</p>
                </div>
                <div class="bg-gray-200 p-4 rounded-lg shadow">
                    <h2 class="text-lg font-semibold mb-2">Dashboard</h2>
                    <p class="text-gray-600">Konten Dashboard</p>
                </div>
                <div class="bg-gray-200 p-4 rounded-lg shadow">
                    <h2 class="text-lg font-semibold mb-2">Dashboard</h2>
                    <p class="text-gray-600">Konten Dashboard</p>
                </div>
                <div class="bg-gray-200 p-4 rounded-lg shadow">
                    <h2 class="text-lg font-semibold mb-2">Dashboard</h2>
                    <p class="text-gray-600">Konten Dashboard</p>
                </div>
            </div>
            <!-- Logout Link -->
            <a href="../db/routes/userLogout.php" class="text-blue-500 hover:underline">Logout</a>
        </main>
    </div>
</body>

</html>