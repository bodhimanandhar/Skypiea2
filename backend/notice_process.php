<?php 

require_once __DIR__ . '/dbconfig.php';

extract($_POST);

if(array_key_exists('sn',$_POST))
{
    $sql = "UPDATE notice SET content = '{$content}' WHERE sn = {$sn}";
} else{
    $sql = "INSERT INTO notice (sn,content,time) values
    (NULL, '{$content}', NULL)";
}

$result = mysqli_query($conn, $sql);

if ($result) {
    header('Location:../frontend/notice.php');
} else {
    die("something went wrong");
}