<?php
date_default_timezone_set('Asia/Jakarta');

include "../db_connect.php";

// Mendapatkan nomor kartu dari parameter query
$nomor_kartu = $_GET['nomor_kartu'];

// Menghapus data dari tabel temprfid
mysqli_query($conn, "DELETE FROM temprfid");

//$current_time = date("H:i:s");
// $current_date = date("Y-m-d");
$current_day = date("l");

//dummy data untuk testing
$current_time = "17:00:00";
$current_date = "2025-07-14";
$current_day = date("l", strtotime($current_date));


// Jika hari Minggu, tidak memproses absen
if ($current_day == 'Sunday') {
    echo "Ini hari Minggu.";
    mysqli_close($conn);
    exit;
}

// Mengambil id_pg dan jabatan dari tb_pengguna berdasarkan nomor_kartu
$user_query = "SELECT id_pg, jabatan FROM tb_pengguna WHERE nomor_kartu = '$nomor_kartu'";
$user_result = mysqli_query($conn, $user_query);

if ($user_result && mysqli_num_rows($user_result) > 0) {
    $user_row = mysqli_fetch_assoc($user_result);
    $id_pg = $user_row['id_pg'];
    $jabatan = $user_row['jabatan'];

    if ($jabatan == 'Dosen Tetap FEB' || $jabatan == 'Dosen Tetap FTD') {
        $check_query = "SELECT * FROM tb_detail WHERE nomor_kartu = '$nomor_kartu' AND DATE(tanggal) = '$current_date'";
        $check_result = mysqli_query($conn, $check_query);

        if ($check_result && mysqli_num_rows($check_result) > 0) {
            $last_scan_row = mysqli_fetch_assoc($check_result);
            $scan_masuk = $last_scan_row['scan_masuk'];
            $scan_keluar = $last_scan_row['scan_keluar'];

            if (!is_null($scan_keluar)) {
                echo "Anda sudah scan masuk dan keluar hari ini.";
                exit;
            } elseif (is_null($scan_masuk)) {
                $jam_kerja = 'Hari Kerja';
                $update_query = "UPDATE tb_detail SET scan_masuk = '$current_time', jam_kerja = '$jam_kerja', keterangan = 'hadir' WHERE nomor_kartu = '$nomor_kartu' AND DATE(tanggal) = '$current_date'";
                $update_result = mysqli_query($conn, $update_query);

                if ($update_result) {
                    echo "Scan masuk berhasil.";
                } else {
                    echo "Gagal saat memperbarui waktu: " . mysqli_error($conn);
                }
            } else {
                if (!is_null($last_scan_row['scan_masuk'])) {
                    $last_scan_time = strtotime($last_scan_row['scan_masuk']);
                    $current_time_stamp = strtotime($current_time);

                    $selisih_waktu = ($current_time_stamp - $last_scan_time) / 60;

                    if ($selisih_waktu < 150) {
                        echo "Anda harus menunggu 150 menit sebelum melakukan scan lagi.";
                        exit;
                    }
                }
                // lanjutkan proses scan keluar jika lolos pengecekan waktu
                $scan_keluar = $current_time;
                $update_query = "UPDATE tb_detail SET scan_keluar = '$scan_keluar' WHERE nomor_kartu = '$nomor_kartu' AND DATE(tanggal) = '$current_date'";

                if (mysqli_query($conn, $update_query)) {
                    $durasi_query = "SELECT TIMEDIFF('$scan_keluar', '$scan_masuk') AS durasi FROM tb_detail WHERE nomor_kartu = '$nomor_kartu' AND DATE(tanggal) = '$current_date'";
                    $durasi_result = mysqli_query($conn, $durasi_query);
                    $durasi_row = mysqli_fetch_assoc($durasi_result);
                    $durasi = $durasi_row['durasi'];

                    $durasi_update_query = "UPDATE tb_detail SET durasi = '$durasi' WHERE nomor_kartu = '$nomor_kartu' AND DATE(tanggal) = '$current_date'";
                    mysqli_query($conn, $durasi_update_query);

                    echo "Scan keluar berhasil. Durasi: $durasi.";
                } else {
                    echo "Gagal saat memperbarui scan keluar: " . mysqli_error($conn);
                }
            }
        } //hapus bagian  ini jika tidak ingin menggunakan insert untuk scan masuk
        else {                
            $details_id = 'details_' . uniqid();
            $jam_kerja = 'Hari Kerja';
            $insert_query = "INSERT INTO tb_detail(id, nomor_kartu, id_pg, scan_masuk, tanggal, jam_kerja, durasi, keterangan) 
                            VALUES ('$details_id','$nomor_kartu', '$id_pg', '$current_time', '$current_date', '$jam_kerja', NULL, 'hadir')";
            $insert_result = mysqli_query($conn, $insert_query);

            if ($insert_result) {
                echo "Scan masuk berhasil.";
            } else {
                echo "Gagal saat menyimpan waktu: " . mysqli_error($conn);
            }
        }
        //sampai sini


        
        //untuk pengaturan hari dosen struktural, karyawan, pimpinan, dan cs
    } elseif ($jabatan == 'Dosen Struktural' || $jabatan == 'Karyawan' || $jabatan == 'Pimpinan' || $jabatan == 'Cleaning Service') {
        $cut_off_time = "12:00:00";

        $check_query = "SELECT * FROM tb_detail WHERE nomor_kartu = '$nomor_kartu' AND DATE(tanggal) = '$current_date'";
        $check_result = mysqli_query($conn, $check_query);

        if ($check_result && mysqli_num_rows($check_result) > 0) {
            $last_scan_row = mysqli_fetch_assoc($check_result);
            $scan_masuk = $last_scan_row['scan_masuk'];
            $scan_keluar = $last_scan_row['scan_keluar'];

            if (!is_null($scan_keluar)) {
                echo "Anda sudah absen hari ini.";
                exit;
            } elseif (is_null($scan_masuk) && $current_time < $cut_off_time) {
                $jam_kerja = 'Hari Kerja';
                $update_query = "UPDATE tb_detail SET scan_masuk = '$current_time', jam_kerja = '$jam_kerja', keterangan = 'hadir' WHERE nomor_kartu = '$nomor_kartu' AND DATE(tanggal) = '$current_date'";
                $update_result = mysqli_query($conn, $update_query);

                if ($update_result) {
                    echo "Scan masuk berhasil.";
                } else {
                    echo "Gagal saat memperbarui waktu: " . mysqli_error($conn);
                }
            } elseif (!is_null($scan_masuk) && is_null($scan_keluar)) {
                if ($current_time >= $cut_off_time) {
                    $scan_keluar = $current_time;
                    $update_query = "UPDATE tb_detail SET scan_keluar = '$scan_keluar' WHERE nomor_kartu = '$nomor_kartu' AND DATE(tanggal) = '$current_date'";

                    if (mysqli_query($conn, $update_query)) {
                        $durasi_query = "SELECT TIMEDIFF('$scan_keluar', '$scan_masuk') AS durasi FROM tb_detail WHERE nomor_kartu = '$nomor_kartu' AND DATE(tanggal) = '$current_date'";
                        $durasi_result = mysqli_query($conn, $durasi_query);
                        $durasi_row = mysqli_fetch_assoc($durasi_result);
                        $durasi = $durasi_row['durasi'];

                        $durasi_update_query = "UPDATE tb_detail SET durasi = '$durasi' WHERE nomor_kartu = '$nomor_kartu' AND DATE(tanggal) = '$current_date'";
                        mysqli_query($conn, $durasi_update_query);

                        echo "Scan keluar berhasil. Durasi: $durasi.";
                    } else {
                        echo "Gagal saat memperbarui scan keluar: " . mysqli_error($conn);
                    }
                } else {
                    echo "Waktu untuk scan keluar belum tiba, harus menunggu lebih dari jam 12 siang.";
                    exit;
                }
            } elseif (is_null($scan_masuk) && $current_time >= $cut_off_time) {
                $scan_keluar = $current_time;
                $update_query_keluar = "UPDATE tb_detail SET scan_keluar = '$scan_keluar', keterangan = 'hadir', jam_kerja = 'Hari Kerja' 
                                                WHERE nomor_kartu = '$nomor_kartu' AND DATE(tanggal) = '$current_date'";

                if (mysqli_query($conn, $update_query_keluar)) {
                    echo "Scan keluar berhasil, scan masuk masih kosong.";
                } else {
                    echo "Gagal saat menyimpan scan keluar: " . mysqli_error($conn);
                }
            }
        } else {
            $check_masuk_query = "SELECT scan_masuk FROM tb_detail WHERE nomor_kartu = '$nomor_kartu' AND DATE(tanggal) = '$current_date'";
            $check_masuk_result = mysqli_query($conn, $check_masuk_query);
            $scan_masuk = null;

            if ($check_masuk_result && mysqli_num_rows($check_masuk_result) > 0) {
                $row = mysqli_fetch_assoc($check_masuk_result);
                if (!is_null($row['scan_masuk'])) {
                    $scan_masuk = $row['scan_masuk'];
                }
            }

            if ($current_time < $cut_off_time) {
                if (!is_null($scan_masuk)) {
                    echo "Anda sudah scan masuk hari ini.";
                    exit;
                }
                //hapus bagian  ini jika tidak ingin menggunakan insert untuk scan masuk
                $details_id = 'details_' . uniqid();
                $jam_kerja = 'Hari Kerja';
                $insert_query = "INSERT INTO tb_detail(id, nomor_kartu, id_pg, scan_masuk, tanggal, jam_kerja, durasi, keterangan) 
                                    VALUES ('$details_id','$nomor_kartu', '$id_pg', '$current_time', '$current_date', '$jam_kerja', NULL, 'hadir')";
                $insert_result = mysqli_query($conn, $insert_query);

                if ($insert_result) {
                    echo "Scan masuk berhasil.";
                } else {
                    echo "Gagal saat menyimpan waktu: " . mysqli_error($conn);
                }
                //sampai sini 
            } else {
                $details_id = 'details_' . uniqid();
                $scan_keluar = $current_time;
                $insert_query_keluar = "INSERT INTO tb_detail(id, nomor_kartu, id_pg, scan_masuk, tanggal, jam_kerja, durasi, keterangan) 
                                            VALUES ('$details_id','$nomor_kartu', '$id_pg', NULL, '$current_date', 'Hari Kerja', NULL, 'hadir')";
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
    $check_anonim_query = "SELECT * FROM tb_anonim WHERE nomor_kartu = '$nomor_kartu'";
    $anonim_result = mysqli_query($conn, $check_anonim_query);

    if ($anonim_result && mysqli_num_rows($anonim_result) > 0) {
        $update_anonim_query = "UPDATE tb_anonim SET jam = '$current_time', tanggal = '$current_date' WHERE nomor_kartu = '$nomor_kartu'";
        mysqli_query($conn, $update_anonim_query);
        echo "Nomor kartu tidak terdapat pada database, silahkan perbarui data.";
    } else {
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
