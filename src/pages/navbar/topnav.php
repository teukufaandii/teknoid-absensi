<!DOCTYPE html>
<html lang="en">

<body>
<div id="topNav" class="relative flex justify-end items-center bg-gray-50 drop-shadow-md h-14 transition duration-500 ease-in-out topNav-expanded">
    <div class="flex items-center justify-center h-14">
        <div id="profileIcon" class="absolute right-3 w-9 h-9 flex cursor-pointer">
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
        <div id="floatingMenu" class="absolute mt-1 mr-1 right-0 top-14 w-48 bg-white rounded-lg shadow-xl p-2 z-50 hidden">
            <a href="#" class="block px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">Pengaturan Akun</a>
            <a href="../db/routes/userLogout.php" class="block px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">Log Out</a>
        </div>

    </div>
</div>

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