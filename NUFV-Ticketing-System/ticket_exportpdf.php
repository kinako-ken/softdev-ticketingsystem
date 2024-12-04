<?php
require_once('vendor/autoload.php');

// Initialize TCPDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Ticket Data Report');
$pdf->SetSubject('Ticket Report');
$pdf->SetKeywords('TCPDF, PDF, ticket, report');

// Header and Footer Settings
$pdf->SetHeaderData('', 0, 'Ticket Data Report', 'Generated on: ' . date('Y-m-d'));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Font and Margin Settings
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Add Page
$pdf->AddPage();

// Start HTML content
ob_start();
?>

<h1>Resolved Ticket List Data Sheet</h1>

<table id="dataTable" cellpadding="5" border="1">
    <thead>
        <tr>
            <th style="background-color: #34418E; color: white;">#</th>
            <th style="background-color: #34418E; color: white;">Date Created</th>
            <th style="background-color: #34418E; color: white;">Submitted By</th>
            <th style="background-color: #34418E; color: white;">Subject</th>
            <th style="background-color: #34418E; color: white;">Support Type</th>
            <th style="background-color: #34418E; color: white;">Status</th>
            <th style="background-color: #34418E; color: white;">Priority</th>
        </tr>
    </thead>
    <tbody>
        <?php
        include 'dbcon.php';
        $i = 1; // Initialize row counter

        // Fetch closed tickets
        $get_closed_tickets = mysqli_query($connection, "
            SELECT t.id, t.date_created, CONCAT(u.lastname, ', ', u.firstname, ' ', u.middlename) AS cname, 
                   t.subject, s.name AS support_type, t.status, t.priority
            FROM tickets t
            INNER JOIN users u ON u.id = t.user_id
            LEFT JOIN support s ON s.id = t.support_id
            WHERE t.status = 2
            ORDER BY UNIX_TIMESTAMP(t.date_created) ASC
        ");

        // Display data in table rows
        while ($row = mysqli_fetch_array($get_closed_tickets)) {
            ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo date("n/j/Y h:i:s A", strtotime($row['date_created'])); ?></td>
                <td><?php echo ucwords($row['cname']); ?></td>
                <td><?php echo $row['subject']; ?></td>
                <td><?php echo $row['support_type']; ?></td>
                <td><?php echo getStatusLabel($row['status']); ?></td>
                <td><?php echo getPriorityLabel($row['priority']); ?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>

<?php
$content = ob_get_clean();
$pdf->writeHTML($content, true, false, true, false, '');
$pdf->Output('Ticket Data Report.pdf', 'D');

// Helper functions for status and priority labels
function getStatusLabel($status) {
    switch ($status) {
        case 0:
            return 'Open';
        case 1:
            return 'On Process';
        case 2:
            return 'Closed';
        default:
            return 'Unknown Status';
    }
}

function getPriorityLabel($priority) {
    switch ($priority) {
        case 0:
            return 'Low';
        case 1:
            return 'Medium';
        case 2:
            return 'High';
        default:
            return 'Unknown Priority';
    }
}
?>