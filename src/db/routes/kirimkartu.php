<?php
date_default_timezone_set('Asia/Jakarta');

include "../db_connect.php";

// Mendapatkan nomor kartu dari parameter query
$nomor_kartu = $_GET['nomor_kartu'];

// Menghapus data dari tabel temprfid
mysqli_query($conn, "DELETE FROM temprfid");

// Mendefinisikan variabel waktu dan tanggal saat ini
$current_time = date("H:i:s");
$current_date = date("Y-m-d");

// Mengambil id_pg dan jabatan dari tb_pengguna berdasarkan nomor_kartu
$user_query = "SELECT id_pg, jabatan FROM tb_pengguna WHERE nomor_kartu = '$nomor_kartu'";
$user_result = mysqli_query($conn, $user_query);

if ($user_result && mysqli_num_rows($user_result) > 0) {
    // Jika nomor_kartu ditemukan di tb_pengguna
    $user_row = mysqli_fetch_assoc($user_result);
    $id_pg = $user_row['id_pg']; // Mendapatkan id_pg
    $jabatan = $user_row['jabatan']; // Mendapatkan jabatan

    // Memeriksa jika jabatan adalah "Dosen Tetap"
    if ($jabatan == 'Dosen Tetap FEB' || $jabatan == 'Dosen Tetap FTD'  ) {
        // Melanjutkan logika yang ada jika jabatan adalah Dosen Tetap
        $current_day = date("l"); // Mendapatkan hari dalam seminggu

        $check_query = "SELECT * FROM tb_detail WHERE nomor_kartu = '$nomor_kartu' AND DATE(tanggal) = '$current_date'";
        $check_result = mysqli_query($conn, $check_query);
        
        if ($check_result && mysqli_num_rows($check_result) > 0) {
            // Jika sudah ada data, periksa apakah scan_masuk ada
            $last_scan_row = mysqli_fetch_assoc($check_result);
            $scan_masuk = $last_scan_row['scan_masuk'];
            $scan_keluar = $last_scan_row['scan_keluar'];

            if (!is_null($scan_masuk) && !is_null($scan_keluar)) {
                // Jika kedua scan_masuk dan scan_keluar sudah ada
                echo "Anda sudah scan masuk dan keluar hari ini.";
                exit;
            } elseif (is_null($scan_masuk)) {
                // Jika scan_masuk masih null, perbarui scan_masuk
                $jam_kerja = ($current_day == 'Sunday') ? 'Libur' : 'Hari Kerja';
                $update_query = "UPDATE tb_detail SET scan_masuk = '$current_time', jam_kerja = '$jam_kerja', keterangan = 'hadir' WHERE nomor_kartu = '$nomor_kartu' AND DATE(tanggal) = '$current_date'";
                
                // Eksekusi query update
                $update_result = mysqli_query($conn, $update_query);

                if ($update_result) {
                    echo "Scan masuk berhasil.";
                } else {
                    echo "Gagal saat memperbarui waktu: " . mysqli_error($conn);
                }
            } else {
                // Jika scan_masuk ada, periksa waktu scan terakhir
                $last_scan_time = strtotime($last_scan_row['scan_masuk'] ?? $last_scan_row['scan_keluar']);
                $current_time_stamp = time();

                // Periksa apakah scan terakhir dalam waktu 150 menit terakhir
                if (($current_time_stamp - $last_scan_time) < 10) { // 9000 detik = 150 menit
                    echo "Anda harus menunggu 150 menit sebelum melakukan scan lagi.";
                    exit;
                } else {
                    // Perbarui scan_keluar jika scan_masuk sudah ada
                    $scan_keluar = $current_time; // format TIME
                    $update_query = "UPDATE tb_detail SET scan_keluar = '$scan_keluar' WHERE nomor_kartu = '$nomor_kartu' AND DATE(tanggal) = '$current_date'";
                    
                    if (mysqli_query($conn, $update_query)) {
                        // Menghitung durasi
                        $durasi_query = "SELECT TIMEDIFF('$scan_keluar', '$scan_masuk') AS durasi FROM tb_detail WHERE nomor_kartu = '$nomor_kartu' AND DATE(tanggal) = '$current_date'";
                        $durasi_result = mysqli_query($conn, $durasi_query);
                        $durasi_row = mysqli_fetch_assoc($durasi_result);
                        $durasi = $durasi_row['durasi'];

                        // Perbarui durasi
                        $durasi_update_query = "UPDATE tb_detail SET durasi = '$durasi' WHERE nomor_kartu = '$nomor_kartu' AND DATE(tanggal) = '$current_date'";
                        mysqli_query($conn, $durasi_update_query);

                        echo "Scan keluar berhasil. Durasi: $durasi.";
                    } else {
                        echo "Gagal saat memperbarui scan keluar: " . mysqli_error($conn);
                    }
                }
            }
        } else {
            // Jika tidak ada data untuk hari ini, masukkan scan_masuk baru
            $jam_kerja = ($current_day == 'Sunday') ? 'Libur' : 'Hari Kerja';
            $insert_query = "INSERT INTO tb_detail(nomor_kartu, id_pg, scan_masuk, tanggal, jam_kerja, durasi, keterangan) VALUES ('$nomor_kartu', '$id_pg', '$current_time', '$current_date', '$jam_kerja', NULL, 'hadir')";
            
            // Eksekusi query insert
            $insert_result = mysqli_query($conn, $insert_query);

            if ($insert_result) {
                echo "Scan masuk berhasil.";
            } else {
                echo "Gagal saat menyimpan waktu: " . mysqli_error($conn);
            }
        }
    } else if ($jabatan == 'Dosen Struktural' || $jabatan == 'Karyawan' || $jabatan == 'Pimpinan' || $jabatan == 'Customer Service' ) {
    // Proceed with the new logic for "Dosen Struktural"
    $cut_off_time = "12:00:00";

    // Check if an entry already exists for today in tb_detail
    $check_query = "SELECT * FROM tb_detail WHERE nomor_kartu = '$nomor_kartu' AND DATE(tanggal) = '$current_date'";
    $check_result = mysqli_query($conn, $check_query);
    
    if ($check_result && mysqli_num_rows($check_result) > 0) {
        // If entry exists, check if scan_masuk and scan_keluar exist
        $last_scan_row = mysqli_fetch_assoc($check_result);
        $scan_masuk = $last_scan_row['scan_masuk'];
        $scan_keluar = $last_scan_row['scan_keluar'];

        if (!is_null($scan_keluar)) {
            // If scan_keluar is already filled, show message
            echo "Anda sudah absen hari ini.";
            exit;
        }

        if (!is_null($scan_masuk) && is_null($scan_keluar)) {
            // If scan_masuk exists but scan_keluar is null, check if it's time to scan keluar
            if ($current_time >= $cut_off_time) {
                // Update scan_keluar if it's past 12:00:00
                $scan_keluar = $current_time; // format TIME
                $update_query = "UPDATE tb_detail SET scan_keluar = '$scan_keluar' WHERE nomor_kartu = '$nomor_kartu' AND DATE(tanggal) = '$current_date'";

                if (mysqli_query($conn, $update_query)) {
                    // Menghitung durasi
                    $durasi_query = "SELECT TIMEDIFF('$scan_keluar', '$scan_masuk') AS durasi FROM tb_detail WHERE nomor_kartu = '$nomor_kartu' AND DATE(tanggal) = '$current_date'";
                    $durasi_result = mysqli_query($conn, $durasi_query);
                    $durasi_row = mysqli_fetch_assoc($durasi_result);
                    $durasi = $durasi_row['durasi'];

                    // Update durasi
                    $durasi_update_query = "UPDATE tb_detail SET durasi = '$durasi' WHERE nomor_kartu = '$nomor_kartu' AND DATE(tanggal) = '$current_date'";
                    mysqli_query($conn, $durasi_update_query);

                    echo "Scan keluar berhasil. Durasi: $durasi.";
                } else {
                    echo "Gagal saat memperbarui scan keluar: " . mysqli_error($conn);
                }
            } else {
                // If it's before 12:00:00, show message for scan_masuk
                echo "Waktu untuk scan keluar belum tiba, harus menunggu lebih dari jam 12 siang.";
                exit;
            }
        } elseif (is_null($scan_masuk)) {
            // If scan_masuk is null, insert scan_keluar only
            if ($current_time >= $cut_off_time) {
                $scan_keluar = $current_time; // format TIME
                $insert_query_keluar = "INSERT INTO tb_detail(nomor_kartu, id_pg, scan_masuk, tanggal, jam_kerja, durasi, keterangan) VALUES ('$nomor_kartu', '$id_pg', NULL, '$current_date', 'Hari Kerja', NULL, 'hadir')";
                $update_query_keluar = "UPDATE tb_detail SET scan_keluar = '$scan_keluar' WHERE nomor_kartu = '$nomor_kartu' AND DATE(tanggal) = '$current_date'";

                if (mysqli_query($conn, $insert_query_keluar) && mysqli_query($conn, $update_query_keluar)) {
                    echo "Scan keluar berhasil, scan masuk masih kosong.";
                } else {
                    echo "Gagal saat menyimpan scan keluar: " . mysqli_error($conn);
                }
            }
        }
    } else {
        // If no entry exists for today, insert new scan_masuk if time is before 12:00:00
        if ($current_time < $cut_off_time) {
            // If scan_masuk already exists for the day, show message that the user has already scanned in
            if (!is_null($scan_masuk)) {
                echo "Anda sudah scan masuk hari ini.";
                exit;
            }

            $jam_kerja = 'Hari Kerja'; // For Dosen Struktural, assume it's a workday
            $insert_query = "INSERT INTO tb_detail(nomor_kartu, id_pg, scan_masuk, tanggal, jam_kerja, durasi, keterangan) VALUES ('$nomor_kartu', '$id_pg', '$current_time', '$current_date', '$jam_kerja', NULL, 'hadir')";
            
            // Execute the insert query
            $insert_result = mysqli_query($conn, $insert_query);

            if ($insert_result) {
                echo "Scan masuk berhasil.";
            } else {
                echo "Gagal saat menyimpan waktu: " . mysqli_error($conn);
            }
        } else {
            // If it's after 12:00:00 and scan_masuk is still missing, insert scan_keluar
            $scan_keluar = $current_time; // format TIME
            $insert_query_keluar = "INSERT INTO tb_detail(nomor_kartu, id_pg, scan_masuk, tanggal, jam_kerja, durasi, keterangan) VALUES ('$nomor_kartu', '$id_pg', NULL, '$current_date', 'Hari Kerja', NULL, 'hadir')";
            $update_query_keluar = "UPDATE tb_detail SET scan_keluar = '$scan_keluar' WHERE nomor_kartu = '$nomor_kartu' AND DATE(tanggal) = '$current_date'";

            if (mysqli_query($conn, $insert_query_keluar) && mysqli_query($conn, $update_query_keluar)) {
                echo "Scan keluar berhasil, scan masuk masih kosong.";
            } else {
                echo "Gagal saat menyimpan scan keluar: " . mysqli_error($conn);
            }
        }
    }
}


} else {
    // Jika nomor_kartu tidak ditemukan di tb_pengguna, periksa tb_anonim
    $check_anonim_query = "SELECT * FROM tb_anonim WHERE nomor_kartu = '$nomor_kartu'";
    $anonim_result = mysqli_query($conn, $check_anonim_query);

    if ($anonim_result && mysqli_num_rows($anonim_result) > 0) {
        // Jika nomor_kartu ditemukan di tb_anonim, perbarui jam dan tanggal
        $update_anonim_query = "UPDATE tb_anonim SET jam = '$current_time', tanggal = '$current_date' WHERE nomor_kartu = '$nomor_kartu'";
        mysqli_query($conn, $update_anonim_query);
        echo "Nomor kartu tidak terdapat pada database, silahkan perbarui data.";
    } else {
        // Jika nomor_kartu tidak ditemukan di tb_anonim, masukkan data baru
        $insert_anonim_query = "INSERT INTO tb_anonim(nomor_kartu, jam, tanggal) VALUES ('$nomor_kartu', '$current_time', '$current_date')";
        $anonim_result = mysqli_query($conn, $insert_anonim_query);
        
        if ($anonim_result) {
            echo "Nomor kartu tidak terdaftar, data disimpan di tb_anonim.";
        } else {
            echo "Gagal menyimpan data ke tb_anonim: " . mysqli_error($conn);
        }
    }
}

// Menutup koneksi database
mysqli_close($conn);
?>
