<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - Sistem Absensi</title>
    <link href="../../css/output.css" rel="stylesheet">
    <link rel="icon" href="../../public/logo.png">
    <link rel="stylesheet" href="./css/login.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/forgot.css">
</head>
<body class="flex justify-center items-center h-screen w-screen bg-gray-100">
    <div class="flex w-full h-full max-w-none bg-white shadow-lg">
        <div class="flex flex-col justify-center w-1/2 p-8">
            <form class="max-w-md mx-auto w-full" method="POST" action="../db/routes/userForgotPass.php">
                <h2 class="text-3xl font-bold text-center mb-6">Lupa Kata Sandi</h2>
                <img src="../../public/logo.png" alt="Logo" class="mb-6 m-auto w-40 h-40 justify-center object-cover items-center">
                
                <!-- Display success or error message -->
                <?php if (isset($_GET['message']) && $_GET['message'] === 'success'): ?>
                    <div class="success-message">
                        Email terkirim, silahkan cek email anda.
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="error-message">
                        <?php
                        if ($_GET['error'] === 'email_not_found') {
                            echo 'Email tidak ditemukan. Silakan periksa dan coba lagi.';
                        } else {
                            echo 'Terjadi kesalahan. Silakan coba lagi.';
                        }
                        ?>
                    </div>
                <?php endif; ?>
                
                <div class="mb-4 relative">
                    <div class="floating-placeholder">
                        <input type="email" id="email" name="email" class="floating-input" placeholder="" required>
                        <label for="email">Masukkan Email Anda</label>
                    </div>
                </div>
                <button type="submit" class="form-button submit-button">Submit</button>

                <!-- Back to Login Button -->
                <div class="mb-4">
                    <a href="./login.php" class="form-button back-to-login">
                        <i class="fas fa-arrow-left"></i> Kembali 
                    </a>
                </div>
            </form>
        </div>
        <div class="flex flex-col items-center justify-center w-1/2 bg-gray-200 p-8">
            <img src="../../public/loginPage.png" alt="Logo" class="mb-8 w-90 h-90 object-cover">
            <p class="mt-4 text-center text-gray-600">&copy; 2024 TeknoGenius. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
