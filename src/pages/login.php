<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem Absensi</title>
    <link href="../../css/output.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/login.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="flex justify-center items-center h-screen w-screen bg-gray-100">
    <div class="flex w-full h-full max-w-none bg-white shadow-lg">
        <div class="flex flex-col justify-center w-1/2 p-8">
            <form class="max-w-md mx-auto">
                <h2 class="text-3xl font-bold text-center mb-6">Login Sistem Absensi</h2>
                <img src="../../public/logo.png" alt="Logo" class="mb-6 m-auto w-40 h-40 justify-center object-cover items-center">
                <div class="mb-4 relative">
                    <div class="floating-placeholder">
                        <input type="text" id="username" class="floating-input" placeholder=" " required>
                        <label for="username">Nama pengguna</label>
                    </div>
                </div>
                <div class="mb-4 relative password-wrapper">
                    <div class="floating-placeholder">
                        <input type="password" id="password" class="floating-input" placeholder=" " required>
                        <label for="password">Kata sandi</label>
                        <i id="togglePassword" class="fas fa-eye"></i> <!-- Eye icon for toggling password visibility -->
                    </div>
                </div>
                <div class="flex justify-end items-center mb-4">
                    <a href="#" class="text-sm text-blue-500 hover:underline">Lupa kata sandi?</a>
                </div>
                <button type="submit" class="w-full bg-gray-800 text-white py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">Masuk</button>
            </form>
        </div>
        <div class="flex flex-col items-center justify-center w-1/2 bg-gray-200 p-8">
            <img src="../../public/loginPage.png" alt="Logo" class="mb-8 w-90 h-90 object-cover">
            <p class="mt-4 text-center text-gray-600">&copy; 2024 TeknoGenius. All rights reserved.</p>
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
    </script>
</body>
</html>
