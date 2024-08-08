<?php
session_start();
include '../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM tb_pengguna WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
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
            $stmt = $conn->prepare("UPDATE tb_pengguna SET failed_attempts = 0, account_locked_until = NULL WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->close();

            $_SESSION['user_id'] = $user['id_pg'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['token'] = bin2hex(random_bytes(32));

            header("Location: ../../success.php");
            exit();
        } else {
            $failed_attempts = $user['failed_attempts'] + 1;

            if ($failed_attempts >= 10) {
                $lock_until = (new DateTime())->modify('+10 minutes')->format('Y-m-d H:i:s');
                $stmt = $conn->prepare("UPDATE tb_pengguna SET failed_attempts = ?, account_locked_until = ? WHERE username = ?");
                $stmt->bind_param("sss", $failed_attempts, $lock_until, $username);

                $email = $user['email'];
            } else {
                $stmt = $conn->prepare("UPDATE tb_pengguna SET failed_attempts = ? WHERE username = ?");
                $stmt->bind_param("is", $failed_attempts, $username);
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
