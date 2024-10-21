<?php
session_start();
include '../../db/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['token']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'unauthorized']);
    exit();
}

$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$rows_per_page = 5;

// Get total count of data_absensi
$total_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tb_pengguna");
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_data_absensi = $total_row['total'];

$stmt = $conn->prepare("SELECT * FROM tb_pengguna LIMIT ?, ?");
$stmt->bind_param("ii", $start, $rows_per_page);
$stmt->execute();
$result = $stmt->get_result();

$data_absensi = [];
while ($row = $result->fetch_assoc()) {
    $data_absensi[] = [
        'id_pg' => htmlspecialchars($row["id_pg"]),
        'noinduk' => htmlspecialchars($row["noinduk"]),
        'nama' => htmlspecialchars($row["nama"]),
        'role' => htmlspecialchars($row["role"]),
    ];
}

$stmt->close();
$conn->close();

echo json_encode([
    'status' => 'success',
    'data_absensi' => $data_absensi,
    'total' => $total_data_absensi
]);

?>
