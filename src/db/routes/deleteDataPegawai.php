<?php
session_start();
include '../../db/db_connect.php';
if ($conn->connect_error) {
    error_log("Koneksi gagal: " . $conn->connect_error);
    echo json_encode(['status' => 'error', 'message' => 'Koneksi database gagal']);
    exit();
}

if (!isset($_SESSION['token']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'unauthorized']);
    exit();
}

if (isset($_POST['id_pg'])) {
    $id_pg = $_POST['id_pg'];
    $id = $conn->real_escape_string($_REQUEST['id_pg']);
    error_log("ID Pegawai: " . $id);
    
    $stmt = $conn->prepare("DELETE FROM tb_pengguna WHERE id_pg = ?");

    $stmt->bind_param("s", $id_pg);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Pengguna berhasil dihapus']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID pengguna tidak ditemukan']);
        }
    } else {
        error_log("SQL Error: " . $stmt->error);
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan saat menghapus pegawai']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID pegawai tidak ditemukan']);
}

$conn->close();
?>
