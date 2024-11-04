<?php
session_start();

include '../db/db_connect.php';

$token = null;

// Check if 'token' parameter is present in the URL
if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($conn, $_GET['token']);
    $query = "SELECT email FROM password_resets WHERE token = '$token' AND expires_at >= '".date("U")."'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $email = $row['email'];
    } else {
        header("Location: ../../src/pages/forgot.php?error=invalid_token");
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
    <link rel="stylesheet" href="./css/reset_password.css">
    <link rel="stylesheet" href="./css/login.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="./css/font/poppins-font.css" rel="stylesheet">
    <style>

    </style>
</head>

<body class="flex justify-center items-center h-screen w-screen bg-gray-100">
    <div class="flex w-full h-full max-w-none bg-white shadow-lg">
        <div class="flex flex-col justify-center w-1/2 p-8">
            <form id="resetForm" class="max-w-md w-full mx-auto" method="POST" action="../db/routes/userResetPass.php">
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
                    <div id="passwordCriteria" class="criteria-message hidden">
                        <ul>
                            <li id="lengthCriteria" class="invalid mb-2">Minimal 8 karakter</li>
                            <li id="uppercaseCriteria" class="invalid mb-2">Setidaknya satu huruf besar</li>
                            <li id="lowercaseCriteria" class="invalid mb-2">Setidaknya satu huruf kecil</li>
                            <li id="numberCriteria" class="invalid mb-2">Setidaknya satu angka</li>
                        </ul>
                    </div>
                </div>

                <div class ="mb-4 relative">
                    <div class="floating-placeholder">
                        <input type="password" id="confirm_password" name="confirm_password" class="floating-input" placeholder="" required>
                        <label for="confirm_password">Konfirmasi Kata Sandi</label>
                        <i id="toggleConfirmPassword" class="fas fa-eye"></i>
                        <div id="confirmPasswordError" class="error_confirm hidden">
                        Kata sandi tidak sesuai.
                     </div>
                    </div>
                    
                </div>

                <button type="submit" class="w-full bg-purpleNavbar text-white py-2 rounded-md hover:bg-purpleNavbarHover focus:outline-none">Reset Kata Sandi</button>
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
        const passwordCriteria = document.getElementById('passwordCriteria');
        
        const lengthCriteria = document.getElementById('lengthCriteria');
        const uppercaseCriteria = document.getElementById('uppercaseCriteria');
        const lowercaseCriteria = document.getElementById('lowercaseCriteria');
        const numberCriteria = document.getElementById('numberCriteria');
        
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordField = document.getElementById('confirm_password');
        const confirmPasswordError = document.getElementById('confirmPasswordWarning'); // Pastikan ini sesuai dengan ID di HTML
        
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

        // Live validation for password length and criteria
        newPasswordField.addEventListener('input', () => {
            const password = newPasswordField.value;
            if (password.length === 0) {
                passwordCriteria.classList.add('hidden'); // Sembunyikan kriteria jika panjang karakter 0
                return; // Keluar dari fungsi jika input kosong
            }

            passwordCriteria.classList.remove('hidden'); // Tampilkan kriteria jika ada karakter

            // Check length
            if (password.length >= 8) {
                lengthCriteria.classList.remove('invalid');
                lengthCriteria.classList.add('valid');
            } else {
                lengthCriteria.classList.add('invalid');
                lengthCriteria.classList.remove('valid');
            }

            // Check for uppercase letter
            if (/[A-Z]/.test(password)) {
                uppercaseCriteria.classList.remove('invalid');
                uppercaseCriteria.classList.add('valid');
            } else {
                uppercaseCriteria.classList.add('invalid');
                uppercaseCriteria.classList.remove('valid');
            }

            // Check for lowercase letter
            if (/[a-z]/.test(password)) {
                lowercaseCriteria.classList.remove('invalid');
                lowercaseCriteria.classList.add('valid');
            } else {
                lowercaseCriteria.classList.add('invalid');
                lowercaseCriteria.classList.remove('valid');
            }

            // Check for number
            if (/\d/.test(password)) {
                numberCriteria.classList.remove('invalid');
                numberCriteria.classList.add('valid');
            } else {
                numberCriteria.classList.add('invalid');
                numberCriteria.classList.remove('valid');
            }
        });

        // Check if passwords match
        confirmPasswordField.addEventListener('input', () => {
            const newPassword = newPasswordField.value;
            const confirmPassword = confirmPasswordField.value;

            if (newPassword !== confirmPassword) {
                confirmPasswordError.classList.remove('hidden');
            } else {
                confirmPasswordError.classList.add('hidden');
            }
        });

        // Prevent form submission if password criteria not met
        document.getElementById('resetForm').addEventListener('submit', (e) => {
            const newPassword = newPasswordField.value;
            const confirmPassword = confirmPasswordField.value;

            if (newPassword.length < 8 || !/[A-Z]/.test(newPassword) || !/[a-z]/.test(newPassword) || !/\d/.test(newPassword) || newPassword !== confirmPassword) {
                e.preventDefault();
            }
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const url = new URL(window.location);
        
        if (url.searchParams.has('message') || url.searchParams.has('error')) {
            url.searchParams.delete('message');
            url.searchParams.delete('error');
            window.history.replaceState({}, document.title, url.pathname);
        }
    });
</script>
</body>
</html>