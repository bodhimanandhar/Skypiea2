<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
session_start();
require 'C:\xampp\htdocs\skypiea\vendor\autoload.php';
require_once __DIR__ . '/dbconfig.php'; 

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    $stmt = $conn->prepare("SELECT email_address FROM user WHERE username = ?");
    
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    $stmt->bind_param('s', $username); 
    $stmt->execute();

    // Bind result
    $stmt->bind_result($email_address);

    // Fetch the result to populate $email_address
    if ($stmt->fetch()) {
        echo "Email Address: " . $email_address;  // This will print the email address
    } else {
        echo "No email found for username: " . $username;
    }

    $stmt->close();
} 
else {
    header("Location: ./frontend/signin.html");
}

// Close the database connection
$conn->close();

// PHPMailer Code (same as before)
$mail = new PHPMailer(true);
try {
    // Generate a random 4-digit number
    $randomNumber = rand(1000, 9999);
    $_SESSION['verification_code'] = $randomNumber;
    // SMTP server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'espemaliservice@gmail.com'; // Your Gmail address
    $mail->Password   = 'rjkn dvxc rqgg zkia';     // Your App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('espemaliservice@gmail.com', 'Mailer'); // Your email
    $mail->addAddress($email_address, 'Recipient'); // Add a recipient

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Your Authentication Code';
    $mail->Body    = 'Here is your code: <b>' . $randomNumber . '</b>';
    $mail->AltBody = 'Here is your code: ' . $randomNumber;

    // Send the email
    $mail->send();
    echo 'Email sent successfully! Your code sent is: ' . $randomNumber;
	//header("Location: auth.php");
    header("Location: /skypiea/frontend/auth.html");
    exit();
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo} ";
}
?>
