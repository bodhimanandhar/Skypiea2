
<?php 

require_once __DIR__ . '/dbconfig.php';


$id = isset($_GET['user_id']) ? $_GET['user_id'] : null;


if ($id == NULL || $id == '')
{
    echo "$id";
    // die("invalid id or id not set");
}

$sql = "SELECT * FROM user WHERE user_id = '{$id}'";

$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) != 1) {
    die("invalid id ");
}

$row = mysqli_fetch_assoc($result);
extract($row);


?>

<html>
    <head>
    <link href="usertable.css" rel="stylesheet" type="text/css">
    </head>
    </head>
<body>
    <div class="form-container">
<form action="usertable_process.php" method="POST">
<h2 class="form-title">Edit Account</h2>

<input type="hidden" name="user_id" value="<?php echo $id;?>"/>
<div class="input-wrapper"><label>First Name</label>   <input type="text" name="First_name" value="<?php echo $first_name;?>"/><br/></div>
<div class="input-wrapper"><label>Last Name</label>    <input type="text" name="Last_name" value="<?php echo $last_name;?>"/><br/></div>
<div class="input-wrapper"><label>Email</label>        <input type="text" name="Email" value="<?php echo $email_address;?>"/><br/></div>
<div class="input-wrapper"><label>User Name</label>    <input type="text" name="User_name" value="<?php echo $username;?>"/><br/></div>
<div class="input-wrapper"><label>Role</label>    <input type="text" name="Role_id" value="<?php echo $role_id;?>"/><br/></div>
<div class="input-wrapper"><input type="submit" value="Save"></div>
</form>
</div>
</body>
</html>

