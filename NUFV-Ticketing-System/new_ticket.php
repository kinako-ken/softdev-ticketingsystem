<?php
if (!isset($conn)) {
    include 'db_connect.php';
}
?>

<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <form action="" id="manage_ticket">
                <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">

                <div class="col-md-6">
                    <?php if ($_SESSION['login_type'] == 3) : ?>
                    <div class="form-group">  
                        <label for="subject" class="control-label">Subject</label>                     
                        <input type="text" name="subject" id="subject" class="form-control form-control-sm" required 
                        value="<?php echo isset($subject) ? $subject : '' ?>" placeholder="No internet connection">
                    </div>

                    <div class="form-group">
                        <label for="support_id" class="control-label">Support Type</label>
                        <select name="support_id" id="support_id" class="custom-select custom-select-sm select2" required>
                            <option value="">Select Support Type</option>
                            <?php
                            $support_query = $conn->query("SELECT * FROM support ORDER BY id ASC");
                            while ($row = $support_query->fetch_assoc()) :
                            ?>
                                <option value="<?php echo $row['id'] ?>" <?php echo isset($support_id) && $support_id == $row['id'] ? "selected" : '' ?>>
                                    <?php echo ucwords($row['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="category_id" class="control-label">Category</label>
                        <select name="category_id" id="category_id" class="custom-select custom-select-sm select2" required>
                            <option value="">Select Category</option>
                            <?php
                            $category_query = $conn->query("SELECT * FROM categories ORDER BY id ASC");
                            while ($row = $category_query->fetch_assoc()) :
                            ?>
                                <option value="<?php echo $row['id'] ?>" <?php echo isset($category_id) && $category_id == $row['id'] ? "selected" : '' ?>>
                                    <?php echo ucwords($row['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="room_id" class="control-label">Room</label>
                        <select name="room_id" id="room_id" class="custom-select custom-select-sm select2" required>
                            <option value="">Select Room</option>
                            <?php
                            $room_query = $conn->query("SELECT * FROM rooms ORDER BY id ASC");
                            while ($row = $room_query->fetch_assoc()) :
                            ?>
                                <option value="<?php echo $row['id'] ?>" <?php echo isset($room_id) && $room_id == $row['id'] ? "selected" : '' ?>>
                                    <?php echo ucwords($row['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description" class="control-label">Description</label>
                        <textarea name="description" id="description" cols="30" rows="10" class="form-control summernote" required><?php echo isset($description) ? $description : '' ?></textarea>
                    </div>
                    <?php endif; ?>
                </div>

                <hr>
                <div class="col-lg-12 text-right justify-content-center d-flex">
                    <button class="btn btn-success mr-2" id="save_ticket">Save</button>
                    <button class="btn btn-secondary" type="button" onclick="window.history.back();">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#manage_ticket').submit(function (e) {
        e.preventDefault();
        
        var subject = $('#subject').val().trim();
        var support_type = $('#support_id').val().trim();
        var category = $('#category_id').val().trim();
        var room = $('#room_id').val().trim();
        var description = $('#description').val().replace(/\s/g, '');

        if (subject === "" || support_type === "" || category === "" || room === "" || description === " ") {
            alert("Please fill out all fields properly. Description cannot be empty or contain only whitespaces.");
            return false;
        }

        start_load();
        $.ajax({
            url: 'ajax.php?action=save_ticket',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            success: function (resp) {
                if (resp == 1) {
                    alert_toast('Data successfully saved.', "success");
                    setTimeout(function () {
                        location.replace('index.php?page=ticket_list');
                    }, 1000);
                } else {
                    alert_toast(resp, "danger");
                }
                end_load();
            },
            error: function(xhr, status, error) {
                console.error("Error: " + error);
                alert("An error occurred while saving the ticket.");
                end_load();
            }
        });
    });
</script>