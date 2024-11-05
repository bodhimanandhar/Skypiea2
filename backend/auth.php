<?php
session_start();

// Check if the 'code' field is set in the POST request
if (isset($_POST['code'])) {
    $code = $_POST['code']; // Get the value of the 'code' input field
} else {
    echo "No code was submitted.";  // If 'code' is not set, output this message
    exit; // End the script execution if no code is submitted
}

// Check if the session variable 'verification_code' is set
if (isset($_SESSION['verification_code'])) {
    $code1 = $_SESSION['verification_code']; 
} else {
    echo "No email was submitted.";  
    exit; // End the script execution if no verification code is in the session
}

// Debugging - Check the values of 'code' and 'verification_code'
echo "Submitted code: " . $code . "<br>";
echo "Session code: " . $code1 . "<br>";

// Compare the submitted 'code' with the session's 'verification_code'
if ($code == $code1) {
    // Make sure this is placed before any output (echo statements)
    header("Location: http://localhost/skypiea/frontend/display%20page/main.html");
    exit; // Don't forget to call exit after header redirection
} else {
    echo "Wrong entry";
}
?>
