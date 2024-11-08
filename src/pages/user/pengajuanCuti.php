<?php
session_start();

// Cek jika user belum login
if (!isset($_SESSION['token'])) {
    header('Location: login');
    exit();
}

// Mengambil data dari session
$username = htmlspecialchars($_SESSION['name']);
$role = $_SESSION['role'];
$id_pg = $_SESSION['user_id']; // Pastikan bahwa 'user_id' di session mengacu pada 'id_pg'

// Koneksi ke database
$servername = "localhost";
$usernameDB = "root";
$passwordDB = "";
$dbname = "db_absensi";

// Buat koneksi
$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query untuk mengambil jatah cuti berdasarkan id_pg pengguna
$sql = "SELECT jatah_cuti FROM tb_pengguna WHERE id_pg = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $id_pg); // Bind parameter id_pg pengguna
    $stmt->execute();
    $stmt->bind_result($jatah_cuti);
    $stmt->fetch();

    $stmt->close();
} else {
    die("Terjadi kesalahan pada query SQL: " . $conn->error);
}

// Menutup koneksi setelah penggunaan
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Cuti</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="css/output.css" rel="stylesheet">
    <link href="src/pages/css/font/poppins-font.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body>
    <div class="flex flex-col md:flex-row lg:flex-row h-screen">
        <!-- Side Navigation -->
        <?php include('src/pages/navbar/sidenav.php') ?>

        <div id="content" class="min-h-screen inline-flex flex-col flex-1 bg-mainBgColor ml-56">
            <!-- Top Navigation -->
            <?php include('src/pages/navbar/topnav.php') ?>

            <!-- Pop Up -->
            <div id="modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden flex items-center justify-center">
                <div class="bg-white p-8 rounded-lg shadow-lg w-80">
                    <h3 class="text-center text-xl font-semibold mb-4">Sisa Cuti Anda: <?= isset($jatah_cuti) ? htmlspecialchars($jatah_cuti) : 'Tidak tersedia' ?> Hari</h3>
                    <p class="text-center mb-6">Ajukan surat cuti?</p>
                    <div class="flex justify-center gap-4">
                        <button id="closeModal" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg">Tidak</button>
                        <button id="ajukanCuti" class="bg-purpleNavbar hover:bg-purpleNavbarHover text-white font-bold py-2 px-4 rounded-lg">Ya</button>
                    </div>
                </div>
            </div>

            <div class="container mx-auto p-4">
                <h1 class="text-center text-2xl font-bold mb-4">Pengajuan Cuti</h1>
                <div class="flex justify-center">
                    <button id="openModal" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Buka Pengajuan Cuti
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Menampilkan modal saat tombol dibuka
        document.getElementById("openModal").addEventListener("click", function() {
            document.getElementById("modal").classList.remove("hidden");
        });

        // Menutup modal
        document.getElementById("closeModal").addEventListener("click", function() {
            document.getElementById("modal").classList.add("hidden");
        });

        // Arahkan ke halaman form pengajuan cuti saat tombol "Ya" ditekan
        document.getElementById("ajukanCuti").addEventListener("click", function() {
            window.location.href = "http://teknoid.itb-ad.ac.id/"; // URL yang diinginkan untuk pengajuan cuti
        });
    </script>

        <?php include('src/pages/navbar/profileInfo.php') ?>
</body>

</html>