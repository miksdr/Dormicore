<?php
session_start(); // Start session to store login status

include('connect.php'); // Include the database connection

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username); // "s" means the parameter is a string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $row['password'])) {
            // Store user data in session
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role']; // Store the user role 

            // Redirect based on the role
            if ($row['role'] == 'admin') {
                header("Location: admin.php"); // Redirect to admin dashboard
            } else {
                header("Location: home.php"); // Redirect to user homepage
            }
            exit(); // Ensure no further code is executed after redirection
        } else {
            echo "<p>Invalid password. Please try again.</p>";
        }
    } else {
        echo "<p>User not found. Please check your username.</p>";
    }

    $stmt->close(); // Close the prepared statement
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dormicore - Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <div class="logo">
        <img src="logo6.png" alt="Dormicore Logo">
    </div>

    <h2>Login to Dormicore</h2>

    <form method="POST" action="login.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <input type="submit" value="Login">
    </form>

    <div class="link">
        <p>Don't have an account? <a href="register.php">Create account</a></p>
    </div>
</div>

</body>
</html>
