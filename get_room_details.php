<?php
header('Content-Type: application/json');

// Include the database connection file
include 'db_connect.php'; // This will use the connection from db_connect.php



if (isset($_GET['id'])) {
    $roomId = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT id, title, description, price FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $roomId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $room = $result->fetch_assoc();
        echo json_encode($room);
    } else {
        echo json_encode(['success' => false, 'message' => 'Room not found']);
    }

    $stmt->close();
}

$conn->close();
?>
