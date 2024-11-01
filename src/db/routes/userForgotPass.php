<?php
// Start session
session_start();

// Include your database connection
include '../db_connect.php';

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if the email exists in the database
    $query = "SELECT id_pg FROM tb_pengguna WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // Email exists, generate a unique token
        $token = bin2hex(random_bytes(50)); 
        $expires = date("U") + 3600; // 1 hour expiration

        // Insert token into the database
        $query = "INSERT INTO password_resets (email, token, expires_at) VALUES ('$email', '$token', '$expires')";
        mysqli_query($conn, $query);

        // Create an instance of PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                       // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = $_ENV['EMAIL_USER'];                    // SMTP username
            $mail->Password   = $_ENV['EMAIL_PASS'];                    // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption
            $mail->Port       = 587;                                    // TCP port to connect to

            // Recipients
            $mail->setFrom('no-reply@teknoid-itbad.com', 'Teknoid ITB Ahmad Dahlan');
            $mail->addAddress($email);                                  // Add recipient

            // Content
            $resetLink = "http://localhost/teknoid-absensi/src/pages/reset_password.php?token=$token";
            $mail->isHTML(true);                                        
            $mail->Subject = 'Password Reset Request';

            // Load the email template and replace placeholder
            $templatePath = './templateEmail.php';
            $emailBody = file_get_contents($templatePath);
            $emailBody = str_replace('{{resetLink}}', $resetLink, $emailBody);
            
            $mail->Body = $emailBody;

            $mail->send();
            $_SESSION['message'] = "Tautan reset kata sandi telah dikirim ke email Anda.";
        } catch (Exception $e) {
            $_SESSION['message'] = "Gagal mengirim email. Kesalahan: {$mail->ErrorInfo}";
        }
    } else {
        // Email not found, redirect back with error
        header("Location: ../../pages/forgot.php?error=email_not_found");
        exit();
    }
} else {
    // If the form was not submitted via POST, redirect back to the form
    header("Location: ../../pages/forgot.php");
    exit();
}

// Redirect back to the forgot password form with success message
header("Location: ../../pages/forgot.php?message=success");
exit();
?>
