<?php
session_start();

include 'src/db/db_connect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require 'vendor/autoload.php';

// Load the .env file
$dotenv = Dotenv::createImmutable(__DIR__);
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

            $client_url = $_ENV['client_url'];
            // Recipients
            $mail->setFrom('no-reply@teknoid-itbad.com', 'Teknoid ITB Ahmad Dahlan');
            $mail->addAddress($email);                                  // Add recipient

            // Content
            $resetLink = "$client_url/teknoid-absensi/reset?token=$token";
            $mail->isHTML(true);                                        
            $mail->Subject = 'Password Reset Request';

            // Load the email template and replace placeholder
            $templatePath = '/teknoid-absensi/src/db/routes/templateEmail.php';
            
            if (file_exists($templatePath)) {
                $emailBody = file_get_contents($templatePath);
                $emailBody = str_replace('{{resetLink}}', $resetLink, $emailBody);
                $mail->Body = $emailBody;
            } else {
                throw new Exception("Email template tidak ditemukan.");
            }

            $mail->send();
            $_SESSION['message'] = "Tautan reset kata sandi telah dikirim ke email Anda.";
            header("Location: /teknoid-absensi/forgot?message=success");
            exit();
        } catch (Exception $e) {
            $_SESSION['message'] = "Gagal mengirim email. Kesalahan: {$mail->ErrorInfo}";
            header("Location: /teknoid-absensi/forgot?error=email_send_failed");
            exit();
        }
    } else {
        // Email not found, redirect back with error
        header("Location: /teknoid-absensi/forgot?error=email_not_found");
        exit();
    }
} else {
    // If the form was not submitted via POST, redirect back to the form
    header("Location: /teknoid-absensi/forgot");
    exit();
}
?>
