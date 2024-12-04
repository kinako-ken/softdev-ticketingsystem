<?php
include('db_connect.php');
include('auth.php');
restrict_to_admin();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="dashstyle.css">
    <script src="js/chart.umd.js"></script>
</head>
<body>
    <div class="main">
        <div class="card-header">
            <div class="card-tools">
                <a href="index.php?page=user_list" class="btn btn-primary mr-1">User List</a>
                <a href="index.php?page=admin_list" class="btn btn-primary mr-1">Admin List</a>
                <a href="index.php?page=homes"><button type="button" class="btn btn-secondary mr-3"><i class="bi bi-arrow-left-short"></i> Return</button></a>
            </div>
        </div>
        <div class="cards">
            <a href="index.php?page=user_list" class="card">
                <div class="icon-box">
                    <i class="fas fa-users"></i>
                </div>
                <div class="info-box">
                    <span class="number"><?php echo $conn->query("SELECT * FROM users WHERE role = 3")->num_rows; ?></span>
                    <span class="card-name">Total Users</span>
                </div>
            </a>
            <a href="index.php?page=admin_list" class="card">
                <div class="icon-box">
                    <i class="fas fa-user"></i>
                </div>
                <div class="info-box">
    <span class="number">
        <?php echo $conn->query("SELECT * FROM users WHERE role IN (1, 2)")->num_rows; ?>
    </span>
    <span class="card-name">Total Admins</span>
</div>

            </a>
            <div class="card">
                <div class="icon-box">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="info-box">
                    <span class="number"><?php echo $conn->query("SELECT * FROM tickets")->num_rows; ?></span>
                    <span class="card-name">Total Tickets</span>
                </div>
            </div>
            <a href="index.php?page=ticket_list" class="card">
                <div class="icon-box">
                    <i class="fa-solid fa-envelope-open"></i>
                </div>
                <div class="info-box">
                    <?php
                    $result = $conn->query("SELECT COUNT(*) AS total_pending_tickets FROM tickets WHERE status = 0 AND archived_date IS NULL");
                    $row = $result->fetch_assoc();
                    $totalPendingTickets = $row['total_pending_tickets'];
                    ?>
                    <span class="number"><?php echo $totalPendingTickets; ?></span>
                    <span class="card-name">Total Open Tickets</span>
                </div>
            </a>
            <a href="index.php?page=ticket_list" class="card">
                <div class="icon-box">
                    <i class="fa-solid fa-spinner"></i>
                </div>
                <div class="info-box">
                    <?php
                    $result = $conn->query("SELECT COUNT(*) AS total_processing_tickets FROM tickets WHERE status = 1 AND archived_date IS NULL");
                    $row = $result->fetch_assoc();
                    $totalProcessingTickets = $row['total_processing_tickets'];
                    ?>
                    <span class="number"><?php echo $totalProcessingTickets; ?></span>
                    <span class="card-name">Total Processing Tickets</span>
                </div>
            </a>
            <a href="index.php?page=history" class="card">
                <div class="icon-box">
                    <i class="fa-solid fa-square-check"></i>
                </div>
                <div class="info-box">
                    <?php
                    $result = $conn->query("SELECT COUNT(*) AS total_closed_tickets FROM tickets WHERE status = 2");
                    $row = $result->fetch_assoc();
                    $totalClosedTickets = $row['total_closed_tickets'];
                    ?>
                    <span class="number"><?php echo $totalClosedTickets; ?></span>
                    <span class="card-name">Total Resolved Tickets</span>
                </div>
            </a>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"></script>

<div style="max-width: 1000px; margin: 40px auto;">
    <canvas id="ticketStatusChart"></canvas>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        var totalOpenTickets = <?php echo $totalPendingTickets; ?>;
        var totalProcessingTickets = <?php echo $totalProcessingTickets; ?>;
        var totalClosedTickets = <?php echo $totalClosedTickets; ?>;

        var ctx = document.getElementById('ticketStatusChart').getContext('2d');
        var ticketStatusChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [
                    'Open Tickets: ' + totalOpenTickets, 
                    'Processing Tickets: ' + totalProcessingTickets, 
                    'Resolved Tickets: ' + totalClosedTickets
                ],
                datasets: [{
                    label: 'Ticket Status',
                    data: [totalOpenTickets, totalProcessingTickets, totalClosedTickets],
                    backgroundColor: ['#007bff', '#17a2b8', '#28a745'],
                    borderColor: ['#007bff', '#17a2b8', '#28a745'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 18,
                                weight: 'bold'
                            },
                            padding: 20,
                            color: '#000'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label;
                            }
                        }
                    },
                    datalabels: {
                        formatter: function(value, context) {
                            return value; 
                        },
                        color: '#fff',
                        font: {
                            weight: 'bold',
                            size: 16
                        },
                        align: 'center',
                        anchor: 'center'
                    }
                }
            }
        });
    });
</script>

</body>
</html>