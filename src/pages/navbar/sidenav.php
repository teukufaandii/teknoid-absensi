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

<style>
    /* Hide the side navigation */
    .closed {
    transform: translateX(-100%); /* Moves it off screen */
    transition: transform 0.5s ease;
    }
    .collapsed {
        margin-left: 0px;
        transition: all 0.5s ease;
    }
    /* Add this to #sideNav to ensure it animates */
    #sideNav {
    transition: transform 0.5s ease;
    }
    input[type="radio"]:checked + span {
        background-color: #8C85FF; /* Purple background when selected */
        border-color: white; /* Purple border when selected */
    }
</style>

<body>
    <div id="sideNav" class="bg-white w-56 h-screen p-4 transition duration-500 ease-linear" style="position: fixed;">
        <div class="flex flex-col items-center mt-4">
            <div class="flex bg-gray-400 rounded-full h-24 w-24 mb-4 text-6xl text-white items-center justify-center">
                <i class="align-middle fa-solid fa-user text-center"></i>
            </div>
            <p class="text-lg font-semibold"><?php echo $username ?></p>
            <p class="text-green-500">Online</p>
        </div>
        <nav class="mt-8">
            <ul>
                <li class="mb-2">
                    <a href="dashboard.php" class="flex items-center p-2 text-purpleNavbar <?php echo $current_page == 'dashboard.php' ? 'bg-purpleNavbar text-white' : ''; ?> rounded-lg hover:bg-purpleNavbar transition">
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
                <li class="mb-2">
                    <a href="dataPegawai.php" class="flex items-center p-2 text-purpleNavbar <?php echo $current_page == 'dataPegawai.php' ? 'bg-purpleNavbar text-white' : ''; ?> rounded-lg hover:bg-purpleNavbar transition">
                        <span id="user-icon" class="flex items-center justify-center w-8 h-8 border-2 rounded-lg   
                            hover:text-white hover:bg-purpleNavbar hover:border-white 
                            <?php echo $current_page == 'dataPegawai.php' ? 'text-white border-white' : 'text-purpleNavbar border-purpleNavbar'; ?>">
                            <i class="fa-solid fa-users-gear"></i>
                        </span>
                        <span class="ml-2 font-medium">Pegawai</span>
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
                <li class="mb-2">
                    <a href="dataAbsensi.php" class="flex items-center p-2 text-purpleNavbar <?php echo $current_page == 'dataAbsensi.php' ? 'bg-purpleNavbar text-white' : ''; ?> rounded-lg hover:bg-purpleNavbar transition">
                        <span id="rekap-icon" class="flex items-center justify-center w-8 h-8 border-2 rounded-lg
                                hover:text-white hover:bg-purpleNavbar hover:border-white 
                            <?php echo $current_page == 'dataAbsensi.php' ? 'text-white border-white' : 'text-purpleNavbar border-purpleNavbar'; ?>">
                            <i class="fa-regular fa-calendar-days"></i>
                        </span>
                        <span class="ml-2 font-medium">Absensi</span>
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
        <div class="absolute items-center bottom-0 left-10 text-gray-500 pb-4">
            © Teknogenius 2024
        </div>
    </div>
</body>

</html>
