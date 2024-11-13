<?php
require_once __DIR__ . '/dbconfig.php';

// Fetch all users with their corresponding role names
$sql = "
    SELECT user.user_id, user.first_name, user.last_name, user.email_address, user.password, user.username, user.created_at, user.updated_at, roles.role_name 
    FROM user 
    INNER JOIN roles ON user.role_id = roles.role_id
";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="usertable.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Users</title>
</head>
<body>
    <div class="container">
    <h1>Users</h1>
    <a href="http://localhost/Skypiea2/frontend/main.php"><i class="fas fa-arrow-left"></i></a>
    <a href="usertable_add.php">Add Record</a>

    <br><br>

    <?php if (mysqli_num_rows($result) === 0): ?>
        <b>No records found</b>
    <?php else: ?>
        <table border="1">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['user_id']; ?></td>
                        <td><?php echo $row['first_name']; ?></td>
                        <td><?php echo $row['last_name']; ?></td>
                        <td><?php echo $row['email_address']; ?></td>
                        <td><?php echo $row['password']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['role_name']; ?></td> <!-- Display role name -->
                        <td><?php echo $row['created_at']; ?></td>
                        <td><?php echo $row['updated_at']; ?></td>
                        <td>
                            <a href="usertable_edit.php?user_id=<?php echo $row['user_id']; ?>">Edit</a> 
                            <a href="usertable_del.php?user_id=<?php echo $row['user_id']; ?>">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>
