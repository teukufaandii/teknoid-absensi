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
$userId = $data['user_id'];
$userData = $data['data'];

// Validate input data
if (empty($userId) || empty($userData)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit();
}

try {
    $stmt = $conn->prepare("
        UPDATE tb_pengguna 
        SET 
            nomor_kartu = ?, 
            nama = ?, 
            noinduk = ?, 
            jenis_kelamin = ?, 
            jabatan = ? 
        WHERE id_pg = ?
    ");

    // Bind parameters
    $stmt->bind_param(
        'ssssss',
        $userData['nomor_kartu'],
        $userData['nama'],
        $userData['noinduk'],
        $userData['jenis_kelamin'],
        $userData['jabatan'],
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

$stmt->close();
$conn->close();
?>
