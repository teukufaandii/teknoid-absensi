<?php
date_default_timezone_set('Asia/Jakarta');

include "../db_connect.php";

// Get the card number from the query parameter
$nomor_kartu = $_GET['nomor_kartu'];

// Function to generate random ID
function generateRandomId($length = 20) {
    return substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', $length)), 0, $length);
}

// Clear the temporary RFID table
mysqli_query($conn, "DELETE FROM temprfid");

// Insert the card number into the temporary RFID table
$simpan = mysqli_query($conn, "INSERT INTO temprfid(nomor_kartu) VALUES ('$nomor_kartu')");

if ($simpan) {
    // Get the current time and date
    $current_time = date("H:i:s");
    $current_date = date("Y-m-d");
    $current_day = date("l"); // Get the day of the week

    // Retrieve id_pg from tb_pengguna based on nomor_kartu
    $user_query = "SELECT id_pg FROM tb_pengguna WHERE nomor_kartu = '$nomor_kartu'";
    $user_result = mysqli_query($conn, $user_query);

    if ($user_result && mysqli_num_rows($user_result) > 0) {
        // If nomor_kartu is found in tb_pengguna
        $user_row = mysqli_fetch_assoc($user_result);
        $id_pg = $user_row['id_pg']; // Get the id_pg

        // Check if an entry already exists for today in tb_detail
        $check_query = "SELECT * FROM tb_detail WHERE nomor_kartu = '$nomor_kartu' AND DATE(tanggal) = '$current_date'";
        $check_result = mysqli_query($conn, $check_query);
        
        if ($check_result && mysqli_num_rows($check_result) > 0) {
            // Check the last scan time
            $last_scan_row = mysqli_fetch_assoc($check_result);
            $scan_masuk = $last_scan_row['scan_masuk'];

            // Check if the last scan was within the last 30 minutes
            $last_scan_time = strtotime($last_scan_row['scan_masuk'] ?? $last_scan_row['scan_keluar']);
            $current_time_stamp = time();

            // Check if the last scan was within the last 30 minutes
            if (($current_time_stamp - $last_scan_time) < 9000) { // 9000 seconds = 150 minutes
                echo "Anda harus menunggu 150 menit sebelum melakukan scan lagi.";
                exit;
            } else {
                // Update scan_keluar if scan_masuk already exists
                $scan_keluar = $current_time; // format TIME
                $update_query = "UPDATE tb_detail SET scan_keluar = '$scan_keluar' WHERE nomor_kartu = '$nomor_kartu' AND DATE(tanggal) = '$current_date'";
                
                if (mysqli_query($conn, $update_query)) {
                    // Menghitung durasi
                    $durasi_query = "SELECT TIMEDIFF('$scan_keluar', '$scan_masuk') AS durasi";
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
            }
        } else {
            // If no entry exists for today, insert new scan_masuk
            $randomId = generateRandomId(20);
            $jam_kerja = ($current_day == 'Sunday') ? 'Libur' : 'Hari Kerja';
            $insert_query = "INSERT INTO tb_detail(id, nomor_kartu, id_pg, scan_masuk, tanggal, jam_kerja, durasi, keterangan) VALUES ('$randomId', '$nomor_kartu', '$id_pg', '$current_time', '$current_date', '$jam_kerja', NULL, 'hadir')";
            
            // Execute the insert query
            $insert_result = mysqli_query($conn, $insert_query);

            if ($insert_result) {
                echo "Scan masuk berhasil.";
            } else {
                echo "Gagal saat menyimpan waktu: " . mysqli_error($conn);
            }
        }
    } else {
        // If nomor_kartu is not found in tb_pengguna, check tb_anonim
        $check_anonim_query = "SELECT * FROM tb_anonim WHERE nomor_kartu = '$nomor_kartu'";
        $anonim_result = mysqli_query($conn, $check_anonim_query);

        if ($anonim_result && mysqli_num_rows($anonim_result) > 0) {
            // If nomor_kartu exists in tb_anonim, update jam and tanggal
            $update_anonim_query = "UPDATE tb_anonim SET jam = '$current_time', tanggal = '$current_date' WHERE nomor_kartu = '$nomor_kartu'";
            mysqli_query($conn, $update_anonim_query);
            echo "Nomor kartu tidak terdapat pada database, silahkan perbarui data.";
        } else {
            // If nomor_kartu does not exist in tb_anonim, insert it
            $insert_anonim_query = "INSERT INTO tb_anonim(nomor_kartu, jam, tanggal) VALUES ('$nomor_kartu', '$current_time', '$current_date')";
            $anonim_result = mysqli_query($conn, $insert_anonim_query);
            
            if ($anonim_result) {
                echo "Nomor kartu tidak terdaftar, data disimpan di tb_anonim.";
            } else {
                echo "Gagal menyimpan data ke tb_anonim: " . mysqli_error($conn);
            }
        }
    }
} else {
    echo "Gagal saat menyimpan nomor kartu: " . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);
?>
