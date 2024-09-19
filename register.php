<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "space_rental";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user input
$user = $_POST['username'];
$pass = password_hash($_POST['password'], PASSWORD_BCRYPT);

// Handle profile picture upload
$profilePicture = null;
if (!empty($_FILES['profile_picture']['name'])) {
    $file = $_FILES['profile_picture'];
    $fileName = basename($file['name']);
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileType = $file['type'];

    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = array('jpg', 'jpeg', 'png', 'gif');

    if (in_array($fileExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 1000000) { // 1MB limit
                $newFileName = uniqid('', true) . "." . $fileExt;
                $fileDestination = 'uploads/profile_pictures/' . $newFileName;
                
                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    $profilePicture = $newFileName;
                } else {
                    echo "Failed to upload the file.";
                    exit();
                }
            } else {
                echo "File size is too big.";
                exit();
            }
        } else {
            echo "There was an error uploading your file.";
            exit();
        }
    } else {
        echo "You cannot upload files of this type.";
        exit();
    }
}

// Insert user into database
$sql = "INSERT INTO users (username, password_hash, profile_picture) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $user, $pass, $profilePicture);
$stmt->execute();

$stmt->close();
$conn->close();

header("Location: login.php");
exit();
?>
