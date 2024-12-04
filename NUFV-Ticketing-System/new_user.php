<?php
include 'db_connect.php';
include('auth.php');
restrict_to_admin();
?>

<title>Create Account</title>
<div class="col-lg-12">
    <div class="card card-outline">
        <div class="card-header">
            <div class="card-tools">
                <a href="index.php?page=home" class="btn btn-secondary mr-4">
                    <i class="bi bi-arrow-left-short"></i> Return
                </a>
        </div>
 </div>

<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">Error</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="error-message">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

        <div class="card-body">
            <div class="d-flex justify-content-end">
            </div>
            <form action="" id="manage_user">
             <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
                <div class="row">
                    <div class="col-md-6 border-right">
                        <b class="text-muted">Personal Information</b>
                        <div class="form-group">
                            <label for="" class="control-label">First Name</label>
                            <input type="text" name="firstname" class="form-control form-control-sm" required value="<?php echo isset($firstname) ? $firstname : '' ?>">
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">Middle Name</label>
                            <input type="text" name="middlename" class="form-control form-control-sm" value="<?php echo isset($middlename) ? $middlename : '' ?>">
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">Last Name</label>
                            <input type="text" name="lastname" class="form-control form-control-sm" required value="<?php echo isset($lastname) ? $lastname : '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="" class="control-label">Role</label>
                            <select name="role" class="form-control form-control-sm" required>
                                <option value="">Select Role</option>
                                <option value="1" <?php echo (isset($role) && $role == 1) ? 'selected' : '' ?>>Admin (ITSO Superadmin)</option>
                                <option value="2" <?php echo (isset($role) && $role == 2) ? 'selected' : '' ?>>Staff (ITSO Admin)</option>
                                <option value="3" <?php echo (isset($role) && $role == 3) ? 'selected' : '' ?>>User (Student/Faculty)</option>
                            </select>
                        </div>

                        <div class="form-group">
                             <label class="control-label">ID Number</label>
                            <input type="text" name="id_number" class="form-control form-control-sm" required value="<?php echo isset($id) ? $id : '' ?>" placeholder="####-######">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <b class="text-muted">System Credentials</b>
                        <div class="form-group">
                            <label class="control-label">Email</label>
                            <input type="email" class="form-control form-control-sm" name="email" required value="<?php echo isset($email) ? $email : '' ?>">
                            <small id="msg"></small>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Password</label>
                            <input type="password" class="form-control form-control-sm" name="password" <?php echo isset($id) ? "" : 'required' ?>>
                            <small><i><?php echo isset($id) ? "Leave this blank if you don't want to change your password" : '' ?></i></small>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Confirm Password</label>
                            <input type="password" class="form-control form-control-sm" name="cpass" <?php echo isset($id) ? "" : 'required' ?>>
                            <small id="pass_match" data-status=''></small>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="col-lg-12 text-right justify-content-center d-flex">
                    <button class="btn btn-primary mr-2">Save</button>
                    <button class="btn btn-secondary" type="reset">Clear</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('[name="password"],[name="cpass"]').keyup(function(){
        var pass = $('[name="password"]').val()
        var cpass = $('[name="cpass"]').val()
        if(cpass == '' || pass == ''){
            $('#pass_match').attr('data-status','')
        } else {
            if(cpass == pass){
                $('#pass_match').attr('data-status','1').html('<i class="text-success">Password Matched.</i>')
            } else {
                $('#pass_match').attr('data-status','2').html('<i class="text-danger">Password does not match.</i>')
            }
        }
    })

    $('#manage_user').submit(function(e){
    e.preventDefault();
    $('input').removeClass("border-danger");
    start_load();

    let emptyFields = false;
    $('#manage_user [required]').each(function() {
        if ($(this).val().trim() === '') {
            $(this).addClass("border-danger");
            emptyFields = true;
        }
    });

    if (emptyFields) {
        $('#error-message').text("Please fill in all required fields.");
        $('#errorModal').modal('show');
        end_load();
        return false;
    }

    if ($('#pass_match').attr('data-status') != 1) {
        if ($("[name='password']").val() != '') {
            $('[name="password"],[name="cpass"]').addClass("border-danger");
            $('#error-message').text("Passwords do not match.");
            $('#errorModal').modal('show');
            end_load();
            return false;
        }
    }

    $.ajax({
        url: 'ajax.php?action=save_user',
        data: new FormData($(this)[0]),
        cache: false,
        contentType: false,
        processData: false,
        method: 'POST',
        success: function(resp) {
            if (resp == 1) {
                alert_toast('Data successfully saved.', "success");
                setTimeout(function(){
                    location.replace('index.php?page=user_list');
                }, 750);
            } else if (resp == 2) {
                $('[name="email"]').addClass("border-danger");
                $('#error-message').text("Email already exists.");
                $('#errorModal').modal('show');
                end_load();
            } else if (resp == 3) {
                $('[name="id_number"]').addClass("border-danger");
                $('#error-message').text("ID Number already exists.");
                $('#errorModal').modal('show');
                end_load();
            } else if (resp == 4) {
                $('[name="firstname"], [name="middlename"], [name="lastname"]').addClass("border-danger");
                $('#error-message').text("Name already exists.");
                $('#errorModal').modal('show');
                end_load();
            } else {
                alert("An error occurred. Please check your input.");
                end_load();
            }
        },
        error: function(xhr, status, error) {
            console.error("Error: " + error);
            alert("An error occurred: " + xhr.responseText);
            end_load();
        }
    });
});
</script>