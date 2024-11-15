<?php
session_start();
$username = $_SESSION["username"];
if (!isset($username)) {
    header("Location: ./signin.html");
    exit();
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
} else {
    echo "Role ID not found for the user.";
}

$sql = "SELECT * FROM `notice`";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
    <link href="./base.css" rel="stylesheet" type="text/css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="base.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <title>Find Weather</title>
</head>

<body>


    <div class="navbar">
        <h1><a href="/Skypiea2" style="a:link{color: red;}">Dashboard</a></h1>
        <div class="right">
            <form action="../backend/signout.php" method="POST" style="display:inline;">
                <button type="submit" class="signout-btn">Sign Out</button>
            </form>
        <button type="submit" class="signout-btn"><a href="mailto:espemaliservice@gmail.com">Mail</a></button>
        <button onclick="window.location='notice.php'" type="submit" class="signout-btn">Notice</button>
        <button onclick="window.location='./findtemp.html'" type="submit" class="signout-btn">FindWeather</button>
        <?php if (!empty($user_permissions['edit_data'])): ?>
            <button onclick="window.location='../backend/usertable.php'" type="submit" class="signout-btn">UserTable</button>
            <button onclick="window.location='../backend/for_roles.php'" type="submit" class="signout-btn">AddRole</button>
            <button onclick="window.location='../backend/for_role_update.php'" type="submit" class="signout-btn">UpdateRole</button>
            <button onclick="window.location='../backend/view_log.php'" type="submit" class="signout-btn">Log</button>
        <?php endif; ?>
        </div>
    </div>


  <div class="container">
    <div class="row">
     <a href="http://localhost/Skypiea2/frontend/main.php" style="float:left"><i class="fas fa-arrow-left"></i></a> 
      <div class="search">  
        <input type="text" placeholder="Search.." value="Kathmandu">
        <button type="submit" class="search_button"><i class="fa fa-search"></i></button>
      </div>
    </div>
    <div class="weather">
      <div class="row">
        <div class="weather-info">
          <i class='icon' id="weather-icon"></i> <!-- Using 'fas' from Font Awesome 5+ -->
          <h1 class="city">Kathmandu</h1>
        </div>
      </div>
      <div class="row1">
        <div class="column">
          <i class='fas fa-temperature-high'></i>
          <h1 class="temp">22°C</h1>
        </div>
        <div class="col"></div>
        <div class="col1"></div>
        <div class="column">
          <i class="fa fa-flag" aria-hidden="true"></i>
          <h1 class="wind">12</h1>
        </div>
        <div class="col"></div>
        <div class="col1"></div>
        <div class="column1">
          <i class="fas fa-tint"></i>
          <h1 class="humidity">45</h1>
        </div>
      </div>
    </div>
  </div>

  <script>

    const apiKey = "d008c91393605f60cd977d9842c37beb";
    const apiUrl = "https://api.openweathermap.org/data/2.5/weather?units=metric&q=";

    const searchBox = document.querySelector(".search input");
    const searchBtn = document.querySelector(".search button");
    const weatherIcon = document.querySelector(".weather-icon");
    async function checkWeather(city) {
      const response = await fetch(apiUrl + city + `&appid=${apiKey}`);
      var data = await response.json();
      console.log(data);
      const weatherIcon = document.getElementById("weather-icon");

      if (data.cod === "404") {
        alert("Location not found. Please try another.");
        return; // Exit the function if the location is not found
      }


      document.querySelector(".city").innerHTML = data.name;
      document.querySelector(".temp").innerHTML = Math.round(data.main.temp) + "°C";
      document.querySelector(".humidity").innerHTML = data.main.humidity + "%";
      document.querySelector(".wind").innerHTML = data.wind.speed + "km/h";



      if (data.weather[0].main === "Clouds") {
        weatherIcon.className = 'icon fas fa-cloud'; // Cloud icon
      } else if (data.weather[0].main === "Clear") {
        weatherIcon.className = 'icon fas fa-sun'; // Sun icon
      } else if (data.weather[0].main === "Rain") {
        weatherIcon.className = 'icon fas fa-cloud-showers-heavy'; // Rain icon
      } else {
        weatherIcon.className = 'icon fas fa-smog'; // Default icon for other conditions
      }
    }
    searchBox.value = "Kathmandu";
    checkWeather("Kathmandu");

    searchBtn.addEventListener("click", () => {
      searchBtn.addEventListener("click", () => {
        const userInput = searchBox.value.trim();

        // Regular expression to allow only letters and spaces
        const locationPattern = /^[a-zA-Z\s]+$/;

        if (locationPattern.test(userInput)) {
          checkWeather(userInput);
        } else {
          alert("Please enter a valid location name.");
        }
      });
    })

    searchBox.addEventListener("keydown", (event) => {
      if (event.key === "Enter") {

        const userInput = searchBox.value.trim();

        // Regular expression to allow only letters and spaces
        const locationPattern = /^[a-zA-Z\s]+$/;

        if (locationPattern.test(userInput)) {
          checkWeather(userInput);
        } else {
          alert("Please enter a valid location name.");
        }
      }
    });


  </script>


</body>

</html>
