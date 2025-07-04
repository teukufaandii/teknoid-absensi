<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem Absensi</title>
    <link href="css/output.css" rel="stylesheet">
    <link rel="icon" href="public/logo.png">
    <link rel="stylesheet" href="src/pages/css/login.css">
    <link href="src/pages/css/font/poppins-font.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body class="flex justify-center items-center h-screen w-screen bg-gray-100">
    <div class="flex flex-col md:flex-row w-full h-full max-w-none bg-white shadow-lg">
        <div class="flex flex-col justify-center h-full w-full md:w-1/2 p-8">
            <form class="max-w-md w-full mx-auto" method="POST" action="/teknoid-absensi/api/auth/login">
                <h2 class="text-3xl font-bold text-center mb-6">Login Sistem Absensi</h2>
                <img src="public/logo.png" alt="Logo" class="mb-6 m-auto w-40 h-40 object-cover">

                <?php if (isset($_GET['success']) && $_GET['success'] === 'password_reset'): ?>
                    <div class="success-message mb-4 text-green-600">
                        Reset password berhasil, silakan input ulang email dan password Anda.
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="error-message mb-4 text-red-600">
                        <?php
                        if ($_GET['error'] === 'locked') {
                            echo 'Akun Anda telah dibekukan selama 10 menit karena terlalu banyak upaya login yang gagal.';
                        } elseif ($_GET['error'] === 'invalid_credentials') {
                            echo 'Email pengguna atau kata sandi salah.';
                        } elseif ($_GET['error'] === 'invalidrole') {
                            echo 'Role pengguna tidak valid. Silakan hubungi administrator.';
                        } else {
                            echo 'Kata Sandi Salah';
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <div class="mb-4 relative">
                    <div class="floating-placeholder">
                        <input type="email" id="email" name="email" class="floating-input" placeholder="" required>
                        <label for="email">Email</label>
                    </div>
                </div>
                <div class="mb-4 relative password-wrapper">
                    <div class="floating-placeholder">
                        <input type="password" id="password" name="password" class="floating-input" placeholder=" " required>
                        <label for="password">Kata sandi</label>
                        <i id="togglePassword" class="fas fa-eye text-gray-400"></i>
                    </div>
                </div>
                <div class="flex justify-end items-center mb-4">
                    <a href="forgot" class="text-sm text-blue-500 hover:underline">Lupa kata sandi?</a>
                </div>
                <button type="submit" class="w-full bg-purpleNavbar text-white py-2 rounded-md hover:bg-purpleNavbarHover focus:outline-none focus:ring-2 focus:ring-purpleNavbar">Masuk</button>
            </form>
        </div>
        <div class="hidden md:flex flex-col items-center justify-center h-full w-1/2 bg-gray-200 p-8">
            <img src="public/loginPage.png" alt="Login Page" class="mb-8 w-90 h-90 object-cover">
            <p class="mt-4 text-center text-gray-600">&copy; 2025 TeknoGenius. All rights reserved.</p>
        </div>
        <div class="md:hidden flex flex-col absolute bottom-0 items-center justify-center w-full bg-gray-200 p-4">
            <p class="whitespace-nowrap text-sm text-center text-gray-600">&copy; 2025 TeknoGenius. All rights reserved.</p>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast-success" class="hidden fixed top-5 right-5 p-4 mb-4 w-80 max-w-xs bg-green-100 border-t-4 border-green-500 rounded-lg shadow-md text-green-800" role="alert">
        <div class="flex">
            <div class="py-1">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">Berhasil logout.</p>
            </div>
        </div>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');

        togglePassword.addEventListener('click', () => {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            togglePassword.classList.toggle('fa-eye');
            togglePassword.classList.toggle('fa-eye-slash');
        });

        document.addEventListener('DOMContentLoaded', () => {
            const url = new URL(window.location);
            const toastSuccess = document.getElementById('toast-success');

            if (url.searchParams.get('success') === 'logout') {
                toastSuccess.classList.remove('hidden');
                setTimeout(() => {
                    toastSuccess.classList.add('hidden');
                }, 3000);
            }

            if (url.searchParams.has('error') || url.searchParams.has('success')) {
                url.searchParams.delete('error');
                url.searchParams.delete('success');
                window.history.replaceState({}, document.title, url.pathname);
            }
        });
    </script>
</body>

</html>