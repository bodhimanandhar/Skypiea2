<?php 
require_once __DIR__ . '/dbconfig.php';

$id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if ($id == NULL || $id == '') {
    die("Invalid user ID");
}

$sql = "SELECT * FROM user WHERE user_id = '{$id}'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) != 1) {
    die("User not found");
}

$row = mysqli_fetch_assoc($result);
extract($row);

// Fetch roles for the dropdown
$role_sql = "SELECT * FROM roles";
$role_result = mysqli_query($conn, $role_sql);
?>

<html>
<head>
    <link href="usertable.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="form-container">
    <form action="usertable_process.php" method="POST">
        <h2 class="form-title">Edit Account</h2>

        <input type="hidden" name="user_id" value="<?php echo $id; ?>"/>
        <div class="input-wrapper">
            <label>First Name</label>
            <input type="text" name="First_name" value="<?php echo $first_name; ?>"/><br/>
        </div>
        <div class="input-wrapper">
            <label>Last Name</label>
            <input type="text" name="Last_name" value="<?php echo $last_name; ?>"/><br/>
        </div>
        <div class="input-wrapper">
            <label>Email</label>
            <input type="text" name="Email" value="<?php echo $email_address; ?>"/><br/>
        </div>
        <div class="input-wrapper">
            <label>User Name</label>
            <input type="text" name="User_name" value="<?php echo $username; ?>"/><br/>
        </div>

        <div class="input-wrapper">
            <label>Role</label>
            <select name="Role_id">
                <?php 
                // Check if there are roles and display them in the select
                while ($role = mysqli_fetch_assoc($role_result)) {
                    // Set the selected role based on current user role
                    $selected = ($role['role_id'] == $role_id) ? 'selected' : '';
                    echo "<option value='{$role['role_id']}' {$selected}>{$role['role_name']}</option>";
                }
                ?>
            </select><br/>
        </div>

        <div class="input-wrapper">
            <input type="submit" value="Save"/>
        </div>
    </form>
    </div>
</body>
</html>
