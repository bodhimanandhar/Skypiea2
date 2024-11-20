<?php
// Get the POST data from Python
$sensor_temp = $_POST['sensor_temp'];
$sensor_humidity = $_POST['sensor_humidity'];
$prediction = $_POST['prediction'];
$target_temperature = $_POST['target_temperature'];

// Display the received data
echo "<h2>Prediction Results</h2>";
echo "Sensor Temperature: " . $sensor_temp . " °C<br>";
echo "Sensor Humidity: " . $sensor_humidity . " %<br>";
echo "Target Temperature: " . $target_temperature . " °C<br>";
echo "Predicted Energy Consumption: " . $prediction . " kWh<br>";
?>
