<?php
// list_rooms.php

// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
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

// Fetch available rooms
$sql = "SELECT id, title, description, price FROM rooms";
$result = $conn->query($sql);
?>
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Rooms</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .plus-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background-color: #007BFF;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            text-align: center;
            z-index: 1000;
        }

        .plus-button:hover {
            background-color: #0056b3;
        }

        .room-item {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .room-images {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
            position: relative;
            max-height: 300px;
            overflow: hidden;
        }

        .room-images .main-images {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .room-images .main-images img {
            height: 200px;
            object-fit: cover;
            cursor: pointer;
            border-radius: 8px;
        }

        .room-images .extra-images {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            display: flex;
            flex-wrap: wrap;
            align-content: flex-start;
            gap: 10px;
        }

        .room-images .extra-images img {
            height: 100px;
            width: 100px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
        }

        .room-images .more-images-overlay {
            position: absolute;
            bottom: 0;
            right: 0;
            background: rgba(0,0,0,0.5);
            color: white;
            padding: 5px;
            border-radius: 8px;
            font-size: 14px;
        }

        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            position: relative;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
.minus-link {
    display: inline-block;
    width: 60px;
    height: 60px;
    background-color: #FF5722; /* Color of the button */
    color: white;
    border-radius: 50%;
    text-align: center;
    line-height: 60px; /* Center the text vertically */
    font-size: 30px;
    text-decoration: none; /* Remove underline */
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.minus-link:hover {
    background-color: #E64A19; /* Hover color */
}
        .modal-image {
            width: 100%;
            height: auto;
            max-height: 400px;
            object-fit: cover;
            border-radius: 8px;
        }

        .prev, .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 24px;
            color: white;
            background: rgba(0,0,0,0.5);
            padding: 10px;
            border-radius: 50%;
        }

        .prev {
            left: 10px;
        }

        .next {
            right: 10px;
        }

        .comments-header {
            cursor: pointer;
            font-weight: bold;
            color: #007BFF;
        }

        .comments-header:hover {
            text-decoration: underline;
        }

        .comments-section {
            display: none; /* Initially hidden */
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .comments-section.expanded {
            display: block; /* Show when expanded */
        }
    </style>
</head>
<body>
<header>
        <h1>Available Rooms</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        <p><a href="logout.php">Logout</a></p>
<a href="edit_room.php">Edit</a></header>

    <main>
        <section id="room-list">
            <a href="update_profile.php">profile</a>
            <h2>Available Rooms</h2>
            <?php
            if ($result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='room-item'>";
                    echo "<h3>" . htmlspecialchars($row["title"]) . "</h3>";
                    echo "<p>" . htmlspecialchars($row["description"]) . "</p>";
                    echo "<p>Price: $" . htmlspecialchars($row["price"]) . " per night</p>";
                    
                    // Display room images if available
                    $roomId = $row["id"];
                    $imageSql = "SELECT image_filename FROM room_images WHERE room_id = ?";
                    $imageStmt = $conn->prepare($imageSql);
                    $imageStmt->bind_param("i", $roomId);
                    $imageStmt->execute();
                    $imageResult = $imageStmt->get_result();

                    $images = [];
                    while ($imageRow = $imageResult->fetch_assoc()) {
                        $images[] = htmlspecialchars($imageRow["image_filename"]);
                    }

                    echo "<div class='room-images'>";
                    if (count($images) > 0) {
                        echo "<div class='main-images'>";
                        for ($i = 0; $i < min(3, count($images)); $i++) {
                            echo "<img src='uploads/" . $images[$i] . "' alt='Room Image' class='room-image' onclick='openModal($roomId, $i)'>";
                        }
                        echo "</div>";

                        if (count($images) > 3) {
                            echo "<div class='extra-images'>";
                            for ($i = 3; $i < count($images); $i++) {
                                echo "<img src='uploads/" . $images[$i] . "' alt='Room Image' class='room-image' onclick='openModal($roomId, $i)'>";
                            }
                            echo "<div class='more-images-overlay'>+" . (count($images) - 3) . " more</div>";
                            echo "</div>";
                        }
                    }
                    echo "</div>";

                    echo "<div class='comments-header' onclick='toggleComments($roomId)'>Comments</div>";
                    echo "<div id='comments-$roomId' class='comments-section'>";

                    // Fetch and display comments for the room
                    $commentSql = "SELECT username, comment FROM comments WHERE room_id = ?";
                    $commentStmt = $conn->prepare($commentSql);
                    $commentStmt->bind_param("i", $roomId);
                    $commentStmt->execute();
                    $commentResult = $commentStmt->get_result();

                    if ($commentResult->num_rows > 0) {
                        while ($commentRow = $commentResult->fetch_assoc()) {
                            echo "<p><strong>" . htmlspecialchars($commentRow["username"]) . ":</strong> " . htmlspecialchars($commentRow["comment"]) . "</p>";
                        }
                    } else {
                        echo "<p>No comments yet.</p>";
                    }

                    // Comment form
                    echo "<form action='submit_comment.php' method='POST' class='comments-form'>
                            <input type='hidden' name='room_id' value='" . htmlspecialchars($roomId) . "'>
                            <label for='comment'>Add a comment:</label>
                            <textarea id='comment' name='comment' required></textarea>
                            <button type='submit'>Submit</button>
                        </form>";

                    echo "</div></div>";
                }
            } else {
                echo "<p>No rooms available.</p>";
            }
            $conn->close();
            ?>
        </section>
    </main>

    <!-- The Modal for Adding Room Details -->
    <div id="addDetailsModal" class="modal">
        <div class="modal-content">
            <span class="close" onClick="closeAddDetailsModal()">&times;</span>
            <h2>Add Room Details</h2>
            <form action="submit_room.php" method="POST" enctype="multipart/form-data">
                <label for="room-title">Room Title:</label>
                <input type="text" id="room-title" name="title" required>
                <label for="room-description">Description:</label>
                <textarea id="room-description" name="description" required></textarea>
                <label for="room-price">Price per Night:</label>
                <input type="number" id="room-price" name="price" required>
                <label for="room-images">Upload Images:</label>
                <input type="file" id="room-images" name="images[]" multiple>
                <button type="submit">Add Room</button>
            </form>
        </div>
    </div>

    <!-- The Modal for Room Images -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onClick="closeModal()">&times;</span>
            <div id="modal-carousel" style="position: relative;">
                <img id="modal-image" class="modal-image" src="" alt="Expanded Image">
                <span class="prev" onClick="prevImage()">&#10094;</span>
                <span class="next" onClick="nextImage()">&#10095;</span>
            </div>
        </div>
    </div>

    <div class="plus-button" onClick="openAddDetailsModal()">+</div>
	
<script>
        var modal = document.getElementById('modal');
        var addDetailsModal = document.getElementById('addDetailsModal');
        var modalImage = document.getElementById('modal-image');
        var currentRoomId = null;
        var currentImageIndex = 0;
        var images = [];

        function openModal(roomId, imageIndex) {
            currentRoomId = roomId;
            currentImageIndex = imageIndex;

            fetchImages(roomId).then(imgs => {
                images = imgs;
                updateModalImage();
                modal.style.display = 'block';
            });
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        function prevImage() {
            if (images.length > 0) {
                currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
                updateModalImage();
            }
        }

        function nextImage() {
            if (images.length > 0) {
                currentImageIndex = (currentImageIndex + 1) % images.length;
                updateModalImage();
            }
        }

        function updateModalImage() {
            if (images.length > 0) {
                modalImage.src = 'uploads/' + images[currentImageIndex];
            }
        }

        async function fetchImages(roomId) {
            const response = await fetch('fetch_images.php?room_id=' + roomId);
            return response.json();
        }

        function toggleComments(roomId) {
            var commentsSection = document.getElementById('comments-' + roomId);
            commentsSection.classList.toggle('expanded');
        }

        function openAddDetailsModal() {
            addDetailsModal.style.display = 'block';
        }

        function closeAddDetailsModal() {
            addDetailsModal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal || event.target == addDetailsModal) {
                closeModal();
                closeAddDetailsModal();
            }
        }
    </script>
</body>
</html>