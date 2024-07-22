<?php
session_start();

if (!isset($_SESSION['Admin-name'])) {
    header("location: login.php");
  }

include 'connectDB.php';

// Check if an project ID is provided for deletion
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $project_id = $_GET['delete'];
    
    // Call the deleteAdmin function
    if (deleteProject($conn, $project_id)) {
        echo "Project deleted successfully.";
    } else {
        echo "Error deleting project.";
    }
}

// Retrieve projects from database
$sql = "SELECT project_id, project_name, start_date , duration, status FROM projects";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "Error: " . mysqli_error($conn);
    exit(); // Exit if there's an error retrieving data
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Projects</title>
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

    <script>       
        function deleteProjectAjax(projectId) {
            if (confirm('Are you sure you want to delete this project?')) {
                $.post('delete_project.php', { project_id: projectId })
                    .done(function(response) {
                        alert(response);
                        location.reload(); // Reload the page to see the changes
                    })
                    .fail(function(xhr, status, error) {
                        console.error('Error deleting project:', error);
                        alert('Error: Could not delete project. Please check the console for more details.');
                    });
            }
        }


        function editProject(projectId, projectName, projectStatus, projectDuration) {
            $('#editProjectId').val(projectId);
            $('#editProjectName').val(projectName);
            $('#editStatus').val(projectStatus.toLowerCase()); // Convert the status to lowercase
            $('#editDuration').val(projectDuration);

            $('#editProjectModal').modal('show');
        }

        function manageMembers(projectId) {
            $('#manageMembersModal').modal('show');
            $('#project_id_for_members').val(projectId); // Set hidden input value to project ID

            // AJAX request to get project members
            $.get('get_project_members.php', { project_id: projectId }, function(response) {
                $('#membersList').html(response); // Update the modal's content
            }).fail(function() {
                alert("Error loading members.");
            });
        }

        function removeMember(userId, projectId) {
            $.ajax({
                url: 'remove_project_member.php',
                type: 'POST',
                data: {
                    user_id: userId,
                    project_id: projectId
                },
                success: function(response) {
                    console.log(response);
                    alert(response); // Show the response message
                    location.reload(); // Reload the page
                },
                error: function() {
                    alert('Failed to remove member. Please try again.');
                }
            });
        }




        function saveProjectChanges(event) {
            event.preventDefault(); // This will stop the form from submitting
            var projectId = $('#editProjectId').val();
            var projectStatus = $('#editStatus').val();

                    // Send an AJAX POST request to update the project status
            $.post('update_project.php', { project_id: projectId, status: projectStatus }, function(response) {
                // Here you can handle the response. For simplicity, let's just alert the response and reload the page
                alert(response);
                location.reload(); // Reload the page to reflect changes
            }).fail(function() {
                alert("Error: Could not contact server.");
            });
        }

        $(document).ready(function() {

           $('#addProjectForm').submit(function(event) {
                event.preventDefault(); // Stop form from causing a page reload
                var formData = $(this).serialize(); // Serialize the form data

                $.ajax({
                    type: "POST",
                    url: "add_project.php",
                    data: formData,
                    success: function(response) {
                        console.log(response); // Log response for debugging
                        alert("Project added successfully!");
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText); // Log error response for debugging
                        alert("Error adding project.");
                    }
                });

            });


            $('#addMemberForm').submit(function(event) {
                event.preventDefault(); // Prevent the form from causing a page reload
                var formData = $(this).serialize(); // Serialize the form data

                $.ajax({
                    type: "POST",
                    url: "add_project_member.php",
                    data: formData,
                    success: function(response) {
                        alert(response);
                        // Optionally, you can update the membersList here without reloading the page
                        location.reload(); // Reload the page to see the changes
                    },
                    error: function() {
                        alert("Error adding member.");
                    }
                });
            });



            $('#editProjectForm').submit(function(event) {
                event.preventDefault(); // Prevent the form from causing a page reload
                var formData = $(this).serialize(); // Serialize the form data

                $.ajax({
                    type: "POST",
                    url: "update_project.php", // Ensure this script correctly processes updates
                    data: formData,
                    success: function(response) {
                        alert("Project updated successfully!");
                        $('#editProjectModal').modal('hide'); // Close the modal
                        location.reload(); // Optionally reload the page or update the UI differently
                    },
                    error: function() {
                        alert("Error updating project.");
                    }
                });
            });

        });


    </script>
