<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Adjust the path if necessary
session_start();
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
    $mail->addAddress('espemaliservice@example.com', 'Recipient'); // Add a recipient

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Your Authentication Code';
    $mail->Body    = 'Here is your code: <b>' . $randomNumber . '</b>';
    $mail->AltBody = 'Here is your code: ' . $randomNumber;

    // Send the email
    $mail->send();
    echo 'Email sent successfully! Your code sent is: ' . $randomNumber;
	//header("Location: auth.php");
	exit();
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
	
}
?>
