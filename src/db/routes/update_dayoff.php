<?php
require_once __DIR__ . '/../db_connect.php';
session_start();

if (!isset($_SESSION['token'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$id_libur = $_POST['id_libur'] ?? '';
$tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
$tanggal_akhir = $_POST['tanggal_akhir'] ?? '';
$nama_hari_libur = $_POST['nama_hari_libur'] ?? '';

if (empty($id_libur) || empty($tanggal_mulai) || empty($tanggal_akhir) || empty($nama_hari_libur)) {
    echo json_encode(['error' => 'All fields are required.']);
    exit();
}

$query = "UPDATE tb_dayoff SET tanggal_mulai = ?, tanggal_akhir = ?, nama_libur = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssss", $tanggal_mulai, $tanggal_akhir, $nama_hari_libur, $id_libur);

if ($stmt->execute()) {
    echo json_encode(['success' => 'Holiday updated successfully.']);
} else {
    echo json_encode(['error' => 'Failed to update holiday.']);
}

$stmt->close();
$conn->close();
?>