<?php 
include 'db_connect.php';
include('auth.php');
restrict_to_admin();
?>
<title>Admin List</title>
<div class="col-lg-12">
    <div class="card card-outline">
        <div class="card-header">
            <div class="card-tools">
                <a href="index.php?page=new_user"><button class="btn btn-primary mr-1"><i class="fa fa-plus"></i>New Admin</button></a>
                <a href="index.php?page=home" class="btn btn-secondary mr-4">
                    <i class="bi bi-arrow-left-short"></i> Return
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-hover table-bordered" id="list">
                <thead>
                    <tr>
                        <th style="background-color: #34418E; color: white;">#</th>
                        <th style="background-color: #34418E; color: white;">Name</th>
                        <th style="background-color: #34418E; color: white;">Role</th>
                        <th style="background-color: #34418E; color: white;">ID Number</th>
                        <th style="background-color: #34418E; color: white;">Email</th>
                        <th style="background-color: #34418E; color: white;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $qry = $conn->query("SELECT id, role, concat(lastname, ', ', firstname, ' ', middlename) as name, email FROM users WHERE role IN (1, 2) ORDER BY lastname, firstname ASC");
                    while ($row = $qry->fetch_assoc()):
                        $role_label = ($row['role'] == 1) ? 'Admin' : 'Staff';
                    ?>
                        <tr>
                            <th class="text-center"><?php echo $i++ ?></th>
                            <td><b><?php echo ucwords($row['name']) ?></b></td>
                            <td><b><?php echo $role_label ?></b></td>
                            <td><b><?php echo $row['id'] ?></b></td>
                            <td><b><?php echo $row['email'] ?></b></td>
                            <td class="text-center">
                            <button type="button" class="btn btn-warning btn-xs update" 
                                    onclick="window.location.href='./index.php?page=edit_user&id=<?php echo $row['id'] ?>'" title="Edit"><i class="fas fa-edit"></i>
                                </button>
                                <a href="javascript:void(0)" class="btn btn-danger btn-xs delete_user" data-id="<?php echo $row['id'] ?>" title="Delete"><i class="fas fa-trash"></i>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $('#list').dataTable({
        "columnDefs": [
            { "orderable": false, "targets": 5 }
        ]
    });

    $('.delete_user').click(function(){
        const id = $(this).attr('data-id');
        if (confirm("Are you sure to delete this user?")) {
            delete_user(id);
        }
    });
});

function delete_user(id){
    console.log("Deleting user with ID:", id);
    start_load();
    $.ajax({
        url:'ajax.php?action=delete_user',
        method:'POST',
        data:{ id: id },
        success:function(resp){
            console.log("Response from delete_user:", resp);
            if(resp == 1){
                alert_toast("Data successfully deleted", 'success');
                setTimeout(function(){
                    location.reload();
                },1500);
            } else {
                alert("Failed to delete user. Please try again.");
                end_load();
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
            alert("An error occurred. Please check the console for details.");
            end_load();
        }
    });
}
</script>