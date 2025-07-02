<?php
session_start();
include 'src/db/db_connect.php';

// Cek role pengguna (hanya admin yang boleh)
if ($_SESSION['role'] !== 'admin') {
    echo "<script>
            alert('Anda tidak memiliki akses.');
            window.location.href = '/dashboard';
          </script>";
    exit();
}

// Eksekusi query penghapusan
$query = "TRUNCATE TABLE tb_anonim";
if (mysqli_query($conn, $query)) {
    echo "<script>
            alert('Semua data berhasil dihapus.');
            window.location.href = '/teknoid-absensi/anonim';
          </script>";
} else {
    $error = mysqli_error($conn);
    echo "<script>
            alert('Gagal menghapus data: $error');
            window.location.href = '/teknoid-absensi/anonim';
          </script>";
}

$conn->close();
?>
