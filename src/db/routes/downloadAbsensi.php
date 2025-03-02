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

// Ambil filter dari URL (harian, mingguan, bulanan) dan tanggal untuk mingguan dan bulanan
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'harian';
$jabatan = isset($_GET['jabatan']) ? $_GET['jabatan'] : 'Karyawan';
$start = isset($_GET['start']) ? $_GET['start'] : null;
$end = isset($_GET['end']) ? $_GET['end'] : null;
$delimiter = ",";

$date_info = "";
if ($start && $end) {
    $date_info = "Mulai Tanggal $start s/d $end";
}

// Query berdasarkan filter
switch ($filter) {
    case 'harian':
        $query = "SELECT 
            p.noinduk, 
            p.nama,
            COALESCE(SUM(CASE WHEN d.keterangan = 'sakit' THEN 1 ELSE 0 END), 0) AS sakit,
            COALESCE(SUM(CASE WHEN d.keterangan = 'izin' THEN 1 ELSE 0 END), 0) AS izin,
            COALESCE(SUM(CASE WHEN d.keterangan = 'alpha' THEN 1 ELSE 0 END), 0) AS alpha,
            COALESCE(SUM(CASE WHEN d.keterangan = 'cuti' THEN 1 ELSE 0 END), 0) AS cuti,
            COALESCE(SUM(CASE WHEN d.keterangan = 'hadir' THEN 1 ELSE 0 END), 0) AS hadir,
            
            -- Tepat Waktu
            COALESCE(SUM(CASE 
                WHEN p.jabatan = 'Customer Service' AND d.scan_masuk <= '07:00:00' THEN 1 
                WHEN p.jabatan <> 'Customer Service' AND d.scan_masuk <= '08:00:00' THEN 1 
                ELSE 0 
            END), 0) AS tepat_datang,
            COALESCE(SUM(CASE 
                WHEN d.scan_keluar >= '16:00:00' THEN 1 
                ELSE 0 
            END), 0) AS tepat_pulang,
            COALESCE(SUM(CASE 
                WHEN p.jabatan = 'Customer Service' AND d.scan_masuk <= '07:00:00' THEN 1 
                WHEN p.jabatan <> 'Customer Service' AND d.scan_masuk <= '08:00:00' THEN 1 
                ELSE 0 
            END) + SUM(CASE WHEN d.scan_keluar >= '16:00:00' THEN 1 ELSE 0 END), 0) AS tepat_jumlah,

            -- Tidak Tepat Waktu
            COALESCE(SUM(CASE 
                WHEN (p.jabatan = 'Customer Service' AND (d.scan_masuk BETWEEN '07:00:01' AND '12:00:00' OR d.scan_masuk IS NULL)) 
                    OR (p.jabatan <> 'Customer Service' AND (d.scan_masuk BETWEEN '08:00:01' AND '12:00:00' OR d.scan_masuk IS NULL)) 
                THEN 1 ELSE 0 
            END), 0) AS tidak_absen_datang,

            COALESCE(SUM(CASE 
                WHEN d.scan_keluar BETWEEN '12:00:00' AND '16:00:00' OR d.scan_keluar IS NULL 
                THEN 1 ELSE 0 
            END), 0) AS tidak_absen_pulang,
            COALESCE(SUM(CASE WHEN d.scan_masuk > '08:00:00' THEN 1 ELSE 0 END), 0) AS telat_datang, 
            -- Pulang cepat
            0 AS pulang_cepat,
            -- Hari Kerja
            0 As jumlah_hari_kerja,
            COALESCE(SUM(CASE 
                WHEN (p.jabatan = 'Customer Service' AND (d.scan_masuk > '07:00:00' OR d.scan_masuk IS NULL)) 
                    OR (p.jabatan <> 'Customer Service' AND (d.scan_masuk > '08:00:00' OR d.scan_masuk IS NULL)) 
                THEN 1 ELSE 0 
            END) + 
            SUM(CASE WHEN d.scan_keluar < '16:00:00' OR d.scan_keluar IS NULL THEN 1 ELSE 0 END), 0) AS tidak_tepat_jumlah
            
            FROM tb_detail d
            JOIN tb_pengguna p ON d.id_pg = p.id_pg
            WHERE DATE(d.tanggal) = CURDATE()
            GROUP BY p.noinduk, p.nama";
        $filename = "data_absensi_harian_" . date('Y-m-d') . ".csv";
        break;
    case 'mingguan':
        if ($start && $end) {
            $query = "SELECT 
                p.noinduk, 
                p.nama,
                COALESCE(SUM(CASE WHEN d.keterangan = 'sakit' THEN 1 ELSE 0 END), 0) AS sakit,
                COALESCE(SUM(CASE WHEN d.keterangan = 'izin' THEN 1 ELSE 0 END), 0) AS izin,
                COALESCE(SUM(CASE WHEN d.keterangan = 'alpha' THEN 1 ELSE 0 END), 0) AS alpha,
                COALESCE(SUM(CASE WHEN d.keterangan = 'cuti' THEN 1 ELSE 0 END), 0) AS cuti,
                COALESCE(SUM(CASE WHEN d.keterangan = 'hadir' THEN 1 ELSE 0 END), 0) AS hadir,
                    
                -- Tepat Waktu
                COALESCE(SUM(CASE 
                    WHEN p.jabatan = 'Customer Service' AND d.scan_masuk <= '07:00:00' THEN 1 
                    WHEN p.jabatan <> 'Customer Service' AND d.scan_masuk <= '08:00:00' THEN 1 
                    ELSE 0 
                END), 0) AS tepat_datang,
                COALESCE(SUM(CASE 
                    WHEN d.scan_keluar >= '16:00:00' THEN 1 
                    ELSE 0 
                END), 0) AS tepat_pulang,
                COALESCE(SUM(CASE 
                    WHEN p.jabatan = 'Customer Service' AND d.scan_masuk <= '07:00:00' THEN 1 
                    WHEN p.jabatan <> 'Customer Service' AND d.scan_masuk <= '08:00:00' THEN 1 
                    ELSE 0 
                END) + SUM(CASE WHEN d.scan_keluar >= '16:00:00' THEN 1 ELSE 0 END), 0) AS tepat_jumlah,

                -- Tidak Tepat Waktu
                COALESCE(SUM(CASE 
                    WHEN (p.jabatan = 'Customer Service' AND (d.scan_masuk BETWEEN '07:00:01' AND '12:00:00' OR d.scan_masuk IS NULL)) 
                        OR (p.jabatan <> 'Customer Service' AND (d.scan_masuk BETWEEN '08:00:01' AND '12:00:00' OR d.scan_masuk IS NULL)) 
                    THEN 1 ELSE 0 
                END), 0) AS tidak_absen_datang,

                COALESCE(SUM(CASE 
                    WHEN d.scan_keluar BETWEEN '12:00:00' AND '16:00:00' OR d.scan_keluar IS NULL 
                    THEN 1 ELSE 0 
                END), 0) AS tidak_absen_pulang,
                COALESCE(SUM(CASE WHEN d.scan_masuk > '08:00:00' THEN 1 ELSE 0 END), 0) AS telat_datang, 
                -- Pulang cepat
                0 AS pulang_cepat,
                -- Hari Kerja
                0 As jumlah_hari_kerja,
                COALESCE(SUM(CASE 
                    WHEN (p.jabatan = 'Customer Service' AND (d.scan_masuk > '07:00:00' OR d.scan_masuk IS NULL)) 
                        OR (p.jabatan <> 'Customer Service' AND (d.scan_masuk > '08:00:00' OR d.scan_masuk IS NULL)) 
                    THEN 1 ELSE 0 
                END) + SUM(CASE WHEN d.scan_keluar < '16:00:00' OR d.scan_keluar IS NULL THEN 1 ELSE 0 END), 0) AS tidak_tepat_jumlah
                FROM tb_detail d
                JOIN tb_pengguna p ON d.id_pg = p.id_pg
                WHERE d.tanggal BETWEEN '$start' AND '$end'
                GROUP BY p.noinduk, p.nama";
            $filename = "data_absensi_mingguan_" . $start . "_to_" . $end . ".csv";
        } else {
            $query = "SELECT 
                p.noinduk, 
                p.nama,
                COALESCE(SUM(CASE WHEN d.keterangan = 'sakit' THEN 1 ELSE 0 END), 0) AS sakit,
                COALESCE(SUM(CASE WHEN d.keterangan = 'izin' THEN 1 ELSE 0 END), 0) AS izin,
                COALESCE(SUM(CASE WHEN d.keterangan = 'alpha' THEN 1 ELSE 0 END), 0) AS alpha,
                COALESCE(SUM(CASE WHEN d.keterangan = 'cuti' THEN 1 ELSE 0 END), 0) AS cuti,
                COALESCE(SUM(CASE WHEN d.keterangan = 'hadir' THEN 1 ELSE 0 END), 0) AS hadir,
                    
                -- Tepat Waktu
                COALESCE(SUM(CASE 
                    WHEN p.jabatan = 'Customer Service' AND d.scan_masuk <= '07:00:00' THEN 1 
                    WHEN p.jabatan <> 'Customer Service' AND d.scan_masuk <= '08:00:00' THEN 1 
                    ELSE 0 
                END), 0) AS tepat_datang,
                COALESCE(SUM(CASE 
                    WHEN d.scan_keluar >= '16:00:00' THEN 1 
                    ELSE 0 
                END), 0) AS tepat_pulang,
                COALESCE(SUM(CASE 
                    WHEN p.jabatan = 'Customer Service' AND d.scan_masuk <= '07:00:00' THEN 1 
                    WHEN p.jabatan <> 'Customer Service' AND d.scan_masuk <= '08:00:00' THEN 1 
                    ELSE 0 
                END) + SUM(CASE WHEN d.scan_keluar >= '16:00:00' THEN 1 ELSE 0 END), 0) AS tepat_jumlah,

                -- Tidak Tepat Waktu
                COALESCE(SUM(CASE 
                    WHEN (p.jabatan = 'Customer Service' AND (d.scan_masuk BETWEEN '07:00:01' AND '12:00:00' OR d.scan_masuk IS NULL)) 
                        OR (p.jabatan <> 'Customer Service' AND (d.scan_masuk BETWEEN '08:00:01' AND '12:00:00' OR d.scan_masuk IS NULL)) 
                    THEN 1 ELSE 0 
                END), 0) AS tidak_absen_datang,

                COALESCE(SUM(CASE 
                    WHEN d.scan_keluar BETWEEN '12:00:00' AND '16:00:00' OR d.scan_keluar IS NULL 
                    THEN 1 ELSE 0 
                END), 0) AS tidak_absen_pulang,
                COALESCE(SUM(CASE WHEN d.scan_masuk > '08:00:00' THEN 1 ELSE 0 END), 0) AS telat_datang, 
                -- Pulang cepat
                0 AS pulang_cepat,
                -- Hari Kerja
                0 As jumlah_hari_kerja,
                COALESCE(SUM(CASE 
                    WHEN (p.jabatan = 'Customer Service' AND (d.scan_masuk > '07:00:00' OR d.scan_masuk IS NULL)) 
                        OR (p.jabatan <> 'Customer Service' AND (d.scan_masuk > '08:00:00' OR d.scan_masuk IS NULL)) 
                    THEN 1 ELSE 0 
                END) + SUM(CASE WHEN d.scan_keluar < '16:00:00' OR d.scan_keluar IS NULL THEN 1 ELSE 0 END), 0) AS tidak_tepat_jumlah
                FROM tb_detail d
                JOIN tb_pengguna p ON d.id_pg = p.id_pg
                WHERE YEARWEEK(d.tanggal, 1) = YEARWEEK(CURDATE(), 1)
                GROUP BY p.noinduk, p.nama";
            $filename = "data_absensi_mingguan_" . date('Y_W') . ".csv";
        }
        break;
    case 'bulanan':
        if ($start && $end) {
            $query = "SELECT 
                p.noinduk, 
                p.nama,
                COALESCE(SUM(CASE WHEN d.keterangan = 'sakit' THEN 1 ELSE 0 END), 0) AS sakit,
                COALESCE(SUM(CASE WHEN d.keterangan = 'izin' THEN 1 ELSE 0 END), 0) AS izin,
                COALESCE(SUM(CASE WHEN d.keterangan = 'alpha' THEN 1 ELSE 0 END), 0) AS alpha,
                COALESCE(SUM(CASE WHEN d.keterangan = 'cuti' THEN 1 ELSE 0 END), 0) AS cuti,
                COALESCE(SUM(CASE WHEN d.keterangan = 'hadir' THEN 1 ELSE 0 END), 0) AS hadir,
                    
                -- Tepat Waktu
                COALESCE(SUM(CASE 
                    WHEN p.jabatan = 'Customer Service' AND d.scan_masuk <= '07:00:00' THEN 1 
                    WHEN p.jabatan <> 'Customer Service' AND d.scan_masuk <= '08:00:00' THEN 1 
                    ELSE 0 
                END), 0) AS tepat_datang,
                COALESCE(SUM(CASE 
                    WHEN d.scan_keluar >= '16:00:00' THEN 1 
                    ELSE 0 
                END), 0) AS tepat_pulang,
                COALESCE(SUM(CASE 
                    WHEN p.jabatan = 'Customer Service' AND d.scan_masuk <= '07:00:00' THEN 1 
                    WHEN p.jabatan <> 'Customer Service' AND d.scan_masuk <= '08:00:00' THEN 1 
                    ELSE 0 
                END) + SUM(CASE WHEN d.scan_keluar >= '16:00:00' THEN 1 ELSE 0 END), 0) AS tepat_jumlah,

                -- Tidak Tepat Waktu
                COALESCE(SUM(CASE 
                    WHEN (p.jabatan = 'Customer Service' AND (d.scan_masuk BETWEEN '07:00:01' AND '12:00:00' OR d.scan_masuk IS NULL)) 
                        OR (p.jabatan <> 'Customer Service' AND (d.scan_masuk BETWEEN '08:00:01' AND '12:00:00' OR d.scan_masuk IS NULL)) 
                    THEN 1 ELSE 0 
                END), 0) AS tidak_absen_datang,

                COALESCE(SUM(CASE 
                    WHEN d.scan_keluar BETWEEN '12:00:00' AND '16:00:00' OR d.scan_keluar IS NULL 
                    THEN 1 ELSE 0 
                END), 0) AS tidak_absen_pulang,
                COALESCE(SUM(CASE WHEN d.scan_masuk > '08:00:00' THEN 1 ELSE 0 END), 0) AS telat_datang, 
                -- Pulang cepat
                0 AS pulang_cepat,
                -- Hari Kerja
                0 As jumlah_hari_kerja,
                COALESCE(SUM(CASE 
                    WHEN (p.jabatan = 'Customer Service' AND (d.scan_masuk > '07:00:00' OR d.scan_masuk IS NULL)) 
                        OR (p.jabatan <> 'Customer Service' AND (d.scan_masuk > '08:00:00' OR d.scan_masuk IS NULL)) 
                    THEN 1 ELSE 0 
                END) + SUM(CASE WHEN d.scan_keluar < '16:00:00' OR d.scan_keluar IS NULL THEN 1 ELSE 0 END), 0) AS tidak_tepat_jumlah
                FROM tb_detail d
                JOIN tb_pengguna p ON d.id_pg = p.id_pg
                WHERE d.tanggal BETWEEN '$start' AND '$end'
                GROUP BY p.noinduk, p.nama";
            $filename = "data_absensi_bulanan_" . $start . "_to_" . $end . ".csv";
        } else {
            $query = "SELECT 
                p.noinduk, 
                p.nama,
                COALESCE(SUM(CASE WHEN d.keterangan = 'sakit' THEN 1 ELSE 0 END), 0) AS sakit,
                COALESCE(SUM(CASE WHEN d.keterangan = 'izin' THEN 1 ELSE 0 END), 0) AS izin,
                COALESCE(SUM(CASE WHEN d.keterangan = 'alpha' THEN 1 ELSE 0 END), 0) AS alpha,
                COALESCE(SUM(CASE WHEN d.keterangan = 'cuti' THEN 1 ELSE 0 END), 0) AS cuti,
                COALESCE(SUM(CASE WHEN d.keterangan = 'hadir' THEN 1 ELSE 0 END), 0) AS hadir,
                    
                -- Tepat Waktu
                COALESCE(SUM(CASE 
                    WHEN p.jabatan = 'Customer Service' AND d.scan_masuk <= '07:00:00' THEN 1 
                    WHEN p.jabatan <> 'Customer Service' AND d.scan_masuk <= '08:00:00' THEN 1 
                    ELSE 0 
                END), 0) AS tepat_datang,
                COALESCE(SUM(CASE 
                    WHEN d.scan_keluar >= '16:00:00' THEN 1 
                    ELSE 0 
                END), 0) AS tepat_pulang,
                COALESCE(SUM(CASE 
                    WHEN p.jabatan = 'Customer Service' AND d.scan_masuk <= '07:00:00' THEN 1 
                    WHEN p.jabatan <> 'Customer Service' AND d.scan_masuk <= '08:00:00' THEN 1 
                    ELSE 0 
                END) + SUM(CASE WHEN d.scan_keluar >= '16:00:00' THEN 1 ELSE 0 END), 0) AS tepat_jumlah,

                -- Tidak Tepat Waktu
                COALESCE(SUM(CASE 
                    WHEN (p.jabatan = 'Customer Service' AND (d.scan_masuk BETWEEN '07:00:01' AND '12:00:00' OR d.scan_masuk IS NULL)) 
                        OR (p.jabatan <> 'Customer Service' AND (d.scan_masuk BETWEEN '08:00:01' AND '12:00:00' OR d.scan_masuk IS NULL)) 
                    THEN 1 ELSE 0 
                END), 0) AS tidak_absen_datang,

                COALESCE(SUM(CASE 
                    WHEN d.scan_keluar BETWEEN '12:00:00' AND '16:00:00' OR d.scan_keluar IS NULL 
                    THEN 1 ELSE 0 
                END), 0) AS tidak_absen_pulang,
                COALESCE(SUM(CASE WHEN d.scan_masuk > '08:00:00' THEN 1 ELSE 0 END), 0) AS telat_datang, 
                -- Pulang cepat
                0 AS pulang_cepat,
                -- Hari Kerja
                0 As jumlah_hari_kerja,
                COALESCE(SUM(CASE 
                    WHEN (p.jabatan = 'Customer Service' AND (d.scan_masuk > '07:00:00' OR d.scan_masuk IS NULL)) 
                        OR (p.jabatan <> 'Customer Service' AND (d.scan_masuk > '08:00:00' OR d.scan_masuk IS NULL)) 
                    THEN 1 ELSE 0 
                END) + SUM(CASE WHEN d.scan_keluar < '16:00:00' OR d.scan_keluar IS NULL THEN 1 ELSE 0 END), 0) AS tidak_tepat_jumlah
                FROM tb_detail d
                JOIN tb_pengguna p ON d.id_pg = p.id_pg
                WHERE MONTH(d.tanggal) = MONTH(CURDATE()) 
                AND YEAR(d.tanggal) = YEAR(CURDATE())
                GROUP BY p.noinduk, p.nama";
            $filename = "data_absensi_bulanan_" . date('Y_m') . ".csv";
        }
        break;
    default:
        $query = "SELECT 
                p.noinduk, 
                p.nama,
                COALESCE(SUM(CASE WHEN d.keterangan = 'sakit' THEN 1 ELSE 0 END), 0) AS sakit,
                COALESCE(SUM(CASE WHEN d.keterangan = 'izin' THEN 1 ELSE 0 END), 0) AS izin,
                COALESCE(SUM(CASE WHEN d.keterangan = 'alpha' THEN 1 ELSE 0 END), 0) AS alpha,
                COALESCE(SUM(CASE WHEN d.keterangan = 'cuti' THEN 1 ELSE 0 END), 0) AS cuti,
                COALESCE(SUM(CASE WHEN d.keterangan = 'hadir' THEN 1 ELSE 0 END), 0) AS hadir,
                    
                -- Tepat Waktu
                COALESCE(SUM(CASE 
                    WHEN p.jabatan = 'Customer Service' AND d.scan_masuk <= '07:00:00' THEN 1 
                    WHEN p.jabatan <> 'Customer Service' AND d.scan_masuk <= '08:00:00' THEN 1 
                    ELSE 0 
                END), 0) AS tepat_datang,
                COALESCE(SUM(CASE 
                    WHEN d.scan_keluar >= '16:00:00' THEN 1 
                    ELSE 0 
                END), 0) AS tepat_pulang,
                COALESCE(SUM(CASE 
                    WHEN p.jabatan = 'Customer Service' AND d.scan_masuk <= '07:00:00' THEN 1 
                    WHEN p.jabatan <> 'Customer Service' AND d.scan_masuk <= '08:00:00' THEN 1 
                    ELSE 0 
                END) + SUM(CASE WHEN d.scan_keluar >= '16:00:00' THEN 1 ELSE 0 END), 0) AS tepat_jumlah,

                -- Tidak Tepat Waktu
                COALESCE(SUM(CASE 
                    WHEN (p.jabatan = 'Customer Service' AND (d.scan_masuk BETWEEN '07:00:01' AND '12:00:00' OR d.scan_masuk IS NULL)) 
                        OR (p.jabatan <> 'Customer Service' AND (d.scan_masuk BETWEEN '08:00:01' AND '12:00:00' OR d.scan_masuk IS NULL)) 
                    THEN 1 ELSE 0 
                END), 0) AS tidak_absen_datang,

                COALESCE(SUM(CASE 
                    WHEN d.scan_keluar BETWEEN '12:00:00' AND '16:00:00' OR d.scan_keluar IS NULL 
                    THEN 1 ELSE 0 
                END), 0) AS tidak_absen_pulang,
                COALESCE(SUM(CASE WHEN d.scan_masuk > '08:00:00' THEN 1 ELSE 0 END), 0) AS telat_datang, 
                -- Pulang cepat
                0 AS pulang_cepat,
                -- Hari Kerja
                0 As jumlah_hari_kerja,
                COALESCE(SUM(CASE 
                    WHEN (p.jabatan = 'Customer Service' AND (d.scan_masuk > '07:00:00' OR d.scan_masuk IS NULL)) 
                        OR (p.jabatan <> 'Customer Service' AND (d.scan_masuk > '08:00:00' OR d.scan_masuk IS NULL)) 
                    THEN 1 ELSE 0 
                END) + SUM(CASE WHEN d.scan_keluar < '16:00:00' OR d.scan_keluar IS NULL THEN 1 ELSE 0 END), 0) AS tidak_tepat_jumlah
                FROM tb_detail d
                JOIN tb_pengguna p ON d.id_pg = p.id_pg
                WHERE DATE(d.tanggal) = CURDATE()
                GROUP BY p.noinduk, p.nama";
        $filename = "data_absensi_harian_" . date('Y-m-d') . ".csv";
        break;
}

