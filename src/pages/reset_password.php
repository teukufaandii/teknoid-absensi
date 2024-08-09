<?php
session_start();

include '../db/db_connect.php';

if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($conn, $_GET['token']);
    $query = "SELECT email FROM password_resets WHERE token = '$token' AND expires_at >= '".date("U")."'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $email = $row['email'];
    } else {
        header("Location: ../../pages/forgot.php?error=invalid_token");
        exit();
    }
} else {
    header("Location: ../../pages/forgot.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Kata Sandi - Sistem Absensi</title>
    <link href="../../css/output.css" rel="stylesheet">
    <link rel="icon" href="../../public/logo.png">
    <link rel="stylesheet" href="./css/login.css">
    <link rel="stylesheet" href="./css/reset_password.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body class="flex justify-center items-center h-screen w-screen bg-gray-100">
    <div class="flex w-full h-full max-w-none bg-white shadow-lg">
        <div class="flex flex-col justify-center w-1/2 p-8">
            <form class="max-w-md mx-auto" method="POST" action="../db/routes/userResetPass.php">
                <h2 class="text-3xl font-bold text-center mb-6">Reset Kata Sandi</h2>
                <img src="../../public/logo.png" alt="Logo" class="mb-6 m-auto w-40 h-40 justify-center object-cover items-center">

                <!-- Display error message if any -->
                <?php if (isset($_GET['error'])): ?>
                    <div class="error-message">
                        <?php
                        if ($_GET['error'] === 'password_mismatch') {
                            echo 'Kata sandi dan konfirmasi kata sandi tidak cocok.';
                        } else {
                            echo 'Terjadi kesalahan. Silakan coba lagi.';
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                <div class="mb-4 relative">
                    <div class="floating-placeholder">
                        <input type="password" id="new_password" name="new_password" class="floating-input" placeholder="" required>
                        <label for="new_password">Kata Sandi Baru</label>
                        <i id="toggleNewPassword" class="fas fa-eye"></i>
                    </div>
                </div>

                <div class="mb-4 relative">
                    <div class="floating-placeholder">
                        <input type="password" id="confirm_password" name="confirm_password" class="floating-input" placeholder="" required>
                        <label for="confirm_password">Konfirmasi Kata Sandi</label>
                        <i id="toggleConfirmPassword" class="fas fa-eye"></i>
                    </div>
                </div>

                <button type="submit" class="w-full bg-gray-800 text-white py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">Reset Kata Sandi</button>
            </form>
        </div>
        <div class="flex flex-col items-center justify-center w-1/2 bg-gray-200 p-8">
            <img src="../../public/loginPage.png" alt="Logo" class="mb-8 w-90 h-90 object-cover">
            <p class="mt-4 text-center text-gray-600">&copy; 2024 TeknoGenius. All rights reserved.</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleNewPassword = document.getElementById('toggleNewPassword');
            const newPasswordField = document.getElementById('new_password');
            
            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
            const confirmPasswordField = document.getElementById('confirm_password');
            
            const togglePasswordVisibility = (toggleElement, passwordField) => {
                toggleElement.addEventListener('click', () => {
                    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordField.setAttribute('type', type);
                    toggleElement.classList.toggle('fa-eye');
                    toggleElement.classList.toggle('fa-eye-slash');
                });
            };

            togglePasswordVisibility(toggleNewPassword, newPasswordField);
            togglePasswordVisibility(toggleConfirmPassword, confirmPasswordField);
        });
    </script>
</body>
</html>
