<?php

session_start();

$username = $_SESSION["username"];

if(!isset($username)){
    header("Location: ./frontend/signin.html");
    return;
}

header("Location: ./frontend/main.php")

?>
