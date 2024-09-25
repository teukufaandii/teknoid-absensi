<!DOCTYPE html>
<html lang="en">

<body>
<div id="topNav" class="block justify-end items-center bg-gray-50 drop-shadow-md h-14 transition duration-500 ease-in-out">
    <div class="flex items-center justify-center h-14">
        <div id="profileIcon" class="absolute right-3 w-9 h-9 flex cursor-pointer ">
            <div class="absolute shadow-lg bg-purpleNavbar w-9 h-9 rounded-full text-md text-white flex items-center justify-center cursor-pointer">
                <i class="fa-solid fa-user"></i>
            </div>
        </div>

        <div id="sideIcon" class="absolute left-3 w-9 h-9 flex cursor-pointer ">
            <div class="absolute shadow-lg bg-purpleNavbar w-9 h-9 rounded-xl text-lg text-white flex items-center justify-center cursor-pointer">
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
        const profileIcon = document.getElementById('profileIcon');
        const floatingMenu = document.getElementById('floatingMenu');

        // Toggle the visibility of the floating menu when the profile icon is clicked
        profileIcon.addEventListener('click', () => {
            floatingMenu.classList.toggle('hidden');
        });

        // Optional: Close the floating menu when clicking outside of it
        document.addEventListener('click', (event) => {
            if (!profileIcon.contains(event.target) && !floatingMenu.contains(event.target)) {
                floatingMenu.classList.add('hidden');
            }
        });
    </script>
    <script>
        // Get the elements
        const sideIcon = document.getElementById('sideIcon');
        const sideNav = document.getElementById('sideNav');

        // Add click event listener to toggle class
        sideIcon.addEventListener('click', function() {
            sideNav.classList.toggle('collapsed');
        });
    </script>

</html>