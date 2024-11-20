<?php
session_start();
$username = $_SESSION["username"];
if (!isset($username)) {
    header("Location: ./signin.html");
    return;
}

require_once 'C:\xampp\htdocs\skypiea2\backend\dbconfig.php';

// Fetch role_id based on user_id stored in the session
$user_id = $_SESSION["user_id"]; // Ensure user_id is set in the session

// Query to get the role_id of the user
$role_query = "SELECT role_id FROM user WHERE user_id = ?";
$stmt = $conn->prepare($role_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($role_id);
$stmt->fetch();
$stmt->close();

if ($role_id) {
    // Fetch permissions associated with the role_id
    $permissions_sql = "
        SELECT p.view_all_data, p.edit_data, p.manage_users, p.view_log 
        FROM role_permissions rp 
        INNER JOIN permissions p ON rp.permission_id = p.permission_id 
        WHERE rp.role_id = ?
    ";
    $stmt = $conn->prepare($permissions_sql);
    $stmt->bind_param("i", $role_id);
    $stmt->execute();
    $permissions_result = $stmt->get_result();
    $user_permissions = $permissions_result->fetch_assoc();
}
else {
    echo "Role ID not found for the user.";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="./base.css" rel="stylesheet" type="text/css">
    <title>Dashboard</title>
</head>

<body>
    <div class="navbar">
        <h1><a href="/Skypiea2">Dashboard</a></h1>
        <div class="right">
            <span class="username">Hi there, <?php echo $_SESSION[
                "username"
            ]; ?></span>
            <form action="../backend/signout.php" method="POST" style="display:inline;">
                <button type="submit" class="signout-btn">Sign Out</button>
            </form>
            <button type="submit" class="signout-btn"><a href="mailto:espemaliservice@gmail.com">Mail</a></button>
            <button onclick="window.location='notice.php'" type="submit" class="signout-btn">Notice</button>
           
            <button onclick="window.location='./findtemp.php'" type="submit" class="signout-btn">FindWeather</button>
            
            <?php if (!empty($user_permissions['edit_data'])): ?>
            <button onclick="window.location='../backend/usertable.php'" type="submit" class="signout-btn">UserTable</button>
            <button onclick="window.location='../backend/for_roles.php'" type="submit" class="signout-btn">AddRole</button>
            <button onclick="window.location='../backend/for_role_update.php'" type="submit" class="signout-btn">UpdateRole</button>
            <button onclick="window.location='../backend/view_log.php'" type="submit" class="signout-btn">Log</button>
        <?php endif; ?>
        </div>
    </div>

    <div id="container" style="width:100%; height:400px;"></div>

    <div style="width: 80%; text-align:center;">
        <div id="temperature_list" style="float:left; margin-left: 10%">
            Average Temp<div style="font-size: 2rem;" id="average">
            </div>

            Min Temp<div style="font-size: 2rem;" id="min">
            </div>

            Max Temp<div style="font-size: 2rem;" id="max">
            </div>
        </div>


        <div id="humidity_list" style="float:right">
            Average Humidity<div style="font-size: 2rem;" id="average_h">
            </div>

            Min Humidity<div style="font-size: 2rem;" id="min_h">
            </div>

            Max Humidity<div style="font-size: 2rem;" id="max_h">
            </div>
        </div>
    </div>

</body>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
        // Fetch data from backend
        document.addEventListener('DOMContentLoaded', function() {
            fetch('../backend/retrieve.php')
                .then(response => response.json())
                .then(data => {
                    // Extract temperature, humidity, and timestamps
                    const temperatures = data.map(item => parseFloat(item.temperature));
                    const humidities = data.map(item => parseFloat(item.humidity));
                    const times = data.map(item => item.times); // This will be used as categories

                    // Create Highcharts chart
                    Highcharts.chart('container', {
                        chart: {
                            type: 'line'
                        },
                        title: {
                            text: 'Temperature and Humidity Readings'
                        },
                        xAxis: {
                            categories: times, // Use the timestamps for the X-axis
                            title: {
                                text: 'Time'
                            }
                        },
                        yAxis: {
                            title: {
                                text: 'Values'
                            }
                        },
                        series: [{
                            name: 'Temperature (°C)',
                            data: temperatures
                        }, {
                            name: 'Humidity (%)',
                            data: humidities
                        }]
                    });

                    // Calculate and display stats for temperature
                    let tempArray = temperatures;
                    let tempAvg = tempArray.reduce((sum, val) => sum + val, 0) / tempArray.length;
                    let tempMin = Math.min(...tempArray);
                    let tempMax = Math.max(...tempArray);
                    document.getElementById("average").innerText = tempAvg.toFixed(1);
                    document.getElementById("min").innerText = tempMin.toFixed(1);
                    document.getElementById("max").innerText = tempMax.toFixed(1);

                    // Calculate and display stats for humidity
                    let humidityArray = humidities;
                    let humidityAvg = humidityArray.reduce((sum, val) => sum + val, 0) / humidityArray.length;
                    let humidityMin = Math.min(...humidityArray);
                    let humidityMax = Math.max(...humidityArray);
                    document.getElementById("average_h").innerText = humidityAvg.toFixed(1);
                    document.getElementById("min_h").innerText = humidityMin.toFixed(1);
                    document.getElementById("max_h").innerText = humidityMax.toFixed(1);
                })
                .catch(error => console.error('Error fetching data:', error));
        });
    </script>
</html>
