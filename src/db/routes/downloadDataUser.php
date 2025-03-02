<?php
ob_start(); // Start output buffering
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
$id_pg  = isset($_GET['id_pg']) ? $_GET['id_pg'] : null;

$date_info = $start && $end ? "Mulai Tanggal $start s/d $end" : "";

// Pastikan id_pg tersedia
if (!$id_pg) {
    die("Parameter id_pg tidak ditemukan.");
}

// Header untuk file Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Absensi_" . date("Y-m-d") . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Buka output HTML untuk Excel
echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" 
             xmlns:x="urn:schemas-microsoft-com:office:excel" 
             xmlns="http://www.w3.org/TR/REC-html40">';
echo '<head>';
echo '<meta http-equiv="Content-type" content="text/html;charset=utf-8" />';
echo '<style>';
echo 'table { border-collapse: collapse; width: 100%; }';
echo 'th, td { border: 1px solid black; padding: 5px; text-align: center; }';
echo 'th { background-color: #4CAF50; color: white; font-weight: bold; }';
echo '.header { font-size: 16pt; font-weight: bold; text-align: center; }';
echo '.subheader { font-size: 12pt; font-weight: bold; text-align: center; }';
echo '</style>';
echo '</head>';
echo '<body>';

// Header laporan
echo '<table>';
echo '<tr><td colspan="7" class="header">HUMAN RESOURCE DEPARTMENT</td></tr>';
echo '<tr><td colspan="7" class="subheader">LAPORAN ABSENSI DETAIL USER</td></tr>';
echo '<tr><td colspan="7" class="subheader">ITB AHMAD DAHLAN JAKARTA</td></tr>';
if (!empty($date_info)) {
    echo '<tr><td colspan="7" class="subheader">Periode: ' . $date_info . '</td></tr>';
}
echo '<tr><td colspan="7"></td></tr>'; // Baris kosong sebagai pemisah

// Header tabel
echo '<tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Jam Kerja</th>
        <th>Scan Masuk</th>
        <th>Scan Keluar</th>
        <th>Durasi (Menit)</th>
        <th>Keterangan</th>
      </tr>';

// Query berdasarkan filter
switch ($filter) {
    case 'harian':
        $query = "SELECT tanggal, MIN(jam_kerja) AS jam_kerja, MIN(scan_masuk) AS scan_masuk, 
                  MAX(scan_keluar) AS scan_keluar, SUM(durasi) AS durasi, keterangan
                  FROM tb_detail 
                  WHERE DATE(tanggal) = CURDATE() AND id_pg = '$id_pg'
                  GROUP BY tanggal, keterangan
                  ORDER BY tanggal ASC";
        break;

    case 'mingguan':
        if ($start && $end) {
            $query = "SELECT tanggal, MIN(jam_kerja) AS jam_kerja, MIN(scan_masuk) AS scan_masuk, 
                      MAX(scan_keluar) AS scan_keluar, SUM(durasi) AS durasi, keterangan
                      FROM tb_detail 
                      WHERE tanggal BETWEEN '$start' AND '$end' AND id_pg = '$id_pg'
                      GROUP BY tanggal, keterangan
                      ORDER BY tanggal ASC";
        } else {
            $query = "SELECT tanggal, MIN(jam_kerja) AS jam_kerja, MIN(scan_masuk) AS scan_masuk, 
                      MAX(scan_keluar) AS scan_keluar, SUM(durasi) AS durasi, keterangan
                      FROM tb_detail 
                      WHERE YEARWEEK(tanggal, 1) = YEARWEEK(CURDATE(), 1) AND id_pg = '$id_pg'
                      GROUP BY tanggal, keterangan
                      ORDER BY tanggal ASC";
        }
        break;

    case 'bulanan':
        if ($start && $end) {
            $query = "SELECT tanggal, MIN(jam_kerja) AS jam_kerja, MIN(scan_masuk) AS scan_masuk, 
                      MAX(scan_keluar) AS scan_keluar, SUM(durasi) AS durasi, keterangan
                      FROM tb_detail 
                      WHERE tanggal BETWEEN '$start' AND '$end' AND id_pg = '$id_pg'
                      GROUP BY tanggal, keterangan
                      ORDER BY tanggal ASC";
        } else {
            $query = "SELECT tanggal, MIN(jam_kerja) AS jam_kerja, MIN(scan_masuk) AS scan_masuk, 
                      MAX(scan_keluar) AS scan_keluar, SUM(durasi) AS durasi, keterangan
                      FROM tb_detail 
                      WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE()) AND id_pg = '$id_pg'
                      GROUP BY tanggal, keterangan
                      ORDER BY tanggal ASC";
        }
        break;

    default:
        die("Filter tidak valid.");
}


// Eksekusi query
$result = mysqli_query($conn, $query);

// Tulis data ke dalam tabel
if ($result && mysqli_num_rows($result) > 0) {
    $no = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>
                <td>' . $no++ . '</td>
                <td>' . $row['tanggal'] . '</td>
                <td>' . $row['jam_kerja'] . '</td>
                <td>' . $row['scan_masuk'] . '</td>
                <td>' . $row['scan_keluar'] . '</td>
                <td>' . $row['durasi'] . '</td>
                <td>' . $row['keterangan'] . '</td>
              </tr>';
    }
} else {
    echo '<tr><td colspan="7">Data tidak ditemukan untuk filter ini.</td></tr>';
}

echo '</table>';
echo '</body>';
echo '</html>';

exit();
