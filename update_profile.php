<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Update Profile</title>
</head>
<body>
    <div class="update-profile-container">
        <h1>Update Profile</h1>
        <form action="updateprofile.php" method="POST" enctype="multipart/form-data">
            <div>
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div>
                <label for="profile_picture">Profile Picture:</label>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
            </div>
            <div>
                <button type="submit" name="update_profile">Update Profile</button>
            </div>
        </form>
    </div>
</body>
</html>
