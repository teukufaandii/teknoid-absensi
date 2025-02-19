<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['token'])) {
    header('Location: login.php');
    exit();
}

// Koneksi ke database
include 'src/db/db_connect.php';

// Ambil filter dari URL (harian, mingguan, bulanan) dan tanggal untuk mingguan dan bulanan
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'harian';
$start = isset($_GET['start']) ? $_GET['start'] : null;
$end = isset($_GET['end']) ? $_GET['end'] : null;
$filename = "data_absensi.csv";
$delimiter = ";";  // Use semicolon instead of comma for better compatibility with Excel in some regions

// Query berdasarkan filter
switch ($filter) {
    case 'harian':
        $query = "SELECT p.nama, p.noinduk, p.jabatan, d.tanggal, d.jam_kerja, d.scan_masuk, d.scan_keluar, d.durasi, d.keterangan 
                  FROM tb_pengguna p 
                  JOIN tb_detail d ON d.id_pg = p.id_pg 
                  WHERE DATE(d.tanggal) = CURDATE() 
                  ORDER BY p.noinduk ASC, d.tanggal ASC";  // Tambahkan ORDER BY
        $filename = "data_absensi_harian_" . date('Y-m-d') . ".csv";
        break;
    case 'mingguan':
        if ($start && $end) {
            $query = "SELECT p.nama, p.noinduk, p.jabatan, d.tanggal, d.jam_kerja, d.scan_masuk, d.scan_keluar, d.durasi, d.keterangan 
                      FROM tb_pengguna p 
                      JOIN tb_detail d ON d.id_pg = p.id_pg 
                      WHERE tanggal BETWEEN '$start' AND '$end' 
                      ORDER BY p.noinduk ASC, d.tanggal ASC";  // Tambahkan ORDER BY
            $filename = "data_absensi_mingguan_" . $start . "_to_" . $end . ".csv";
        } else {
            $query = "SELECT p.nama, p.noinduk, p.jabatan, d.tanggal, d.jam_kerja, d.scan_masuk, d.scan_keluar, d.durasi, d.keterangan 
                      FROM tb_pengguna p 
                      JOIN tb_detail d ON d.id_pg = p.id_pg 
                      WHERE YEARWEEK(tanggal, 1) = YEARWEEK(CURDATE(), 1) 
                      ORDER BY p.noinduk ASC, d.tanggal ASC";  // Tambahkan ORDER BY
            $filename = "data_absensi_mingguan_" . date('Y_W') . ".csv";
        }
        break;
    case 'bulanan':
        if ($start && $end) {
            $query = "SELECT p.nama, p.noinduk, p.jabatan, d.tanggal, d.jam_kerja, d.scan_masuk, d.scan_keluar, d.durasi, d.keterangan 
                      FROM tb_pengguna p 
                      JOIN tb_detail d ON d.id_pg = p.id_pg 
                      WHERE tanggal BETWEEN '$start' AND '$end' 
                      ORDER BY p.noinduk ASC, d.tanggal ASC";  // Tambahkan ORDER BY
            $filename = "data_absensi_bulanan_" . $start . "_to_" . $end . ".csv";
        } else {
            $query = "SELECT p.nama, p.noinduk, p.jabatan, d.tanggal, d.jam_kerja, d.scan_masuk, d.scan_keluar, d.durasi, d.keterangan 
                      FROM tb_pengguna p 
                      JOIN tb_detail d ON d.id_pg = p.id_pg 
                      WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE()) 
                      ORDER BY p.noinduk ASC, d.tanggal ASC";  // Tambahkan ORDER BY
            $filename = "data_absensi_bulanan_" . date('Y_m') . ".csv";
        }
        break;
    default:
        $query = "SELECT p.nama, p.noinduk, p.jabatan, d.tanggal, d.jam_kerja, d.scan_masuk, d.scan_keluar, d.durasi, d.keterangan 
                  FROM tb_pengguna p 
                  JOIN tb_detail d ON d.id_pg = p.id_pg 
                  WHERE DATE(tanggal) = CURDATE() 
                  ORDER BY p.noinduk ASC, d.tanggal ASC";  // Tambahkan ORDER BY
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
    $fields = ['No', 'Nama Pegawai', 'Nomor Induk', 'Jabatan', 'Tanggal', 'Jam Kerja', 'Scan Masuk', 'Scan Keluar', 'Durasi (Menit)', 'Keterangan'];
    fputcsv($f, $fields, $delimiter);

    // Counter untuk nomor urut
    $counter = 1;

    // Tulis baris data ke file CSV dengan nomor urut
    while ($row = mysqli_fetch_assoc($result)) {
        // Reorder the row array to place 'No' at the beginning
        $rowWithNo = [
            $counter,  // This will be the 'No' column
            $row['nama'],
            $row['noinduk'],
            $row['jabatan'],
            $row['tanggal'],
            $row['jam_kerja'],
            $row['scan_masuk'],
            $row['scan_keluar'],
            $row['durasi'],
            $row['keterangan'],
        ];

        // Tulis data ke CSV (setiap row terpisah dengan newline)
        fputcsv($f, $rowWithNo, $delimiter);

        // Increment counter
        $counter++;
    }

    // Tutup file
    fclose($f);
    exit();
} else {
    echo "Data tidak ditemukan untuk filter ini.";
}
?>