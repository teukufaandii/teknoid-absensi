<?php

?>
<!-- Floating Menu -->
<div id="profileMenu" class="profile-menu right-0 top-14 z-[999999]">
    <div class="profile-header flex p-2 text-center border-b">
        <div class="align-middle justify-center profilePicContainer">
            <img src="/teknoid-absensi/public/logo.png" alt="Profile Picture" class="w-12 h-auto rounded-lg">
        </div>
        <div class="ml-3">
            <p class="font-bold text-left"><?php echo $username; ?></p>
            <p class="text-left"><?php echo $noinduk; ?></p>
        </div>
    </div>
    <a href="/teknoid-absensi/setting" class="menu-item block text-center border-t hover:bg-gray-100 p-2 text-sm">
        <i class="fa-solid fa-gear"></i> Pengaturan Akun
    </a>
    <a href="/teknoid-absensi/api/auth/logout" class="text-red-400 menu-item block text-center border-t hover:bg-gray-100 rounded-b-md p-2 text-sm ">
        <i class="fa-solid fa-right-from-bracket"></i> Log Out
    </a>
</div>