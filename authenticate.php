<?php
// authenticate.php
// Include the database connection file
include 'db_connect.php'; // This will use the connection from db_connect.php


// Get user input from POST request
$username = $_POST['username'];
$password = $_POST['password'];

// Prepare and execute the query to fetch password hash for the given username
$stmt = $conn->prepare("SELECT password_hash FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

// Check if a user with the provided username exists
if ($stmt->num_rows > 0) {
    // Bind the result (password hash) to a variable
    $stmt->bind_result($password_hash);
    $stmt->fetch();

    // Verify the entered password against the stored hash
    if (password_verify($password, $password_hash)) {
        // Start session and store user information
        session_start();
        $_SESSION['username'] = $username;

        // Redirect to the list_rooms.php page
        header("Location: list_rooms.php");
        exit();
    } else {
        echo "Invalid username or password.";
    }
} else {
    echo "Invalid username or password.";
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
