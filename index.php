<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to RentMe</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        header {
            background-color: #007BFF;
            color: white;
            padding: 20px;
        }

        main {
            margin: 40px auto;
            padding: 20px;
            max-width: 600px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        h1 {
            margin: 0;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            color: white;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
        }

        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to RentMe</h1>
    </header>

    <main>
        <h2>Find or List a Room</h2>
        <p>RentMe is the perfect platform for finding available spaces or listing your own. Please log in or sign up to get started.</p>
        <a href="login.php" class="button">Login</a>
        <a href="signup.php" class="button">Sign Up</a>
    </main>
</body>
</html>
