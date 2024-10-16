<?php


?>
        <!-- Floating Menu -->
        <div id="profileMenu" class="profile-menu right-0 top-14 z-[999999]">
            <div class="profile-header flex p-2 text-center border-b">
                <div class="align-middle justify-center profilePicContainer">
                    <img src="/teknoid-absensi/public/gigadam.jpg" alt="Profile Picture" class="w-12 h-auto rounded-lg">
                </div>
                <div class="ml-3">
                    <p class="font-bold text-left"><?php echo $username; ?></p>
                    <p class="text-left"><?php echo $noinduk; ?></p>
                </div>
            </div>
            <div class="profile-info p-2 text-xs">
                <p>Jabatan: Lecturer</p>
                <p>Status: <span class="status inline-block px-2 py-1 bg-green-200 text-green-700 rounded text-xs">Aktif</span></p>
            </div>
            <a href="../pengaturanAkun.php" class="menu-item block text-center border-t hover:bg-gray-100 p-2 text-sm">
                <i class="fa-solid fa-gear"></i> Pengaturan Akun
            </a>
            <a href="../../db/routes/userLogout.php" class="text-red-400 menu-item block text-center border-t hover:bg-gray-100 rounded-b-md p-2 text-sm ">
                <i class="fa-solid fa-right-from-bracket"></i> Log Out
            </a>
        </div>
