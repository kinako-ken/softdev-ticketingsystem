<?php
include 'db_connect.php';

$id = $_GET['id'] ?? null;
$qry = $conn->query("SELECT * FROM users WHERE id = '$id'");

if ($qry && $qry->num_rows > 0) {
    $result = $qry->fetch_assoc();
    foreach ($result as $k => $v) {
        $$k = $v;
    }
} else {
    echo "<p>User not found.</p>";
    exit;
}

include 'new_user.php';
?>
