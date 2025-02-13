<?php
session_start();

if (!isset($_SESSION['token'])) {
    header('Location: login.php');
    exit();
}

include 'src/db/db_connect.php';

// Query untuk Card (Bulan Ini)
$queryCard = "
    SELECT 
        COUNT(CASE WHEN keterangan = 'hadir' THEN 1 END) AS total_hadir,
        COUNT(CASE WHEN keterangan = 'sakit' THEN 1 END) AS total_sakit,
        COUNT(CASE WHEN keterangan = 'izin' THEN 1 END) AS total_izin
    FROM tb_detail
    WHERE id_pg = ? 
        AND YEAR(tanggal) = YEAR(CURDATE()) 
        AND MONTH(tanggal) = MONTH(CURDATE());
";

$stmtCard = $conn->prepare($queryCard);
if (!$stmtCard) {
    echo json_encode(['error' => 'Database query error']);
    exit();
}
$stmtCard->bind_param('s', $_SESSION['user_id']);
$stmtCard->execute();
$resultCard = $stmtCard->get_result()->fetch_assoc();

// Query untuk Chart (Tahun Ini, 12 Bulan)
$queryChart = "
    SELECT 
        MONTH(tanggal) AS bulan,
        COUNT(CASE WHEN keterangan = 'hadir' THEN 1 END) AS total_hadir,
        COUNT(CASE WHEN keterangan = 'sakit' THEN 1 END) AS total_sakit,
        COUNT(CASE WHEN keterangan = 'izin' THEN 1 END) AS total_izin
    FROM tb_detail
    WHERE id_pg = ? 
        AND YEAR(tanggal) = YEAR(CURDATE()) 
    GROUP BY MONTH(tanggal);
";

$stmtChart = $conn->prepare($queryChart);
if (!$stmtChart) {
    echo json_encode(['error' => 'Database query error']);
    exit();
}
$stmtChart->bind_param('s', $_SESSION['user_id']);
$stmtChart->execute();
$resultChart = $stmtChart->get_result();

$dataChart = array_fill(1, 12, ['total_hadir' => 0, 'total_sakit' => 0, 'total_izin' => 0]); // Default 12 bulan kosong
while ($row = $resultChart->fetch_assoc()) {
    $bulan = (int) $row['bulan'];
    $dataChart[$bulan] = [
        'total_hadir' => $row['total_hadir'],
        'total_sakit' => $row['total_sakit'],
        'total_izin' => $row['total_izin']
    ];
}

echo json_encode([
    'card' => $resultCard,
    'chart' => $dataChart
]);

$stmtCard->close();
$stmtChart->close();
$conn->close();
