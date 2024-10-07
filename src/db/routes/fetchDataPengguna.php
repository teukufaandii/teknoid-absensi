<?php
require_once '../db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $data['user_id']; // Change 'id_pg' to 'user_id'

$query = "SELECT nama, nomor_kartu, noinduk, jenis_kelamin, jabatan FROM tb_pengguna WHERE id_pg = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'nomor_kartu' => $row['nomor_kartu'],
        'nama' => $row['nama'],
        'noinduk' => $row['noinduk'],
        'jenis_kelamin' => $row['jenis_kelamin'],
        'jabatan' => $row['jabatan']
    ]);
} else {
    echo json_encode(['success' => false]);
}

$stmt->close();
$conn->close();
?>
