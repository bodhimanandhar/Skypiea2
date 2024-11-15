<?php 

require_once __DIR__ . '/dbconfig.php';

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if ($user_id == NULL || $user_id == '') {
    die("Invalid ID or ID not set");
}

// Fetch user details to log the deletion
$sql = "SELECT * FROM user WHERE user_id = {$user_id}";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) != 1) {
    die("Invalid ID");
}

// Fetch user information for logging
$user = mysqli_fetch_assoc($result);
$username = $user['username']; // or any other field you want to log, like email

// Delete the user
$delSql = "DELETE FROM user WHERE user_id = {$user_id}";
$delResult = mysqli_query($conn, $delSql);

if ($delResult) {
    // Log the deletion action
    $log_message = "User deleted: {$username} (User ID: {$user_id})";
    
    // Assuming you have a session or variable for the current logged-in user to record who deleted the user
    // If the current user performing the deletion is stored in a session, use that (e.g., $_SESSION['user_id'])
    $admin_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Default to 1 if no session is set (you can modify this)

    // Prepare and insert the log
    $log_stmt = $conn->prepare("INSERT INTO logs (user_id, log_message) VALUES (?, ?)");
    if ($log_stmt === false) {
        die("Error preparing log statement: " . $conn->error);
    }
    $log_stmt->bind_param("is", $admin_user_id, $log_message);

    if (!$log_stmt->execute()) {
        echo "Error logging the deletion action: " . $log_stmt->error;
    }

    $log_stmt->close();

    // Redirect to user table
    header('Location: usertable.php');
} else {
    die("Something went wrong");
}
?>
