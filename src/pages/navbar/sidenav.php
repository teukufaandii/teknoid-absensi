<?php
$username = htmlspecialchars($_SESSION['name']);
$role = $_SESSION['role'];
$id = $_SESSION['user_id'];
$token = $_SESSION['token'];

// Tentukan halaman aktif berdasarkan nama file
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../../../css/vanilla.css" rel="stylesheet">
    <link href="../../../css/output.css" rel="stylesheet">
</head>

<style>
    #sideNav.collapsed {
        width: 0px;
        padding: 0px;
        transition: all 0.5s ease-in-out;
    }
</style>

<body>
    <div id="sideNav" class="bg-white w-56 h-screen p-4 transition duration-500 ease-in-out">
        <div class="flex flex-col items-center">
            <div class="bg-gray-400 rounded-full h-24 w-24 mb-4"></div>
            <p class="text-lg font-semibold"><?php echo $username ?></p>
            <p class="text-green-500">Online</p>
        </div>
        <nav class="mt-8">
            <ul>
                <li class="mb-4">
                    <a href="dashboard.php" class="flex items-center p-2 text-purpleNavbar <?php echo $current_page == 'dashboard.php' ? 'bg-purpleNavbar text-white' : ''; ?> rounded-lg hover:bg-purpleNavbar">
                        <span class="flex items-center justify-center w-8 h-8 border-2 rounded-lg    
                            <?php echo $current_page == 'dashboard.php' ? 'text-white border-white' : 'text-purpleNavbar border-purpleNavbar'; ?>">
                            <i class="fa-solid fa-house"></i>
                        </span>
                        <span class="ml-2 font-medium">Dashboard</span>
                    </a>
                    <style>
                        .flex.items-center.p-2.text-purpleNavbar:hover .flex.items-center.justify-center.w-8.h-8.border-2.rounded-lg {
                            border-color: white !important;
                            color: white !important;
                        }
                        .flex.items-center.p-2.text-purpleNavbar:hover .fa-solid {
                            color: white !important;
                        }
                        .flex.items-center.p-2.text-purpleNavbar:hover .ml-2 {
                            color: white !important;
                        }
                    </style>
                </li>
                <li class="mb-4">
                    <a href="pengaturan_akun.php" class="flex items-center p-2 text-purpleNavbar <?php echo $current_page == 'pengaturan_akun.php' ? 'bg-purpleNavbar text-white' : ''; ?> rounded-lg hover:bg-purpleNavbar">
                        <span id="user-icon" class="flex items-center justify-center w-8 h-8 border-2 rounded-lg   
                            hover:text-white hover:bg-purpleNavbar hover:border-white 
                            <?php echo $current_page == 'pengaturan_akun.php' ? 'text-white border-white' : 'text-purpleNavbar border-purpleNavbar'; ?>">
                            <i class="fa-solid fa-gear"></i>
                        </span>
                        <span class="ml-2 font-medium">Pengguna</span>
                    </a>
                    <style>
                        .flex.items-center.p-2.text-purpleNavbar:hover .flex.items-center.justify-center.w-8.h-8.border-2.rounded-lg {
                            border-color: white !important;
                            color: white !important;
                        }
                        .flex.items-center.p-2.text-purpleNavbar:hover .fa-solid {
                            color: white !important;
                        }
                        .flex.items-center.p-2.text-purpleNavbar:hover .ml-2 {
                            color: white !important;
                        }
                    </style>
                </li>
                <li class="mb-4">
                    <a href="rekap_absensi.php" class="flex items-center p-2 text-purpleNavbar <?php echo $current_page == 'rekap_absensi.php' ? 'bg-purpleNavbar text-white' : ''; ?> rounded-lg hover:bg-purpleNavbar">
                        <span id="rekap-icon" class="flex items-center justify-center w-8 h-8 border-2 rounded-lg   
                            hover:text-white hover:bg-purpleNavbar hover:border-white 
                            <?php echo $current_page == 'rekap_absensi.php' ? 'text-white border-white' : 'text-purpleNavbar border-purpleNavbar'; ?>">
                            <i class="fa-solid fa-file-lines"></i>
                        </span>
                        <span class="ml-2 font-medium">Rekap Absensi</span>
                    </a>
                    <style>
                        .flex.items-center.p-2.text-purpleNavbar:hover .flex.items-center.justify-center.w-8.h-8.border-2.rounded-lg {
                            border-color: white !important;
                            color: white !important;
                        }
                        .flex.items-center.p-2.text-purpleNavbar:hover .fa-solid {
                            color: white !important;
                        }
                        .flex.items-center.p-2.text-purpleNavbar:hover .ml-2 {
                            color: white !important;
                        }
                    </style>
                </li>
            </ul>
        </nav>
        <div class="absolute items-center bottom-0 left-10 text-gray-500">
            © Teknogenius 2024
        </div>
    </div>
</body>

</html>
