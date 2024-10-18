<?php
session_start();
include '../../db/db_connect.php';

if (!isset($_SESSION['token']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'unauthorized']);
    exit();
}

$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$rows_per_page = 5;

// Get total count of holidays
$total_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tb_dayoff");
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_holidays = $total_row['total'];

$stmt = $conn->prepare("SELECT * FROM tb_dayoff LIMIT ?, ?");
$stmt->bind_param("ii", $start, $rows_per_page);
$stmt->execute();
$result = $stmt->get_result();

$holidays = [];
while ($row = $result->fetch_assoc()) {
    $holidays[] = [
        'id' => htmlspecialchars($row["id"]),
        'nama_libur' => htmlspecialchars($row["nama_libur"]),
        'tanggal_mulai' => htmlspecialchars($row["tanggal_mulai"]),
        'tanggal_akhir' => htmlspecialchars($row["tanggal_akhir"]),
    ];
}

$stmt->close();
$conn->close();

echo json_encode([
    'status' => 'success',
    'holidays' => $holidays,
    'total' => $total_holidays
]);

?>
