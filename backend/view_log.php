<?php
session_start();
require_once __DIR__ . '/dbconfig.php';

// Fetch role_id based on user_id stored in the session
$user_id = $_SESSION["user_id"]; // Ensure user_id is set in the session

// Query to get the role_id of the user
$role_query = "SELECT role_id FROM user WHERE user_id = ?";
$stmt = $conn->prepare($role_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($role_id);
$stmt->fetch();
$stmt->close();

if ($role_id) {
    // Fetch permissions associated with the role_id
    $permissions_sql = "
        SELECT p.view_all_data, p.edit_data, p.manage_users, p.view_log 
        FROM role_permissions rp 
        INNER JOIN permissions p ON rp.permission_id = p.permission_id 
        WHERE rp.role_id = ?
    ";
    $stmt = $conn->prepare($permissions_sql);
    $stmt->bind_param("i", $role_id);
    $stmt->execute();
    $permissions_result = $stmt->get_result();
    $user_permissions = $permissions_result->fetch_assoc();
} else {
    echo "Role ID not found for the user.";
}

// Default query parameters
$search_query = "";  // No condition by default
$search_params = []; // Array to store any bound parameters

// Check if there's a search term in the URL
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = '%' . $_GET['search'] . '%'; // Wildcard for partial matching
    $search_query = " AND u.username LIKE ?";  // Modify the query to search by username
    $search_params[] = $search_term; // Add the search term to the parameters
}

// Query to get logs with optional search by username
$sql = "
    SELECT l.log_id, u.first_name, u.last_name, u.username, l.log_message, l.created_at 
    FROM logs l
    INNER JOIN user u ON l.user_id = u.user_id
    WHERE 1=1 $search_query
    ORDER BY l.created_at DESC
";

// Prepare the statement
$stmt = $conn->prepare($sql);

// Bind parameters if there are any search terms
if (!empty($search_params)) {
    $stmt->bind_param("s", ...$search_params); // Bind the search term
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="usertable.css" rel="stylesheet" type="text/css">
    <link href="./base.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Log Viewer</title>
</head>
<body>

<div class="container">
<h1 style="font-size:50px;margin-top: -140px;">Log Viewer</h1>
    <div style="display: flex; align-items: center; justify-content: flex-start; gap: 15px;">
    <!-- Arrow Icon -->
    <a href="http://localhost/Skypiea2/frontend/main.php" style="font-size: 19px; color: #333; text-decoration: none; display: inline-flex; align-items: center;margin-top: -9px;">
        <i class="fas fa-arrow-left"></i>
    </a>

    <!-- Search Form -->
    <form method="GET" action="view_log.php" style="display: inline-flex; align-items: center; margin: 0;">
        <input type="text" name="search" placeholder="Search logs by username..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" 
        style="padding: 8px 12px; font-size: 16px; border: 1px solid #ccc; border-radius: 4px; outline: none; width: 250px;"/>
        
        <button type="submit" 
        style="padding: 8px 12px; margin-left: 8px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;margin-top: -14px;">
            Search
        </button>
    </form>
</div>



    <?php if (!empty($user_permissions['view_log']) && $result->num_rows > 0): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Log ID</th>
                    <th>User Name</th>
                    <th>Log Message</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['log_id']; ?></td>
                        <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                        <td><?php echo nl2br(htmlspecialchars($row['log_message'])); ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No logs found or you do not have permission to view the logs.</p>
    <?php endif; ?>
</div>
</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>
