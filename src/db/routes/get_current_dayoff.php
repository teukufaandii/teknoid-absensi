<?php
require_once __DIR__ . '/../db_connect.php';
session_start();

header('Content-Type: application/json');

$id_libur = $_GET['id'] ?? '';
$response = [];

if ($id_libur) {
    $query = "SELECT * FROM tb_dayoff WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $id_libur);
    $stmt->execute();
    $result = $stmt->get_result();
    $holiday = $result->fetch_assoc();
    
    if ($holiday) {
        $response = [
            'tanggal_mulai' => $holiday['tanggal_mulai'],
            'tanggal_akhir' => $holiday['tanggal_akhir'],
            'nama_hari_libur' => $holiday['nama_libur']
        ];
    } else {
        $response['error'] = 'Holiday not found.';
    }

    $stmt->close();
} else {
    $response['error'] = 'Invalid holiday ID.';
}

echo json_encode($response);
$conn->close();
?>
