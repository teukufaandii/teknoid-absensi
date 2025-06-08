<?php
session_start();
include 'src/db/db_connect.php';

if (!isset($_SESSION['token']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'unauthorized']);
    exit();
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$rows_per_page = 5;

$searchPattern = '%' . $search . '%';

// Hitung total data libur dengan filter pencarian
$total_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tb_dayoff 
    WHERE nama_libur LIKE ? OR tanggal_mulai LIKE ? OR tanggal_akhir LIKE ?");
$total_stmt->bind_param("sss", $searchPattern, $searchPattern, $searchPattern);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_holidays = $total_row['total'];
$total_stmt->close();

// Ambil data libur dengan filter pencarian dan limit
$stmt = $conn->prepare("SELECT * FROM tb_dayoff 
    WHERE nama_libur LIKE ? OR tanggal_mulai LIKE ? OR tanggal_akhir LIKE ? 
    LIMIT ?, ?");
$stmt->bind_param("ssssi", $searchPattern, $searchPattern, $searchPattern, $start, $rows_per_page);
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
