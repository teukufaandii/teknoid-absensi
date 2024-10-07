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

// Ambil filter dari URL (harian, mingguan, bulanan) dan tanggal untuk mingguan dan bulanan
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'harian';
$start = isset($_GET['start']) ? $_GET['start'] : null;
$end = isset($_GET['end']) ? $_GET['end'] : null;
$filename = "data_absensi.csv";
$delimiter = ",";

// Query berdasarkan filter
switch ($filter) {
    case 'harian':
        $query = "SELECT * FROM tb_detail WHERE DATE(tanggal) = CURDATE()";
        $filename = "data_absensi_harian_" . date('Y-m-d') . ".csv";
        break;
    case 'mingguan':
        // Cek apakah ada rentang tanggal
        if ($start && $end) {
            $query = "SELECT * FROM tb_detail WHERE tanggal BETWEEN '$start' AND '$end'";
            $filename = "data_absensi_mingguan_" . $start . "_to_" . $end . ".csv";
        } else {
            // Default ke minggu ini
            $query = "SELECT * FROM tb_detail WHERE YEARWEEK(tanggal, 1) = YEARWEEK(CURDATE(), 1)";
            $filename = "data_absensi_mingguan_" . date('Y_W') . ".csv";
        }
        break;
    case 'bulanan':
        // Cek apakah ada rentang tanggal
        if ($start && $end) {
            $query = "SELECT * FROM tb_detail WHERE tanggal BETWEEN '$start' AND '$end'";
            $filename = "data_absensi_bulanan_" . $start . "_to_" . $end . ".csv";
        } else {
            // Default ke bulan ini
            $query = "SELECT * FROM tb_detail WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())";
            $filename = "data_absensi_bulanan_" . date('Y_m') . ".csv";
        }
        break;
    default:
        $query = "SELECT * FROM tb_detail WHERE DATE(tanggal) = CURDATE()";
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
    $fields = ['ID', 'Nomor Pegawai', 'Tanggal', 'Jam Kerja', 'Scan Masuk', 'Scan Keluar', 'Durasi (Menit)', 'Keterangan', 'Aksi'];
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
