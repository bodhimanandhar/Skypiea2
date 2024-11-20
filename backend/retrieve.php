<?php
require_once __DIR__ . '/dbconfig.php';

header('Content-Type: application/json; charset=utf-8');

// Prepare the SQL query to get temperature, humidity, and times from the 'read' table
$stmt = $conn->prepare("SELECT temperature, humidity, times FROM `read`");

if ($stmt === false) {
    die('MySQL prepare error: ' . $conn->error);
}

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'temperature' => $row['temperature'],
            'humidity' => $row['humidity'],
            'times' => $row['times'] // Including the timestamp
        ];
    }

    // Return the data as a JSON response
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} else {
    die('Execute failed: ' . $stmt->error);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
