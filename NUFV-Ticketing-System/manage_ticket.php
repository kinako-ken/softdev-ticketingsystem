<?php 
include 'db_connect.php'; 
?>
<?php 

$qry = $conn->query("SELECT * FROM tickets WHERE id = '".$conn->real_escape_string($_GET['id'])."'")->fetch_array();
foreach($qry as $k => $v){
    $$k = $v;
}
?>
<div class="container-fluid">
    <form action="" id="update-ticket">
        <input type="hidden" value="<?php echo $id ?>" name='id'>
        
        <div class="form-group">
            <label for="" class="control-label">Status</label>
            <select name="status" id="status-select" class="custom-select custom-select-sm">
                <option value="0" <?php echo $status == 0 ? 'selected' : ''; ?>>Open</option>
                <option value="1" <?php echo $status == 1 ? 'selected' : ''; ?>>Processing</option>
                <option value="2" <?php echo $status == 2 ? 'selected' : ''; ?>>Resolved</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="" class="control-label">Priority</label>
            <select name="priority" id="priority-select" class="custom-select custom-select-sm">
                <option value="0" <?php echo $priority == 0 ? 'selected' : ''; ?>>Low</option>
                <option value="1" <?php echo $priority == 1 ? 'selected' : ''; ?>>Medium</option>
                <option value="2" <?php echo $priority == 2 ? 'selected' : ''; ?>>High</option>
            </select>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    $('#update-ticket').submit(function(e) {
        e.preventDefault();
        let status = $('#status-select').val();
        
        // Additional logic for resolving the ticket
        if (status == 2) {
            $.ajax({
                url: 'ajax.php?action=check_done_comment',
                method: 'POST',
                data: { ticket_id: <?php echo json_encode($id); ?> },
                success: function(resp) {
                    if (resp == 0) {
                        alert('You must comment "Done" in the comment section before marking the ticket as Resolved.');
                        location.reload(); // Reload the page if the "Done" comment is missing
                    } else {
                        submitTicketForm(); // Submit the form only if comment check passes
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: ", error);
                    alert_toast('Something went wrong. Please try again.', "danger");
                    location.reload(); // Reload the page on AJAX error
                }
            });
        } else {
            submitTicketForm(); // Direct submission if status is not 'Resolved'
        }
    });

    function submitTicketForm() {
        start_load(); // Assuming this is a loading animation function
        $.ajax({
            url: 'ajax.php?action=update_ticket',
            data: new FormData($('#update-ticket')[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            success: function(resp) {
                if (resp == 1) {
                    alert_toast('Ticket has been updated.', "success");
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    alert_toast('Ticket failed to update. You must comment "Done" before resolving a ticket.', "danger");
                    setTimeout(function() {
                        location.reload();
                    }, 3000);
                }
            },
            error: function(err) {
                console.error("Error in AJAX call: ", err);
                alert_toast('Something went wrong. Please try again.', "danger");
                location.reload(); // Reload the page on AJAX error
            }
        });
    }
});
</script>