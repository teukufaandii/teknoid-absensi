<?php
session_start();

if (!isset($_SESSION['token'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/../db_connect.php';

$query = "
    SELECT 
        MONTH(tanggal) AS bulan, 
        COUNT(CASE WHEN keterangan = 'Alpha' THEN 1 END) AS total_alpha,
        COUNT(CASE WHEN keterangan = 'hadir' AND scan_masuk IS NOT NULL THEN 1 END) AS total_hadir,
        COUNT(CASE WHEN keterangan = 'hadir' AND scan_masuk >= '08:30:00' THEN 1 END) AS total_telat
    FROM tb_detail
    WHERE YEAR(tanggal) = YEAR(CURDATE())
    GROUP BY bulan
    ORDER BY bulan
";

$stmt = $conn->prepare($query);
if ($stmt === false) {
    echo json_encode(['error' => 'Database query error']);
    exit();
}

$stmt->execute();
$result = $stmt->get_result();
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[(int)$row['bulan']] = [
        'alpha' => (int)$row['total_alpha'],
        'hadir' => (int)$row['total_hadir'],
        'telat' => (int)$row['total_telat']
    ];
}

// Buat array dengan semua bulan (jika ada bulan tanpa data, isi 0)
$finalData = [];
for ($i = 1; $i <= 12; $i++) {
    $finalData[$i] = $data[$i] ?? ['alpha' => 0, 'hadir' => 0, 'telat' => 0];
}

echo json_encode(['chart' => $finalData]);

$stmt->close();
$conn->close();
