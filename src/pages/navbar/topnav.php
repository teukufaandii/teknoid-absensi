<?php
include '../db/db_connect.php';

if (!isset($_SESSION['token'])) {
    header('Location: login.php');
    exit();
}

$username = htmlspecialchars($_SESSION['name']);
$role = $_SESSION['role'];
$id = $_SESSION['user_id'];
$token = $_SESSION['token'];

$stmt = $conn->prepare("SELECT * FROM tb_pengguna WHERE id_pg = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    $username = htmlspecialchars($user['nama']);
    $email = htmlspecialchars($user['email']);
    $noinduk = htmlspecialchars($user['noinduk']);
    $role = htmlspecialchars($user['role']);
} else {
    header('Location: login.php?error=usernotfound');
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
    <style>
        /* Profile Menu */
        .profile-menu {
            width: 200px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: none; /* Hidden initially */
            position: absolute;
        }
        .profile-header {
            display: flex;
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        .profile-header img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }
        .profile-header p {
            margin: 5px 0;
            font-size: 14px;
            font-weight: bold;
        }
        .profile-info {
            padding: 10px;
        }
        .profile-info p {
            margin: 5px 0;
        }
        .status {
            display: inline-block;
            padding: 3px 8px;
            background-color: #d1fad1;
            color: #28a745;
            border-radius: 5px;
            font-size: 12px;
        }
        .menu-item {
            padding: 10px;
            text-decoration: none;
            display: block;
            border-top: 1px solid #ddd;
            text-align: center;
        }
        .menu-item:hover {
            background-color: #f0f0f0;
        }

        /* Button to trigger the menu */
        .menu-trigger {
            position: absolute;
            top: 14px;
            right: 10px;
            cursor: pointer;
        }
        .menu-trigger img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
        }
        #topnav {
            position: relative;
            z-index: 99999;
        }
    </style>
<body>
<div id="topNav" class="relative flex justify-end items-center bg-gray-50 drop-shadow-md h-14 transition duration-500 ease-in-out topNav-expanded">
    <div class="flex items-center justify-center h-14">
        <div id="profileIcon" class="absolute right-3 w-9 h-9 flex cursor-pointer" onclick="toggleMenu()">
            <div class="absolute shadow-lg bg-purpleNavbar w-9 h-9 rounded-full text-md text-white flex items-center justify-center cursor-pointer hover:bg-purpleNavbarHover transition">
                <i class="fa-solid fa-user"></i>
            </div>
        </div>

        <div id="sideIcon" class="absolute left-3 w-9 h-9 flex cursor-pointer"  onclick="toggleSideNav()">
            <div class="absolute shadow-lg bg-purpleNavbar w-9 h-9 rounded-xl text-lg text-white flex items-center justify-center cursor-pointer hover:bg-purpleNavbarHover transition">
                <i class="fa-solid fa-bars"></i>
            </div>
        </div>
        
        <!-- Floating Menu -->
        <div id="profileMenu" class="mt-1 mr-1 right-0 top-14 profile-menu absolute bg-white rounded-lg shadow-lg z-50 hidden">
            <div class="profile-header flex p-2 text-center border-b">
                <div class="align-middle justify-center profilePicContainer">
                    <img src="/teknoid-absensi/public/logo.png" alt="Profile Picture" class="w-12 h-12 rounded-full">
                </div>
                <div class="ml-4">
                    <p class="font-bold"><?php echo $username; ?></p>
                    <p><?php echo $noinduk; ?></p>
                </div>
            </div>
            <div class="profile-info p-2">
                <p>Jabatan: Lecturer</p>
                <p>Status: <span class="status inline-block px-2 py-1 bg-green-200 text-green-700 rounded text-xs">Aktif</span></p>
            </div>
            <a href="../pages/pengaturanAkun.php" class="menu-item block text-center border-t hover:bg-gray-100 p-2"><i class="fa-solid fa-gear"></i> Pengaturan Akun</a>
            <a href="../db/routes/userLogout.php" class="text-red-400 menu-item block text-center border-t hover:bg-gray-100 p-2"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a>
        </div>


    </div>
</div>
    <script>
        // Toggle the profile menu when the profileIcon is clicked
        function toggleMenu() {
            var menu = document.getElementById('profileMenu');
            if (menu.style.display === "block") {
                menu.style.display = "none";
            } else {
                menu.style.display = "block";
            }
        }

        // Optional: Close the menu if clicked outside
        window.onclick = function(event) {
            if (!event.target.closest('#profileIcon') && !event.target.closest('#profileMenu')) {
                var menu = document.getElementById('profileMenu');
                if (menu.style.display === "block") {
                    menu.style.display = "none";
                }
            }
        }
    </script>
    <script>
        function toggleSideNav() {
            const sideNav = document.getElementById('sideNav');
            const content = document.getElementById('content');
            const topNav = document.getElementById('topNav');

            // Toggle classes to control the state of sideNav and content
            sideNav.classList.toggle('closed');
            content.classList.toggle('collapsed');

            // Add or remove class to adjust topNav width
            topNav.classList.toggle('topNav-expanded', sideNav.classList.contains('closed'));

            // Save the current state in localStorage
            if (sideNav.classList.contains('closed')) {
                localStorage.setItem('sideNavState', 'closed');
            } else {
                localStorage.setItem('sideNavState', 'open');
            }
        }
    </script>

</html>