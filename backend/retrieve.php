<?php
require_once __DIR__ . '/dbconfig.php'; 

header('Content-Type: application/json; charset=utf-8');

$stmt = $conn->prepare("SELECT temperature, humidity FROM readings ORDER BY id DESC LIMIT 2");
 
if ($stmt === false) {
    die('MySQL prepare error: ' . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'temperature' => $row['temperature'],
        'humidity' => $row['humidity']
    ];
}

$data = array_reverse($data);

echo json_encode($data);

$stmt->close();
$conn->close();
?>
