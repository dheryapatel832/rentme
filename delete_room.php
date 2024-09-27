<?php
// delete_room.php

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Include the database connection file
include 'db_connect.php'; // This will use the connection from db_connect.php

if (!isset($_GET['id'])) {
    die("Room ID is required.");
}

$roomId = intval($_GET['id']);

// Delete the room
$sql = "DELETE FROM rooms WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $roomId);

if ($stmt->execute()) {
    // Redirect to the list of rooms
    header("Location: list_rooms.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
