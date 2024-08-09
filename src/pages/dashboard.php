<?php
session_start();

if (!isset($_SESSION['token'])) {
    header('Location: login.php');
    exit();
}

$username = htmlspecialchars($_SESSION['name']);
$role = $_SESSION['role'];
$id = $_SESSION['user_id'];
$token = $_SESSION['token'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
</head>
<body>
    <h1>Welcome, <?php echo $username; ?>!</h1>
    <p>Role: <?php echo $role; ?></p>
    <p>User ID: <?php echo $id; ?></p>
    <p>Token: <?php echo $token; ?></p>
    <p>Login Sukses</p>

    <!-- Logout Link -->
    <a href="../db/routes/userLogout.php">Logout</a>
</body>
</html>

