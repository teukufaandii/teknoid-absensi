<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['token'])) {
    header('Location: login.php');
    exit();
}

// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "db_absensi");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil filter dari URL (harian, mingguan, bulanan)
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'hari';
$filename = "data_absensi.csv";
$delimiter = ",";

// Query berdasarkan filter
switch ($filter) {
    case 'hari':
        $query = "SELECT * FROM tb_absensi WHERE DATE(tanggal) = CURDATE()";
        $filename = "data_absensi_harian_" . date('Y-m-d') . ".csv";
        break;
    case 'minggu':
        $query = "SELECT * FROM tb_absensi WHERE YEARWEEK(tanggal, 1) = YEARWEEK(CURDATE(), 1)";
        $filename = "data_absensi_mingguan_" . date('Y_W') . ".csv";
        break;
    case 'bulan':
        $query = "SELECT * FROM tb_absensi WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())";
        $filename = "data_absensi_bulanan_" . date('Y_m') . ".csv";
        break;
    default:
        $query = "SELECT * FROM tb_absensi WHERE DATE(tanggal) = CURDATE()";
        $filename = "data_absensi_harian_" . date('Y-m-d') . ".csv";
        break;
}

// Eksekusi query
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    // Header untuk download file CSV
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '";');
    
    // Tulis kolom header ke file CSV
    $f = fopen('php://output', 'w');
    $fields = ['ID', 'Nama', 'Tanggal', 'Waktu Masuk', 'Waktu Keluar', 'Status'];
    fputcsv($f, $fields, $delimiter);
    
    // Tulis baris data ke file CSV
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($f, $row, $delimiter);
    }
    
    // Tutup file
    fclose($f);
    exit();
} else {
    echo "Data tidak ditemukan untuk filter ini.";
}
?>
