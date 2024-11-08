<?php
include 'src/db/db_connect.php';

// Ambil query pencarian dari GET request
$search = isset($_GET['search']) ? $_GET['search'] : '';
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$limit = 5;

// Query untuk pencarian, menggunakan LIKE untuk mencari kata kunci
$query = "SELECT nomor_kartu, jam, tanggal, id FROM tb_anonim WHERE nomor_kartu LIKE ? OR jam LIKE ? OR tanggal LIKE ? LIMIT ?, ?";
$stmt = $conn->prepare($query);
$searchPattern = '%' . $search . '%';
$stmt->bind_param('ssiii', $searchPattern, $searchPattern, $searchPattern, $start, $limit);
$stmt->execute();
$result = $stmt->get_result();

// Simpan data yang diambil dalam array
$data_anonim = [];
while ($row = $result->fetch_assoc()) {
    $data_anonim[] = $row;
}

// Ambil total data
$totalQuery = "SELECT COUNT(*) as total FROM tb_anonim WHERE nomor_kartu LIKE ? OR jam LIKE ? OR tanggal LIKE ?";
$totalStmt = $conn->prepare($totalQuery);
$totalStmt->bind_param('sss', $searchPattern, $searchPattern, $searchPattern);
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalData = $totalResult->fetch_assoc()['total'];

// Kirim hasil pencarian sebagai JSON
echo json_encode([
    'status' => 'success',
    'data_anonim' => $data_anonim,
    'total' => $totalData
]);

$conn->close();
?>
