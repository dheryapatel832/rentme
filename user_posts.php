<?php
// user_posts.php

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

// Check if a user ID is passed in the URL
if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);

    // Debug: Output the user ID
    // Uncomment the line below if you want to see the user ID for debugging
    // echo "User ID: " . $user_id . "<br>";

    // Fetch the user's rooms directly from the rooms table using user_id
    $sql = "SELECT rooms.id AS room_id, rooms.title, rooms.description, rooms.price, 
            users.username, users.profile_picture 
            FROM rooms 
            JOIN users ON users.id = rooms.user_id 
            WHERE rooms.user_id = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Failed to prepare the statement: " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any rooms are found for the user
    if ($result->num_rows > 0) {
        // Fetch the first room to get the username and profile picture
        $firstRow = $result->fetch_assoc();
        $username = $firstRow['username'];
        $profile_picture = $firstRow['profile_picture'];

        // Display user's profile information
        echo "<div class='user-profile'>";
        echo "<img src='uploads/profile_pictures/" . htmlspecialchars($profile_picture) . "' alt='Profile Picture' class='profile-pic'>";
        echo "<h1>Posts by " . htmlspecialchars($username) . "</h1>";
        echo "</div>";

        // Loop through the rooms and display them
        do {
            echo "<div class='room'>";
            echo "<h2>Room ID: " . htmlspecialchars($firstRow['room_id']) . "</h2>";  // Displaying the room ID
            echo "<h2>" . htmlspecialchars($firstRow['title']) . "</h2>";
            echo "<p>" . htmlspecialchars($firstRow['description']) . "</p>";
            echo "<p>Price: $" . htmlspecialchars($firstRow['price']) . "</p>";

            // Fetch and display images for the room
            $room_id = $firstRow['room_id'];
            $imageSql = "SELECT image_filename FROM room_images WHERE room_id = ?";
            $imageStmt = $conn->prepare($imageSql);
            $imageStmt->bind_param("i", $room_id);
            $imageStmt->execute();
            $imageResult = $imageStmt->get_result();

            if ($imageResult->num_rows > 0) {
                echo "<div class='room-images'>";
                while ($imageRow = $imageResult->fetch_assoc()) {
                    echo "<img src='uploads/" . htmlspecialchars($imageRow['image_filename']) . "' alt='Room Image' class='room-image'>";
                }
                echo "</div>"; // Close room-images div
            }

            echo "</div>"; // Close room div
        } while ($firstRow = $result->fetch_assoc());

    } else {
        echo "<p>This user has not posted any rooms yet.</p>";
    }

    // Close the statement
    $stmt->close();

} else {
    echo "<p>No user ID provided.</p>";
}

// Close the database connection
$conn->close();
?>
