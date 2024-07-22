<head>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel='stylesheet' type='text/css' href="css/bootstrap.css"/>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="css/header.css"/>
</head>
<header>
  <div class="header">
    <div class="logo">
      <a href="index.php">ERP SYSTEM</a>
    </div>
  </div>
  <?php  
  if (isset($_GET['error'])) {
    // Error handling code here...
  } 
  if (isset($_GET['success'])) {
    // Success handling code here...
  }
  ?>
  <div class="topnav" id="myTopnav">
    <!-- Additional navigation links for index, ManageUsers, UsersLog, and devices -->
    <a href="index2.php">Users</a>
    <a href="ManageUsers2.php">Manage Users</a>
    <a href="UsersLog2.php">Attendance Logs</a>
    <a href="projects.php">Projects</a>
    <a href="inventory.php">Inventory</a>

    <!-- Other navigation links -->
    <?php  
    if (isset($_SESSION['Admin-name'])) {
      echo '<a href="#" data-toggle="modal" data-target="#admin-account">'.$_SESSION['Admin-name'].'</a>';
      echo '<a href="logout.php">Log Out</a>';
    }
    else{
      echo '<a href="login.php">Log In</a>';
    }
    ?>
    <!-- End of additional navigation links -->
  </div>
  <!-- Other HTML elements -->
</header>
