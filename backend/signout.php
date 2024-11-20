<?php
require_once __DIR__ . '/dbconfig.php'; 
session_start();
$username = $_SESSION['username'];
$user_id = $_SESSION["user_id"]; 
$log_message = "User signed out: " . $username;
$log_stmt = $conn->prepare("INSERT INTO logs (user_id, log_message) VALUES (?, ?)");
$log_stmt->bind_param("is", $user_id, $log_message);

if (!$log_stmt->execute()) {
    // If logging fails, output the error
    echo "Error logging the action: " . $log_stmt->error;
}

// Close the log statement
$log_stmt->close();
session_destroy();
header("Location: /Skypiea2/index.php")
?>
