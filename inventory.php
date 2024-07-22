<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
  header("location: login.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="icons/atte1.jpg">

    <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <link rel="stylesheet" type="text/css" href="css/Users.css">

    <script>
        $(document).ready(function() {
            
            $('#assign-form').submit(function(event) {
                event.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    type: 'POST',
                    url: 'assign_component.php',
                    data: formData,
                    success: function(response) {
                        if(response === 'success') {
                            alert('Component assigned successfully!');
                            $('#assignModal').modal('hide');
                            window.location.reload();
                        } else {
                            alert(response);
                        }
                    }
                });
            });

            $('#return-form').submit(function(event) {
                event.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    type: 'POST',
                    url: 'return_component.php',
                    data: formData,
                    success: function(response) {
                        if(response === 'success') {
                            alert('Component returned successfully!');
                            $('#returnModal').modal('hide');
                            window.location.reload();
                        } else {
                            alert(response);
                        }
                    }
                });
            });


            $(document).ready(function() {
                $('#usersModal').on('show.bs.modal', function (event) {
                    var componentId = $(event.relatedTarget).data('component-id');
                    $.ajax({
                        url: 'fetch_users.php',
                        type: 'POST',
                        data: { component_id: componentId },
                        dataType: 'json',
                        success: function(data) {
                            var tbodyHtml = '';
                            data.forEach(function(user) {
                                tbodyHtml += '<tr><td>' + user.username + '</td><td>' + user.quantity + '</td></tr>';
                            });
                            $('#users-list').html(tbodyHtml);
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log('Error loading data: ' + textStatus);
                            $('#users-list').html('<tr><td colspan="2">Error loading data</td></tr>');
                        }
                    });
                });
            });



            $('#edit-form').submit(function(event) {
            event.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: 'update_inventory.php',
                data: formData,
                success: function(response) {
                    if (response === 'success') {
                        alert('Component updated successfully!');
                        $('#editModal').modal('hide');
                        window.location.reload();
                    } else {
                        alert(response);
                    }
                }
                });
            });
        });

        
        function showEditModal(id, name, quantity) {
            $('#editModal').modal('show');
            $('#edit-id').val(id);
            $('#edit-name').val(name);
            $('#edit-quantity').val(quantity);
        }

        function showAssignModal(id, name) {
            $('#assignModal').modal('show');
            $('#assign-id').val(id);
            $('#assign-name').val(name);
        }

        function showReturnModal(id, name) {
            $('#returnModal').modal('show');
            $('#return-id').val(id);
            $('#return-name').val(name);
        }

        function showUsersModal(id, name) {
            $('#usersModal').modal('show');
            $.ajax({
                type: 'POST',
                url: 'fetch_users.php',
                data: {component_id: id},
                success: function(response) {
                    $('#users-list').html(response);
                },
                error: function() {
                    alert('Error loading users.');
                }
            });
        }


        function deleteComponent(id) {
            if (confirm('Are you sure you want to delete this component?')) {
                $.post('delete_component.php', {id: id}, function(data) {
                    if (data === 'success') {
                        alert('Component deleted successfully!');
                        window.location.reload();
                    } else {
                        alert('Error deleting component: ' + data);
                    }
                });
            }
        }


      $(window).on("load resize ", function() {
            var scrollWidth = $('.tbl-content').width() - $('.tbl-content table').width();
            $('.tbl-header').css({'padding-right':scrollWidth});
        }).resize();


            $(document).ready(function() {
            $('#usersModal').on('show.bs.modal', function (event) {
                $.ajax({
                    url: 'fetch_users.php', // URL to the PHP script that will fetch user data
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        var tbodyHtml = '';
                        data.forEach(function(user) {
                            tbodyHtml += '<tr><td>' + user.username + '</td><td>' + user.quantity + '</td></tr>';
                        });
                        $('#users-list').html(tbodyHtml);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log('Error loading data: ' + textStatus);
                        $('#users-list').html('<tr><td colspan="2">Error loading data</td></tr>');
                    }
                });
            });
        });


    </script>
</head>

