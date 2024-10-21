<?php
session_start();
include '../../db/db_connect.php';

if (!isset($_SESSION['token']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'unauthorized']);
    exit();
}

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    
    $stmt = $conn->prepare("DELETE FROM tb_dayoff WHERE id = ?");
    $stmt->bind_param("s", $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Hari libur berhasil dihapus']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID hari libur tidak ditemukan']);
        }
    } else {
        error_log("SQL Error: " . $stmt->error);
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan saat menghapus hari libur']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID hari libur tidak ditemukan']);
}

$conn->close();
?>
