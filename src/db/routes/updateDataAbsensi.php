<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['token'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Include the database connection
require_once '../db_connect.php'; 

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['user_id'];
$absensiData = $data['data'];

// Validate input data
if (empty($userId) || empty($absensiData)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit();
}

try {
    $stmt = $conn->prepare("
    UPDATE tb_detail 
    SET 
        keterangan = ? 
    WHERE id = ?
    ");

    $stmt->bind_param(
        'ss',
        $absensiData['keterangan'],
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
?>
