<?php
// fetch_images.php

$roomId = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;

if ($roomId > 0) {
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

    // Fetch images for the room
    $sql = "SELECT image_filename FROM room_images WHERE room_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $roomId);
    $stmt->execute();
    $result = $stmt->get_result();

    $images = [];
    while ($row = $result->fetch_assoc()) {
        $images[] = $row['image_filename'];
    }

    $conn->close();
    echo json_encode($images);
} else {
    echo json_encode([]);
}
?>
