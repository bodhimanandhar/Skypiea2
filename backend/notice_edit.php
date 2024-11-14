<?php 
require_once __DIR__ . '/dbconfig.php';

$id = isset($_GET['sn']) ? $_GET['sn'] : null;

if ($id == NULL || $id == '') {
    die("Invalid user ID");
}

$sql = "SELECT * FROM notice WHERE sn = '{$id}'";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="form-container">
    <a href="http://localhost/Skypiea2/frontend/notice.php" style="float:left"><i class="fas fa-arrow-left"></i></a>
    <form action="notice_process.php" method="POST">
        <h2 class="form-title">Edit Account</h2>

        <input type="hidden" name="user_id" value="<?php echo $id; ?>"/>
        <div class="input-wrapper">
            <label>Content</label>
            <textarea name="content"rows="4" cols="75"><?php echo $content; ?></textarea>
            <br/>
        </div>
     
        <div class="input-wrapper">
            <input type="submit" value="Save"/>
        </div>
    </form>
    </div>
</body>
</html>