<body>
<?php include'header.php'; ?> 
<main>
<section>
  <h1 class="slideInDown animated">Inventory List</h1>

    <div class="row">
        <div class="col-lg-12 mt-4">
            <div class="panel">
                <div class="panel-heading" style="font-size: 19px;">Inventory:
                    <!-- Button to open admin registration modal -->
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#admin-registration-modal" style="font-size: 18px; float: right; margin-top: -6px;">New Components</button>
                </div>
                
                <!--User table-->
                <div class="table-responsive slideInRight animated" style="max-height: 400px;"> 
                    <table class="table">
                    <thead class="table-primary">
                        <tr>
                        <th>Name</th>
                        <th>Quantity</th>
                        <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-secondary">
                    <?php
                        include 'connectDB.php';

                        $sql = "SELECT component_id, name, quantity FROM inventory ORDER BY component_id DESC";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                    echo "<td>" . $row['name'] . "</td>";
                                    echo "<td>" . $row['quantity'] . "</td>";
                                    echo "<td>";
                                    echo "<button class='btn btn-primary' onclick='showEditModal(" . $row['component_id'] . ", \"" . $row['name'] . "\", " . $row['quantity'] . ")'>Edit</button> ";
                                    echo "<button class='btn btn-info' onclick='showUsersModal(" . $row['component_id'] . ", \"" . $row['name'] . "\")'>Users</button> ";
                                    echo "<button class='btn btn-info' onclick='showAssignModal(" . $row['component_id'] . ", \"" . $row['name'] . "\")'>Assign</button> ";
                                    echo "<button class='btn btn-warning' onclick='showReturnModal(" . $row['component_id'] . ", \"" . $row['name'] . "\")'>Return</button>";
                                    echo "<button class='btn btn-danger' onclick='deleteComponent(" . $row['component_id'] . ")'>Delete</button>";
                                    echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "0 results";
                        }
                        $conn->close();
                    ?>

                    </tbody>
                    </table>
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
                <h3 class="modal-title" id="exampleModalLongTitle">Add Component</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Admin registration form starts here -->
                <form action="add_component.php" method="post">
                    <label for="name">Component Name:</label>
                    <input type="text" id="name" name="name" required><br>

                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" required><br>
                    

                    <!-- Add more options as needed -->
                    <input type="submit" value="Add">
                </form>
                <!-- Admin registration form ends here -->
            </div>
        </div>
    </div>
</div> 

<!-- Assign Component Modal -->
<div class="modal fade" id="assignModal" tabindex="-1" role="dialog" aria-labelledby="Assign Component" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLongTitle">Assign Component</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="assign-form">
                    <input type="hidden" id="assign-id" name="component_id">
                    <div class="form-group">
                        <label for="user-select">Assign to User:</label>
                        <select id="user-select" name="user_id" class="form-control" required>
                            <?php
                            // PHP code to fetch all users
                            include 'connectDB.php';
                            $sql = "SELECT id, username FROM users";
                            $result = $conn->query($sql);
                            while ($user = $result->fetch_assoc()) {
                                echo "<option value='" . $user['id'] . "'>" . $user['username'] . "</option>";
                            }
                            $conn->close();
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="assign-quantity">Quantity:</label>
                        <input type="number" id="assign-quantity" name="quantity" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Assign</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Return Component Modal -->
<div class="modal fade" id="returnModal" tabindex="-1" role="dialog" aria-labelledby="Return Component" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLongTitle"> Return Component</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form id="return-form">
                <input type="hidden" id="return-id" name="component_id">
                <div class="form-group">
                    <label for="user-select">User:</label>
                    <select id="user-select" name="user_id" class="form-control" required>
                            <?php
                            // PHP code to fetch all users
                            include 'connectDB.php';
                            $sql = "SELECT id, username FROM users";
                            $result = $conn->query($sql);
                            while ($user = $result->fetch_assoc()) {
                                echo "<option value='" . $user['id'] . "'>" . $user['username'] . "</option>";
                            }
                            $conn->close();
                            ?>
                    </select>
                        <!-- Option elements here -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="return-quantity">Quantity:</label>
                    <input type="number" id="return-quantity" name="quantity" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Return</button>
            </form>
            </div>
        </div>
    </div>
</div>


<!-- Edit Component Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="Edit Component" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLongTitle">Edit Quantity</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit-form">
                    <input type="hidden" id="edit-id" name="component_id">
                    <div class="form-group">
                        <label for="edit-name">Component Name:</label>
                        <input type="text" id="edit-name" name="name" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="edit-quantity">Quantity:</label>
                        <input type="number" id="edit-quantity" name="quantity" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Component Users Modal -->
<div class="modal fade" id="usersModal" tabindex="-1" role="dialog" aria-labelledby="Users List" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLongTitle">Borrowers List</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table">
                    
                    <tbody id="users-list">
                        <!-- Users data will be loaded here via jQuery -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>





</body>
</html>