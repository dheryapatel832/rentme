<?php
// manage_my_rooms.php

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "space_rental";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user = $_SESSION['username'];

$sql = "SELECT id, title, description, price FROM rooms WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage My Rooms</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Manage My Rooms</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        <p><a href="logout.php">Logout</a></p>
        <a href="list_rooms.php" class="manage-icon"><i class="fas fa-arrow-left"></i></a>
    </header>

    <main>
        <section id="my-rooms">
            <h2>Your Rooms</h2>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='room-item'>";
                    echo "<h3>" . htmlspecialchars($row["title"]) . "</h3>";
                    echo "<p>" . htmlspecialchars($row["description"]) . "</p>";
                    echo "<p>Price: $" . htmlspecialchars($row["price"]) . " per night</p>";
                    echo "<a href='edit_room.php?id=" . $row["id"] . "' class='edit-button'>Edit</a>";
                    echo "<a href='delete_room.php?id=" . $row["id"] . "' class='delete-button'>Delete</a>";
                    echo "</div>";
                }
            } else {
                echo "<p>You have no rooms.</p>";
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
