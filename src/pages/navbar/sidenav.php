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
    <link rel="stylesheet" href="../css/global/generalStyling.css">
    <link rel="stylesheet" href="../css/global/tableFormat.css">
    <link href="../css/font/poppins-font.css" rel="stylesheet">
</head>
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
    margin-left: 60px; /* Keep some margin for collapsed sidenav */
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

#sideNav.closed ul {
    bottom: 90px;
    transition: all 0.3s ease;
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
    transition: all 0.5 ease;
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
    width: 200px; 
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

#topNav {
    width: 100%;
    transition: width 0.5s ease;
}

#topNav.topNav-expanded {
    width: 100%;
    transition: width 0.5s ease;
}

#content {
    margin-left: 14rem; 
    transition: margin-left 0.5s ease;
    overflow-x: hidden;
}

/* Collapsed state */
#content.collapsed {
    display: inline-flex;
    margin-left: 0px; 
    transition: margin-left 0.5s ease;
}

#content.collapsed .mainContent {
    margin-left: 60px;
    transition: margin-left 0.5s ease;
    margin-top: 3.5rem;
}

menu, ol, ul {
    list-style: none;
    margin: 0;
    padding: 0;
    position: relative;
    bottom: 0px;
}

input[type="radio"]:checked + span {
    background-color: #8C85FF; /* Purple background when selected */
    border-color: white; /* Purple border when selected */
}
</style>


<body>
<?php if ($_SESSION['role'] == 'admin') { ?>
    <div id="sideNav" class="bg-white w-56 h-screen pt-3 transition duration-500 ease-linear <?php echo $sidebarClass; ?>" style="position: fixed;">
        <div class="flex flex-col items-center p-3">
            <div class="userIcon flex bg-gray-400 rounded-full h-24 w-24 mb-4 text-6xl text-white items-center justify-center">
                <img src="/teknoid-absensi/public/logo.png" class="align-middle fa-solid fa-user text-center"/>
            </div>
            <p class="text-lg font-semibold sideNav-text"><?php echo $username ?></p>
            <p class="text-green-500 sideNav-text">Online</p>
        </div>
        <nav class="p-3">
            <ul class="">
                <li class="mb-2">
                    <a href="../humas/dashboard.php" class="flex items-center p-2 text-purpleNavbar <?php echo $current_page == 'dashboard.php' ? 'bg-purpleNavbar text-white' : ''; ?> rounded-lg hover:bg-purpleNavbar transition">
                        <span class="sideNav-icon flex items-center justify-center w-8 h-8 border-none rounded-lg 
                            <?php echo $current_page == 'dashboard.php' ? 'text-white' : 'text-purpleNavbar'; ?>">
                            <i class="fa-solid fa-house"></i>
                        </span>
                        <span class="ml-2 font-medium sideNav-text">Dashboard</span>
                    </a>
                </li>
                <li class="mb-2">
                    <a href="./dataPegawai.php" class="flex items-center p-2 text-purpleNavbar <?php echo $current_page == 'dataPegawai.php' ? 'bg-purpleNavbar text-white' : ''; ?> rounded-lg hover:bg-purpleNavbar transition">
                        <span id="user-icon" class="sideNav-icon flex items-center justify-center w-8 h-8 border-none rounded-lg">
                            <i class="fa-solid fa-users-gear"></i>
                        </span>
                        <span class="ml-2 font-medium sideNav-text">Pegawai</span>
                    </a>
                </li>
                <li class="mb-2">
                    <a href="./dataAbsensi.php" class="flex items-center p-2 text-purpleNavbar <?php echo $current_page == 'dataAbsensi.php' ? 'bg-purpleNavbar text-white' : ''; ?> rounded-lg hover:bg-purpleNavbar transition">
                        <span id="rekap-icon" class="sideNav-icon flex items-center justify-center w-8 h-8 border-none rounded-lg">
                            <i class="fa-regular fa-calendar-days"></i>
                        </span>
                        <span class="ml-2 font-medium sideNav-text">Absensi</span>
                    </a>
                </li>
                <li class="mb-2">
                    <a href="./setDayOff.php" class="flex items-center p-2 text-purpleNavbar <?php echo $current_page == 'setDayOff.php' ? 'bg-purpleNavbar text-white' : ''; ?> rounded-lg hover:bg-purpleNavbar transition">
                        <span id="rekap-icon" class="sideNav-icon flex items-center justify-center w-8 h-8 border-none rounded-lg">
                        <i class="fa-regular fa-calendar-plus"></i>
                        </span>
                        <span class="ml-2 font-medium sideNav-text whitespace-nowrap">Atur Hari Libur</span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="copyright absolute w-full text-center items-center bottom-0 text-gray-500 pb-4">
            © Teknogenius 2024
        </div>
    </div>
<?php } elseif ($_SESSION['role'] == 'user') { ?>
    <div id="sideNav" class="bg-white w-56 h-screen pt-3 transition duration-500 ease-linear <?php echo $sidebarClass; ?>" style="position: fixed;">
        <div class="flex flex-col items-center p-3">
            <div class="userIcon flex bg-gray-400 rounded-full h-24 w-24 mb-4 text-6xl text-white items-center justify-center">
                <img src="/teknoid-absensi/public/logo.png" class="align-middle fa-solid fa-user text-center"/>
            </div>
            <p class="text-lg font-semibold sideNav-text"><?php echo $username ?></p>
            <p class="text-green-500 sideNav-text">Online</p>
        </div>
        <nav class="p-3">
            <ul class="">
                <li class="mb-2">
                    <a href="./dashboard.php" class="flex items-center p-2 text-purpleNavbar <?php echo $current_page == 'dashboard.php' ? 'bg-purpleNavbar text-white' : ''; ?> rounded-lg hover:bg-purpleNavbar transition">
                        <span class="sideNav-icon flex items-center justify-center w-8 h-8 border-none rounded-lg 
                            <?php echo $current_page == 'dashboard.php' ? 'text-white' : 'text-purpleNavbar'; ?>">
                            <i class="fa-solid fa-house"></i>
                        </span>
                        <span class="ml-2 font-medium sideNav-text">Dashboard</span>
                    </a>
                </li>
                <li class="mb-2">
                    <a href="./pengajuanCuti.php" class="flex items-center p-2 text-purpleNavbar <?php echo $current_page == 'pengajuanCuti.php' ? 'bg-purpleNavbar text-white' : ''; ?> rounded-lg hover:bg-purpleNavbar transition">
                        <span class="sideNav-icon flex items-center justify-center w-8 h-8 border-none rounded-lg 
                            <?php echo $current_page == 'dashboard.php' ? 'text-white' : 'text-purpleNavbar'; ?>">
                            <i class="fa-solid fa-house"></i>
                        </span>
                        <span class="ml-2 font-medium sideNav-text">Pengajuan Cuti</span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="copyright absolute w-full text-center items-center bottom-0 text-gray-500 pb-4">
            © Teknogenius 2024
        </div>
    </div>
<?php } ?>

</body>

<script>

// Function to toggle sidebar
function toggleSideNav() {
    const sideNav = document.getElementById('sideNav');
    const content = document.getElementById('content');
    const topNav = document.getElementById('topNav');

    // Toggle classes to control the state of sideNav and content
    const isClosed = sideNav.classList.toggle('closed');
    content.classList.toggle('collapsed');

    // Add or remove class to adjust topNav width
    topNav.classList.toggle('topNav-expanded', isClosed);

    // Use Fetch API to update session state
    fetch('update_sidebar_state.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'isClosed=' + (isClosed ? 'true' : 'false'),
    });
}

</script>

</html>
