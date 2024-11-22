<?php
// Include Composer's autoload file
require '../vendor/autoload.php';
require_once __DIR__ . '/dbconfig.php';  // Database connection
use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture form input
    $target_temperature = $_POST['target_temperature'];
    $insulation_type = $_POST['insulation_type'];
    $num_of_windows = $_POST['num_of_windows'];
    $room_volume = $_POST['room_volume'];

    // SSH credentials
    $ssh_host = '192.168.1.76';  // Remote host IP
    $ssh_user = 'admin';          // SSH username
    $ssh_pass = 'admin';          // SSH password
    $remote_dir = '/home/pi/myenv/lib/python3.12/site-packages/Adafruit_Python_DHT/model';  // Remote directory
    $script = 'run_energy_prediction.sh'; // Bash script to execute

    // Create a new SSH connection
    $ssh = new SSH2($ssh_host);
    if (!$ssh->login($ssh_user, $ssh_pass)) {
        exit('Login Failed');
    }

    // Build the remote command
    $command = "cd {$remote_dir} && ./{$script} {$target_temperature} {$insulation_type} {$num_of_windows} {$room_volume}";

    // Execute the command
    $output = $ssh->exec($command);

    // Clean and filter the output
    $output = preg_replace('/\x1b\[[0-9;]*m/', '', $output); // Remove ANSI escape codes (for terminal colors, etc.)
    $output = preg_replace('/^[^\n]*Target Temperature[^\n]*\n/', '', $output); // Remove any intro text
    $output = preg_replace('/^[^\n]*Adafruit_DHT[^\n]*\n/', '', $output); // Remove "Adafruit_DHT is available" message
    $output = preg_replace('/^[^\n]*Raspberry Pi[^\n]*\n/', '', $output); // Remove Raspberry Pi detection message
    $output = preg_replace('/^[^\n]*Data successfully sent to PHP server[^\n]*\n/', '', $output); // Remove Data sent confirmation

    // Extract the current temperature and humidity using regex
    preg_match('/Current Temperature \(°C\):\s*(\d+\.\d+)/', $output, $temp_match);
    preg_match('/Current Humidity \(%\):\s*(\d+\.\d+)/', $output, $humidity_match);
    preg_match('/Predicted Energy Consumption \(kWh\):\s*(\d+\.\d+)/', $output, $energy_match);

    $current_temperature = isset($temp_match[1]) ? $temp_match[1] : null;
    $current_humidity = isset($humidity_match[1]) ? $humidity_match[1] : null;
    $current_energy = isset($energy_match[1]) ? $energy_match[1]: null;
    if ($current_temperature !== null && $current_humidity !== null) {
        // Insert current temperature and humidity into the database using MySQLi
        $stmt = $conn->prepare("INSERT INTO `read` (`reading_id`, `temperature`, `humidity`, `times`) VALUES (NULL, ?, ?, current_timestamp())");
        $stmt->bind_param("dd", $current_temperature, $current_humidity);
        $stmt->execute();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Energy Consumption Prediction</title>
    <style>
        /* Global Styles */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
        }
        h1, h2, h3 {
            color: #4CAF50;
            text-align: center;
        }

        /* Container for the form and result */
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-container {
            margin-bottom: 30px;
        }

        /* Form Styling */
        .form-container label {
            font-size: 16px;
            margin-bottom: 8px;
            display: block;
        }

        .form-container input, .form-container select {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-container input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .form-container input[type="submit"]:hover {
            background-color: #45a049;
        }

        /* Result Styling */
        .result-container {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .result-container h3 {
            color: #333;
            margin-top: 0;
        }

        .result-container pre {
            background-color: #2c2c2c;
            color: #fff;
            padding: 15px;
            border-radius: 4px;
            font-size: 14px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        /* Responsive Design */
        @media screen and (max-width: 768px) {
            .container {
                width: 90%;
                padding: 15px;
            }
            .form-container input, .form-container select {
                font-size: 14px;
            }
        }
        /* Styling for the result container */
.result-container {
    background-color: #f9f9f9;      /* Light background */
    color: #333;                    /* Dark text color for readability */
    padding: 20px;                  /* Padding around the content */
    border-radius: 8px;             /* Rounded corners */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
    width: 100%;                    /* Full width of its container */
    max-width: 800px;               /* Max width for content */
    margin: 20px auto;              /* Centered with some spacing */
}

/* Heading for the result section */
.result-container h3 {
    color: #4CAF50;                /* Green color for the heading */
    font-size: 1.5em;              /* Larger font size for the heading */
    margin-bottom: 20px;           /* Space below the heading */
}

/* Styling for the prediction values */
.result-container .result-item {
    font-size: 1.1em;              /* Slightly larger font for the result items */
    margin-bottom: 12px;           /* Space between each result item */
}

/* Style for specific result values */
.result-container .result-item span {
    font-weight: bold;             /* Make the value bold */
    color: #4CAF50;                /* Green color for the values */
}

    </style>
</head>
<body>
  
    <div class="container">
    <a href="http://localhost/Skypiea2/frontend/main.php" style="float:left; color: #4CAF50; font-size:30px"><i class="fas fa-arrow-left"></i></a> 
        <h1>Energy Consumption Prediction</h1>

        <!-- Form for input -->
        <div class="form-container">
            <form method="POST" action="">
                <label for="target_temperature">Target Temperature (°C):</label>
                <input type="number" step="0.1" name="target_temperature" required><br>

                <label for="insulation_type">Insulation Type:</label>
                <select name="insulation_type">
                    <option value="Good">Good</option>
                    <option value="Average">Average</option>
                    <option value="Poor">Poor</option>
                </select><br>

                <label for="num_of_windows">Number of Windows:</label>
                <input type="number" name="num_of_windows" required><br>

                <label for="room_volume">Room Volume (m³):</label>
                <input type="number" step="0.1" name="room_volume" required><br>

                <input type="submit" value="Submit">
            </form>
        </div>

     <!-- Display result -->
    
        <div class="result-container">
    <h3>Prediction Result:</h3>
    <div class="result-item">
        Current Temperature (°C): <span><?php echo $current_temperature; ?></span>
    </div>
    <div class="result-item">
        Current Humidity (%): <span><?php echo $current_humidity; ?></span>
    </div>
    <div class="result-item">
        Predicted Energy Consumption (kWh): <span><?php echo $current_energy; ?></span>
    </div>
    </div>

    

</body>
</html>
