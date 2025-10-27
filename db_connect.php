<?php
$servername = "sql208.hstn.me";                   // From AeonFree MySQL Host
$username   = "mseet_40264447";                   // AeonFree DB username
$password   = "Alohomora";                        // AeonFree DB password
$database   = "mseet_40264447_hostel_food_waste"; // AeonFree DB name
$port       = 3306;                               // Default MySQL port

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database, $port);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
