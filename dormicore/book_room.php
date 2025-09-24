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

$room_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->bind_param("i", $room_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>Room not found.</p>";
    exit();
}

$room = $result->fetch_assoc();
$remaining_capacity = $room['capacity'] - $room['occupied'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $checkin_date = $_POST['checkin_date'];
    $duration = $_POST['duration'];
    $emergency_contact = $_POST['emergency_contact'];
    $emergency_number = $_POST['emergency_number'];
    $gender = $_POST['gender'];
    $payment_option = $_POST['payment_option'];

    // Handle ID uploads
    $id_front = $_FILES['id_front']['name'];
    $id_back = $_FILES['id_back']['name'];

    $target_front = "uploads/ids/" . basename($id_front);
    $target_back = "uploads/ids/" . basename($id_back);

    move_uploaded_file($_FILES['id_front']['tmp_name'], $target_front);
    move_uploaded_file($_FILES['id_back']['tmp_name'], $target_back);

    // Update room occupancy
    if ($remaining_capacity <= 0) {
        echo "<p>This room is already full.</p>";
        exit();
    }

    $new_occupied = $room['occupied'] + 1;
    $new_status = ($new_occupied >= $room['capacity']) ? 'full' : 'vacant';

    $update = $conn->prepare("UPDATE rooms SET occupied = ?, status = ? WHERE id = ?");
    $update->bind_param("isi", $new_occupied, $new_status, $room_id);
    $update->execute();

    // Optional: Insert booking info into a bookings table here

    header("Location: room_details.php?id=$room_id&booked=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Check-In - Dormicore</title>
    <link rel="stylesheet" href="book-style.css">
</head>
<body>

<header class="main-header">
    <div class="header-left">
        <img src="logo6.png" alt="Dormicore Logo" class="header-logo">
        <h1 class="app-title">Dormicore</h1>
    </div>
    <div class="header-right">
        <a href="room_details.php?id=<?php echo $room_id; ?>" class="back-button">← Back to Room</a>
    </div>
</header>

<main>
    <div class="checkin-container">
        <h2>Check-In for Room <?php echo htmlspecialchars($room['room_number']); ?></h2>

        <section class="room-top">
            <div class="room-image">
                <img src="uploads/<?php echo htmlspecialchars($room['picture']); ?>" alt="Room Image">
            </div>
            <div class="room-info">
                <h3>Room Details</h3>
                <p><?php echo htmlspecialchars($room['description']); ?></p>
                <p><strong>Price:</strong> Php <?php echo htmlspecialchars($room['price']); ?></p>
                <p><strong>Available Spots:</strong> <?php echo $remaining_capacity; ?></p>
            </div>
        </section>


        <form method="POST" enctype="multipart/form-data" class="booking-form">
            <h3>Guest Information</h3>
            <label>Name</label>
            <input type="text" name="name" placeholder="Full Name" required>
            <label>Age</label>
            <input type="number" name="age" placeholder="Age" min="1" required>
            <label>Gender</label>
            <select name="gender" required>
                <option value="" disabled selected hidden>Gender</option>
                <option value="female">Female</option>
                <option value="male">Male</option>
                <option value="other">Other</option>
            </select>
            <label>Contact Number</label>
            <input type="text" name="contact" placeholder="Contact Number" required>
            <label>Email Address</label>
            <input type="email" name="email" placeholder="Email Address (optional)">

            <h3>Upload Valid ID</h3>
            <label>Front</label>
            <input type="file" name="id_front" accept="image/*" required>
            <label>Back</label>
            <input type="file" name="id_back" accept="image/*" required>

            <h3>Emergency Contact</h3>
            <label>Contact Name</label>
            <input type="text" name="emergency_contact" placeholder="Emergency Contact Name" required>
            <label>Contact Number</label>
            <input type="text" name="emergency_number" placeholder="Emergency Contact Number" required>

            <h3>Booking Details</h3>
            <label>Check-In Date</label>
            <input type="date" name="checkin_date" required>
            <label>Months Staying</label>
            <input type="number" name="duration" placeholder="Duration of Stay (in months)" min="1" required>
            <label>Payment</label>
            <select name="payment_option" required>
                <option value="" disabled selected hidden>Payment Method</option>
                <option value="cash">Cash</option>
                <option value="gcash">GCash</option>
                <option value="maya">Maya</option>
                <option value="other">Other</option>
            </select>

            <input type="submit" value="Confirm Booking">
        </form>
    </div>
</main>

</body>
</html>
