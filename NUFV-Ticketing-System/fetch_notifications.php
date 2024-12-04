<?php
session_start();
include 'db_connect.php';

$userRole = $_SESSION['login_type'];

if ($userRole == 1 || $userRole == 2) { // Check if the user is admin or staff
    // Fetch notifications with NULL user_id or matching user_id
    $stmt = $conn->prepare("SELECT id, message, is_read, created_at, ticket_id FROM notifications WHERE (user_id IS NULL OR user_id = ?) AND is_read = 0 ORDER BY created_at DESC LIMIT 10");
    $stmt->bind_param("i", $_SESSION['login_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }

    echo json_encode($notifications);
    $stmt->close();
} else {
    // If the user is not admin or staff, return an empty array
    echo json_encode([]);
}
?>