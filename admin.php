<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
    header("location: login.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="icons/atte1.jpg">

    <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <link rel="stylesheet" type="text/css" href="css/admin.css">
    <script>
        $(window).on("load resize ", function() {
            var scrollWidth = $('.tbl-content').width() - $('.tbl-content table').width();
            $('.tbl-header').css({'padding-right':scrollWidth});
        }).resize();
    </script>
</head>
<body>
<?php include 'header.php'; ?>
<main>
    <section>
        <h1 class="slideInDown animated">Admin Information</h1>
        <!--Admin table-->
        <div class="table-responsive slideInRight animated" style="max-height: 400px;">
            <table class="table">
                <thead class="table-primary">
                <tr>
                    <th>Username</th>
                    <th>Email Address</th>
                    <th>Password</th>
                </tr>
                </thead>
                <tbody class="table-secondary">
                <?php
                require_once 'connectDB.php';

                $sql = "SELECT * FROM admins";
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <tr>
                            <td><?php echo $row['admin_name']; ?></td>
                            <td><?php echo $row['admin_email']; ?></td>
                            <td><?php echo $row['admin_password']; ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    echo '<tr><td colspan="3">No admin information available</td></tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
</body>
</html>
