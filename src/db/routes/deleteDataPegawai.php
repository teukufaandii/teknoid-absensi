<?php
session_start();
header('Content-Type: application/json');
include 'src/db/db_connect.php';

if ($conn->connect_error) {
    error_log("Koneksi gagal: " . $conn->connect_error);
    echo json_encode(['status' => 'error', 'message' => 'Koneksi database gagal']);
    exit();
}

if (!isset($_SESSION['token']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'unauthorized', 'message' => 'Anda tidak memiliki akses']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id_pg'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID pegawai tidak ditemukan']);
    exit();
}

$id_pg = $conn->real_escape_string($data['id_pg']);

$stmt = $conn->prepare("DELETE FROM tb_pengguna WHERE id_pg = ?");
$stmt->bind_param("s", $id_pg);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Pengguna berhasil dihapus']);
    } else {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'ID pengguna tidak ditemukan']);
    }
} else {
    error_log("SQL Error: " . $stmt->error);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan saat menghapus pegawai']);
}

$stmt->close();
$conn->close();
?>
