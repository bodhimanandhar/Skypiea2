<?php
session_start();
require_once __DIR__ . '/dbconfig.php';

// Correct variable assignment (use = instead of ==)
$firstname = mysqli_real_escape_string($conn, $_POST['First_name']);
$lastname = mysqli_real_escape_string($conn, $_POST['Last_name']);
$email = mysqli_real_escape_string($conn, $_POST['Email']); // Corrected assignment
$username = mysqli_real_escape_string($conn, $_POST['User_name']); // Corrected field name ('User_name')
$password = mysqli_real_escape_string($conn, $_POST['Password']); // Corrected field name ('Password')

// Hash the password for storage
$hash_password = password_hash($password, PASSWORD_DEFAULT);

// Correct the number of placeholders in the INSERT statement (no need for user_id since it's auto-increment)
$stmt = $conn->prepare("INSERT INTO user (first_name, last_name, email_address, password, username) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $firstname, $lastname, $email, $hash_password, $username); // No need for extra "" for user_id

// Execute the query and check for errors
if ($stmt->execute()) {
    $_SESSION["username"] = $username;
    // Redirect to the email page after successful signup
    header("Location: /skypiea/backend/email.php");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
