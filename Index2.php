<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
  header("location: login.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Users</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="icons/atte1.jpg">

    <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <link rel="stylesheet" type="text/css" href="css/Users.css">
    <script>
      $(window).on("load resize ", function() {
        var scrollWidth = $('.tbl-content').width() - $('.tbl-content table').width();
        $('.tbl-header').css({'padding-right':scrollWidth});
    }).resize();
    </script>
</head>
<body>
<?php include'header2.php'; ?> 
<main>
<section>
  <h1 class="slideInDown animated">Here are all the Users</h1>
  <!--User table-->
  <div class="table-responsive slideInRight animated" style="max-height: 400px;"> 
    <table class="table">
      <thead class="table-primary">
        <tr>
          <th>Name</th>
          <th>User Number</th>
          <th>Gender</th>
          <th>Email</th>
          <th>Finger ID</th>
          <th>Dept</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody class="table-secondary">
        <?php
          //Connect to database
          require'connectDB.php';

            $sql = "SELECT * FROM users WHERE fingerprint_id ORDER BY id DESC";
            $result = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($result, $sql)) {
                echo '<p class="error">SQL Error</p>';
            }
            else{
                mysqli_stmt_execute($result);
                $resultl = mysqli_stmt_get_result($result);
                if (mysqli_num_rows($resultl) > 0){
                    while ($row = mysqli_fetch_assoc($resultl)){
        ?>
                      <TR>
                      <TD><?php echo $row['username'];?></TD>
                      <TD><?php echo $row['serialnumber'];?></TD>
                      <TD><?php echo $row['gender'];?></TD>
                      <TD><?php echo $row['email'];?></TD>
                      <TD><?php echo $row['fingerprint_id'];?></TD>                     
                      <TD><?php echo $row['user_dept'];?></TD>
                      <TD><?php echo "<button class='btn btn-danger' onclick='deleteAdmin(" . $row['id'] . ")'>Delete</button>"; ?></TD>
                      </TR>
        <?php
                    }   
                }
            }
        ?>
      </tbody>
    </table>
  </div>
</section>
</main>

<script>
function deleteAdmin(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "delete_user.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                alert('User deleted successfully');
                window.location.reload(); // Reload the page to see the change
            }
        };
        xhr.send("id=" + userId);
    }
}
</script>

</body>
</html>