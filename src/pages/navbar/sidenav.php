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
/* Closed sidenav state */
.closed {
    width: 60px;
    transition: width 0.5s ease;
}

/* Sidebar open state */
#sideNav {
    transition: width 0.5s ease;
}

/* Collapse content */
.collapsed {
    margin-left: 70px; /* Keep some margin for collapsed sidenav */
    transition: all 0.5s ease;
}

/* Hide text when sidebar is closed */
.sideNav-text {
    opacity: 0;
    display: none;
    transition: opacity 0.3s ease, margin-left 0.3s ease;
}


/* Show text when sidebar is opened */
#sideNav:not(.closed) .sideNav-text {
    display: inline-block;
    opacity: 1;
    margin-left: 0;
    transition: opacity 0.3s ease, margin-left 0.3s ease;
}

.closed .copyright {
    opacity: 0;
    transition: opacity 0.3s ease;
}

/* Shrink icon when sidebar is closed */
.closed .sideNav-icon {
    width: 40px;
    height: 40px;
    transition: all 0.3s ease;
}

ul li a {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: start;
    width: 80px;
    max-width: 240px; 
    overflow: hidden;
    transition: width 0.5s ease; 
    width: 100%;
}

.closed li:hover a {
    width: 220px; 
    background-color: #8C85FF; 
    border-radius: 12px; 
    transition: width 0.5s ease, background-color 0.3s ease;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
}

.closed .userIcon{
    position: relative;
    top: 12px;
    opacity: 0;
    transition: all 0.3s ease;
}

/* Hover effect for icon */
li:hover .sideNav-icon {
    background-color: transparent;
    color: white;
    transition: all 0.3s ease;
}

li:hover .sideNav-text {
    opacity: 1;
    display: inline-block;
    color: white;
    margin-left: 10px;
    transition: opacity 0.3s ease, margin-left 0.3s ease;
}

li:hover .sideNav-icon,
li:hover .sideNav-text {
    display: flex;
    align-items: center;
}

.closed li:hover a {
    width: 240px; 
    background-color: #8C85FF;
    border-radius: 12px;
    transition: width 0.5s ease, background-color 0.3s ease;
}

.closed li:hover .sideNav-text {
    display: inline-block;
    opacity: 1;
    margin-left: 10px;
    transition: opacity 0.3s ease, margin-left 0.3s ease;
}

/* Default topNav width */
#topNav {
    width: calc(100% - 14rem);
    transition: width 0.5s ease;
}

#topNav.topNav-expanded {
    width: 100%;
    transition: width 0.5s ease;
}

#content {
    margin-left: 14rem; 
    transition: margin-left 0.5s ease;
}

/* Collapsed state */
#content.collapsed {
    margin-left: 0px; 
    transition: margin-left 0.5s ease;
}

#content.collapsed .mainContent {
    margin-left: 80px;
    transition: margin-left 0.5s ease;
}

</style>

<body>
    <div id="sideNav" class="bg-white w-56 h-screen p-3 transition duration-500 ease-linear" style="position: fixed;">
        <div class="flex flex-col items-center mt-4">
            <div class="userIcon flex bg-gray-400 rounded-full h-24 w-24 mb-4 text-6xl text-white items-center justify-center">
                <i class="align-middle fa-solid fa-user text-center"></i>
            </div>
            <p class="text-lg font-semibold sideNav-text"><?php echo $username ?></p>
            <p class="text-green-500 sideNav-text">Online</p>
        </div>
        <nav class="mt-8">
            <ul>
                <li class="mb-2">
                    <a href="dashboard.php" class="flex items-center p-2 text-purpleNavbar <?php echo $current_page == 'dashboard.php' ? 'bg-purpleNavbar text-white' : ''; ?> rounded-lg hover:bg-purpleNavbar transition">
                        <span class="sideNav-icon flex items-center justify-center w-8 h-8 border-2 rounded-lg border-none 
                            <?php echo $current_page == 'dashboard.php' ? 'text-white' : 'text-purpleNavbar'; ?>">
                            <i class="fa-solid fa-house"></i>
                        </span>
                        <span class="ml-2 font-medium sideNav-text">Dashboard</span>
                    </a>
                </li>
                <li class="mb-2">
                    <a href="dataPegawai.php" class="flex items-center p-2 text-purpleNavbar <?php echo $current_page == 'dataPegawai.php' ? 'bg-purpleNavbar text-white' : ''; ?> rounded-lg hover:bg-purpleNavbar transition">
                        <span id="user-icon" class="sideNav-icon flex items-center justify-center w-8 h-8 border-2 border-none rounded-lg">
                            <i class="fa-solid fa-users-gear"></i>
                        </span>
                        <span class="ml-2 font-medium sideNav-text">Pegawai</span>
                    </a>
                </li>
                <li class="mb-2">
                    <a href="dataAbsensi.php" class="flex items-center p-2 text-purpleNavbar <?php echo $current_page == 'dataAbsensi.php' ? 'bg-purpleNavbar text-white' : ''; ?> rounded-lg hover:bg-purpleNavbar transition">
                        <span id="rekap-icon" class="sideNav-icon flex items-center justify-center w-8 h-8 border-2 border-none rounded-lg">
                            <i class="fa-regular fa-calendar-days"></i>
                        </span>
                        <span class="ml-2 font-medium sideNav-text">Absensi</span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="copyright absolute items-center bottom-0 left-10 text-gray-500 pb-4">
            © Teknogenius 2024
        </div>
    </div>
</body>

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
}

</script>

</html>
