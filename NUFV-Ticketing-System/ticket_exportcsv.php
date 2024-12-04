<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Ticket Data Report</title> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="dashstyle.css">
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        .head {
            text-align: center;
            margin-bottom: 20px;
        }

        .head h1, .head h2 {
            margin: 0;
        }

        hr {
            margin-bottom: 20px;
            border: none;
            border-top: 1px solid #ddd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        th {
            background-color: #34418E;
            color: white;
            padding: 12px;
        }

        td {
            padding: 12px;
            color: black;
        }
    </style>
</head> 

<body> 
    <div class="container"> 
        <div class="head"> 
            <h1><strong>Ticket Data Report</strong></h1>
            <h2>Resolved Ticket List Data Sheet</h2>
        </div> 
        <hr> 

        <label for="reportType">Select Report Type:</label>
        <select id="reportType" onchange="updateReport()">
            <option value="weekly">Weekly</option>
            <option value="monthly">Monthly</option>
            <option value="yearly">Yearly</option>
        </select>
        <button onclick="exportToExcel()" data-toggle="modal" data-target="#exampleModal">Export CSV</button>
        <table id="dataTable"> 
            <thead> 
                <tr> 
                    <th>#</th>
                    <th>Date Created</th> 
                    <th>Submitted By</th> 
                    <th>Subject</th> 
                    <th>Support Type</th>
                    <th>Status</th> 
                    <th>Priority</th>
                </tr> 
            </thead> 
            <tbody> 
                <?php 
                include 'dbcon.php'; 

                $get_closed_tickets = mysqli_query($connection, "
                    SELECT t.id, t.date_created, CONCAT(u.lastname, ', ', u.firstname, ' ', u.middlename) AS cname, t.subject, s.name AS support_type, t.status, t.priority
                    FROM tickets t
                    INNER JOIN users u ON u.id = t.user_id
                    LEFT JOIN support s ON s.id = t.support_id
                    WHERE t.status = 2
                    ORDER BY UNIX_TIMESTAMP(t.date_created) ASC
                ");

                $i = 1;
                while($row = mysqli_fetch_array($get_closed_tickets)){ 
                ?> 
                    <tr> 
                        <td>
                            <?php
                            switch ($row['status']) {
                                case 0:
                                    echo '<span class="badge badge-primary">Open</span>';
                                    break;
                                case 1:
                                    echo '<span class="badge badge-info">On Process</span>';
                                    break;
                                case 2:
                                    echo '<span class="badge badge-success">Resolved</span>';
                                    break;
                                default:
                                    echo 'Unknown';
                                    break;
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            switch ($row['priority']) {
                                case 0:
                                    echo '<span class="badge badge-secondary">Low</span>';
                                    break;
                                case 1:
                                    echo '<span class="badge badge-warning">Medium</span>';
                                    break;
                                case 2:
                                    echo '<span class="badge badge-danger">High</span>';
                                    break;
                                default:
                                    echo 'Unknown';
                                    break;
                            }
                            ?>
                        </td>
                    </tr> 
                <?php } ?> 
            </tbody> 
        </table> 
    </div> 

    <script>
        function updateReport() {
            var reportType = document.getElementById('reportType').value;

            // Add code to handle report filtering here if needed
        }

        function exportToExcel() {
            var table = document.querySelector('#dataTable');
            var ws = XLSX.utils.table_to_sheet(table);
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
            XLSX.writeFile(wb, 'Ticket_Data_Report.xlsx');
        }
    </script>
</body> 
</html> 
