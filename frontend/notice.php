<?php
session_start();
$username = $_SESSION["username"];
if (!isset($username)) {
    header("Location: ./signin.html");
    exit();
}
require_once 'C:\xampp\htdocs\skypiea2\backend\dbconfig.php';

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

$sql = "SELECT * FROM `notice`";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="./base.css" rel="stylesheet" type="text/css">
    <title>Notice</title>
    <style>
        /* Table Styling */
        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
        }

        th {
            background-color: #3498db;
            color: white;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }

        td {
            background-color: #ffffff;
            color: #333;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
        }

        tr:nth-child(even) td {
            background-color: #f9f9f9;
        }

        tr:hover td {
            background-color: #f1f1f1;
            cursor: pointer;
        }

        /* Navbar Styles */
        .navbar {
            background-color: #333;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h1 a {
            color: white;
            text-decoration: none;
            font-size: 24px;
        }

        .navbar .right button {
            background-color:green;
            color: white;
            padding: 10px 15px;
            margin-left: 10px;
            border: none;
            cursor: pointer;
        }

        .navbar .right button a {
            color: white;
            text-decoration: none;
        }

        .navbar .right button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
<div class="navbar">
    <h1><a href="/Skypiea2">Dashboard</a></h1>
    <div class="right">
        <span class="username">Hi there, <?php echo $_SESSION["username"]; ?></span>
        <form action="../backend/signout.php" method="POST" style="display:inline;">
            <button type="submit">Sign Out</button>
        </form>
        <button type="submit" class="signout-btn"><a href="mailto:espemaliservice@gmail.com">Mail</a></button>
        <button onclick="window.location='notice.php'" type="submit" class="signout-btn">Notice</button>
        <button onclick="window.location='./findtemp.php'" type="submit" class="signout-btn">FindWeather</button>
        <?php if (!empty($user_permissions['edit_data'])): ?>
            <button onclick="window.location='../backend/usertable.php'" type="submit" class="signout-btn">UserTable</button>
            <button onclick="window.location='../backend/for_roles.php'" type="submit" class="signout-btn">AddRole</button>
            <button onclick="window.location='../backend/for_role_update.php'" type="submit" class="signout-btn">UpdateRole</button>
        <?php endif; ?>
    </div>
</div>
<br>
<?php if (!empty($user_permissions['manage_users'])): ?>
    <a href="../backend/notice_add.php" style="color: green; text-decoration: none; margin-left: 20px; font-size: 25px;">Add Record</a>
<?php endif; ?>

<?php if (mysqli_num_rows($result) === 0): ?>
    <b>No records found</b>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>S.N.</th>
                <th>Notice Content</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['sn']; ?></td>
                    <td><?php echo htmlspecialchars($row['content']); ?></td>
                    <td><?php echo $row['time']; ?></td>
                    <td>
                        <?php if (!empty($user_permissions['edit_data'])): ?>
                            <a href="../backend/notice_edit.php?sn=<?php echo $row['sn']; ?>" style="color: black;">Edit</a>
                        <?php endif; ?>
                        <?php if (!empty($user_permissions['manage_users'])): ?>
                            <a href="../backend/notice_del.php?sn=<?php echo $row['sn']; ?>" style="color: black;">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>
