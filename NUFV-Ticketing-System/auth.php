<?php
if (!isset($_SESSION['login_type'])) {
    header('Location: login.php');
    exit();
}

function restrict_to_admin() {
    if ($_SESSION['login_type'] != 1 && $_SESSION['login_type'] != 2) {
        header('Location: index.php?page=unauthorized');
        exit();
    }
}
?>