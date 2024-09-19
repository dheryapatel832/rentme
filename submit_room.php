<?php
// submit_room.php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    die("Access denied. You must be logged in to add a room.");
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "space_rental";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in username
$logged_in_username = $_SESSION['username'];

// Fetch user ID for the logged-in user
$sql = "SELECT id FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $logged_in_username);
$stmt->execute();
$user_result = $stmt->get_result();

if ($user_result->num_rows === 0) {
    die("User not found.");
}

$user_row = $user_result->fetch_assoc();
$user_id = $user_row['id'];

// Get room details from the form
$title = $_POST['title'];
$description = $_POST['description'];
$price = $_POST['price'];

// Insert new room with the user ID
$sql = "INSERT INTO rooms (title, description, price, user_id) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssdi", $title, $description, $price, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $room_id = $stmt->insert_id; // Get the ID of the newly inserted room

    // Handle image uploads
    if (isset($_FILES['images'])) {
        $upload_dir = 'uploads/';
        $total_files = count($_FILES['images']['name']);

        for ($i = 0; $i < $total_files; $i++) {
            $tmp_name = $_FILES['images']['tmp_name'][$i];
            $file_name = basename($_FILES['images']['name'][$i]);
            $target_file = $upload_dir . $file_name;

            // Check if file is an image (optional)
            $check = getimagesize($tmp_name);
            if ($check !== false) {
                if (move_uploaded_file($tmp_name, $target_file)) {
                    // Insert image record into the database
                    $sql = "INSERT INTO room_images (room_id, image_filename) VALUES (?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("is", $room_id, $file_name);
                    $stmt->execute();
                } else {
                    echo "Error uploading file: " . $file_name . "<br>";
                }
            } else {
                echo "File is not an image: " . $file_name . "<br>";
            }
        }
    }

    // Redirect to the list rooms page
    header("Location: list_rooms.php");
    exit();
} else {
    echo "Error adding room: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
