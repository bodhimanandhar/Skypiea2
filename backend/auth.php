<?php
session_start();

// Check if the 'code' field is set in the POST request
if (isset($_POST['code'])) {
    $code = $_POST['code']; // Get the value of the 'code' input field
    echo $code;  // Echo the value of 'code'
} else {
    echo "No code was submitted.";  // If 'code' is not set, output this message
}

if (isset($_SESSION['verification_code'])){
    $code1 = $_SESSION['verification_code']; 
    echo $code1;  
} else {
    echo "No email was submitted.";  
}


if($_POST['code']==$_SESSION['verification_code'])
{header("C:\Bodhi\New folder\Skypeia\display page\main.html");}
else{
echo"Wrong entry";
}
?>
