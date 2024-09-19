<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

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

// Get the user ID from the session
$currentUserId = $_SESSION['user_id']; // Ensure this is set when the user logs in

// Get the room ID from POST data
$roomId = intval($_POST['room_id']);

// Verify that the room belongs to the current user
$sql = "SELECT user_id FROM rooms WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $roomId);
$stmt->execute();
$result = $stmt->get_result();
$room = $result->fetch_assoc();

if ($room['user_id'] != $currentUserId) {
    echo "You do not have permission to update this room.";
    exit();
}

// Update room details
$title = $_POST['title'];
$description = $_POST['description'];
$price = $_POST['price'];

$sql = "UPDATE rooms SET title = ?, description = ?, price = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssdi", $title, $description, $price, $roomId);
$stmt->execute();

// Handle image uploads if any
if (!empty($_FILES['images']['name'][0])) {
    $uploadedImages = $_FILES['images'];
    $uploadDir = 'uploads/';
    
    // Create upload directory if not exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    foreach ($uploadedImages['name'] as $key => $name) {
        if ($uploadedImages['error'][$key] == UPLOAD_ERR_OK) {
            $tmpName = $uploadedImages['tmp_name'][$key];
            $fileExtension = pathinfo($name, PATHINFO_EXTENSION);
            $newFileName = uniqid() . '.' . $fileExtension;
            $uploadFile = $uploadDir . $newFileName;

            // Move the uploaded file to the destination directory
            if (move_uploaded_file($tmpName, $uploadFile)) {
                // Save file information to database
                $sql = "INSERT INTO room_images (room_id, image_filename) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("is", $roomId, $newFileName);
                $stmt->execute();
            } else {
                echo "Failed to upload image: " . htmlspecialchars($name);
            }
        } else {
            echo "Error uploading file: " . htmlspecialchars($name);
        }
    }
}

$stmt->close();
$conn->close();

header("Location: room_list.php");
exit();
?>
