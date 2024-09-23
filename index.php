<?php
// MySQL database connection
$host = "localhost";
$user = "root";   
$password = "";   
$dbname = "db_absensi"; 

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
