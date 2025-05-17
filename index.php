<?php
session_start();

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'] ?? 'Guest';
$date = date('l, F j, Y - h:i A');
?>

<!DOCTYPE html>
<html>
<head><title>Welcome</title></head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
    <p>Today is <?php echo $date; ?></p>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>
