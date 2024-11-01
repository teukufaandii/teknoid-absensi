<?php
session_start();

if (!isset($_SESSION['token'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/../db_connect.php';

$query_telat = "SELECT COUNT(*) as total_telat FROM tb_detail WHERE scan_masuk >= '08:30:00' AND tanggal = CURDATE() AND keterangan = 'Hadir'";
$stmt_telat = $conn->prepare($query_telat);
if ($stmt_telat === false) {
    echo json_encode(['error' => 'Database query error']);
    exit();
}
$stmt_telat->execute();
$result_telat = $stmt_telat->get_result();
$count_telat = $result_telat->fetch_assoc();
$total_telat = $count_telat['total_telat'];

echo json_encode([
    'total_telat' => $total_telat
]);

$stmt_telat->close();
$conn->close();
?>