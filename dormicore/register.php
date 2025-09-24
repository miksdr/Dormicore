<?php
include('connect.php'); // Include the database connection

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Set the role as 'user' by default
    $role = 'user';

    // Hash the password before storing it in the database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the username already exists
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "
        <div class='toast-overlay'>
            <div class='toast-message'>
                Username already taken! Please choose another.
            </div>
        </div>
        <script>
                setTimeout(function() {
                    window.location.href = 'register.php';
                }, 3000);
        </script>
        ";
    } else {
        // Insert new user data into the database
        $sql = "INSERT INTO users (username, password, email) 
                VALUES ('$username', '$hashed_password', '$email')";

        if ($conn->query($sql) === TRUE) {
            echo "
            <div class='toast-overlay'>
                <div class='toast-message'>
                    Registration successful! Redirecting to login...
                </div>
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = 'login.php';
                }, 3000);
            </script>
            ";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dormicore - Register</title>
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>

<div class="container">
    <h2>Create an Account</h2>

    <!-- Registration form -->
    <form method="POST" action="register.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <input type="submit" value="Register">
    </form>

    <div class="link">
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>

</div>

</body>
</html>
