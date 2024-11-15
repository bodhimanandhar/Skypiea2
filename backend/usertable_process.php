<?php 
require_once __DIR__ . '/dbconfig.php';

extract($_POST);

// Check if it's an update or insert operation
if (array_key_exists('user_id', $_POST)) {
    // Update query with prepared statement
    $sql = "UPDATE user SET first_name = ?, last_name = ?, email_address = ?, username = ?, role_id = ? WHERE user_id = ?";
    
    // Prepare the statement
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing the statement: " . $conn->error);
    }
    
    // Bind parameters for update (note the type binding)
    $stmt->bind_param("ssssii", $First_name, $Last_name, $Email, $User_name, $Role_id, $user_id);
    
    // Log the update action
    $log_message = "User updated: {$User_name} (User ID: {$user_id})";
} else {
    // Insert query with prepared statement
    $hash_password = password_hash($password, PASSWORD_DEFAULT);  // Ensure $password is coming from $_POST
    $sql = "INSERT INTO user (user_id, first_name, last_name, email_address, password, username, created_at, updated_at) 
            VALUES (NULL, ?, ?, ?, ?, ?, NULL, NULL)";
    
    // Prepare the statement
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing the statement: " . $conn->error);
    }
    
    // Bind parameters for insert
    $stmt->bind_param("sssss", $First_name, $Last_name, $Email, $hash_password, $User_name);
    
    // Log the insert action
    $log_message = "New user created: {$User_name} (Email: {$Email})";
}

// Execute the main SQL query
$result = $stmt->execute();

if ($result) {
    // Get user_id from the insert or update operation
    $user_id = isset($user_id) ? $user_id : mysqli_insert_id($conn);  // If it's an update, use $user_id, for insert, get the last inserted ID

    // Log the action
    $log_stmt = $conn->prepare("INSERT INTO logs (user_id, log_message) VALUES (?, ?)");
    if ($log_stmt === false) {
        die("Error preparing the log statement: " . $conn->error);
    }

    $log_stmt->bind_param("is", $user_id, $log_message);

    if (!$log_stmt->execute()) {
        // If logging fails, output the error
        echo "Error logging the action: " . $log_stmt->error;
    }

    // Close the log statement
    $log_stmt->close();

    // Redirect to user table or any other page as needed
    header('Location: usertable.php');
} else {
    die("Something went wrong: " . mysqli_error($conn)); // Show MySQL error if query fails
}
?>
