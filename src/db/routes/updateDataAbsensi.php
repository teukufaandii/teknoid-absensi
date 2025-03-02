<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['token'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Include the database connection
require_once 'src/db/db_connect.php';

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);
$detailId = $data['id'];
$userId = $data['user_id'];
$absensiData = $data['data'];

// Validate input data
if (empty($detailId) || empty($userId) || empty($absensiData)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit();
}

try {
    $stmt = $conn->prepare("UPDATE tb_detail SET keterangan = ? WHERE id = ? AND id_pg = ?");

    $stmt->bind_param(
        'sss',
        $absensiData['keterangan'],
        $detailId,
        $userId
    );

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update data']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
