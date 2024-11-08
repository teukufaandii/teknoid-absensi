<?php
session_start();
include 'src/db/db_connect.php';

if ( $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'unauthorized']);
    exit();
}

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    
    $stmt = $conn->prepare("DELETE FROM tb_anonim WHERE id = ?");
    $stmt->bind_param("s", $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Data berhasil dihapus']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
        }
    } else {
        error_log("SQL Error: " . $stmt->error);
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan saat menghapus data']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID hari libur tidak ditemukan']);
}

$conn->close();
?>
