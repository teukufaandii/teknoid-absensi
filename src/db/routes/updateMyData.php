<?php
session_start();

if (!isset($_SESSION['token'])) {
  echo json_encode(['error' => 'Unauthorized']);
  exit();
}

require_once __DIR__ . '/../db_connect.php';

$username = htmlspecialchars($_POST['username']);
$email = htmlspecialchars($_POST['email']);
$noinduk = htmlspecialchars($_POST['noinduk']);
$gender = htmlspecialchars($_POST['gender']);
$user_id = $_SESSION['user_id'];

$query = "SELECT email FROM tb_pengguna WHERE id_pg = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $user_id);
$stmt->execute();
$stmt->bind_result($currentEmail);
$stmt->fetch();
$stmt->close();

if ($email !== $currentEmail) {
  $checkEmailQuery = "SELECT id_pg FROM tb_pengguna WHERE email = ? AND id_pg != ?";
  $checkStmt = $conn->prepare($checkEmailQuery);
  $checkStmt->bind_param('ss', $email, $user_id);
  $checkStmt->execute();
  $checkStmt->store_result();

  if ($checkStmt->num_rows > 0) {
    echo json_encode(['error' => 'Email sudah terdaftar oleh pengguna lain.']);
    $checkStmt->close();
    $conn->close();
    exit();
  }

  $checkStmt->close();
}

$query = "UPDATE tb_pengguna SET nama=?, noinduk=?, jenis_kelamin=? WHERE id_pg=?";
if ($email !== $currentEmail) {
  $query = "UPDATE tb_pengguna SET nama=?, email=?, noinduk=?, jenis_kelamin=? WHERE id_pg=?";
}
$stmt = $conn->prepare($query);

if ($email !== $currentEmail) {
  $stmt->bind_param('sssss', $username, $email, $noinduk, $gender, $user_id);
} else {
  $stmt->bind_param('ssss', $username, $noinduk, $gender, $user_id);
}

if ($stmt->execute()) {
  echo json_encode(['success' => true, 'message' => 'Data berhasil diperbarui.']);
} else {
  echo json_encode(['success' => false, 'message' => 'Gagal memperbarui data.']);
}

$stmt->close();
$conn->close();
?>