<?php
session_start();

if (!isset($_SESSION['token'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/../db_connect.php';

$query = "SELECT COUNT(*) as total FROM tb_detail WHERE keterangan = 'hadir' AND MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE()) AND scan_masuk IS NOT NULL";
$stmt = $conn->prepare($query);
if ($stmt === false) {
    echo json_encode(['error' => 'Database query error']);
    exit();
}

$stmt->execute();
$result = $stmt->get_result();
$count = $result->fetch_assoc();
echo json_encode(['total' => $count['total']]);

$stmt->close();
$conn->close();
