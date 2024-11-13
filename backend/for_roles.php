<?php
session_start();
require_once __DIR__ . '/dbconfig.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: /skypiea/frontend/signin.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the logged-in user's role
$sql = "SELECT role_id FROM user WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $role_id);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Handle form submission for creating a new role
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['new_role_name'])) {
        $new_role_name = trim($_POST['new_role_name']);
        $permissions_description = trim($_POST['permissions_description']);  // Description for the entire permissions set

        // Check if the role already exists
        $check_role_sql = "SELECT role_id FROM roles WHERE role_name = ?";
        $stmt = mysqli_prepare($conn, $check_role_sql);
        mysqli_stmt_bind_param($stmt, 's', $new_role_name);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            echo "<script>alert('Role already exists! Please choose a different name.');</script>";
        } else {
            // Insert the new role
            $insert_role_sql = "INSERT INTO roles (role_name) VALUES (?)";
            $stmt = mysqli_prepare($conn, $insert_role_sql);
            mysqli_stmt_bind_param($stmt, 's', $new_role_name);
            mysqli_stmt_execute($stmt);
            $new_role_id = mysqli_insert_id($conn);  // Get the last inserted role ID
            mysqli_stmt_close($stmt);

            // Permissions fields (view_all_data, edit_data, etc.)
            $permissions = [
                'view_all_data' => isset($_POST['view_all_data']) ? 1 : 0,
                'edit_data' => isset($_POST['edit_data']) ? 1 : 0,
                'manage_users' => isset($_POST['manage_users']) ? 1 : 0,
                'view_log' => isset($_POST['view_log']) ? 1 : 0,
            ];

            // Insert the permissions with the provided description
            $insert_permissions_sql = "
                INSERT INTO permissions (view_all_data, edit_data, manage_users, view_log, description) 
                VALUES (?, ?, ?, ?, ?)
            ";
            $stmt = mysqli_prepare($conn, $insert_permissions_sql);
            mysqli_stmt_bind_param($stmt, 'iiiss', 
                $permissions['view_all_data'],
                $permissions['edit_data'],
                $permissions['manage_users'],
                $permissions['view_log'],
                $permissions_description
            );
            mysqli_stmt_execute($stmt);
            $permission_id = mysqli_insert_id($conn);  // Get the last inserted permission ID
            mysqli_stmt_close($stmt);

            // Link the role with the newly inserted permission set
            $insert_role_permission_sql = "INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $insert_role_permission_sql);
            mysqli_stmt_bind_param($stmt, 'ii', $new_role_id, $permission_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            echo "<script>alert('New role created and permissions assigned successfully!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Roles and Permissions</title>
    <link href="usertable.css" rel="stylesheet" type="text/css">
    <style>
        /* Styles for layout */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            max-width: 1200px;
            width: 90%;
            margin: 20px auto;
            gap: 20px;
        }

        /* Form Styling */
        .form-container {
            flex: 1;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .input-wrapper {
            margin-bottom: 20px;
        }

        label {
            font-size: 16px;
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        input[type="submit"] {
            padding: 10px 20px;
            border: none;
            background-color: #4CAF50;
            color: #fff;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        .permission-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        /* Toggle Switch Styling */
        .toggle-button {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
            margin-right: 10px;
        }

        .toggle-button input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 30px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            border-radius: 50%;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.4s;
        }

        input:checked + .slider {
            background-color: #4CAF50;
        }

        input:checked + .slider:before {
            transform: translateX(30px);
        }

        /* Roles Table Styling */
        .role-container {
            flex: 1.5;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .role-container h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border: 1px solid #ddd;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:hover {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Form to add a new role -->
        <div class="form-container">
            <h2>Create New Role and Assign Permissions</h2>
            <form action="" method="POST">
                <div class="input-wrapper">
                    <label for="new_role_name">New Role Name:</label>
                    <input type="text" name="new_role_name" id="new_role_name" required />
                </div>
                <h3>Permissions for the new role:</h3>
                <div class="permission-item">
                    <label class="toggle-button">
                        <input type="checkbox" name="view_all_data" value="1" />
                        <span class="slider"></span>
                    </label>
                    <label>View All Data</label>
                </div>
                <div class="permission-item">
                    <label class="toggle-button">
                        <input type="checkbox" name="edit_data" value="1" />
                        <span class="slider"></span>
                    </label>
                    <label>Edit Data</label>
                </div>
                <div class="permission-item">
                    <label class="toggle-button">
                        <input type="checkbox" name="manage_users" value="1" />
                        <span class="slider"></span>
                    </label>
                    <label>Manage Users</label>
                </div>
                <div class="permission-item">
                    <label class="toggle-button">
                        <input type="checkbox" name="view_log" value="1" />
                        <span class="slider"></span>
                    </label>
                    <label>View Log</label>
                </div>
                <div class="input-wrapper">
                    <label for="permissions_description">Permissions Description:</label>
                    <input type="text" name="permissions_description" id="permissions_description" placeholder="Enter description for these permissions" required />
                </div>
                <div class="input-wrapper">
                    <input type="submit" value="Create Role and Assign Permissions" />
                </div>
            </form>
        </div>

        <!-- Display existing roles and permissions -->
        <div class="role-container">
            <h2>Existing Roles and Permissions</h2>
            <?php
            // Fetch all roles and their permissions
            $roles_sql = "
                SELECT r.role_name, p.description, p.view_all_data, p.edit_data, p.manage_users, p.view_log
                FROM roles r
                LEFT JOIN role_permissions rp ON r.role_id = rp.role_id
                LEFT JOIN permissions p ON rp.permission_id = p.permission_id
            ";
            $result = mysqli_query($conn, $roles_sql);

            if (mysqli_num_rows($result) > 0) {
                echo "<table><thead><tr><th>Role Name</th><th>Permissions Description</th><th>View All Data</th><th>Edit Data</th><th>Manage Users</th><th>View Log</th></tr></thead><tbody>";

                while ($role = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $role['role_name'] . "</td>";
                    echo "<td>" . $role['description'] . "</td>";
                    echo "<td>" . ($role['view_all_data'] ? "Enabled" : "Disabled") . "</td>";
                    echo "<td>" . ($role['edit_data'] ? "Enabled" : "Disabled") . "</td>";
                    echo "<td>" . ($role['manage_users'] ? "Enabled" : "Disabled") . "</td>";
                    echo "<td>" . ($role['view_log'] ? "Enabled" : "Disabled") . "</td>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p>No roles found.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
  