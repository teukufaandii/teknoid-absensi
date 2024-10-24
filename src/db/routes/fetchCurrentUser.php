<?php
require_once __DIR__ . '/../db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$query = "SELECT * FROM tb_pengguna WHERE id_pg = ?";
$stmt = $conn->prepare($query);
if ($stmt === false) {
    echo json_encode(['error' => 'Database query error']);
    exit();
}

$stmt->bind_param("s", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    echo json_encode($user);
} else {
    echo json_encode(['error' => 'User not found']);
}

$stmt->close();
$conn->close();
?>