<?php
include('db_connect.php');

if(isset($_GET['ticket_id']) && !empty($_GET['ticket_id'])) {
    $ticket_id = $conn->real_escape_string($_GET['ticket_id']);

    $archive_query = $conn->query("UPDATE tickets SET archived_date = NOW() WHERE id = '$ticket_id'");

    if ($archive_query) {
        if ($conn->affected_rows > 0) {
            $_SESSION['success'] = "Ticket archived successfully.";
        } else {
            $_SESSION['error'] = "Ticket not found.";
        }

        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
        } else {
            header("Location: index.php?page=ticket_list");
        }
        exit();
    } else {

        $_SESSION['error'] = "Error archiving ticket: " . $conn->error;
        header("Location: index.php?page=ticket_list");
        exit();
    }
} else {
    
    $_SESSION['error'] = "Invalid Ticket ID.";
    header("Location: index.php?page=ticket_list");
    exit();
}

$conn->close();
?>