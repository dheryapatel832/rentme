<?php
// list_rooms.php

// Start the session
session_start();

// Include the database connection file
include 'db_connect.php'; // This will use the connection from db_connect.php



// Fetch available rooms
$sql = "SELECT id, title, description, price FROM rooms";
$result = $conn->query($sql);
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
            right: 80px; /* Adjusted to make room for the P icon */
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

        .manage-icon {
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
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 1000;
        }

        .manage-icon:hover {
            background-color: #0056b3;
        }

        .manage-icon p {
            margin: 0;
        }

        .room-item {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <header>
        <h1>Available Rooms</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        <p><a href="logout.php">Logout</a></p>
    </header>

    <main>
        <section id="room-list">
            <h2>Available Rooms</h2>
            <?php
            if ($result->num_rows > 0) {
                // Output data of each room
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='room-item'>";
                    echo "<h3>" . htmlspecialchars($row["title"]) . "</h3>";
                    echo "<p>" . htmlspecialchars($row["description"]) . "</p>";
                    echo "<p>Price: $" . htmlspecialchars($row["price"]) . " per night</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>No rooms available.</p>";
            }
            ?>
        </section>

        <!-- Add Room Button -->
        <div class="plus-button" onClick="openAddRoomModal()">+</div>

        
        <!-- Add Room Modal -->
        <div id="addRoomModal" class="modal">
            <div class="modal-content">
                <span class="close" onClick="closeAddRoomModal()">&times;</span>
                <h2>Add New Room</h2>
                <form action="add_room.php" method="POST" enctype="multipart/form-data">
                    <label for="title">Room Title:</label>
                    <input type="text" name="title" required>

                    <label for="description">Room Description:</label>
                    <textarea name="description" required></textarea>

                    <label for="price">Price per night:</label>
                    <input type="number" name="price" step="0.01" required>

                    <label for="images">Room Images (up to 5):</label>
                    <input type="file" name="images[]" accept="image/*" multiple required>

                    <button type="submit">Add Room</button>
                </form>
            </div>
        </div>

        <!-- Image Modal -->
        <div id="imageModal" class="modal">
            <span class="close" onClick="closeImageModal()">&times;</span>
            <img id="modalImage" class="modal-image">
            <span class="prev" onClick="prevImage()">&#10094;</span>
            <span class="next" onClick="nextImage()">&#10095;</span>
        </div>
    </main>

    <script>
        let currentImageIndex = 0;
        let currentRoomImages = [];

        function openModal(roomId, imageIndex) {
            // Fetch images for the selected room
            const roomImages = document.querySelectorAll(`#room-${roomId} .room-image`);
            currentRoomImages = Array.from(roomImages).map(img => img.src);
            currentImageIndex = imageIndex;

            document.getElementById('modalImage').src = currentRoomImages[currentImageIndex];
            document.getElementById('imageModal').style.display = 'block';
        }

        function closeImageModal() {
            document.getElementById('imageModal').style.display = 'none';
        }

        function prevImage() {
            currentImageIndex = (currentImageIndex > 0) ? currentImageIndex - 1 : currentRoomImages.length - 1;
            document.getElementById('modalImage').src = currentRoomImages[currentImageIndex];
        }

        function nextImage() {
            currentImageIndex = (currentImageIndex < currentRoomImages.length - 1) ? currentImageIndex + 1 : 0;
            document.getElementById('modalImage').src = currentRoomImages[currentImageIndex];
        }

        function openAddRoomModal() {
            document.getElementById('addRoomModal').style.display = 'block';
        }

        function closeAddRoomModal() {
            document.getElementById('addRoomModal').style.display = 'none';
        }
    </script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
