<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="5;url=login"> <!-- Redirect setelah 5 detik -->
    <title>401 Unauthorized</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .fade-in {
            animation: fadeIn 2s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="text-center fade-in">
        <h1 class="text-6xl font-bold text-red-600">401</h1>
        <h2 class="text-2xl text-gray-800 mt-4">Unauthorized Access</h2>
        <p class="text-lg text-gray-600 mt-2">You will be redirected to the login page in 5 seconds...</p>
        
        <div class="mt-8 animate-bounce inline-block">
            <svg class="w-20 h-20 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12V8a4 4 0 00-8 0v4M5 12h14v10H5V12z"></path>
            </svg>
        </div>
    </div>
</body>
</html>
