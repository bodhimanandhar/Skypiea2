<?php
// Initialize variables for displaying results
$sensor_temp = '';
$sensor_humidity = '';
$predicted_energy = '';
$target_temperature = '';
$error_message = '';

// Include phpseclib for SSH support
require_once('../vendor/autoload.php'); // Adjust path if needed
use phpseclib3\Net\SSH2;

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $target_temperature = $_POST['target_temperature'];
    $insulation_type = $_POST['insulation_type'];
    $num_of_windows = $_POST['num_of_windows'];
    $room_volume = $_POST['room_volume'];

    // Escape the user inputs to prevent shell injection
    $target_temperature = escapeshellarg($target_temperature);
    $insulation_type = escapeshellarg($insulation_type);
    $num_of_windows = escapeshellarg($num_of_windows);
    $room_volume = escapeshellarg($room_volume);

    // SSH connection to the Raspberry Pi
    $ssh = new SSH2('192.168.1.76'); // IP of the Pi
    if (!$ssh->login('admin', 'admin')) { // Replace with Pi's username and password
        $error_message = "SSH login failed";
    } else {
        // Path to your bash script on the Raspberry Pi
        $bash_script_path = '/home/pi/myenv/lib/python3.12/site-packages/Adafruit_Python_DHT/model/run_energy_prediction.sh';

        // Prepare the SSH command to run the bash script
        // Pass form data as environment variables
        $command = "TARGET_TEMPERATURE=$target_temperature INSULATION_TYPE=$insulation_type NUM_OF_WINDOWS=$num_of_windows ROOM_VOLUME=$room_volume $bash_script_path";

        // Execute the command on the Pi and capture the output
        $output = $ssh->exec($command);

        // Check if output is received
        if ($output) {
            // Split the output by new lines and extract data
            $output_lines = explode("\n", $output);

            // Debugging output
            echo "<pre>" . htmlspecialchars($output) . "</pre>";

            foreach ($output_lines as $line) {
                if (strpos($line, 'Sensor Temperature') !== false) {
                    $sensor_temp = trim(str_replace('Sensor Temperature:', '', $line));
                }
                if (strpos($line, 'Sensor Humidity') !== false) {
                    $sensor_humidity = trim(str_replace('Sensor Humidity:', '', $line));
                }
                if (strpos($line, 'Predicted Energy Consumption') !== false) {
                    $predicted_energy = trim(str_replace('Predicted Energy Consumption:', '', $line));
                }
                if (strpos($line, 'Target Temperature') !== false) {
                    $target_temperature = trim(str_replace('Target Temperature:', '', $line));
                }
            }
        } else {
            // If there's no output or an error in execution
            $error_message = "Error: Could not execute bash script.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Energy Consumption Prediction</title>
</head>
<body>
    <h2>Energy Consumption Prediction Form</h2>
    <!-- Form for taking user input -->
    <form action="predict_energy.php" method="POST">
        <label for="target_temperature">Target Temperature (°C):</label>
        <input type="number" step="0.1" name="target_temperature" required><br><br>

        <label for="insulation_type">Insulation Type:</label>
        <select name="insulation_type" required>
            <option value="none">None</option>
            <option value="poor">Poor</option>
            <option value="average">Average</option>
            <option value="good">Good</option>
            <option value="excellent">Excellent</option>
        </select><br><br>

        <label for="num_of_windows">Number of Windows:</label>
        <input type="number" name="num_of_windows" required><br><br>

        <label for="room_volume">Room Volume (m³):</label>
        <input type="number" step="0.1" name="room_volume" required><br><br>

        <input type="submit" value="Submit">
    </form>

    <!-- Display results -->
    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
        <h2>Prediction Results</h2>
        <?php if ($error_message): ?>
            <p style="color: red;"><?= $error_message ?></p>
        <?php else: ?>
            <p>Sensor Temperature: <?= $sensor_temp ?> °C</p>
            <p>Sensor Humidity: <?= $sensor_humidity ?> %</p>
            <p>Predicted Energy Consumption: <?= $predicted_energy ?> kWh</p>
            <p>Target Temperature: <?= $target_temperature ?> °C</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
