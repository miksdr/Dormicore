<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include('connect.php');

if (!isset($_GET['id'])) {
    echo "<p>Room not found.</p>";
    exit();
}

$room_id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->bind_param("i", $room_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>Room not found.</p>";
    exit();
}

$room = $result->fetch_assoc();

// Fetch other rooms
$other_rooms = $conn->query("SELECT * FROM rooms WHERE id != $room_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Room Details - Dormicore</title>
    <link rel="stylesheet" href="room-style.css">
</head>
<body>

<header class="main-header">
    <div class="header-left">
        <img src="logo6.png" alt="Dormicore Logo" class="header-logo">
        <h1 class="app-title">Dormicore</h1>
    </div>
    <div class="header-right">
        <a href="home.php" class="back-button">← Back to Home</a>
    </div>
</header>

<main>
    <section class="room-details-section">
        <div class="room-image">
            <img src="uploads/<?php echo htmlspecialchars($room['picture']); ?>" alt="Room Image">
        </div>
        <div class="room-info">
            <h2>Room <?php echo htmlspecialchars($room['room_number']); ?></h2>
            <p><?php echo htmlspecialchars($room['description']); ?></p>
            <p><strong>Price:</strong> Php <?php echo htmlspecialchars($room['price']); ?></p>
            <p><strong>Capacity:</strong> <?php echo htmlspecialchars($room['capacity']); ?> occupants</p>
            <p><strong>Currently Occupied:</strong> <?php echo htmlspecialchars($room['occupied']); ?></p>
            <p><strong>Available to Book:</strong> <?php echo $room['capacity'] - $room['occupied']; ?> spots</p>
            <a href="book_room.php?id=<?php echo $room['id']; ?>" class="book-button">Book Now</a>
        </div>
    </section>

    <section class="other-rooms-section">
        <h3>Other Rooms You Might Like</h3>
        <div class="other-room-list">
            <?php while ($r = $other_rooms->fetch_assoc()) {
                echo "
                <a href='room_details.php?id=" . $r['id'] . "' class='other-room-card'>
                    <img src='uploads/" . htmlspecialchars($r['picture']) . "' alt='Room Image'>
                    <div class='other-room-info'>
                        <h4>Room " . htmlspecialchars($r['room_number']) . "</h4>
                        <p>Php " . htmlspecialchars($r['price']) . "</p>
                    </div>
                </a>";
            } ?>
        </div>
    </section>
</main>

</body>
</html>