// Eksekusi query
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    // Change content type to Excel and use .xls extension
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . str_replace(".csv", ".xls", $filename) . '";');

    // Start the HTML output for Excel
    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    echo '<head>';
    echo '<meta http-equiv="Content-type" content="text/html;charset=utf-8" />';
    echo '<style>';
    echo 'table { border-collapse: collapse; }';
    echo 'table, th, td { border: 1px solid black; }';
    echo 'th { background-color: #4CAF50; color: white; font-weight: bold; }';
    echo '.header { font-size: 16pt; font-weight: bold; text-align: center }';
    echo '.subheader { font-size: 12pt; font-weight: bold; text-align: center }';
    echo '.number { mso-number-format:"0"; }';
    echo '.text-center { text-align: center; }';
    echo '.text-right { text-align: right; }';
    echo '.highlight { background-color: #ffffcc; }';
    echo '</style>';
    echo '</head>';
    echo '<body>';

    // Title and date information
    echo '<table width="100%">';
    echo '<tr><td colspan="17" style="border-bottom: 1px border-style: dotted solid double;" class="header">HUMAN RESOURCE DEPARTMENT</td></tr>';
    echo '<tr><td colspan="17" style="border: none;" class="subheader">LAPORAN ABSENSI KARYAWAN</td></tr>';
    echo '<tr><td colspan="17" style="border: none;" class="subheader">PIMPINAN, DOSEN DAN KARYAWAN TETAP ITB AHMAD DAHLAN JAKARTA</td></tr>';

    if (!empty($date_info)) {
        echo '<tr><td colspan="17" style="border: none;" class="subheader">' . $date_info . '</td></tr>';
    } else {
        // Show appropriate title based on filter
        switch ($filter) {
            case 'harian':
                echo '<tr><td colspan="17" style="border: none;" class="subheader">Data Harian: ' . date('d F Y') . '</td></tr>';
                break;
            case 'mingguan':
                echo '<tr><td colspan="17" style="border: none;" class="subheader">Data Mingguan: Minggu ' . date('W, Y') . '</td></tr>';
                break;
            case 'bulanan':
                echo '<tr><td colspan="17" style="border: none;" class="subheader">Data Bulanan: ' . date('F Y') . '</td></tr>';
                break;
        }
    }
    echo '<tr><td colspan="17" style="border: none;"></td></tr>'; // Empty row for spacing

    // Column headers with better formatting
    echo '<tr>';
    echo '<th rowspan="2">No</th>';
    echo '<th rowspan="2">NIK</th>';
    echo '<th rowspan="2">Nama Karyawan</th>';
    echo '<th colspan="4">Keterangan</th>';
    echo '<th colspan="3">Tepat Waktu</th>';
    echo '<th colspan="5">Tidak Tepat Waktu</th>';
    echo '<th colspan="2">Jml. Hr. Krj</th>';
    // echo '<th rowspan="2">Hadir</th>';
    echo '</tr>';

    echo '<tr>';
    echo '<th>S</th>';
    echo '<th>I</th>';
    echo '<th>A</th>';
    echo '<th>CUTI</th>';
    echo '<th>Dtng</th>';
    echo '<th>Plng</th>';
    echo '<th>Jml</th>';
    echo '<th>TAD</th>';
    echo '<th>TAP</th>';
    echo '<th>Tlt.Dtg</th>';
    echo '<th>Plg.Cpt</th>';
    echo '<th>Jml</th>';
    echo '<th>Hari</th>';
    echo '<th>Hadir</th>';
    echo '</tr>';

    // Tulis baris data dengan nomor urut dan formatting
    $no = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td class="text-center">' . $no++ . '</td>';
        echo '<td class="text-center">' . $row['noinduk'] . '</td>';  // NIK
        echo '<td class="text-center">' . $row['nama'] . '</td>';  // Nama Karyawan

        // Keterangan
        echo '<td class="text-center">' . $row['sakit'] . '</td>';
        echo '<td class="text-center">' . $row['izin'] . '</td>';
        echo '<td class="text-center">' . $row['alpha'] . '</td>';
        echo '<td class="text-center">' . $row['cuti'] . '</td>';

        // Tepat Waktu
        echo '<td class="text-center">' . $row['tepat_datang'] . '</td>';
        echo '<td class="text-center">' . $row['tepat_pulang'] . '</td>';
        echo '<td><b>' . $row['tepat_jumlah'] . '</b></td>';  // Diberi tebal karena tampak menonjol

        // Tidak Tepat Waktu
        echo '<td class="text-center">' . $row['tidak_absen_datang'] . '</td>';
        echo '<td class="text-center">' . $row['tidak_absen_pulang'] . '</td>';
        echo '<td class="text-center">' . $row['telat_datang'] . '</td>';
        echo '<td class="text-center">' . $row['pulang_cepat'] . '</td>';
        echo '<td class="text-center"><b>' . $row['tidak_tepat_jumlah'] . '</b></td>';  // Diberi tebal karena tampak menonjol

        // Hari kerja & kehadiran
        echo '<td class="text-center">' . $row['jumlah_hari_kerja'] . '</td>';
        echo '<td class="text-center"><b>' . $row['hadir'] . '</b></td>';  // Diberi tebal karena tampak menonjol
        echo '</tr>';
    }

    echo '</table>';
    echo '</body>';
    echo '</html>';

    exit();
} else {
    echo "Data tidak ditemukan untuk filter ini.";
}