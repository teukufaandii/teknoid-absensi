<?php
include 'src/db/db_connect.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$limit = 10;

$query = "
    SELECT 
        p.*,
        SUM(CASE WHEN d.keterangan = 'sakit' THEN 1 ELSE 0 END) AS sakit,
        SUM(CASE WHEN d.keterangan = 'izin' THEN 1 ELSE 0 END) AS izin,
        SUM(CASE WHEN d.keterangan = 'alpha' THEN 1 ELSE 0 END) AS alpha,
        SUM(CASE WHEN d.keterangan = 'cuti' THEN 1 ELSE 0 END) AS cuti,
        SUM(CASE WHEN d.keterangan = 'hadir' THEN 1 ELSE 0 END) AS hadir
    FROM tb_pengguna p
    LEFT JOIN tb_detail d ON p.id_pg = d.id_pg 
        AND MONTH(d.tanggal) = MONTH(CURRENT_DATE()) 
        AND YEAR(d.tanggal) = YEAR(CURRENT_DATE())
    WHERE (p.nama LIKE ? OR p.noinduk LIKE ? OR p.jabatan LIKE ?)
        AND p.role = ?
    GROUP BY p.id_pg
    LIMIT ?, ?
";

$stmt = $conn->prepare($query);
$searchPattern = '%' . $search . '%';
$role = 'user';
$stmt->bind_param('ssssii', $searchPattern, $searchPattern, $searchPattern, $role, $start, $limit);
$stmt->execute();
$result = $stmt->get_result();

$data_absensi = [];
while ($row = $result->fetch_assoc()) {
    $data_absensi[] = $row;
}

$totalQuery = "SELECT COUNT(*) as total FROM tb_pengguna WHERE (nama LIKE ? OR noinduk LIKE ? OR jabatan LIKE ?) AND role = ?";
$totalStmt = $conn->prepare($totalQuery);
$totalStmt->bind_param('ssss', $searchPattern, $searchPattern, $searchPattern, $role);
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalData = $totalResult->fetch_assoc()['total'];

echo json_encode([
    'status' => 'success',
    'data_absensi' => $data_absensi,
    'total' => $totalData
]);
