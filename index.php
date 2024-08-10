<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Teknoid ITB Ahmad Dahlan</title>
    <link href="css/output.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 flex flex-col items-center justify-center min-h-screen md:mx-0 mx-4">

    <div class="text-center">
        <h1 class="mt-5 text-4xl sm:text-4xl md:text-5xl font-semibold text-gray-800 mb-8">
            PORTAL TEKNOID ITB AHMAD DAHLAN
        </h1>


        <img src="/public/logo.png" alt="Logo ITB Ahmad Dahlan" class="mx-auto mb-6 w-36 h-36">

        <h2 class="text-xl font-medium text-gray-600 mb-8">Pilih Modul</h2>

        <!-- Parent container with padding applied on mobile -->
        <div class="flex flex-col md:flex-row justify-center items-center gap-6 w-full px-4 md:px-0">
            <a href="http://teknoid.itb-ad.ac.id/" class="flex flex-col items-center bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl px-8 py-6 shadow-lg transform hover:scale-105 transition-transform w-full">
                <i class="fas fa-envelope fa-2x mb-2"></i> <!-- Font Awesome Mail Icon -->
                <span>Surat Menyurat</span>
            </a>

            <a href="/src/pages/login.php" class="flex flex-col items-center bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl px-8 py-6 shadow-lg transform hover:scale-105 transition-transform w-full">
                <i class="fas fa-calendar fa-2x mb-2"></i> <!-- Font Awesome Calendar Icon -->
                <span>Absensi</span>
            </a>
        </div>

        <footer class="mt-12 text-gray-400 text-sm">
            &copy; 2024 TeknoGenius. All rights reserved.
        </footer>
    </div>

</body>

</html>