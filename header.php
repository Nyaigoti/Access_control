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
        <a href="index.php">Users</a>
        <a href="ManageUsers.php">Manage Users</a>
        <a href="UsersLog.php">Attendance Logs</a>
        <a href="projects.php">Projects</a>
        <a href="inventory.php">Inventory</a>
        <a href="admins.php">Sub-Admins</a>

        <!-- Other navigation links -->
        <?php  
            if (isset($_SESSION['Admin-name'])) {
                // Admin name link with modal trigger, wrapped in a div for separation
                //echo '<div class="admin-name-link">';
                //echo '<a href="#" data-toggle="modal" data-target="#admin-account">' . $_SESSION['Admin-name'] . '</a>';
                //echo '</div>'; // Close admin-name-link div

                // Log Out link, also wrapped in a separate div for separation
                echo '<div class="logout-link">';
                echo '<a href="logout.php">Log Out</a>';
                echo '</div>'; // Close logout-link div
            }
            else{
                // Log In link for users who are not logged in, optionally wrapped in a div
                echo '<div class="login-link">';
                echo '<a href="login.php">Log In</a>';
                echo '</div>'; // Close login-link div
            }
        ?>
            
    </div>
</header>
