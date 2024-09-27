<?php
// register.php
// Include the database connection file
include 'db_connect.php'; // This will use the connection from db_connect.php


// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input from POST request
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate input
    if (!empty($username) && !empty($password)) {
        // Check if the username already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "Username already taken. Please choose another.";
        } else {
            // Hash the password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user into the database
            $stmt = $conn->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $password_hash);

            if ($stmt->execute()) {
                // Redirect to login page after successful registration
                header("Location: login.php");
                exit();
            } else {
                echo "Error: Could not register user.";
            }
        }

        $stmt->close();
    } else {
        echo "Please fill in both username and password.";
    }
}

$conn->close();
?>
