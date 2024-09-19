<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Spaces</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Available Spaces</h1>
    </header>

    <main>
        <section id="space-list">
            <h2>Available Spaces</h2>
            <div id="spaces">
                <?php
                // Database connection
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "space_rental";

                // Create a new connection
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Fetch spaces from the database
                $sql = "SELECT title, description, price FROM spaces";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<div class='space-item'>";
                        echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
                        echo "<p>" . htmlspecialchars($row['description']) . "</p>";
                        echo "<p><strong>Price:</strong> $" . htmlspecialchars($row['price']) . " per night</p>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No spaces available.</p>";
                }

                $conn->close();
                ?>
            </div>
        </section>
    </main>
</body>
</html>
