<?php
session_start();
include('src/db/db_connect.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User  not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT keterangan FROM tb_detail WHERE id_pg = ? && tanggal = CURDATE()";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$status = 'Belum Absen';
$color = 'bg-red-500';

if ($row = $result->fetch_assoc()) {
    if ($row['keterangan'] === 'hadir') {
        $status = 'Sudah Absen';
        $color = 'bg-green-500';
    } elseif ($row['keterangan'] === 'alpha') {
        $status = 'Belum Absen';
        $color = 'bg-red-500';
    }
}

echo json_encode(['status' => 'success', 'message' => $status, 'color' => $color]);
?>