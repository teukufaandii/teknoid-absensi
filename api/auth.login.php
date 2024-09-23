<?php
session_start();
include '../index.php';

header('Content-Type: application/json'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $email = $input['email'] ?? null;
    $password = $input['password'] ?? null;

    if (!$email || !$password) {
        echo json_encode(["status" => "error", "message" => "Email and password are required."]);
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM tb_pengguna WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if ($user['account_locked_until'] && new DateTime() < new DateTime($user['account_locked_until'])) {
            echo json_encode(["status" => "error", "message" => "Account is locked. Try again later."]);
            exit();
        }

        $hashed_password = $user['password'];

        if (password_verify($password, $hashed_password)) {
            // Successful login: Generate session data
            $_SESSION['user_id'] = $user['id_pg'];
            $_SESSION['name'] = $user['nama'];
            $_SESSION['role'] = $user['role'];
            $token = bin2hex(random_bytes(32));  // Example token generation
            $_SESSION['token'] = $token;

            // Reset failed attempts
            $stmt = $conn->prepare("UPDATE tb_pengguna SET failed_attempts = 0, account_locked_until = NULL WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->close();

            echo json_encode([
                "status" => "200",
                "message" => "Login successful.",
                "user" => [
                    "id" => $user['id_pg'],
                    "name" => $user['nama'],
                    "role" => $user['role']
                ],
                "token" => $token
            ]);
            exit();
        } else {
            // Increment failed attempts
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

            echo json_encode(["status" => "error", "message" => "Invalid email or password."]);
            exit();
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid email or password."]);
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
    exit();
}
?>
