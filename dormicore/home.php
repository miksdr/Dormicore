<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include('connect.php');

$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

if ($filter == 'fully_occupied') {
    $sql = "SELECT * FROM rooms WHERE status='full' AND (occupied = capacity)";
} elseif ($filter == 'available') {
    $sql = "SELECT * FROM rooms WHERE status='vacant' AND (occupied < capacity)";
} else {
    $sql = "SELECT * FROM rooms WHERE status='vacant'";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dormicore Home</title>
    <link rel="stylesheet" href="home-style.css">
</head>
<body>

<header class="main-header">
    <div class="header-left">
        <img src="logo6.png" alt="Dormicore Logo" class="header-logo">
        <h1 class="app-title">Dormicore</h1>
    </div>
    <nav class="room-nav">
        <a href="?filter=available">Available Rooms</a>
        <a href="?filter=fully_occupied">Fully Occupied Rooms</a>
    </nav>
</header>

<main>
    <div class="room-list">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $remaining_capacity = $row['capacity'] - $row['occupied'];
                $card_class = ($row['occupied'] == $row['capacity']) ? 'room-card full' : 'room-card';

                echo "
                <a href='room_details.php?id=" . $row['id'] . "' class='" . $card_class . "'>
                    <div class='room-image'>
                        <img src='uploads/" . htmlspecialchars($row['picture']) . "' alt='Room Image'>
                    </div>
                    <div class='room-details'>
                        <h4>Room " . htmlspecialchars($row['room_number']) . "</h4>
                        <p>Price: Php " . htmlspecialchars($row['price']) . "</p>
                        <p>Occupied: " . htmlspecialchars($row['occupied']) . "/" . htmlspecialchars($row['capacity']) . "</p>
                        <p>Available " . $remaining_capacity . " spots</p>
                    </div>
                </a>";
            }
        } else {
            echo "<div class='no-rooms'>No rooms</div>";
        }
        ?>
    </div>
</main>

</body>
</html>
