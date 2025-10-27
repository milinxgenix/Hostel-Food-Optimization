<?php
// db_connect.php
$servername = "sql208.hstn.me";                                 // AeonFree MySQL Host
$username   = "mseet_40264447";                                 // AeonFree DB username
$password   = "Alohomora";                                      // AeonFree DB password
$database   = "mseet_40264447_hostel_food_waste";               // AeonFree DB name
$port       = 3306;                                             // usually 3306

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database, $port);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// Optionally: remove the debug echo on production
// echo "Connected successfully";
?>
