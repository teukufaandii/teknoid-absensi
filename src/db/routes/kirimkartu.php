<?php
date_default_timezone_set('Asia/Jakarta');

include "../db_connect.php";

// Get the card number from the query parameter
$nomor_kartu = $_GET['nomor_kartu'];

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
            echo "Data untuk hari ini sudah ada.";
        } else {
            // Determine if it's before or after 12 PM
            if (strtotime($current_time) < strtotime("12:00:00")) {
                // Insert into tb_detail scan_masuk
                $jam_kerja = ($current_day == 'Sunday') ? 'Libur' : 'Hari Kerja';
                $insert_query = "INSERT INTO tb_detail(nomor_kartu, id_pg, scan_masuk, tanggal, jam_kerja, durasi) VALUES ('$nomor_kartu', '$id_pg', '$current_date $current_time', '$current_date', '$jam_kerja', NULL)";
            } else {
                // Insert into tb_detail scan_keluar
                $jam_kerja = ($current_day == 'Sunday') ? 'Libur' : 'Hari Kerja';
                $insert_query = "INSERT INTO tb_detail(nomor_kartu, id_pg, scan_keluar, tanggal, jam_kerja, durasi) VALUES ('$nomor_kartu', '$id_pg', '$current_date $current_time', '$current_date', '$jam_kerja', NULL)";
            }

            // Execute the insert query
            $insert_result = mysqli_query($conn, $insert_query);

            if ($insert_result) {
                // Calculate duration if both entries exist
                $duration_query = "SELECT scan_masuk, scan_keluar FROM tb_detail WHERE nomor_kartu = '$nomor_kartu' AND DATE(tanggal) = '$current_date'";
                $duration_result = mysqli_query($conn, $duration_query);
                
                if ($duration_result && mysqli_num_rows($duration_result) == 2) {
                    $duration_row = mysqli_fetch_assoc($duration_result);
                    $start_time = strtotime($duration_row['scan_masuk']);
                    $end_time = strtotime($duration_row['scan_keluar']);
                    
                    if ($end_time > $start_time) { // Ensure end time is after start time
                        $duration = ($end_time - $start_time) / 3600; // Duration in hours
                        $update_duration_query = "UPDATE tb_detail SET durasi = '$duration' WHERE nomor_kartu = '$nomor_kartu' AND DATE(tanggal) = '$current_date'";
                        mysqli_query($conn, $update_duration_query);
                    }
                }
                echo "Berhasil";
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
