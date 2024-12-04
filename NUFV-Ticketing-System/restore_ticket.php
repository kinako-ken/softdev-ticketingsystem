<?php
include('db_connect.php');

if (isset($_GET['ticket_id'])) {
    $ticket_id = $conn->real_escape_string($_GET['ticket_id']);

    $restore_query = $conn->query("UPDATE tickets SET archived_date = NULL WHERE id = '$ticket_id'");

    if ($restore_query) {
        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
        } else {
            header("Location: index.php?page=ticket_list");
        }
        exit();
    } else {
        echo "Failed to restore ticket.";
    }
}
?>

<title>Archive List</title>

<div class="col-lg-12">
    <div class="card card-outline">
        <div class="card-header">
            <div class="card-tools">
                <a href="index.php?page=homes"><button type="button" class="btn btn-secondary mr-4"><i class="bi bi-arrow-left-short"></i> Return</button></a>    
            </div>
        </div>

        <div class="card-body">
            <table class="table table-hover table-bordered" id="archivedList">
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

                    $archived_qry = $conn->query("SELECT t.*, 
                                                         CONCAT(u.lastname, ', ', u.firstname, ' ', u.middlename) AS cname, 
                                                         s.name AS support_type 
                                                  FROM tickets t 
                                                  INNER JOIN users u ON u.id = t.user_id 
                                                  LEFT JOIN support s ON s.id = t.support_id 
                                                  WHERE t.archived_date IS NOT NULL 
                                                  ORDER BY UNIX_TIMESTAMP(t.date_created) ASC");

                    while ($archived_row = $archived_qry->fetch_assoc()) :
                        $trans = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
                        unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
                        $desc = strtr(html_entity_decode($archived_row['description']), $trans);
                        $desc = str_replace(array("<li>", "</li>"), array("", ", "), $desc);
                    ?>
                        <tr>
                            <th class="text-center"><?php echo $i++ ?></th>
                            <td><b><?php echo date("n-j-Y h:i:s A", strtotime($archived_row['date_created'])) ?></b></td>
                            <td><b><?php echo ucwords($archived_row['cname']) ?></b></td>
                            <td><b><?php echo $archived_row['subject'] ?></b></td>
                            <td><b class="truncate"><?php echo strip_tags($desc) ?></b></td>
                            <td><b><?php echo $archived_row['support_type'] ?? 'N/A' ?></b></td>
                            <td>
                                <?php if ($archived_row['status'] == 0) : ?>
                                    <span class="badge badge-primary">Open</span>
                                <?php elseif ($archived_row['status'] == 1) : ?>
                                    <span class="badge badge-info">Processing</span>
                                <?php elseif ($archived_row['status'] == 2) : ?>
                                    <span class="badge badge-success">Resolved</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($archived_row['priority'] == 0) : ?>
                                    <span class="badge badge-secondary">Low</span>
                                <?php elseif ($archived_row['priority'] == 1) : ?>
                                    <span class="badge badge-warning">Medium</span>
                                <?php elseif ($archived_row['priority'] == 2) : ?>
                                    <span class="badge badge-danger">High</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-warning btn-xs update" onclick="window.location.href='./restore_ticket.php?ticket_id=<?php echo $archived_row['id'] ?>'" title="Restore">
                                    <i class="fas fa-check"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#archivedList').dataTable({
            "columnDefs": [
                { "orderable": false, "targets": 8 }
            ]
        });
    });
</script>

<?php
$conn->close();
?>