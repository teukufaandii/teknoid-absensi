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
    // Old Card
    $stmtOld = $conn->prepare("SELECT nomor_kartu FROM tb_pengguna WHERE id_pg = ?");
    $stmtOld->bind_param('s', $userId);
    $stmtOld->execute();
    $result = $stmtOld->get_result();
    $oldData = $result->fetch_assoc();
    $oldNomorKartu = $oldData['nomor_kartu'];
    $stmtOld->close();

    // New Card
    $newNomorKartu = $userData['nomor_kartu'];

    if ($oldNomorKartu !== $newNomorKartu) {
        $stmtUpdateDetail = $conn->prepare("UPDATE tb_detail SET nomor_kartu = ? WHERE nomor_kartu = ?");
        $stmtUpdateDetail->bind_param('ss', $newNomorKartu, $oldNomorKartu);
        $stmtUpdateDetail->execute();
        $stmtUpdateDetail->close();
    }

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

    $stmt->bind_param(
        'ssssss',
        $userData['nomor_kartu'],
        $userData['nama'],
        $userData['noinduk'],
        $userData['jenis_kelamin'],
        $userData['jabatan'],
        $userId
    );

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update data']);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
