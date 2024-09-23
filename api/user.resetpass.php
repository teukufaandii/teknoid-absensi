<?php
// Start session
session_start();

// Include your database connection
include '../index.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    // Check for required fields
    if (isset($input['token'], $input['new_password'], $input['confirm_password'])) {
        $token = mysqli_real_escape_string($conn, $input['token']);
        $new_password = $input['new_password'];
        $confirm_password = $input['confirm_password'];

        // Check if passwords match
        if ($new_password !== $confirm_password) {
            echo json_encode(["status" => "error", "message" => "Passwords do not match."]);
            exit();
        }

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $query = "SELECT email FROM password_resets WHERE token = '$token' AND expires_at >= '".date("U")."'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            // Token is valid, get the email
            $row = mysqli_fetch_assoc($result);
            $email = $row['email'];

            // Update the user's password in the database
            $update_query = "UPDATE tb_pengguna SET password = '$hashed_password' WHERE email = '$email'";
            if (mysqli_query($conn, $update_query)) {
                // Delete the token to prevent reuse
                $delete_query = "DELETE FROM password_resets WHERE email = '$email'";
                mysqli_query($conn, $delete_query);

                echo json_encode(["status" => "success", "message" => "Password has been reset successfully."]);
                exit();
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to update password."]);
                exit();
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid or expired token."]);
            exit();
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Missing required data."]);
        exit();
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
    exit();
}
?>
