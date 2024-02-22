<?php
session_start();

// Include your database connection file here
include 'connectDB.php';

// Function to delete an admin by ID
function deleteAdmin($conn, $admin_id) {
    $sql = "DELETE FROM admins WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $admin_id);
    
    if ($stmt->execute()) {
        return true; // Admin deleted successfully
    } else {
        return false; // Error deleting admin
    }
}

// Check if an admin ID is provided for deletion
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $admin_id = $_GET['delete'];
    
    // Call the deleteAdmin function
    if (deleteAdmin($conn, $admin_id)) {
        echo "Admin deleted successfully.";
    } else {
        echo "Error deleting admin.";
    }
}

// Check if user is logged in as admin
if (!isset($_SESSION['Admin-name'])) {
    header("location: login.php");
    exit();
}

// Retrieve admins from database
$sql = "SELECT id, admin_name, admin_email, admin_password FROM admins";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "Error: " . mysqli_error($conn);
    exit(); // Exit if there's an error retrieving data
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Admins</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="icons/atte1.jpg">
    <link rel="stylesheet" type="text/css" href="css/devices.css"/>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Bootstrap JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <!-- Bootbox -->
    <script type="text/javascript" src="js/bootbox.min.js"></script>

    <!-- Custom JavaScript -->
    <script src="js/dev_config.js"></script>

    <style>
        /* Add your custom styles here */
    </style>

    <script>
        

        // Function to delete an admin
        function deleteAdmin(adminId) {
            bootbox.confirm({
                message: "Are you sure you want to delete this admin?",
                buttons: {
                    confirm: {
                        label: 'Yes',
                        className: 'btn-danger'
                    },
                    cancel: {
                        label: 'No',
                        className: 'btn-secondary'
                    }
                },
                callback: function (result) {
                    if (result) {
                        $.ajax({
                            url: "admins.php", // This PHP file will handle the backend processing
                            type: 'GET',
                            data: {'delete': adminId}, // Pass the admin ID to be deleted
                        }).done(function(data) {
                            location.reload(); // Reload admins after deletion
                        });
                    }
                }
            });
        }
    </script>
</head>
<body>
<?php include 'header.php'; ?>
<main>
    <h1 class="slideInDown animated">Manage Admins</h1>

    <section class="container py-lg-5">
        <div class="alert_dev"></div>
        <!-- Admins -->
        <div class="row">
            <div class="col-lg-12 mt-4">
                <div class="panel">
                    <div class="panel-heading" style="font-size: 19px;">Admins:
                        <!-- Button to open admin registration modal -->
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#admin-registration-modal" style="font-size: 18px; float: right; margin-top: -6px;">New Admin</button>
                    </div>
                    <div class="panel-body">
                        <div id="admins">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email Address</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Loop through each admin and display their details
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . $row['admin_name'] . "</td>";
                                        echo "<td>" . $row['admin_email'] . "</td>";
                                        //echo "<td>" . $row['admin_password'] . "</td>";
                                        echo "<td><button class='btn btn-danger' onclick='deleteAdmin(" . $row['id'] . ")'>Delete</button></td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Admin Registration Modal -->
<div class="modal fade" id="admin-registration-modal" tabindex="-1" role="dialog" aria-labelledby="Admin Registration" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLongTitle">Admin Registration</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Admin registration form starts here -->
                <form action="admin_register.php" method="post">
                    <label for="admin_name">Admin Name:</label>
                    <input type="text" id="admin_name" name="admin_name" required><br>

                    <label for="admin_email">Admin Email:</label>
                    <input type="email" id="admin_email" name="admin_email" required><br>

                    <label for="admin_password">Admin Password:</label>
                    <input type="password" id="admin_password" name="admin_password" required><br>

                    <input type="submit" value="Register">
                </form>
                <!-- Admin registration form ends here -->
            </div>
            <div class="modal-footer">
               
