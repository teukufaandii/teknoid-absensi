<?php
session_start();
include '../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM tb_pengguna WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if ($user['account_locked_until'] && new DateTime() < new DateTime($user['account_locked_until'])) {
            header("Location: ../../pages/login.php?error=locked");
            exit();
        }

        $hashed_password = $user['password'];

        if (password_verify($password, $hashed_password)) {
            $stmt = $conn->prepare("UPDATE tb_pengguna SET failed_attempts = 0, account_locked_until = NULL WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->close();

            $_SESSION['user_id'] = $user['id_pg'];
            $_SESSION['name'] = $user['nama'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['token'] = bin2hex(random_bytes(32));

            header("Location: ../../success.php");
            exit();
        } else {
            $failed_attempts = $user['failed_attempts'] + 1;

            if ($failed_attempts == 10) {  
                $lock_until = (new DateTime())->modify('+10 minutes')->format('Y-m-d H:i:s');
                $stmt = $conn->prepare("UPDATE tb_pengguna SET failed_attempts = ?, account_locked_until = ? WHERE email = ?");
                $stmt->bind_param("iss", $failed_attempts, $lock_until, $email);  
            } else {
                $stmt = $conn->prepare("UPDATE tb_pengguna SET failed_attempts = ? WHERE email = ?");
                $stmt->bind_param("is", $failed_attempts, $email);
            }
            $stmt->execute();
            $stmt->close();

            header("Location: ../../pages/login.php?error=invalid");
            exit();
        }
    } else {
        header("Location: ../../pages/login.php?error=invalid");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
