<?php
session_start(); // Start the session

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    echo "Error: You must be logged in to update your profile.";
    exit;
}

// Database connection
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "space_rental";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user ID from session
$user_id = $_SESSION['username']; // Ensure this matches how you're storing user ID in session
if (!$user_id) {
    die("Error: User ID is not set in session.");
}

// Debugging: Print user ID and session values
// print_r($_SESSION); // Uncomment this line if needed for debugging

// Check if the form is submitted
if (isset($_POST['update_profile'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $password_hash = password_hash($password, PASSWORD_BCRYPT); // Encrypt the password
    
    // Handle profile picture upload
    $profile_picture = null; // Default value if no new picture is uploaded
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $file_name = basename($_FILES['profile_picture']['name']); // Sanitize file name
        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_type = $_FILES['profile_picture']['type'];
        $file_size = $_FILES['profile_picture']['size'];
        
        // Validate file type and size
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($file_type, $allowed_types) && $file_size < 5000000) { // Max size: 5MB
            // Save the file to the server
            $target_dir = "uploads/";
            $target_file = $target_dir . $file_name;
            
            // Ensure uploads directory exists
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true); // Create directory if not exists
            }

            if (move_uploaded_file($file_tmp, $target_file)) {
                $profile_picture = $file_name; // Store only the file name
            } else {
                echo "Error uploading file.";
                exit;
            }
        } else {
            echo "Invalid file type or size.";
            exit;
        }
    }

    // Prepare the SQL update statement
    $query = "UPDATE users SET username = ?, password_hash = ?" . ($profile_picture ? ", profile_picture = ?" : "") . " WHERE id = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters and execute statement
    if ($profile_picture) {
        $stmt->bind_param('sssi', $username, $password_hash, $profile_picture, $user_id);
    } else {
        $stmt->bind_param('ssi', $username, $password_hash, $user_id);
    }

    if ($stmt->execute()) {
        echo "Profile updated successfully!";
    } else {
        echo "Error updating profile: " . $stmt->error;
    }
    
    $stmt->close();
}

$conn->close();
?>
