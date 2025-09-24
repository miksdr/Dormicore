<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include('connect.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_number = $_POST['room_number'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $capacity = $_POST['capacity'];
    $status = 'vacant';
    $occupied = 0;

    // Handle image upload
    $picture = $_FILES['picture']['name'];
    $target = "uploads/" . basename($picture);
    move_uploaded_file($_FILES['picture']['tmp_name'], $target);

    $stmt = $conn->prepare("INSERT INTO rooms (room_number, description, price, capacity, occupied, status, picture) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdiiss", $room_number, $description, $price, $capacity, $occupied, $status, $picture);

    if ($stmt->execute()) {
        echo "<div class='toast-message'>Room added successfully!</div>
        <script>
            setTimeout(() => {
                document.querySelector('.toast-message').style.opacity = '0';
            }, 2500);
        </script>";
    } else {
        echo "<div class='toast-message'>Failed to add room.</div>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Dormicore</title>
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>

<header class="main-header">
    <h1>Dormicore Admin</h1>
</header>

<main>
    <div class="form-container">
        <h2>Add a New Room</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="room_number" placeholder="Room Number" required>
            <textarea name="description" placeholder="Room Description" required></textarea>
            <input type="number" name="price" placeholder="Price" required>
            <input type="number" name="capacity" placeholder="Capacity" required>
            <input type="file" name="picture" accept="image/*" required>
            <input type="submit" value="Add Room">
        </form>
    </div>
</main>

</body>
</html>
