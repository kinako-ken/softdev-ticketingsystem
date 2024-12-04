<?php
include 'db_connect.php';

if (isset($_POST['id'])) {
    $notificationId = $_POST['id'];

    // Update notification to mark it as read
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
    $stmt->bind_param("i", $notificationId);
    $stmt->execute();
    $stmt->close();
}
?>