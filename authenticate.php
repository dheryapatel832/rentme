<?php
// authenticate.php

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "space_rental";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user input
$username = $_POST['username'];
$password = $_POST['password'];

// Prepare and execute the query
$stmt = $conn->prepare("SELECT password_hash FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($password_hash);
    $stmt->fetch();

    // Verify the password
    if (password_verify($password, $password_hash)) {
        // Start session and store user information
        session_start();
        $_SESSION['username'] = $username;

        // Redirect to the list_rooms.php page
        header("Location: list_rooms.php");
        exit();
    } else {
        echo "Invalid username or password.";
    }
} else {
    echo "Invalid username or password.";
}

$stmt->close();
$conn->close();
?>
