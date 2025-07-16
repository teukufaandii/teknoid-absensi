<?php
ob_start();
session_start();

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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
            p.kampus,
            COALESCE(SUM(CASE WHEN d.keterangan = 'sakit' THEN 1 ELSE 0 END), 0) AS sakit,
            COALESCE(SUM(CASE WHEN d.keterangan = 'izin' THEN 1 ELSE 0 END), 0) AS izin,
            COALESCE(SUM(CASE WHEN d.keterangan = 'alpha' THEN 1 ELSE 0 END), 0) AS alpha,
            COALESCE(SUM(CASE WHEN d.keterangan = 'cuti' THEN 1 ELSE 0 END), 0) AS cuti,
            COALESCE(SUM(CASE WHEN d.keterangan = 'hadir' THEN 1 ELSE 0 END), 0) AS hadir,
            
            -- Tepat Waktu
            COALESCE(SUM(CASE 
                WHEN p.jabatan = 'Cleaning Service' AND d.scan_masuk <= '07:30:00' THEN 1 
                WHEN p.jabatan <> 'Cleaning Service' AND d.scan_masuk <= '08:00:00' THEN 1 
                ELSE 0 
            END), 0) AS tepat_datang,
            COALESCE(SUM(CASE 
            WHEN p.jabatan = 'Cleaning Service' 
                AND ((DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 AND d.scan_keluar >= '16:30:00')
                    OR (DAYOFWEEK(d.tanggal) = 7 AND d.scan_keluar >= '13:00:00'))
            THEN 1 
            WHEN p.jabatan <> 'Cleaning Service' 
                AND ((DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 AND d.scan_keluar >= '16:00:00')
                    OR (DAYOFWEEK(d.tanggal) = 7 AND d.scan_keluar >= '13:00:00'))
            THEN 1 
            ELSE 0 
            END), 0) AS tepat_pulang,
                    COALESCE(SUM(CASE 
            WHEN p.jabatan = 'Cleaning Service' AND d.scan_masuk <= '07:30:00' THEN 1 
            WHEN p.jabatan <> 'Cleaning Service' AND d.scan_masuk <= '08:00:00' THEN 1 
            ELSE 0 
            END) + 
            SUM(CASE 
                WHEN p.jabatan = 'Cleaning Service' 
                    AND ((DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 AND d.scan_keluar >= '16:30:00')
                        OR (DAYOFWEEK(d.tanggal) = 7 AND d.scan_keluar >= '13:00:00'))
                THEN 1 
                WHEN p.jabatan <> 'Cleaning Service' 
                    AND ((DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 AND d.scan_keluar >= '16:00:00')
                        OR (DAYOFWEEK(d.tanggal) = 7 AND d.scan_keluar >= '13:00:00'))
                THEN 1 
                ELSE 0 
            END), 0) AS tepat_jumlah,

            -- Tidak absen datang dan pulang
            COALESCE(SUM(CASE 
                WHEN d.scan_masuk IS NULL AND d.keterangan = 'hadir'
                THEN 1 ELSE 0 
            END), 0) AS tidak_absen_datang,

            COALESCE(SUM(CASE 
                WHEN d.scan_keluar IS NULL AND d.keterangan = 'hadir'
                THEN 1 ELSE 0 
            END), 0) AS tidak_absen_pulang,

            -- telat datang 
            COALESCE(SUM(CASE 
                WHEN (p.jabatan = 'Cleaning Service' AND d.scan_masuk > '07:30:00')
                    OR (p.jabatan <> 'Cleaning Service' AND d.scan_masuk > '07:00:00')
                THEN 1 ELSE 0 
            END), 0) AS telat_datang,

            -- Pulang cepat
            COALESCE(SUM(CASE 
                WHEN DAYOFWEEK(d.tanggal) = 7 
                    AND d.scan_keluar > '12:00:00' 
                    AND d.scan_keluar < '13:00:00' THEN 1
                WHEN DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 
                    AND d.scan_keluar > '12:00:00' 
                    AND d.scan_keluar < '16:00:00' THEN 1
                ELSE 0
            END), 0) AS pulang_cepat,

            -- Hari Kerja
            COALESCE(COUNT(d.tanggal), 0) AS jumlah_hari_kerja,

            -- Tidak Tepat Jumlah
            (COALESCE(SUM(CASE 
                WHEN d.scan_masuk IS NULL AND d.keterangan = 'hadir'
                    THEN 1 ELSE 0 
                END), 0) 
                + COALESCE(SUM(CASE 
                    WHEN d.scan_keluar IS NULL AND d.keterangan = 'hadir'
                    THEN 1 ELSE 0 
                END), 0)
                + COALESCE(SUM(CASE WHEN d.scan_masuk > '08:00:00' THEN 1 ELSE 0 END), 0)
                + COALESCE(SUM(CASE 
                    WHEN DAYOFWEEK(d.tanggal) = 7 
                        AND d.scan_keluar > '12:00:00' 
                        AND d.scan_keluar < '13:00:00' THEN 1
                    WHEN DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 
                        AND d.scan_keluar > '12:00:00' 
                        AND d.scan_keluar < '16:00:00' THEN 1
                    ELSE 0
                END), 0)
            ) AS tidak_tepat_jumlah
            FROM tb_detail d
            JOIN tb_pengguna p ON d.id_pg = p.id_pg
            WHERE DATE(d.tanggal) = CURDATE()
            GROUP BY p.noinduk, p.nama, p.kampus";
        $filename = "data_absensi_harian_" . date('Y-m-d') . ".csv";
        break;
    case 'mingguan':
        if ($start && $end) {
            $query = "SELECT 
                p.noinduk, 
                p.nama,
                p.kampus,
                COALESCE(SUM(CASE WHEN d.keterangan = 'sakit' THEN 1 ELSE 0 END), 0) AS sakit,
                COALESCE(SUM(CASE WHEN d.keterangan = 'izin' THEN 1 ELSE 0 END), 0) AS izin,
                COALESCE(SUM(CASE WHEN d.keterangan = 'alpha' THEN 1 ELSE 0 END), 0) AS alpha,
                COALESCE(SUM(CASE WHEN d.keterangan = 'cuti' THEN 1 ELSE 0 END), 0) AS cuti,
                COALESCE(SUM(CASE WHEN d.keterangan = 'hadir' THEN 1 ELSE 0 END), 0) AS hadir,
                    
            -- Tepat Waktu
                COALESCE(SUM(CASE
                    WHEN p.jabatan = 'Cleaning Service' AND d.scan_masuk <= '07:30:00' THEN 1 
                    WHEN p.jabatan <> 'Cleaning Service' AND d.scan_masuk <= '08:00:00' THEN 1 
                    ELSE 0 
                END), 0) AS tepat_datang,
                COALESCE(SUM(CASE 
                WHEN p.jabatan = 'Cleaning Service' 
                    AND ((DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 AND d.scan_keluar >= '16:30:00')
                        OR (DAYOFWEEK(d.tanggal) = 7 AND d.scan_keluar >= '13:00:00'))
                THEN 1 
                WHEN p.jabatan <> 'Cleaning Service' 
                    AND ((DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 AND d.scan_keluar >= '16:00:00')
                        OR (DAYOFWEEK(d.tanggal) = 7 AND d.scan_keluar >= '13:00:00'))
                THEN 1 
                ELSE 0 
                END), 0) AS tepat_pulang,
                    COALESCE(SUM(CASE 
                    WHEN p.jabatan = 'Cleaning Service' AND d.scan_masuk <= '07:30:00' THEN 1 
                    WHEN p.jabatan <> 'Cleaning Service' AND d.scan_masuk <= '08:00:00' THEN 1 
                    ELSE 0 
                END) + 
                SUM(CASE 
                    WHEN p.jabatan = 'Cleaning Service' 
                        AND ((DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 AND d.scan_keluar >= '16:30:00')
                            OR (DAYOFWEEK(d.tanggal) = 7 AND d.scan_keluar >= '13:00:00'))
                    THEN 1 
                    WHEN p.jabatan <> 'Cleaning Service' 
                        AND ((DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 AND d.scan_keluar >= '16:00:00')
                            OR (DAYOFWEEK(d.tanggal) = 7 AND d.scan_keluar >= '13:00:00'))
                    THEN 1 
                    ELSE 0 
                END), 0) AS tepat_jumlah,

                -- Tidak absen datang dan pulang
                COALESCE(SUM(CASE 
                    WHEN d.scan_masuk IS NULL AND d.keterangan = 'hadir'
                    THEN 1 ELSE 0 
                END), 0) AS tidak_absen_datang,

                COALESCE(SUM(CASE 
                    WHEN d.scan_keluar IS NULL AND d.keterangan = 'hadir'
                    THEN 1 ELSE 0 
                END), 0) AS tidak_absen_pulang,

                -- telat datang 
                COALESCE(SUM(CASE 
                    WHEN (p.jabatan = 'Cleaning Service' AND d.scan_masuk > '07:30:00')
                        OR (p.jabatan <> 'Cleaning Service' AND d.scan_masuk > '07:00:00')
                    THEN 1 ELSE 0 
                END), 0) AS telat_datang,

            -- Pulang cepat
            COALESCE(SUM(CASE 
                WHEN DAYOFWEEK(d.tanggal) = 7 
                    AND d.scan_keluar > '12:00:00' 
                    AND d.scan_keluar < '13:00:00' THEN 1
                WHEN DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 
                    AND d.scan_keluar > '12:00:00' 
                    AND d.scan_keluar < '16:00:00' THEN 1
                ELSE 0
            END), 0) AS pulang_cepat,

            -- Hari Kerja
            COALESCE(COUNT(d.tanggal), 0) AS jumlah_hari_kerja,

            -- Tidak Tepat Jumlah
            (COALESCE(SUM(CASE 
                WHEN d.scan_masuk IS NULL AND d.keterangan = 'hadir'
                    THEN 1 ELSE 0 
                END), 0) 
                + COALESCE(SUM(CASE 
                    WHEN d.scan_keluar IS NULL AND d.keterangan = 'hadir'
                    THEN 1 ELSE 0 
                END), 0)
                + COALESCE(SUM(CASE WHEN d.scan_masuk > '08:00:00' THEN 1 ELSE 0 END), 0)
                + COALESCE(SUM(CASE 
                    WHEN DAYOFWEEK(d.tanggal) = 7 
                        AND d.scan_keluar > '12:00:00' 
                        AND d.scan_keluar < '13:00:00' THEN 1
                    WHEN DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 
                        AND d.scan_keluar > '12:00:00' 
                        AND d.scan_keluar < '16:00:00' THEN 1
                    ELSE 0
                END), 0)
            ) AS tidak_tepat_jumlah
                FROM tb_detail d
                JOIN tb_pengguna p ON d.id_pg = p.id_pg
                WHERE d.tanggal BETWEEN '$start' AND '$end'
                GROUP BY p.noinduk, p.nama, p.kampus";
            $filename = "data_absensi_mingguan_" . $start . "_to_" . $end . ".csv";
        } else {
            $query = "SELECT 
                p.noinduk, 
                p.nama,
                p.kampus,
                COALESCE(SUM(CASE WHEN d.keterangan = 'sakit' THEN 1 ELSE 0 END), 0) AS sakit,
                COALESCE(SUM(CASE WHEN d.keterangan = 'izin' THEN 1 ELSE 0 END), 0) AS izin,
                COALESCE(SUM(CASE WHEN d.keterangan = 'alpha' THEN 1 ELSE 0 END), 0) AS alpha,
                COALESCE(SUM(CASE WHEN d.keterangan = 'cuti' THEN 1 ELSE 0 END), 0) AS cuti,
                COALESCE(SUM(CASE WHEN d.keterangan = 'hadir' THEN 1 ELSE 0 END), 0) AS hadir,
                    
                -- Tepat Waktu
                COALESCE(SUM(CASE 
                    WHEN p.jabatan = 'Cleaning Service' AND d.scan_masuk <= '07:00:00' THEN 1 
                    WHEN p.jabatan <> 'Cleaning Service' AND d.scan_masuk <= '08:00:00' THEN 1 
                    ELSE 0 
                END), 0) AS tepat_datang,
                COALESCE(SUM(CASE 
                WHEN p.jabatan = 'Cleaning Service' 
                    AND ((DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 AND d.scan_keluar >= '16:30:00')
                        OR (DAYOFWEEK(d.tanggal) = 7 AND d.scan_keluar >= '13:00:00'))
                THEN 1 
                WHEN p.jabatan <> 'Cleaning Service' 
                    AND ((DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 AND d.scan_keluar >= '16:00:00')
                        OR (DAYOFWEEK(d.tanggal) = 7 AND d.scan_keluar >= '13:00:00'))
                THEN 1 
                ELSE 0 
                END), 0) AS tepat_pulang,
                COALESCE(SUM(CASE 
                    WHEN p.jabatan = 'Cleaning Service' AND d.scan_masuk <= '07:00:00' THEN 1 
                    WHEN p.jabatan <> 'Cleaning Service' AND d.scan_masuk <= '08:00:00' THEN 1 
                    ELSE 0 
                END) + SUM(CASE WHEN d.scan_keluar >= '16:00:00' THEN 1 ELSE 0 END), 0) AS tepat_jumlah,

                -- Tidak Tepat Waktu
                COALESCE(SUM(CASE 
                WHEN d.scan_masuk IS NULL AND d.keterangan = 'hadir'
                    THEN 1 ELSE 0 
                END), 0) AS tidak_absen_datang,

                COALESCE(SUM(CASE 
                    WHEN d.scan_keluar IS NULL AND d.keterangan = 'hadir'
                    THEN 1 ELSE 0 
                END), 0) AS tidak_absen_pulang,
                COALESCE(SUM(CASE WHEN d.scan_masuk > '08:00:00' THEN 1 ELSE 0 END), 0) AS telat_datang, 
                -- Pulang cepat
                COALESCE(SUM(CASE 
                    WHEN DAYOFWEEK(d.tanggal) = 7 
                        AND d.scan_keluar > '12:00:00' 
                        AND d.scan_keluar < '13:00:00' THEN 1
                    WHEN DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 
                        AND d.scan_keluar > '12:00:00' 
                        AND d.scan_keluar < '16:00:00' THEN 1
                    ELSE 0
                END), 0) AS pulang_cepat,

                -- Hari Kerja
                COALESCE(COUNT(d.tanggal), 0) AS jumlah_hari_kerja,

                -- Tidak Tepat Jumlah
                (COALESCE(SUM(CASE 
                    WHEN d.scan_masuk IS NULL AND d.keterangan = 'hadir'
                        THEN 1 ELSE 0 
                    END), 0) 
                    + COALESCE(SUM(CASE 
                        WHEN d.scan_keluar IS NULL AND d.keterangan = 'hadir'
                        THEN 1 ELSE 0 
                    END), 0)
                    + COALESCE(SUM(CASE WHEN d.scan_masuk > '08:00:00' THEN 1 ELSE 0 END), 0)
                    + COALESCE(SUM(CASE 
                        WHEN DAYOFWEEK(d.tanggal) = 7 
                            AND d.scan_keluar > '12:00:00' 
                            AND d.scan_keluar < '13:00:00' THEN 1
                        WHEN DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 
                            AND d.scan_keluar > '12:00:00' 
                            AND d.scan_keluar < '16:00:00' THEN 1
                        ELSE 0
                    END), 0)
                ) AS tidak_tepat_jumlah
                FROM tb_detail d
                JOIN tb_pengguna p ON d.id_pg = p.id_pg
                WHERE YEARWEEK(d.tanggal, 1) = YEARWEEK(CURDATE(), 1)
                GROUP BY p.noinduk, p.nama, p.kampus";
            $filename = "data_absensi_mingguan_" . date('Y_W') . ".csv";
        }
        break;
    case 'bulanan':
        if ($start && $end) {
            $query = "SELECT 
                p.noinduk, 
                p.nama,
                p.kampus,
                COALESCE(SUM(CASE WHEN d.keterangan = 'sakit' THEN 1 ELSE 0 END), 0) AS sakit,
                COALESCE(SUM(CASE WHEN d.keterangan = 'izin' THEN 1 ELSE 0 END), 0) AS izin,
                COALESCE(SUM(CASE WHEN d.keterangan = 'alpha' THEN 1 ELSE 0 END), 0) AS alpha,
                COALESCE(SUM(CASE WHEN d.keterangan = 'cuti' THEN 1 ELSE 0 END), 0) AS cuti,
                COALESCE(SUM(CASE WHEN d.keterangan = 'hadir' THEN 1 ELSE 0 END), 0) AS hadir,
                    
                -- Tepat Waktu
                COALESCE(SUM(CASE 
                WHEN p.jabatan = 'Cleaning Service' AND d.scan_masuk <= '07:00:00' THEN 1 
                    WHEN p.jabatan <> 'Cleaning Service' AND d.scan_masuk <= '08:00:00' THEN 1 
                    ELSE 0 
                    END), 0) AS tepat_datang,
                COALESCE(SUM(CASE 
                WHEN p.jabatan = 'Cleaning Service' 
                    AND ((DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 AND d.scan_keluar >= '16:30:00')
                        OR (DAYOFWEEK(d.tanggal) = 7 AND d.scan_keluar >= '13:00:00'))
                THEN 1 
                WHEN p.jabatan <> 'Cleaning Service' 
                    AND ((DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 AND d.scan_keluar >= '16:00:00')
                        OR (DAYOFWEEK(d.tanggal) = 7 AND d.scan_keluar >= '13:00:00'))
                    THEN 1 ELSE 0 
                    END), 0) AS tepat_pulang,
                COALESCE(SUM(CASE 
                    WHEN p.jabatan = 'Cleaning Service' AND d.scan_masuk <= '07:30:00' THEN 1 
                    WHEN p.jabatan <> 'Cleaning Service' AND d.scan_masuk <= '08:00:00' THEN 1 
                    ELSE 0 
                END) + 
                SUM(CASE 
                    WHEN p.jabatan = 'Cleaning Service' 
                        AND ((DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 AND d.scan_keluar >= '16:30:00')
                            OR (DAYOFWEEK(d.tanggal) = 7 AND d.scan_keluar >= '13:00:00'))
                    THEN 1 
                    WHEN p.jabatan <> 'Cleaning Service' 
                        AND ((DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 AND d.scan_keluar >= '16:00:00')
                            OR (DAYOFWEEK(d.tanggal) = 7 AND d.scan_keluar >= '13:00:00'))
                    THEN 1 
                    ELSE 0 
                END), 0) AS tepat_jumlah,

                -- Tidak Tepat Waktu
                COALESCE(SUM(CASE 
                WHEN d.scan_masuk IS NULL AND d.keterangan = 'hadir'
                    THEN 1 ELSE 0 
                END), 0) AS tidak_absen_datang,

                COALESCE(SUM(CASE 
                    WHEN d.scan_keluar IS NULL AND d.keterangan = 'hadir'
                    THEN 1 ELSE 0 
                END), 0) AS tidak_absen_pulang,
                COALESCE(SUM(CASE WHEN d.scan_masuk > '08:00:00' THEN 1 ELSE 0 END), 0) AS telat_datang, 
                -- Pulang cepat
                COALESCE(SUM(CASE 
                    WHEN DAYOFWEEK(d.tanggal) = 7 
                        AND d.scan_keluar > '12:00:00' 
                        AND d.scan_keluar < '13:00:00' THEN 1
                    WHEN DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 
                        AND d.scan_keluar > '12:00:00' 
                        AND d.scan_keluar < '16:00:00' THEN 1
                    ELSE 0
                END), 0) AS pulang_cepat,

                -- Hari Kerja
                COALESCE(COUNT(d.tanggal), 0) AS jumlah_hari_kerja,

                -- Tidak Tepat Jumlah
                (COALESCE(SUM(CASE 
                    WHEN d.scan_masuk IS NULL AND d.keterangan = 'hadir'
                        THEN 1 ELSE 0 
                    END), 0) 
                    + COALESCE(SUM(CASE 
                        WHEN d.scan_keluar IS NULL AND d.keterangan = 'hadir'
                        THEN 1 ELSE 0 
                    END), 0)
                    + COALESCE(SUM(CASE WHEN d.scan_masuk > '08:00:00' THEN 1 ELSE 0 END), 0)
                    + COALESCE(SUM(CASE 
                        WHEN DAYOFWEEK(d.tanggal) = 7 
                            AND d.scan_keluar > '12:00:00' 
                            AND d.scan_keluar < '13:00:00' THEN 1
                        WHEN DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 
                            AND d.scan_keluar > '12:00:00' 
                            AND d.scan_keluar < '16:00:00' THEN 1
                        ELSE 0
                    END), 0)
                ) AS tidak_tepat_jumlah
                FROM tb_detail d
                JOIN tb_pengguna p ON d.id_pg = p.id_pg
                WHERE d.tanggal BETWEEN '$start' AND '$end'
                GROUP BY p.noinduk, p.nama, p.kampus";
            $filename = "data_absensi_bulanan_" . $start . "_to_" . $end . ".csv";
        } else {
            $query = "SELECT 
                p.noinduk, 
                p.nama,
                p.kampus,
                COALESCE(SUM(CASE WHEN d.keterangan = 'sakit' THEN 1 ELSE 0 END), 0) AS sakit,
                COALESCE(SUM(CASE WHEN d.keterangan = 'izin' THEN 1 ELSE 0 END), 0) AS izin,
                COALESCE(SUM(CASE WHEN d.keterangan = 'alpha' THEN 1 ELSE 0 END), 0) AS alpha,
                COALESCE(SUM(CASE WHEN d.keterangan = 'cuti' THEN 1 ELSE 0 END), 0) AS cuti,
                COALESCE(SUM(CASE WHEN d.keterangan = 'hadir' THEN 1 ELSE 0 END), 0) AS hadir,
                    
                -- Tepat Waktu
                COALESCE(SUM(CASE
                    WHEN p.jabatan = 'Cleaning Service' AND d.scan_masuk <= '07:30:00' THEN 1 
                    WHEN p.jabatan <> 'Cleaning Service' AND d.scan_masuk <= '08:00:00' THEN 1 
                    ELSE 0 
                END), 0) AS tepat_datang,
                COALESCE(SUM(CASE 
                WHEN p.jabatan = 'Cleaning Service' 
                    AND ((DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 AND d.scan_keluar >= '16:30:00')
                        OR (DAYOFWEEK(d.tanggal) = 7 AND d.scan_keluar >= '13:00:00'))
                THEN 1 
                WHEN p.jabatan <> 'Cleaning Service' 
                    AND ((DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 AND d.scan_keluar >= '16:00:00')
                        OR (DAYOFWEEK(d.tanggal) = 7 AND d.scan_keluar >= '13:00:00'))
                THEN 1 
                ELSE 0 
                END), 0) AS tepat_pulang,
                COALESCE(SUM(CASE 
                    WHEN p.jabatan = 'Cleaning Service' AND d.scan_masuk <= '07:30:00' THEN 1 
                    WHEN p.jabatan <> 'Cleaning Service' AND d.scan_masuk <= '08:00:00' THEN 1 
                    ELSE 0 
                END) + 
                SUM(CASE 
                    WHEN p.jabatan = 'Cleaning Service' 
                        AND ((DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 AND d.scan_keluar >= '16:30:00')
                            OR (DAYOFWEEK(d.tanggal) = 7 AND d.scan_keluar >= '13:00:00'))
                    THEN 1 
                    WHEN p.jabatan <> 'Cleaning Service' 
                        AND ((DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 AND d.scan_keluar >= '16:00:00')
                            OR (DAYOFWEEK(d.tanggal) = 7 AND d.scan_keluar >= '13:00:00'))
                    THEN 1 
                    ELSE 0 
                END), 0) AS tepat_jumlah,

                -- Tidak absen datang dan pulang
                COALESCE(SUM(CASE 
                    WHEN d.scan_masuk IS NULL AND d.keterangan = 'hadir'
                    THEN 1 ELSE 0 
                END), 0) AS tidak_absen_datang,

                COALESCE(SUM(CASE 
                    WHEN d.scan_keluar IS NULL AND d.keterangan = 'hadir'
                    THEN 1 ELSE 0 
                END), 0) AS tidak_absen_pulang,

                -- telat datang 
                COALESCE(SUM(CASE 
                    WHEN (p.jabatan = 'Cleaning Service' AND d.scan_masuk > '07:30:00')
                        OR (p.jabatan <> 'Cleaning Service' AND d.scan_masuk > '07:00:00')
                    THEN 1 ELSE 0 
                END), 0) AS telat_datang,

                -- Pulang cepat
                COALESCE(SUM(CASE 
                    WHEN DAYOFWEEK(d.tanggal) = 7 
                        AND d.scan_keluar > '12:00:00' 
                        AND d.scan_keluar < '13:00:00' THEN 1
                    WHEN DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 
                        AND d.scan_keluar > '12:00:00' 
                        AND d.scan_keluar < '16:00:00' THEN 1
                    ELSE 0
                END), 0) AS pulang_cepat,

                -- Hari Kerja
                COALESCE(COUNT(d.tanggal), 0) AS jumlah_hari_kerja,

                -- Tidak Tepat Jumlah
                (COALESCE(SUM(CASE 
                    WHEN d.scan_masuk IS NULL AND d.keterangan = 'hadir'
                        THEN 1 ELSE 0 
                    END), 0) 
                    + COALESCE(SUM(CASE 
                        WHEN d.scan_keluar IS NULL AND d.keterangan = 'hadir'
                        THEN 1 ELSE 0 
                    END), 0)
                    + COALESCE(SUM(CASE WHEN d.scan_masuk > '08:00:00' THEN 1 ELSE 0 END), 0)
                    + COALESCE(SUM(CASE 
                        WHEN DAYOFWEEK(d.tanggal) = 7 
                            AND d.scan_keluar > '12:00:00' 
                            AND d.scan_keluar < '13:00:00' THEN 1
                        WHEN DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 
                            AND d.scan_keluar > '12:00:00' 
                            AND d.scan_keluar < '16:00:00' THEN 1
                        ELSE 0
                    END), 0)
                ) AS tidak_tepat_jumlah
                FROM tb_detail d
                JOIN tb_pengguna p ON d.id_pg = p.id_pg
                WHERE MONTH(d.tanggal) = MONTH(CURDATE()) 
                AND YEAR(d.tanggal) = YEAR(CURDATE())
                GROUP BY p.noinduk, p.nama, p.kampus";
            $filename = "data_absensi_bulanan_" . date('Y_m') . ".csv";
        }
        break;
    default:
        $query = "SELECT 
                p.noinduk, 
                p.nama,
                p.kampus,
                COALESCE(SUM(CASE WHEN d.keterangan = 'sakit' THEN 1 ELSE 0 END), 0) AS sakit,
                COALESCE(SUM(CASE WHEN d.keterangan = 'izin' THEN 1 ELSE 0 END), 0) AS izin,
                COALESCE(SUM(CASE WHEN d.keterangan = 'alpha' THEN 1 ELSE 0 END), 0) AS alpha,
                COALESCE(SUM(CASE WHEN d.keterangan = 'cuti' THEN 1 ELSE 0 END), 0) AS cuti,
                COALESCE(SUM(CASE WHEN d.keterangan = 'hadir' THEN 1 ELSE 0 END), 0) AS hadir,
                    
                -- Tepat Waktu
                COALESCE(SUM(CASE 
                    WHEN p.jabatan = 'Cleaning Service' AND d.scan_masuk <= '07:30:00' THEN 1 
                    WHEN p.jabatan <> 'Cleaning Service' AND d.scan_masuk <= '08:00:00' THEN 1 
                    ELSE 0 
                END), 0) AS tepat_datang,
                COALESCE(SUM(CASE 
                WHEN p.jabatan = 'Cleaning Service' 
                    AND ((DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 AND d.scan_keluar >= '16:30:00')
                        OR (DAYOFWEEK(d.tanggal) = 7 AND d.scan_keluar >= '13:00:00'))
                THEN 1 
                WHEN p.jabatan <> 'Cleaning Service' 
                    AND ((DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 AND d.scan_keluar >= '16:00:00')
                        OR (DAYOFWEEK(d.tanggal) = 7 AND d.scan_keluar >= '13:00:00'))
                THEN 1 
                ELSE 0 
                END), 0) AS tepat_pulang,
                COALESCE(SUM(CASE 
                    WHEN p.jabatan = 'Cleaning Service' AND d.scan_masuk <= '07:30:00' THEN 1 
                    WHEN p.jabatan <> 'Cleaning Service' AND d.scan_masuk <= '08:00:00' THEN 1 
                    ELSE 0 
                END) + SUM(CASE WHEN d.scan_keluar >= '16:00:00' THEN 1 ELSE 0 END), 0) AS tepat_jumlah,

                -- Tidak Tepat Waktu
                COALESCE(SUM(CASE 
                WHEN d.scan_masuk IS NULL AND d.keterangan = 'hadir'
                    THEN 1 ELSE 0 
                END), 0) AS tidak_absen_datang,

                COALESCE(SUM(CASE 
                    WHEN d.scan_keluar IS NULL AND d.keterangan = 'hadir'
                    THEN 1 ELSE 0 
                END), 0) AS tidak_absen_pulang,
                COALESCE(SUM(CASE WHEN d.scan_masuk > '08:00:00' THEN 1 ELSE 0 END), 0) AS telat_datang,
                
                -- Pulang cepat
                COALESCE(SUM(CASE 
                    WHEN DAYOFWEEK(d.tanggal) = 7 
                        AND d.scan_keluar > '12:00:00' 
                        AND d.scan_keluar < '13:00:00' THEN 1
                    WHEN DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 
                        AND d.scan_keluar > '12:00:00' 
                        AND d.scan_keluar < '16:00:00' THEN 1
                    ELSE 0
                END), 0) AS pulang_cepat,

                -- Hari Kerja
                COALESCE(COUNT(d.tanggal), 0) AS jumlah_hari_kerja,

            -- Tidak Tepat Jumlah
                (COALESCE(SUM(CASE 
                    WHEN d.scan_masuk IS NULL AND d.keterangan = 'hadir'
                        THEN 1 ELSE 0 
                    END), 0) 
                    + COALESCE(SUM(CASE 
                        WHEN d.scan_keluar IS NULL AND d.keterangan = 'hadir'
                        THEN 1 ELSE 0 
                    END), 0)
                    + COALESCE(SUM(CASE WHEN d.scan_masuk > '08:00:00' THEN 1 ELSE 0 END), 0)
                    + COALESCE(SUM(CASE 
                        WHEN DAYOFWEEK(d.tanggal) = 7 
                            AND d.scan_keluar > '12:00:00' 
                            AND d.scan_keluar < '13:00:00' THEN 1
                        WHEN DAYOFWEEK(d.tanggal) BETWEEN 2 AND 6 
                            AND d.scan_keluar > '12:00:00' 
                            AND d.scan_keluar < '16:00:00' THEN 1
                        ELSE 0
                    END), 0)
                ) AS tidak_tepat_jumlah
                FROM tb_detail d
                JOIN tb_pengguna p ON d.id_pg = p.id_pg
                WHERE DATE(d.tanggal) = CURDATE()
                GROUP BY p.noinduk, p.nama, p.kampus";
        $filename = "data_absensi_harian_" . date('Y-m-d') . ".csv";
        break;
}

