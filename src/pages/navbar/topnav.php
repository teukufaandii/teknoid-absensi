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
$stmt->bind_param("s", $id);
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
            z-index: 1000; /* Higher z-index for the profile menu */
            display: none; /* Hidden initially */
            position: absolute; /* Positioning is required for z-index to work */
            margin: 4px;
        }
        .profile-header {
            display: flex;
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
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
<div id="topNav" class="relative flex justify-end items-center bg-gray-50 drop-shadow-md h-14 transition duration-500 ease-in-out">
    <div class="flex items-center justify-center h-14">
        <div id="profileIcon" class="absolute right-3 w-9 h-9 flex cursor-pointer" onclick="toggleMenu()">
            <div class="absolute shadow-lg bg-purpleNavbar w-9 h-9 rounded-full text-md text-white flex items-center justify-center cursor-pointer hover:bg-purpleNavbarHover transition">
                <i class="fa-solid fa-user"></i>
            </div>
        </div>

        <div id="sideIcon" class="absolute left-3 w-9 h-9 flex cursor-pointer"  onclick="toggleSideNav(), toggleTopNav()">
            <div class="absolute shadow-lg bg-purpleNavbar w-9 h-9 rounded-xl text-lg text-white flex items-center justify-center cursor-pointer hover:bg-purpleNavbarHover transition">
                <i class="fa-solid fa-bars"></i>
            </div>
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
        // Add an event listener to the window object
        window.addEventListener('resize', handleResize);

        // Define the handleResize function
        function handleResize() {
            // Get the current screen width
            const screenWidth = window.innerWidth;

            // Check if the screen width is less than 768px
            if (screenWidth < 768) {
                // Toggle the sideNav and topNav functions
                toggleSideNav();
                toggleTopNav();
            }
        }
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
        function toggleTopNav() {
            var topNav = document.getElementById('topNav');
            if (topNav.style.position === "absolute") {
                topNav.style.position = "relative";
            } else {
                topNav.style.position = "absolute";
            }
        }
    </script>

</html>