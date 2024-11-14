<?php
session_start();  // Start session to store session variables
require_once __DIR__ . '/dbconfig.php';  // Include database connection

// Correct variable assignment (use = instead of ==)
$firstname = mysqli_real_escape_string($conn, $_POST['First_name']);
$lastname = mysqli_real_escape_string($conn, $_POST['Last_name']);
$email = mysqli_real_escape_string($conn, $_POST['Email']);
$username = mysqli_real_escape_string($conn, $_POST['User_name']);
$password = mysqli_real_escape_string($conn, $_POST['Password']);

// Hash the password for secure storage
$hash_password = password_hash($password, PASSWORD_DEFAULT);

// Prepare the INSERT statement to insert the new user
$stmt = $conn->prepare("INSERT INTO user (first_name, last_name, email_address, password, username) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $firstname, $lastname, $email, $hash_password, $username);

// Execute the query and check for errors
if ($stmt->execute()) {
    // On successful insertion, fetch the user_id of the newly created user
    $user_id = $stmt->insert_id;

    // Optionally, set the session ID here (if you want to manually handle the session ID)
    // You usually don't need this as PHP handles sessions automatically.
    session_regenerate_id();  // Regenerate session ID to prevent session fixation attacks

    // Store the user information in session
    $_SESSION["user_id"] = $user_id;  // Store user_id in session
    $_SESSION["username"] = $username;  // Store username in session

    // Log the user creation action
    $log_message = "User signed up: " . $username;
    $log_stmt = $conn->prepare("INSERT INTO logs (user_id, log_message) VALUES (?, ?)");
    $log_stmt->bind_param("is", $user_id, $log_message);

    if (!$log_stmt->execute()) {
        // If logging fails, output the error
        echo "Error logging the action: " . $log_stmt->error;
    }

    // Close the log statement
    $log_stmt->close();

    // Redirect to the email page after successful signup
    header("Location: /Skypiea2/backend/email.php");
    exit();  // Ensure no further code is executed after the redirect
} else {
    // If insertion fails, output the error
    echo "Error: " . $stmt->error;
}

// Close the statement and database connection
$stmt->close();
$conn->close();
?>
