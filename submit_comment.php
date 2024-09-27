<?php
// submit_comment.php

// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Include the database connection file
include 'db_connect.php'; // This will use the connection from db_connect.php
// Get user input
$room_id = $_POST['room_id'];
$comment = $_POST['comment'];
$username = $_SESSION['username'];

// Prepare and execute the query
$stmt = $conn->prepare("INSERT INTO comments (room_id, username, comment) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $room_id, $username, $comment);

if ($stmt->execute()) {
    // Redirect to the list of rooms after successful submission
    header("Location: list_rooms.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
