<?php 
include 'db_connect.php'; 

if (!isset($_GET['id'])) {
    echo "Ticket ID not provided.";
    exit;
}

$ticket_id = $conn->real_escape_string($_GET['id']);

$qry = $conn->query("SELECT t.*, 
                            CONCAT(u.lastname, ', ', u.firstname, ' ', u.middlename) AS cname, 
                            cat.name AS category_name, 
                            r.name AS room_name 
                     FROM tickets t 
                     INNER JOIN users u ON u.id = t.user_id 
                     LEFT JOIN categories cat ON cat.id = t.category_id 
                     LEFT JOIN rooms r ON r.id = t.room_id 
                     WHERE t.id = '$ticket_id'");

if ($qry && $qry->num_rows > 0) {
    $row = $qry->fetch_array();
    foreach ($row as $k => $v) {
        $$k = $v;
    }
} else {
    echo "Ticket not found.";
    exit;
}
?>

<style>
    .d-list { display: list-item; }
    .ticket-header { 
        display: flex; 
        justify-content: space-between; 
        align-items: center;
    }
    .ticket-id {
        font-weight: bold;
        font-size: 1.2rem;
    }
    .return-button {
        margin-left: auto;
    }
</style>

<div class="col-lg-12">
    <div class="row">
        <div class="col-md-7 mx-auto">
            <div class="card card-outline">
                <div class="card-header ticket-header">
                    <span class="ticket-id">Ticket ID: #<?php echo isset($ticket_id) ? $ticket_id : 'N/A'; ?></span>
                    <div class="return-button">
                        <button type="button" class="btn btn-secondary mr-4" onclick="window.history.back();">
                            <i class="bi bi-arrow-left-short"></i> Return
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label border-bottom border-grey">Subject</label>
                                <p class="ml-2 d-list"><b><?php echo isset($subject) ? $subject : 'N/A'; ?></b></p>

                                <label class="control-label border-bottom border-grey">Category</label>
                                <p class="ml-2 d-list"><b><?php echo isset($category_name) ? $category_name : 'N/A'; ?></b></p>

                                <label class="control-label border-bottom border-grey">Submitted By</label>
                                <p class="ml-2 d-list"><b><?php echo isset($cname) ? $cname : 'N/A'; ?></b></p>
                            </div>

                            <div class="col-md-6">

                                <label class="control-label border-bottom border-grey">Status</label>
                                <p class="ml-2 d-list">
                                    <?php if ($status == 0): ?>
                                        <span class="badge badge-primary">Open</span>
                                    <?php elseif ($status == 1): ?>
                                        <span class="badge badge-info">Processing</span>
                                    <?php elseif ($status == 2): ?>
                                        <span class="badge badge-success">Done</span>
                                    <?php endif; ?>

                                    <?php if ($_SESSION['login_type'] != 3): ?>
                                        <span class="badge btn-outline-primary btn update_status" data-id="<?php echo $id; ?>">Update Status</span>
                                    <?php endif; ?>
                                </p>

                                <label class="control-label border-bottom border-grey">Priority</label>
                                <p class="ml-2 d-list">
                                    <?php if ($priority == 0): ?>
                                        <span class="badge badge-secondary">Low</span>
                                    <?php elseif ($priority == 1): ?>
                                        <span class="badge badge-warning">Medium</span>
                                    <?php elseif ($priority == 2): ?>
                                        <span class="badge badge-danger">High</span>
                                    <?php endif; ?>

                                    <?php if ($_SESSION['login_type'] != 3): ?>
                                        <span class="badge btn-outline-primary btn update_priority" data-id="<?php echo $id; ?>">Update Priority</span>
                                    <?php endif; ?>
                                </p>

                                <label class="control-label border-bottom border-grey">Room</label>
                                <p class="ml-2 d-list"><b><?php echo isset($room_name) ? $room_name : 'N/A'; ?></b></p>
                            </div>
                        </div>
                        <hr class="border-grey">
                        <label class="control-label border-bottom border-grey">Description</label>
                        <div>
                            <b><?php echo isset($description) ? html_entity_decode($description) : 'No description available'; ?></b>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mx-auto">
            <div class="card card-outline card-dark">
                <div class="card-header">
                    <h3 class="card-title">Comment/s</h3>
                </div>
                <div class="card-body p-0 py-2">
                <div class="container-fluid">
    <?php 
    $comments = $conn->query("SELECT * FROM comments WHERE ticket_id = '$id' ORDER BY unix_timestamp(date_created) ASC");
    while ($row = $comments->fetch_assoc()):
        $uname_query = $conn->query("SELECT CONCAT(lastname, ', ', firstname, ' ', middlename) AS name FROM users WHERE id = '{$row['user_id']}'");
        $uname_row = $uname_query->fetch_array();
        $uname = $uname_row ? $uname_row['name'] : 'Unknown User';
    ?>
    <div class="card card-outline card-dark">
        <div class="card-header">
            <h5 class="card-title"><?php echo ucwords($uname); ?></h5>
            <div class="card-tools">
                <span class="text-muted"><?php echo date("n-j-Y h:i:s A", strtotime($row['date_created'])); ?></span>
                <?php if ($row['user_type'] == $_SESSION['login_type'] && $row['user_id'] == $_SESSION['login_id'] && $status != 2 && $status != 3): ?>
                    <span class="dropleft">
                        <a class="fa fa-ellipsis-v text-muted" href="javascript:void(0)" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item delete_comment" href="javascript:void(0)" data-id="<?php echo $row['id']; ?>">Delete</a>
                        </div>
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <div>
                <?php echo html_entity_decode($row['comment']); ?>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>
                    <?php if ($status != 2 && $status != 3): ?>
    <div class="px-2">
        <form action="" id="manage-comment">
            <div class="form-group">
                <input type="hidden" name="ticket_id" value="<?php echo $id; ?>">
                <label class="control-label">New Comment</label>
                <textarea name="comment" id="comment_textarea" cols="30" rows="" class="form-control summernote2"></textarea>
            </div>
            <button type="button" class="btn btn-secondary btn-sm float-right mr-1" id="clear_comment_btn">Clear</button>
            <button class="btn btn-success btn-sm float-right mr-1" id="save_comment_btn">Save</button>
        </form>
    </div>
<?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('.summernote2').summernote({
            height: 150,
            toolbar: [
                ['font', ['bold', 'fontsize', 'undo', 'redo']]
            ]
        });
    });

    $('.update_priority').click(function(){
        uni_modal("Update Ticket", "manage_ticket.php?id=" + $(this).attr('data-id'));
    });

    $('.update_status').click(function(){
        uni_modal("Update Ticket", "manage_ticket.php?id=" + $(this).attr('data-id'));
    });

    $('#manage-comment').submit(function(e){
        e.preventDefault();
        var comment = $('#comment_textarea').val().replace(/\s/g, '');
        
        if(comment == '') {
            alert('Comment cannot be empty or consist only of whitespaces.');
            return false;
        }
        
        start_load();
        $.ajax({
            url: 'ajax.php?action=save_comment',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            success: function(resp){
                if (resp == 1) {
                    alert_toast('Comment has been saved.', "success");
                    setTimeout(function(){ location.reload(); }, 1000);
                }
            }
        });
    });

    // Clear comment
    $('#clear_comment_btn').click(function(){
        $('#comment_textarea').val('');  
        $('.summernote2').summernote('reset');  
    });

    // Delete comment
    $('.delete_comment').click(function(){
        _conf("Are you sure to delete this comment?", "delete_comment", [$(this).attr('data-id')]);
    });

    function delete_comment(id){
        start_load();
        $.ajax({
            url: 'ajax.php?action=delete_comment',
            method: 'POST',
            data: {id: id},
            success: function(resp){
                if (resp == 1) {
                    alert_toast("Comment has been deleted.", 'success');
                    setTimeout(function(){ location.reload(); }, 1000);
                }
            }
        });
    }
</script>