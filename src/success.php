<?php
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: ../login.php");
    exit();
}

$redirectUrl = '';
if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'user') {
    $redirectUrl = 'dashboard';
} else {
    $redirectUrl = 'login?error=invalidrole';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Success</title>
    <link href="css/output.css" rel="stylesheet">
    <link href="src/pages/css/font/poppins-font.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Poppins, 'sans-serif';
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
    </style>
</head>
<body class="flex justify-center items-center h-screen w-screen bg-gray-100">
    <div class="flex flex-col justify-center items-center w-full max-w-md p-6 bg-white shadow-lg rounded-lg overflow-hidden relative animate-fade-in" id="successNotification">
        <div class="text-center mb-4">
            <i class="fas fa-check-circle text-green-500 text-5xl"></i>
        </div>
        <h2 class="text-2xl font-bold mb-2">Login Berhasil!</h2>
        <p class="text-gray-700 mb-4">Selamat datang! Anda telah berhasil masuk ke sistem.</p>
    </div>
    
    <script>
        setTimeout(() => {
            window.location.href = '<?php echo $redirectUrl; ?>';
        }, 2000); 
    </script>
</body>
</html>
