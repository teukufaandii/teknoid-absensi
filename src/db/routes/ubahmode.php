<?php
include "../db_connect.php";

$mode = mysqli_query($conn, "select * from status");
$data_mode = mysqli_fetch_array($mode);
$mode_absen = $data_mode['mode'];

$mode_absen = $mode_absen + 1;
if($mode_absen > 2)
$mode_absen = 1;

$simpan = mysqli_query($conn, "update status set mode='$mode_absen'");
if ($simpan) {
    echo "Berhasil";
} else {
    echo "Gagal";
}
?>