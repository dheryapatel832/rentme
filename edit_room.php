<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    die("Access denied. You must be logged in to view this page.");
}

// Include the database connection file
include 'db_connect.php'; // This will use the connection from db_connect.php

// Get the logged-in username
$logged_in_username = $_SESSION['username'];

// Fetch user details
$sql = "SELECT id, profile_picture FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $logged_in_username);
$stmt->execute();
$user_result = $stmt->get_result();

if ($user_result->num_rows === 0) {
    die("User not found.");
}

$user_row = $user_result->fetch_assoc();
$user_id = $user_row['id']; // Correct column name is 'id'
$profile_picture = $user_row['profile_picture'] ? 'uploads/profile_pictures/' . htmlspecialchars($user_row['profile_picture']) : 'default-avatar.png';

// Fetch rooms for the logged-in user
$sql = "SELECT id, title, description, price FROM rooms WHERE user_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare for rooms query failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$room_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Rooms</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .room-item {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .room-images {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
        }

        .room-images img {
            width: 150px; /* Adjust as needed */
            height: 150px; /* Adjust as needed */
            object-fit: cover;
            border-radius: 8px;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .profile-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .profile-pic {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 15px;
        }

        .edit-button, .delete-button {
            display: inline-block;
            margin-right: 10px;
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .delete-button {
            background-color: #dc3545; /* Red */
        }
    </style>
</head>
<body>
    <header>
        <h1>My Rooms</h1>
        <div class="profile-container">
            <img src="<?php echo $profile_picture; ?>" alt="Profile Picture" class="profile-pic">
            <p>Welcome, <?php echo htmlspecialchars($logged_in_username); ?>!</p>
        </div>
        <p><a href="logout.php">Logout</a></p>
    </header>

    <main>
        <section id="users">
            <h2>Your Rooms</h2>
            <?php
            if ($room_result->num_rows > 0) {
                while ($row = $room_result->fetch_assoc()) {
                    $room_id = $row['id'];
                    
                    // Fetch images for the room
                    $imageSql = "SELECT image_filename FROM room_images WHERE room_id = ?";
                    $imageStmt = $conn->prepare($imageSql);
                    $imageStmt->bind_param("i", $room_id);
                    $imageStmt->execute();
                    $imageResult = $imageStmt->get_result();

                    $images = [];
                    while ($imageRow = $imageResult->fetch_assoc()) {
                        $images[] = htmlspecialchars($imageRow["image_filename"]);
                    }

                    echo "<div class='room-item'>";
                    echo "<h3>" . htmlspecialchars($row["title"]) . "</h3>";
                    echo "<p>" . htmlspecialchars($row["description"]) . "</p>";
                    echo "<p>Price: $" . htmlspecialchars($row["price"]) . " per night</p>";

                    // Display room images if available
                    if (count($images) > 0) {
                        echo "<div class='room-images'>";
                        foreach ($images as $image) {
                            echo "<img src='uploads/" . $image . "' alt='Room Image'>";
                        }
                        echo "</div>";
                    }

                    // Links to edit and delete the room
                    echo "<a href='update_room.php?id=" . urlencode($room_id) . "' class='edit-button'>Edit</a>";
                    echo "<a href='delete_room.php?id=" . urlencode($row["id"]) . "' class='delete-button'>Delete</a>";
                    echo "</div>"; // Close room-item div
                }
            } else {
                echo "<p>You have no rooms listed.</p>";
            }
            ?>
        </section>
    </main>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