// Eksekusi query
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    // Pisahkan data berdasarkan kampus
    $dataCiputat = [];
    $dataKarawaci = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $kampus = strtolower(trim($row['kampus']));
        if ($kampus === 'ciputat') {
            $dataCiputat[] = $row;
        } elseif ($kampus === 'karawaci') {
            $dataKarawaci[] = $row;
        }
    }

    // Buat spreadsheet baru
    $spreadsheet = new Spreadsheet();
    
    // Function untuk membuat header dan styling
    function createSheetHeader($sheet, $kampusName, $date_info, $filter) {
        // Header utama
        $sheet->setCellValue('A1', 'HUMAN RESOURCE DEPARTMENT');
        $sheet->mergeCells('A1:Q1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A2', 'LAPORAN ABSENSI KARYAWAN');
        $sheet->mergeCells('A2:Q2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A3', 'PIMPINAN, DOSEN DAN KARYAWAN TETAP ITB AHMAD DAHLAN JAKARTA');
        $sheet->mergeCells('A3:Q3');
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A4', 'KAMPUS ' . strtoupper($kampusName));
        $sheet->mergeCells('A4:Q4');
        $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Info tanggal
        if (!empty($date_info)) {
            $sheet->setCellValue('A5', $date_info);
        } else {
            switch ($filter) {
                case 'harian':
                    $sheet->setCellValue('A5', 'Data Harian: ' . date('d F Y'));
                    break;
                case 'mingguan':
                    $sheet->setCellValue('A5', 'Data Mingguan: Minggu ' . date('W, Y'));
                    break;
                case 'bulanan':
                    $sheet->setCellValue('A5', 'Data Bulanan: ' . date('F Y'));
                    break;
            }
        }
        $sheet->mergeCells('A5:Q5');
        $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Header kolom - baris pertama
        $headers1 = [
            'A7' => 'No', 'B7' => 'NIK', 'C7' => 'Nama Karyawan',
            'D7' => 'Keterangan', 'H7' => 'Tepat Waktu', 'K7' => 'Tidak Tepat Waktu',
            'P7' => 'Jml. Hr. Krj'
        ];
        
        foreach ($headers1 as $cell => $value) {
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4CAF50');
            $sheet->getStyle($cell)->getFont()->getColor()->setRGB('FFFFFF');
        }
        
        // Merge cells untuk header grup
        $sheet->mergeCells('A7:A8'); // No
        $sheet->mergeCells('B7:B8'); // NIK
        $sheet->mergeCells('C7:C8'); // Nama Karyawan
        $sheet->mergeCells('D7:G7'); // Keterangan
        $sheet->mergeCells('H7:J7'); // Tepat Waktu
        $sheet->mergeCells('K7:O7'); // Tidak Tepat Waktu
        $sheet->mergeCells('P7:Q7'); // Jml. Hr. Krj
        
        // Header kolom - baris kedua
        $headers2 = [
            'D8' => 'S', 'E8' => 'I', 'F8' => 'A', 'G8' => 'CUTI',
            'H8' => 'Dtng', 'I8' => 'Plng', 'J8' => 'Jml',
            'K8' => 'TAD', 'L8' => 'TAP', 'M8' => 'Tlt.Dtg', 'N8' => 'Plg.Cpt', 'O8' => 'Jml',
            'P8' => 'Hari', 'Q8' => 'Hadir'
        ];
        
        foreach ($headers2 as $cell => $value) {
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4CAF50');
            $sheet->getStyle($cell)->getFont()->getColor()->setRGB('FFFFFF');
        }
        
        // Set border untuk header
        $sheet->getStyle('A7:Q8')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        return 9; // Return starting row for data
    }
    
    // Function untuk mengisi data
    function fillSheetData($sheet, $data, $startRow) {
        $row = $startRow;
        $no = 1;
        
        foreach ($data as $rowData) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $rowData['noinduk']);
            $sheet->setCellValue('C' . $row, $rowData['nama']);
            
            // Keterangan
            $sheet->setCellValue('D' . $row, $rowData['sakit']);
            $sheet->setCellValue('E' . $row, $rowData['izin']);
            $sheet->setCellValue('F' . $row, $rowData['alpha']);
            $sheet->setCellValue('G' . $row, $rowData['cuti']);
            
            // Tepat Waktu
            $sheet->setCellValue('H' . $row, $rowData['tepat_datang']);
            $sheet->setCellValue('I' . $row, $rowData['tepat_pulang']);
            $sheet->setCellValue('J' . $row, $rowData['tepat_jumlah']);
            
            // Tidak Tepat Waktu
            $sheet->setCellValue('K' . $row, $rowData['tidak_absen_datang']);
            $sheet->setCellValue('L' . $row, $rowData['tidak_absen_pulang']);
            $sheet->setCellValue('M' . $row, $rowData['telat_datang']);
            $sheet->setCellValue('N' . $row, $rowData['pulang_cepat']);
            $sheet->setCellValue('O' . $row, $rowData['tidak_tepat_jumlah']);
            
            // Hari kerja & kehadiran
            $sheet->setCellValue('P' . $row, $rowData['jumlah_hari_kerja']);
            $sheet->setCellValue('Q' . $row, $rowData['hadir']);
            
            // Style untuk baris data
            $sheet->getStyle('A' . $row . ':Q' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A' . $row . ':Q' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            
            // Bold untuk kolom jumlah
            $sheet->getStyle('J' . $row)->getFont()->setBold(true); // tepat_jumlah
            $sheet->getStyle('O' . $row)->getFont()->setBold(true); // tidak_tepat_jumlah
            $sheet->getStyle('Q' . $row)->getFont()->setBold(true); // hadir
            
            $row++;
        }
        
        return $row;
    }
    
    $sheetIndex = 0;
    
    // Sheet 1: Data Ciputat
    if (!empty($dataCiputat)) {
        if ($sheetIndex > 0) {
            $sheet = $spreadsheet->createSheet();
        } else {
            $sheet = $spreadsheet->getActiveSheet();
        }
        
        $sheet->setTitle('Kampus Ciputat');
        $startRow = createSheetHeader($sheet, 'Ciputat', $date_info, $filter);
        fillSheetData($sheet, $dataCiputat, $startRow);
        
        // Auto-size columns
        foreach (range('A', 'Q') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $sheetIndex++;
    }
    
    // Sheet 2: Data Karawaci
    if (!empty($dataKarawaci)) {
        if ($sheetIndex > 0) {
            $sheet = $spreadsheet->createSheet();
        } else {
            $sheet = $spreadsheet->getActiveSheet();
        }
        
        $sheet->setTitle('Kampus Karawaci');
        $startRow = createSheetHeader($sheet, 'Karawaci', $date_info, $filter);
        fillSheetData($sheet, $dataKarawaci, $startRow);
        
        // Auto-size columns
        foreach (range('A', 'Q') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $sheetIndex++;
    }
    
    // Jika tidak ada data sama sekali
    if (empty($dataCiputat) && empty($dataKarawaci)) {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('No Data');
        $sheet->setCellValue('A1', 'Tidak ada data untuk kedua kampus.');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getFont()->getColor()->setRGB('FF0000');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }
    
    // Set active sheet ke yang pertama
    $spreadsheet->setActiveSheetIndex(0);
    
    // Generate filename
    $filename = str_replace(".csv", ".xlsx", $filename);
    
    // Set headers untuk download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    // Write file
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    
    exit();
} else {
    echo "Data tidak ditemukan untuk filter ini.";
}
