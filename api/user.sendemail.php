<?php
// Start session
session_start();

// Include your database connection
include '../index.php';

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');

$dotenv->load();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    $email = mysqli_real_escape_string($conn, $input['email']);

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
            $mail->Host       = 'smtp.gmail.com';                     // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = $_ENV['EMAIL_USER'];                   // SMTP username
            $mail->Password   = $_ENV['EMAIL_PASS'];                   // SMTP password                        
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;        // Enable TLS encryption
            $mail->Port       = 587;                                    // TCP port to connect to

            // Recipients
            $mail->setFrom('no-reply@teknoid-itbad.com', 'Teknoid ITB Ahmad Dahlan');
            $mail->addAddress($email);                                  // Add a recipient

            // Content
            $resetLink = "http://localhost/teknoid-absensi/src/pages/reset_password.php?token=$token";
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = "You requested a password reset. Click the link below to reset your password:<br><br>
                              <a style='color: blue; background-color: white; padding: 10px; border-radius: 5px;' href='$resetLink'>$resetLink</a><br><br>
                              If you did not request a password reset, please ignore this email.";

            $mail->send();
            echo json_encode(["status" => "success", "message" => "Password reset link has been sent to your email."]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "Failed to send email. Error: {$mail->ErrorInfo}"]);
        }
    } else {
        // Email not found, respond with error
        echo json_encode(["status" => "error", "message" => "Email not found."]);
        exit();
    }
} else {
    // If the form was not submitted via POST, respond with an error
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
    exit();
}
?>
