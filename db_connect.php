<?php
// Database connection
$servername = "sql307.infinityfree.com";
$username = "if0_37352237";
$password = "Dp1245dP";
$dbname = "if0_37352237_space_rental";


// Create the database connection using MySQLi
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
