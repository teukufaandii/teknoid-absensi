<?php
ob_start();
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['token'])) {
    header('Location: login.php');
    exit();
}

// Koneksi ke database
include 'src/db/db_connect.php';

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'harian';
$jabatan = isset($_GET['jabatan']) ? $_GET['jabatan'] : 'dosenTetap';
$start = isset($_GET['start']) ? $_GET['start'] : null;
$end = isset($_GET['end']) ? $_GET['end'] : null;

// Header untuk file Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="data_absensi_dosen_' . date('Y-m-d') . '.xls";');

echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
echo '<head>';
echo '<meta http-equiv="Content-type" content="text/html;charset=utf-8" />';
echo '<style>';
echo 'table { border-collapse: collapse; width: 100%; }';
echo 'table, th, td { border: 1px solid black; padding: 5px; text-align: center; }';
echo 'th { background-color: #4CAF50; color: white; font-weight: bold; }';
echo '.header { font-size: 16pt; font-weight: bold; text-align: center; }';
echo '.subheader { font-size: 12pt; font-weight: bold; text-align: center; }';
echo '</style>';
echo '</head>';
echo '<body>';

// Menghitung jumlah hari dalam rentang tanggal yang dipilih
$period = new DatePeriod(new DateTime($start), new DateInterval('P1D'), new DateTime($end . ' +1 day'));
$total_days = iterator_count($period);
$colspan = $total_days + 3; // 3 kolom tambahan untuk No, Nama, Jabatan

// Judul laporan dengan colspan dinamis
echo '<table>';
echo '<tr><td colspan="' . $colspan . '" style="border: none;" class="header">HUMAN RESOURCE DEPARTMENT</td></tr>';
echo '<tr><td colspan="' . $colspan . '" style="border: none;" class="subheader">LAPORAN ABSENSI DOSEN</td></tr>';
echo '<tr><td colspan="' . $colspan . '" style="border: none;" class="subheader">ITB AHMAD DAHLAN JAKARTA</td></tr>';
echo '<tr><td colspan="' . $colspan . '" style="border: none;" class="subheader">Mulai Tanggal ' . $start . ' s/d ' . $end . '</td></tr>';
echo '<tr><td colspan="' . $colspan . '" style="border: none;"></td></tr>'; // Baris kosong untuk pemisah

// Header tabel
echo '<tr>';
echo '<th>No</th>';
echo '<th>Nama</th>';
echo '<th>Jabatan</th>';

// Menampilkan tanggal dalam format "01", "02", dst.
foreach ($period as $date) {
    echo '<th>' . $date->format('d') . '</th>';
}
echo '</tr>';

// Fungsi untuk mengambil dan menampilkan data berdasarkan jabatan
function fetch_and_display_data($conn, $jabatan, $total_days, $start, $end)
{
    echo '<tr><td colspan="' . ($total_days + 3) . '" class="subheader">' . $jabatan . '</td></tr>'; // Header jabatan

    // Query untuk mendapatkan data kehadiran
    $query = "
        SELECT 
            p.nama,
            GROUP_CONCAT(
                CASE 
                    WHEN d.keterangan = 'hadir' THEN '1' 
                    ELSE '0' 
                END ORDER BY d.tanggal SEPARATOR ','
            ) AS kehadiran
        FROM tb_detail d
        JOIN tb_pengguna p ON d.id_pg = p.id_pg
        WHERE d.tanggal BETWEEN '$start' AND '$end' 
        AND p.jabatan = '$jabatan'
        GROUP BY p.nama
        ORDER BY p.nama
    ";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $no = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $kehadiran = explode(',', $row['kehadiran']); // Ubah data kehadiran menjadi array
            echo '<tr>';
            echo '<td>' . $no++ . '</td>';
            echo '<td>' . $row['nama'] . '</td>';
            echo '<td>' . $jabatan . '</td>';

            foreach ($kehadiran as $status) {
                echo '<td>' . $status . '</td>';
            }

            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="' . ($total_days + 3) . '">Tidak ada data untuk ' . $jabatan . '</td></tr>';
    }
}

// Ambil dan tampilkan data untuk Dosen Tetap FEB
fetch_and_display_data($conn, "Dosen Tetap FEB", $total_days, $start, $end);

// Ambil dan tampilkan data untuk Dosen Tetap FTD
fetch_and_display_data($conn, "Dosen Tetap FTD", $total_days, $start, $end);

echo '</table>';
echo '</body>';
echo '</html>';

exit();
