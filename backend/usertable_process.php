<?php 

require_once __DIR__ . '/dbconfig.php';

extract($_POST);

if(array_key_exists('user_id',$_POST))
{
    $sql = "UPDATE user SET first_name = '{$First_name}', last_name = '{$Last_name}', email_address = '{$Email}',username='{$User_name}',role_id='{$Role_id}' WHERE user_id = {$user_id}";
} else {
    $hash_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO user (user_id,first_name,last_name,email_address,password,username,created_at,updated_at) values
    (NULL, '{$First_name}', '{$Last_name}', '{$Email}','{$hash_password}','{$User_name}',NULL,NULL)";
}

$result = mysqli_query($conn, $sql);

if ($result) {
    header('Location: usertable.php');
} else {
    die("something went wrong");
}