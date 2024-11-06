<?php
session_start();
require_once __DIR__ . '/dbconfig.php';  // Include database connection

// Get the user input
$user_input = $_POST['user_input'];
$password = $_POST['password'];

// Clean the input values
$user_input = stripslashes($user_input);
$password = stripslashes($password);
$user_input = mysqli_real_escape_string($conn, $user_input);
$password = mysqli_real_escape_string($conn, $password);

// Check if the input is an email or username
if (strpos($user_input, '@') !== false) {
    // If it's an email address, check the email in the database
    $stmt = $conn->prepare("SELECT password FROM user WHERE email_address = ?");
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    $stmt->bind_param('s', $user_input);  // Bind the email input
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // If user found, get the stored hashed password
        $stmt->bind_result($stored_password);
        $stmt->fetch();  // Fetch the result
        $stmt->close();

        // Verify the entered password against the stored password hash
        if (password_verify($password, $stored_password)) {
            $stmt = $conn->prepare("SELECT username FROM user WHERE email_address = ?");
 if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

        $stmt->bind_param('s', $user_input);  // Bind the email input
        $stmt->execute();
        // If user found, get the stored hashed password
        $stmt->bind_result($_SESSION["username"]);
        $stmt->fetch();  // Fetch the result
        $stmt->close();

          
            header("Location: /skypiea/backend/email.php");  // Redirect to email page
            exit();
        } else {
            header("Location: /skypiea/frontend/signin.html");  // Incorrect password
            exit();
        }
    } else {
        // If email doesn't exist
        header("Location: /skypiea/frontend/signin.html");
        exit();
    }
} else {
    // If it's a username, check the username in the database
    $stmt = $conn->prepare("SELECT password FROM user WHERE username = ?");
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    $stmt->bind_param('s', $user_input);  // Bind the username input
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // If user found, get the stored hashed password
        $stmt->bind_result($stored_password);
        $stmt->fetch();  // Fetch the result
        $stmt->close();

        // Verify the entered password against the stored password hash
        if (password_verify($password, $stored_password)) {
            $_SESSION["username"] = $user_input;  // Set session variable for username
            header("Location: /skypiea/backend/email.php");  // Redirect to email page
            exit();
        } else {
            header("Location: /skypiea/frontend/signin.html");  // Incorrect password
            exit();
        }
    } else {
        // If username doesn't exist
        header("Location: /skypiea/frontend/signin.html");
        exit();
    }
}
?>
