<?php
// Start session
session_start();

// Include your database connection
include '../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the token, new password, and confirm password from the form
    if (isset($_POST['token'], $_POST['new_password'], $_POST['confirm_password'])) {
        $token = mysqli_real_escape_string($conn, $_POST['token']);
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Check if passwords match
        if ($new_password !== $confirm_password) {
            header("Location: ../../pages/reset_password.php?token=$token&error=password_mismatch");
            exit();
        }

        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Get the email associated with the token
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

                // Password reset successful, redirect to login page with success message
                header("Location: ../../pages/login.php?success=password_reset");
                exit();
            } else {
                // Database update failed, show an error
                header("Location: ../../pages/reset_password.php?token=$token&error=update_failed");
                exit();
            }
        } else {
            // Token is invalid or expired
            header("Location: ../../src/pages/forgot.php?error=invalid_token");
            exit();
        }
    } else {
        // Redirect back if required data is not set
        header("Location: ../../pages/reset_password.php?error=missing_data");
        exit();
    }
} else {
    // Invalid request method
    header("Location: ../../pages/forgot.php");
    exit();
}
?>
