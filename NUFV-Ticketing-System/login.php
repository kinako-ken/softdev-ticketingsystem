<!DOCTYPE html>
<html lang="en">

<?php 
session_start();
include('./db_connect.php');
?>

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Login</title>

    <?php include('./header.php'); ?>
    <?php 
    if(isset($_SESSION['login_id']))
        header("location:index.php?page=homes");
    ?>

</head>

<style>
    body {
        margin: 0;
        overflow: hidden;
        background: url('NUFV Background.png');
        background-size: cover;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
    }

    main#main {
        width: 100%;
        height: calc(100%);
        display: flex;
    }

    .logo {
        position: fixed;
        top: 10px;
        left: 10px;
        width: 150px; 
        height: auto;
    }

    .container {
        position: relative;
        width: 500px;
        height: auto;
        flex-shrink: 0;
        background: rgba(52, 65, 142, 0.9);
        border-radius: 10px;
        padding: 50px;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .header-text {
        color: #FFF;
        font-family: Inter;
        font-size: 30px;
        font-style: normal;
        font-weight: 700;
        margin-bottom: 20px;
        text-align: center;
    }

    #login-alert {
        color: red;
        margin-bottom: 20px;
    }

    .input-group {
        width: 100%;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
    }

    .form-control {
        width: 100%;
        background: white;
        border-radius: 5px;
        padding: 10px;
    }

    .btn-success {
        width: 30%;
        height: 35px;
        background: rgba(166, 142, 17, 0.80);
        border: none;
        border-radius: 0px;
        color: white;
    }

    .divider-line {
        margin: 30px 0;
        border: none;
        height: 1px;
        background: white; 
    }

    .additional-text {
        margin-top: 20px;
        font-size: 14px;
        color: #FFF;
    }
</style>

<body class="bg-dark">

    <main id="main">
        <img src="NUFV Watermark.png" alt="NUFV Logo" class="logo">
        <div class="align-self-center w-100">
            <div class="container contact text-center">
                <div class="header-text">
                    ITSO HELPDESK TICKETING SYSTEM
                </div>
                <form id="login-form" class="form-horizontal" role="form" method="POST" action="">
                    <div class="input-group">
                        <input type="text" class="form-control" id="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    </div>

                    <button class=" btn-sm btn-block btn-wave col-md-2 btn-success">Login</button>
                </form>
                <hr class="divider-line">
                <div class="additional-text">
                    All modules, contents, and services included in this system are intended for Nationalians' use only. You may not, except with our express written permission, distribute or commercially exploit its contents. Nor may you transmit it or store it in any other website or other forms of electronic retrieval system. 
                    National University © <span id="currentYear"></span>.
                </div>
            </div>
        </div>
    </main>

    <a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>

</body>

<script>
    document.getElementById("currentYear").textContent = new Date().getFullYear();

    $('#login-form').submit(function (e) {
        e.preventDefault()
        $('#login-form button[type="button"]').attr('disabled', true).html('Logging in...');
        if ($(this).find('.alert-danger').length > 0)
            $(this).find('.alert-danger').remove();
        $.ajax({
            url: 'ajax.php?action=login',
            method: 'POST',
            data: $(this).serialize(),
            error: err => {
                console.log(err)
                $('#login-form button[type="button"]').removeAttr('disabled').html('Login');
            },
            success: function (resp) {
                if (resp == 1) {
                    location.href = 'index.php?page=homes';
                } else {
                    $('#login-form').prepend('<div class="alert alert-danger">Email or password is incorrect.</div>')
                    $('#login-form button[type="button"]').removeAttr('disabled').html('Login');
                }
            }
        })
    })

    $('.number').on('input', function () {
        var val = $(this).val()
        val = val.replace(/[^0-9 \\,]/, '');
        $(this).val(val)
    })
</script>

</html>
