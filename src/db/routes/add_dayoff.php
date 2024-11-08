<?php
session_start();
include 'src/db/db_connect.php';

if (!isset($_SESSION['token']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'unauthorized']);
    exit();
}

// Sanitize and validate the input
$nama_libur = htmlspecialchars($_POST['nama_hari_libur']);
$tanggal_mulai = $_POST['tanggal_mulai'];
$tanggal_akhir = $_POST['tanggal_akhir'];

$id = uniqid('dayoff_');

$stmt = $conn->prepare("INSERT INTO tb_dayoff (id, nama_libur, tanggal_mulai, tanggal_akhir) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $id, $nama_libur, $tanggal_mulai, $tanggal_akhir);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Hari libur berhasil ditambahkan']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan hari libur']);
}

$stmt->close();
$conn->close();
?>
