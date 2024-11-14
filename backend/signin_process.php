<?php
session_start();
require_once __DIR__ . '/dbconfig.php';  // Include database connection

// Get the user input (either email or username)
$user_input = $_POST['user_input'];
$password = $_POST['password'];

// Clean the input values to prevent SQL Injection
$user_input = stripslashes($user_input);
$password = stripslashes($password);
$user_input = mysqli_real_escape_string($conn, $user_input);
$password = mysqli_real_escape_string($conn, $password);

// Check if the input is an email or username (using strpos to detect @ symbol)
if (strpos($user_input, '@') !== false) {
    // If it's an email address, check the email in the database
    $stmt = $conn->prepare("SELECT user_id, username, password FROM user WHERE email_address = ?");
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    $stmt->bind_param('s', $user_input);  // Bind the email input
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // If user found, get the user_id, username, and hashed password
        $stmt->bind_result($user_id, $username, $stored_password); // Bind user_id, username, and password
        $stmt->fetch();  // Fetch the result
        $stmt->close();

        // Verify the entered password against the stored password hash
        if (password_verify($password, $stored_password)) {
            // Store the user_id and username in the session
            $_SESSION["user_id"] = $user_id;
            $_SESSION["username"] = $username;  // Store username for easy access

            // Log the successful sign-in
            $log_message = "User signed in: " . $username;
            $log_stmt = $conn->prepare("INSERT INTO logs (user_id, log_message) VALUES (?, ?)");
            $log_stmt->bind_param("is", $user_id, $log_message);

            if (!$log_stmt->execute()) {
                // If logging fails, output the error
                echo "Error logging the action: " . $log_stmt->error;
            }

            // Close the log statement
            $log_stmt->close();

            // Redirect to the email page or dashboard
            header("Location: /Skypiea2/backend/email.php");  
            exit();
        } else {
            // Incorrect password
            header("Location: /Skypiea2/frontend/signin.html");
            exit();
        }
    } else {
        // If email doesn't exist
        header("Location: /Skypiea2/frontend/signin.html");
        exit();
    }
} else {
    // If it's a username, check the username in the database
    $stmt = $conn->prepare("SELECT user_id, password FROM user WHERE username = ?");
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    $stmt->bind_param('s', $user_input);  // Bind the username input
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // If user found, get the user_id and hashed password
        $stmt->bind_result($user_id, $stored_password);  // Bind user_id and password
        $stmt->fetch();  // Fetch the result
        $stmt->close();

        // Verify the entered password against the stored password hash
        if (password_verify($password, $stored_password)) {
            // Store the user_id and username in the session
            $_SESSION["user_id"] = $user_id;
            $_SESSION["username"] = $user_input;  // Store username for easy access

            // Log the successful sign-in
            $log_message = "User signed in: " . $user_input;
            $log_stmt = $conn->prepare("INSERT INTO logs (user_id, log_message) VALUES (?, ?)");
            $log_stmt->bind_param("is", $user_id, $log_message);

            if (!$log_stmt->execute()) {
                // If logging fails, output the error
                echo "Error logging the action: " . $log_stmt->error;
            }

            // Close the log statement
            $log_stmt->close();

            // Redirect to the email page or dashboard
            header("Location: /Skypiea2/backend/email.php");  
            exit();
        } else {
            // Incorrect password
            header("Location: /Skypiea2/frontend/signin.html");
            exit();
        }
    } else {
        // If username doesn't exist
        header("Location: /Skypiea2/frontend/signin.html");
        exit();
    }
}
?>
