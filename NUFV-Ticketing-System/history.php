<?php 
include 'db_connect.php'; 
?>

<title>Resolved Tickets</title>

<div class="col-lg-12">
    <div class="card card-outline">
        <div class="card-header">
            <div class="card-tools">
            <?php if ($_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 2): ?>
            <a href="index.php?page=restore_ticket" class="btn btn-danger mr-1"><i class="fas fa-trash"></i> Archive </a>
            <a href="index.php?page=ticket_exportcsv"><button class="btn btn-success mr-1" data-toggle="modal" data-target="#exampleModal">Export CSV</button></a>
            <a href="ticket_exportpdf.php"><button class="btn btn-danger mr-1" data-toggle="modal" data-target="#exampleModal">Export PDF</button></a>
        <?php endif; ?>
        <a href="index.php?page=homes"><button type="button" class="btn btn-secondary mr-4"><i class="bi bi-arrow-left-short"></i> Return</button></a>  
            </div>
        </div>

    <div class="card-body">
        <table class="table table-hover table-bordered" id="list">
            <colgroup>
                <col width="5%">
                <col width="15%">
                <col width="15%">
                <col width="15%">
                <col width="15%">
                <col width="10%">
                <col width="5%">
                <col width="5%">
                <col width="10%">
            </colgroup>
            <thead>
                <tr>
                    <th style="background-color: #34418E; color: white;">#</th>
                    <th style="background-color: #34418E; color: white;">Date Created</th>
                    <th style="background-color: #34418E; color: white;">Submitted By</th>
                    <th style="background-color: #34418E; color: white;">Subject</th>
                    <th style="background-color: #34418E; color: white;">Description</th>
                    <th style="background-color: #34418E; color: white;">Support Type</th>
                    <th style="background-color: #34418E; color: white;">Status</th>
                    <th style="background-color: #34418E; color: white;">Priority</th>
                    <th style="background-color: #34418E; color: white;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
$i = 1;
$where = "WHERE t.status = 2 AND t.archived_date IS NULL";

if ($_SESSION['login_type'] == 3) {
    $where .= " AND t.user_id = '{$_SESSION['login_id']}'";
}

$qry = $conn->query("SELECT t.*, 
                            CONCAT(u.lastname, ', ', u.firstname, ' ', u.middlename) AS cname, 
                            s.name AS support_type 
                     FROM tickets t 
                     INNER JOIN users u ON u.id = t.user_id 
                     LEFT JOIN support s ON s.id = t.support_id 
                     $where 
                     ORDER BY UNIX_TIMESTAMP(t.date_created) ASC");

                while ($row = $qry->fetch_assoc()):
                    $trans = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
                    unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
                    $desc = strtr(html_entity_decode($row['description']), $trans);
                    $desc = str_replace(array("<li>", "</li>"), array("", ", "), $desc);
                ?>
                <tr>
                    <th class="text-center"><?php echo $i++ ?></th>
                    <td class="nowrap-date"><b><?php echo date("n/j/Y h:i:s A", strtotime($row['date_created'])); ?></b></td>
                    <td><b><?php echo ucwords($row['cname']) ?></b></td>
                    <td><b><?php echo $row['subject'] ?></b></td>
                    <td><b class="truncate"><?php echo strip_tags($desc) ?></b></td>
                    <td><b><?php echo ucwords($row['support_type']) ?></b></td>
                    <td>
                        <?php if ($row['status'] == 0): ?>
                            <span class="badge badge-primary">Open</span>
                        <?php elseif ($row['status'] == 1): ?>
                            <span class="badge badge-info">Processing</span>
                        <?php elseif ($row['status'] == 2): ?>
                            <span class="badge badge-success">Resolved</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['priority'] == 0): ?>
                            <span class="badge badge-secondary">Low</span>
                        <?php elseif ($row['priority'] == 1): ?>
                            <span class="badge badge-warning">Medium</span>
                        <?php elseif ($row['priority'] == 2): ?>
                            <span class="badge badge-danger">High</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-success btn-xs update" onclick="window.location.href='./index.php?page=view_ticket&id=<?php echo $row['id'] ?>'" title="View"><i class="fas fa-eye"></i></button>
                        <button type="button" class="btn btn-danger btn-xs update" onclick="window.location.href='./archive_ticket.php?ticket_id=<?php echo $row['id'] ?>'" title="Archive"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>    
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('#list').dataTable({
            "columnDefs": [
                { "orderable": false, "targets": -1 }
            ]
        });
        
        $('.delete_ticket').click(function(){
            _conf("Are you sure to delete this ticket?", "delete_ticket", [$(this).attr('data-id')])
        });
    });

    function delete_ticket($id){
        start_load();
        $.ajax({
            url: 'ajax.php?action=delete_ticket',
            method: 'POST',
            data: {id: $id},
            success: function(resp){
                if(resp == 1){
                    alert_toast("Data successfully deleted", 'success');
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                }
            }
        });
    }
</script>