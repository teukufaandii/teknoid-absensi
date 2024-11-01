<?php
// Include config file
include '../../db/db_connect.php';

// Get the employee ID from the request
$id_pg = $_POST['id_pg'];

// Prepare the DELETE SQL statement
$sql = "DELETE FROM tb_pengguna WHERE id_pg = ?"; // Adjust the table name and column as necessary

// Initialize a statement
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $id_pg); // Bind the parameter

// Execute the statement
if ($stmt->execute()) {
    // If successful, return a success response
    $response = array("status" => "success", "message" => "Employee deleted successfully.");
} else {
    // If there was an error, return an error response
    $response = array("status" => "error", "message" => "Error deleting employee: " . $stmt->error);
}

// Close the statement and connection
$stmt->close();
$db->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>