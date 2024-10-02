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

// Check if a record was found
if ($result->num_rows > 0) {
    // Fetch all the user data
    $user = $result->fetch_assoc();

    // Assign the fetched data to variables
    $username = htmlspecialchars($user['nama']);
    $email = htmlspecialchars($user['email']);
    $noinduk = htmlspecialchars($user['noinduk']);
    $role = htmlspecialchars($user['role']);
    // Add any other fields you may need
} else {
    // Handle case if no user found (redirect or display error)
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
            position: absolute;
            width: 200px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 50;
            display: none; /* Hidden initially */
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
        <div id="profileMenu" class="mt-1 mr-1 right-0 top-14 profile-menu">
            <div class="profile-header">
                <div class="align-middle justify-center profilePicContainer">
                    <img src="/teknoid-absensi/public/logo.png" alt="Profile Picture">
                </div>
                <div class="ml-4">
                    <p>Nama</p>
                    <p>NIDN</p>
                </div>
            </div>
            <div class="profile-info">
                <p>Jabatan: Lecturer</p>
                <p>Status: <span class="status">Aktif</span></p>
            </div>
            <a href="../pages/pengaturanAkun.php" class="menu-item"><i class="fa-solid fa-gear"></i> Pengaturan Akun</a>
            <a href="../db/routes/userLogout.php" class="text-red-400 menu-item"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a>
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
    <script>
        function toggleSideNav() {
            const sideNav = document.getElementById('sideNav');
            const content = document.getElementById('content');
            // Toggle a class that hides or shows the side navigation
            sideNav.classList.toggle('closed');
            content.classList.toggle('collapsed');
        }
    </script>

</html>