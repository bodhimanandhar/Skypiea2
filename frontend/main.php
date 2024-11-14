<?php
session_start();
$username = $_SESSION["username"];
if (!isset($username)) {
    header("Location: ./signin.html");
    return;
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
            <button onclick="window.location='../backend/usertable.php'" type="submit" class="signout-btn">UserTable</button>
            <button onclick="window.location='../backend/for_roles.php'" type="submit" class="signout-btn">AddRole</button>
            <button onclick="window.location='../backend/for_role_update.php'" type="submit" class="signout-btn">UpdateRole</button>
            <button onclick="window.location='./findtemp.html'" type="submit" class="signout-btn">FindWeather</button>
            
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
    document.addEventListener('DOMContentLoaded', function() {
        fetch('../backend/retrieve.php')
            .then(response => response.json())
            .then(data => {
                const temperatures = data.map(item => parseFloat(item.temperature));
                const humidities = data.map(item => parseFloat(item.humidity));
                const categories = data.map((_, index) => `Reading ${index + 1}`);

                Highcharts.chart('container', {
                    chart: {
                        type: 'line'
                    },
                    title: {
                        text: 'Temperature and Humidity Readings'
                    },
                    xAxis: {
                        categories: categories,
                        title: {
                            text: 'Readings'
                        }
                    },
                    yAxis: {
                        title: {
                            text: 'Values'
                        }
                    },
                    series: [{
                        name: 'Temperature',
                        data: temperatures
                    }, {
                        name: 'Humidity',
                        data: humidities
                    }]
                });
            })
            .catch(error => console.error('Error fetching data:', error));
    });
</script>


<script>
    async function filldata() {
        const response = await fetch("../backend/retrieve.php")
        const data = await response.json()
        let temp_array = [];
        for (v in data) {
            temp_array.push(data[v]['temperature'])
        }
        let avg = 0;
        for (let i = 0; i < temp_array.length; i++) {
            avg += temp_array[i]
        }
        document.getElementById("average").innerText = (avg / temp_array.length).toFixed(1)
        document.getElementById("min").innerText = (Math.min(...temp_array)).toFixed(1)
        document.getElementById("max").innerText = (Math.max(...temp_array)).toFixed(1)


        let humidity_array = [];
        for (v in data) {
            humidity_array.push(data[v]['humidity'])
        }
        avg = 0;
        for (let i = 0; i < humidity_array.length; i++) {
            avg += humidity_array[i]
        }
        document.getElementById("average_h").innerText = (avg / humidity_array.length).toFixed(1)
        document.getElementById("min_h").innerText = (Math.min(...humidity_array)).toFixed(1)
        document.getElementById("max_h").innerText = (Math.max(...humidity_array)).toFixed(1)
    }
    filldata();
</script>

</html>
