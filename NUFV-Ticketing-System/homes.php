<?php
if (!isset($_SESSION['login_type'])) {
    header('Location: login.php');
    exit();
}
include('db_connect.php');
?>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="font-awesome/css/all.min.css">
<link rel="stylesheet" href="dashstyle.css">

<title>Homepage</title>

<body>
  <div class="section">
    <div class="services">
      
      <?php if(in_array($_SESSION['login_type'], [1, 2])): ?>
        <div class="card">
          <a href="index.php?page=home" class="button">
            <div class="icon">
              <i class="fa-solid fa-chart-line" aria-hidden="true"></i>                   
            </div>
            <h2>Dashboard</h2>
          </a>
        </div>
      <?php endif; ?>
      
      <div class="card">
        <a href="index.php?page=ticket_list" class="button">
          <div class="icon">
            <i class="fa-solid fa-ticket" aria-hidden="true"></i>                      
          </div>
          <h2>Ticket List</h2>
        </a>
      </div>
      
      <?php if($_SESSION['login_type'] == 3): ?>
        <div class="card">
          <a href="index.php?page=history" class="button">
            <div class="icon">
              <i class="fa-solid fa-history" aria-hidden="true"></i>                      
            </div>
            <h2>Ticket History</h2>
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>