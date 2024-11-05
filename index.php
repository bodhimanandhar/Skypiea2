<?php

session_start();

$username = $_SESSION["username"];

if(!isset($username)){
    echo("okaa");
    header("Location: ./frontend/signin.html");
}

header("Location: ./frontend/main.html")

?>
