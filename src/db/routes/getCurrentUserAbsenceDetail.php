<?php
session_start();

if (!isset($_SESSION['token'])) {
    header('Location: login.php');
    exit();
}

include 'src/db/db_connect.php';

$query = "
    SELECT 
        COUNT(CASE WHEN keterangan = 'hadir' THEN 1 END) AS total_hadir,
        COUNT(CASE WHEN keterangan = 'sakit' THEN 1 END) AS total_sakit,
        COUNT(CASE WHEN keterangan = 'izin' THEN 1 END) AS total_izin
    FROM tb_detail 
    WHERE id_pg = ?
";

$stmt = $conn->prepare($query);
if ($stmt === false) {
    echo json_encode(['error' => 'Database query error']);
    exit();
}

$stmt->bind_param('s', $_SESSION['user_id']);
if ($stmt === false) {
    echo json_encode(['error' => 'Database query error']);
    exit();
}

$stmt->execute();
$result = $stmt->get_result();
$result = $result->fetch_assoc();
echo json_encode(['result' => $result]);

$stmt->close();
$conn->close();
?>