</head>
<body>
<?php include 'header.php'; ?>
<main>
    <h1 class="slideInDown animated">Manage Projects</h1>

    <section class="container py-lg-5">
        <div class="alert_dev"></div>
        <!-- Admins -->
        <div class="row">
            <div class="col-lg-12 mt-4">
                <div class="panel">
                    <div class="panel-heading" style="font-size: 19px;">Projects:
                        <!-- Button to open admin registration modal -->
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#admin-registration-modal" style="font-size: 18px; float: right; margin-top: -6px;">New Project</button>
                    </div>
                    <div class="panel-body">
                        <div id="admins">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Start Date</th>
                                        <th>Duration(in months)</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    include 'connectDB.php';

                                    $sql = "SELECT project_id, project_name, start_date, status, duration FROM projects ORDER BY project_id DESC";
                                    $result = $conn->query($sql);

                                    if ($result->num_rows > 0) {
                                        while($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                                echo "<td>" . $row['project_name'] . "</td>";
                                                echo "<td>" . $row['start_date'] . "</td>";
                                                echo "<td>" . $row['duration'] . "</td>";
                                                echo "<td>" . $row['status'] . "</td>";
                                                echo "<td>";
                                                echo "<button class='btn btn-primary' onclick='editProject(" . $row['project_id'] . ", \"" . $row['project_name'] . "\", \"" . $row['status'] . "\", " . $row['duration'] . ")'>Edit</button> ";
                                                echo "<button class='btn btn-info' onclick='manageMembers(" . $row['project_id'] . ")'>Members</button> ";
                                                echo "<button class='btn btn-danger' onclick='deleteProjectAjax(" . $row['project_id'] . ")'>Delete</button>";

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
        </div>
    </section>
</main>

<!-- Project Addition Modal -->
<div class="modal fade" id="admin-registration-modal" tabindex="-1" role="dialog" aria-labelledby="Project Addition" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLongTitle">Add Project</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Project addition form starts here -->
                <form action="add_project.php" id="addProjectForm" method="post">
                    <label for="project_name">Project Name:</label>
                    <input type="text" id="project_name" name="project_name" required><br>

                    <label for="project_members">Project Members:</label>
                    <div id="project_members" class="form-control" style="height: auto; overflow: auto;">
                        <?php
                        // PHP code to fetch all users
                        include 'connectDB.php';
                        $sql = "SELECT id, username FROM users";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($user = $result->fetch_assoc()) {
                                echo "<div class='checkbox'>";
                                echo "<label>";
                                echo "<input type='checkbox' name='user_id[]' value='" . $user['id'] . "'>" . $user['username'];
                                echo "</label>";
                                echo "</div>";
                            }
                        } else {
                            echo "No users found.";
                        }

                        $conn->close();
                        ?>
                    </div>

                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" required><br>

                    <label for="duration">Duration (in Months):</label>
                    <input type="number" id="duration" name="duration" required><br>

                    
                    <!-- Retrieve department options from the database -->
                    <label for="status">Status:</label>
                    <select name="status" id="status">
                        <option value="pending">Pending</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                    <br>
                    

                    <!-- Add more options as needed -->
                    <input type="submit" value="Add">
                </form>
                <!-- Admin registration form ends here -->
            </div>
        </div>
    </div>
</div> 

<!-- Edit Project Modal -->
<div class="modal fade" id="editProjectModal" tabindex="-1" role="dialog" aria-labelledby="editProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLongTitle">Edit Project</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editProjectForm">
                    <input type="hidden" id="editProjectId" name="project_id">
                    <div class="form-group">
                        <label for="editProjectName">Project Name:</label>
                        <input type="text" id="editProjectName" name="project_name" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="editDuration">Duration (in Months):</label>
                        <input type="number" id="editDuration" name="duration" class="form-control" required>
                    </div>
                    <div class="form-group">
                    <label for="editStatus">Status:</label>
                        <select class="form-control" id="editStatus" name="status">
                            <option value="pending">Pending</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Manage Project Members Modal -->
<div class="modal fade" id="manageMembersModal" tabindex="-1" role="dialog" aria-labelledby="manageMembersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Manage Members</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul id="membersList"></ul>
                <form id="addMemberForm">
                    <input type="hidden" id="project_id_for_members" name="project_id">
                    <!-- Assume dropdown populated with user options -->
                    <label for="user-select">New Member:</label>
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
                    <button type="submit">Add Member</button>
                </form>
            </div>
        </div>
    </div>
</div>



