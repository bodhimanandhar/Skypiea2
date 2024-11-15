<?php
session_start();
require_once __DIR__ . '/dbconfig.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: /skypiea/frontend/signin.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch roles for the dropdown
$role_sql = "SELECT * FROM roles";
$role_result = mysqli_query($conn, $role_sql);

// Handle form submission to update permissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['role_id'])) {
        // Get the role ID and permissions from the submitted form
        $role_id_to_update = $_POST['role_id'];
        $permissions = [
            'view_all_data' => isset($_POST['view_all_data']) ? 1 : 0,
            'edit_data' => isset($_POST['edit_data']) ? 1 : 0,
            'manage_users' => isset($_POST['manage_users']) ? 1 : 0,
            'view_log' => isset($_POST['view_log']) ? 1 : 0,
        ];

        // Update the permissions in the database
        $update_permissions_sql = "
            UPDATE permissions
            SET view_all_data = ?, edit_data = ?, manage_users = ?, view_log = ?
            WHERE permission_id = ?
        ";
        $stmt = mysqli_prepare($conn, $update_permissions_sql);
        mysqli_stmt_bind_param($stmt, 'iiiii',
            $permissions['view_all_data'],
            $permissions['edit_data'],
            $permissions['manage_users'],
            $permissions['view_log'],
            $role_id_to_update
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Log the update action
        $log_message = "Updated permissions for role ID: {$role_id_to_update}";
        $admin_user_id = $_SESSION['user_id']; // Logged-in user performing the action
        $log_stmt = mysqli_prepare($conn, "INSERT INTO logs (user_id, log_message) VALUES (?, ?)");
        mysqli_stmt_bind_param($log_stmt, 'is', $admin_user_id, $log_message);
        mysqli_stmt_execute($log_stmt);
        mysqli_stmt_close($log_stmt);

        echo "<script>alert('Role permissions updated successfully!');</script>";
    }

    // Handle role deletion
    if (isset($_POST['delete_role_id'])) {
        $delete_role_id = $_POST['delete_role_id'];

        // Delete the permissions linked to the role
        $delete_permissions_sql = "DELETE FROM role_permissions WHERE role_id = ?";
        $stmt = mysqli_prepare($conn, $delete_permissions_sql);
        mysqli_stmt_bind_param($stmt, 'i', $delete_role_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Now delete the role itself
        $delete_role_sql = "DELETE FROM roles WHERE role_id = ?";
        $stmt = mysqli_prepare($conn, $delete_role_sql);
        mysqli_stmt_bind_param($stmt, 'i', $delete_role_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Log the deletion action
        $log_message = "Deleted role ID: {$delete_role_id}";
        $admin_user_id = $_SESSION['user_id']; // Logged-in user performing the action
        $log_stmt = mysqli_prepare($conn, "INSERT INTO logs (user_id, log_message) VALUES (?, ?)");
        mysqli_stmt_bind_param($log_stmt, 'is', $admin_user_id, $log_message);
        mysqli_stmt_execute($log_stmt);
        mysqli_stmt_close($log_stmt);

        echo "<script>alert('Role deleted successfully!');</script>";
    }
}

// Fetch permissions for the selected role
if (isset($_POST['role_id'])) {
    $role_id_to_edit = $_POST['role_id'];
    
    $permissions_sql = "
        SELECT p.view_all_data, p.edit_data, p.manage_users, p.view_log
        FROM role_permissions rp
        JOIN permissions p ON rp.permission_id = p.permission_id
        WHERE rp.role_id = ?
    ";
    $stmt = mysqli_prepare($conn, $permissions_sql);
    mysqli_stmt_bind_param($stmt, 'i', $role_id_to_edit);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $view_all_data, $edit_data, $manage_users, $view_log);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
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
        /* Styling for form and toggle switches */
        .container {
            display: flex;
            max-width: 1200px;
            width: 90%;
            margin: 20px auto;
            gap: 20px;
        }
        .form-container {
            flex: 1;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .permission-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
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
    </style>
</head>
<body>
    <div class="container">
        <!-- Form to update role permissions -->
        <div class="form-container">
        <a href="http://localhost/Skypiea2/frontend/main.php" style="float:left"><i class="fas fa-arrow-left"></i></a>
            <h2>Update Role Permissions</h2>
            <form action="" method="POST">
                <div class="input-wrapper">
                    <label for="role_id">Select Role:</label>
                    <select name="role_id" id="role_id" required>
                        <option value="">-- Select a Role --</option>
                        <?php
                        while ($role = mysqli_fetch_assoc($role_result)) {
                            $selected = (isset($role_id_to_edit) && $role['role_id'] == $role_id_to_edit) ? 'selected' : '';
                            echo "<option value='{$role['role_id']}' {$selected}>{$role['role_name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <?php if (isset($role_id_to_edit)): ?>
                    <h3>Permissions:</h3>
                    <div class="permission-item">
                        <label class="toggle-button">
                            <input type="checkbox" name="view_all_data" value="1" <?php echo $view_all_data ? 'checked' : ''; ?> />
                            <span class="slider"></span>
                        </label>
                        <label>View All Data</label>
                    </div>
                    <div class="permission-item">
                        <label class="toggle-button">
                            <input type="checkbox" name="edit_data" value="1" <?php echo $edit_data ? 'checked' : ''; ?> />
                            <span class="slider"></span>
                        </label>
                        <label>Edit Data</label>
                    </div>
                    <div class="permission-item">
                        <label class="toggle-button">
                            <input type="checkbox" name="manage_users" value="1" <?php echo $manage_users ? 'checked' : ''; ?> />
                            <span class="slider"></span>
                        </label>
                        <label>Manage Users</label>
                    </div>
                    <div class="permission-item">
                        <label class="toggle-button">
                            <input type="checkbox" name="view_log" value="1" <?php echo $view_log ? 'checked' : ''; ?> />
                            <span class="slider"></span>
                        </label>
                        <label>View Logs</label>
                    </div>

                    <button type="submit">Update Permissions</button>
                <?php endif; ?>
            </form>
        </div>

        <!-- Form to delete role -->
        <div class="form-container">
            <h2>Delete Role</h2>
            <form action="" method="POST">
                <label for="delete
