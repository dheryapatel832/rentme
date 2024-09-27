<?php
session_start();
// Include the database connection file
include 'db_connect.php'; // This will use the connection from db_connect.php

// Get the room ID from GET data and validate it
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid room ID.");
}
$roomId = intval($_GET['id']);

// Update room details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['description'], $_POST['price'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = floatval($_POST['price']); // Ensure price is a float value

    $sql = "UPDATE rooms SET title = ?, description = ?, price = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdi", $title, $description, $price, $roomId);
    if ($stmt->execute()) {
        // Redirect to the room list page after successful update
        header("Location: edit_room.php");
        exit();
    } else {
        echo "Error updating room: " . $conn->error;
    }
}

// Fetch current room details for the form
$sql = "SELECT title, description, price FROM rooms WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $roomId);
$stmt->execute();
$result = $stmt->get_result();
$roomDetails = $result->fetch_assoc();

if (!$roomDetails) {
    die("Room not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Room</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Edit Room</h1>
    </header>

    <main>
        <form action="update_room.php?id=<?php echo urlencode($roomId); ?>" method="POST" enctype="multipart/form-data">
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($roomDetails['title']); ?>" required>

            <label for="description">Description:</label>
            <textarea name="description" id="description" required><?php echo htmlspecialchars($roomDetails['description']); ?></textarea>

            <label for="price">Price:</label>
            <input type="number" name="price" id="price" value="<?php echo htmlspecialchars($roomDetails['price']); ?>" required>

            <label for="images">Upload Images:</label>
            <input type="file" name="images[]" id="images" multiple>

            <button type="submit">Update Room</button>
        </form>
    </main>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
