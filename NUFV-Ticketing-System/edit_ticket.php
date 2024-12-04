<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $ticket_id = $conn->real_escape_string($_GET['id']);
    $qry = $conn->query("SELECT * FROM tickets WHERE id = '$ticket_id'");

    if ($qry && $qry->num_rows > 0) {
        $ticket = $qry->fetch_assoc();
        
        foreach ($ticket as $k => $v) {
            $$k = $v;
        }
    } else {
        echo "Ticket not found.";
        exit;
    }
} else {
    echo "No ticket ID provided.";
    exit;
}

$status = isset($status) ? $status : 0;

include 'new_ticket.php';
?>