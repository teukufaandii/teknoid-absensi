<?php
header('Content-Type: application/json');
session_start();

$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"], $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

$response = [
    "status" => "success",
    "message" => "Logged out successfully."
];

echo json_encode($response);
?>
