
<?php 

require_once __DIR__ . '/dbconfig.php';


$user_id = isset($_GET['sn']) ? $_GET['sn'] : null;

if ($user_id == NULL || $user_id == '')
{
    die("invalid id or id not set");
}

$sql = "SELECT * FROM notice WHERE sn = {$user_id}";

$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) != 1) {
    die("invalid id ");
}


$delSql = "DELETE FROM notice WHERE sn = {$user_id}";
$result = mysqli_query($conn, $delSql);

if ($result) {
    header('Location: ../frontend/notice.php');
} else {
    die("something went wrong");
}
