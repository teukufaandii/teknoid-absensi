<?php
include 'src/db/db_connect.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$limit = 5;

$query = "SELECT * FROM tb_pengguna WHERE (nama LIKE ? OR noinduk LIKE ?) AND role = ? LIMIT ?, ?";
$stmt = $conn->prepare($query);
$searchPattern = '%' . $search . '%';
$role = 'user';
$stmt->bind_param('ssssi', $searchPattern, $searchPattern, $role, $start, $limit);
$stmt->execute();
$result = $stmt->get_result();

$data_absensi = [];
while ($row = $result->fetch_assoc()) {
    $data_absensi[] = $row;
}

$totalQuery = "SELECT COUNT(*) as total FROM tb_pengguna WHERE (nama LIKE ? OR noinduk LIKE ?) AND role = ?";
$totalStmt = $conn->prepare($totalQuery);
$totalStmt->bind_param('ssi', $searchPattern, $searchPattern, $role);
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalData = $totalResult->fetch_assoc()['total'];

echo json_encode([
    'status' => 'success',
    'data_absensi' => $data_absensi,
    'total' => $totalData
]);
