<?php
session_start();
require_once '../backend/dbconfig.php';

$username = $_SESSION["username"];

if (!isset($username)) {
    header("Location: /Skypiea2");
    return;
}

$stmt = $conn->prepare("SELECT role_id FROM user WHERE username = ?");
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row && $row['role_id'] != 1) {
    header("Location: /Skypiea2");
    exit;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE HTML>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="./base.css" rel="stylesheet" type="text/css">
    <title>Admin panel</title>
</head>

<body>
    <div class="navbar">
        <h1>Admin</h1>
        <div class="right">
            <form action="../backend/signout.php" method="POST" style="display:inline;">
                <button type="submit" class="signout-btn">Sign Out</button>
            </form>
        </div>
    </div>

</body>

</html>
