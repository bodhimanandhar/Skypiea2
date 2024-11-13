<?php
session_start();
$username = $_SESSION['username'];
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
        <h1>Dashboard</h1>
        <div class="right">
            <span class="username">Hi there, <?php echo $_SESSION['username'] ?></span>
            <form action="../backend/signout.php" method="POST" style="display:inline;">
                <button type="submit" class="signout-btn">Sign Out</button>
            </form>
        </div>
    </div>

    <div id="container" style="width:100%; height:400px;"></div>
    <!--
    TODO: PREDICTION FROM RASPBERRY PI GOES HERE
    -->
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

</html>
