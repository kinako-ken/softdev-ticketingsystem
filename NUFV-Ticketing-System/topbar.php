<?php
include 'db_connect.php'; 
?>

<nav class="navbar sticky-top navbar-light" style="height: 60px; background-color: #34418e;">
    <ul class="navbar-nav" style="margin-right: 10%;">
        <li class="nav-item">
            <a class="nav-link" href="index.php?page=homes" role="button">
                <img src="NUFV Watermark.png" alt="Logo" style="height: 40px; margin-top: -5px;">
            </a>
        </li>
    </ul>

    <li class="nav-item dropdown" id="notification_area" style="margin-left: -100px;">
        <a href="javascript:void(0)" class="nav-link" data-toggle="dropdown" aria-expanded="true" style="display: flex; align-items: center;">
            <i class="fas fa-bell" style="position: absolute; top: 10px; right: 20px; color: white; font-size: 20px;"></i>
            <span class="badge badge-danger" id="notification_count" style="position: absolute; top: 0px; right: 5px;">0</span>
        </a>
        <div class="dropdown-menu dropdown-menu-right" id="notification_list" style="max-height: 200px; overflow-y: auto;">
            <div class="dropdown-item" id="no_notification" style="display: none;">No new notifications</div>
        </div>
    </li>

    <ul class="navbar-nav ml-auto" style="margin-right: 0%; margin-top: -5px;">
        <li class="nav-item dropdown">
            <a href="javascript:void(0)" class="brand-link dropdown-toggle" data-toggle="dropdown" aria-expanded="true" style="display: flex; align-items: center;">
                <span class="brand-image img-circle elevation-3 d-flex justify-content-center align-items-center bg-primary text-white font-weight-bold user-initials" style="width: 38px;height:50px">
                    <?php echo strtoupper(substr($_SESSION['login_firstname'], 0,1).substr($_SESSION['login_lastname'], 0,1)) ?>
                </span>
                <span class="brand-text user-name" style="margin-left: 5px; color: white;">
                    <?php echo ucwords($_SESSION['login_firstname'].' '.$_SESSION['login_lastname']) ?>
                </span>
            </a>
            <div class="dropdown-menu" aria-labelledby="pushMenuDropdown" style="position: absolute; right: 100px;">
                <a class="dropdown-item" href="ajax.php?action=logout">Logout</a>
            </div>
        </li>
    </ul>
</nav>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    function fetchNotifications() {
        $.ajax({
            url: 'fetch_notifications.php',
            method: 'GET',
            success: function(data) {
                const notifications = JSON.parse(data);
                const notificationList = $('#notification_list');
                const notificationCount = $('#notification_count');

                notificationList.empty();
                if (notifications.length > 0) {
                    notifications.forEach(notification => {
                        const notificationItem = $('<div class="dropdown-item notification-item"></div>')
                            .text(notification.message)
                            .attr('data-ticket-id', notification.ticket_id)
                            .attr('data-notification-id', notification.id);

                        notificationList.prepend(notificationItem);
                    });
                    notificationCount.text(notifications.length);
                } else {
                    notificationCount.text(0);
                    notificationList.append('<div class="dropdown-item" id="no_notification">No new notifications</div>');
                }
            },
            error: function(err) {
                console.error('Error fetching notifications:', err);
            }
        });
    }

    // Handle notification click
    $('#notification_list').on('click', '.notification-item', function() {
        const ticketId = $(this).data('ticket-id');
        const notificationId = $(this).data('notification-id');

        // Mark the notification as read
        $.ajax({
            url: 'mark_notification_read.php', // Use a script to mark as read
            method: 'POST',
            data: { id: notificationId },
            success: function() {
                window.location.href = `index.php?page=view_ticket&id=${ticketId}`;
            },
            error: function(err) {
                console.error('Error marking notification as read:', err);
            }
        });
    });

    setInterval(fetchNotifications, 10000);
});
</script>