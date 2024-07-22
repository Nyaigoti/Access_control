<?php
session_start();

// Check if the user is logged in as an admin
if (!isset($_SESSION['Admin-name'])) {
    header("location: login.php");
    exit;
}

include 'connectDB.php';


// Handle fetch request
$userData = '';
if (isset($_POST['fetch_user']) && isset($_POST['serialnumber'])) {
    $serialnumber = $conn->real_escape_string($_POST['serialnumber']);

    // Using prepared statement for security
    $stmt = $conn->prepare("SELECT * FROM users WHERE serialnumber = ?");
    $stmt->bind_param("s", $serialnumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userData .= "serialnumber: " . htmlspecialchars($row["serialnumber"]) . "<br>";
        $userData .= "Username: " . htmlspecialchars($row["username"]) . "<br>";
        $userData .= "Email: " . htmlspecialchars($row["email"]) . "<br>";
        $userData .= "Gender: " . htmlspecialchars($row["gender"]) . "<br>";
    } else {
        $userData .= "User not found";
    }
    $stmt->close();
}

$updateMessage = ''; // To hold the message after update operation
if (isset($_POST['assign_fingerprint'])) {
    $serialnumber = $conn->real_escape_string($_POST['serialnumber']);
    $fingerprint_id = $conn->real_escape_string($_POST['fingerprint_id']);

    // Update the user with the new fingerprint ID using a prepared statement
    $stmt = $conn->prepare("UPDATE users SET fingerprint_id = ? WHERE serialnumber = ?");
    $stmt->bind_param("ss", $fingerprint_id, $serialnumber);
    if ($stmt->execute()) {
        $updateMessage = "Fingerprint ID successfully assigned!";
    } else {
        $updateMessage = "Failed to assign Fingerprint ID.";
    }
    $stmt->close();
}
// Re-fetch user data after update to display updated data
if (isset($_POST['fetch_user']) || isset($_POST['assign_fingerprint'])) {
    // Your existing user fetching logic here
    // Ensure you're also fetching the fingerprint_id to display in the form
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" type="text/css" href="css/manageusers.css">
    
</head>
<body>
    <?php include'header.php'; ?>

    <main>
    <div class="container">
    <!-- Upload section -->
    <div class="row">
        <div class="col-md-12 mt-4">
            <div class="card">
                <div class="card-header">
                    <h2>Upload User Data</h2>
                </div>
                <div class="card-body">
                    <form action="manage_users_conf.php" method="POST" enctype="multipart/form-data">
                        <input type="file" name="import_file" class="form-control" />
                        <button type="submit" name="save_excel_data" class="btn btn-primary mt-3">Import</button>
                    </form>
                </div>
                <?php 
                 if(isset($_SESSION['message'])) {
                    echo '<p>'.$_SESSION['message'].'</p>'; // Display the message
                    unset($_SESSION['message']); // Clear the message from the session
                }
                 ?>
            </div>
        </div>
    </div>
    <!-- Enhanced form for fetching and updating user data -->
    <div class="col-md-12 mt-4">
        <h2>Assign Fingerprint ID</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="serialnumber">Enter Serial Number:</label>
                <input type="text" id="serialnumber" name="serialnumber" value="<?php echo isset($row['serialnumber']) ? $row['serialnumber'] : ''; ?>" required>
            </div>
            <button type="submit" name="fetch_user">Load Data</button>
            
            <div class="form-row">
                <div class="form-column">
                    <label for="username">Full Name:</label>
                    <input type="text" id="username" name="username" value="<?php echo isset($row['username']) ? $row['username'] : ''; ?>" disabled>
                </div>
                <div class="form-column">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($row['email']) ? $row['email'] : ''; ?>" disabled>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-column">
                    <label for="gender">Gender:</label>
                    <input type="text" id="gender" name="gender" value="<?php echo isset($row['gender']) ? $row['gender'] : ''; ?>" disabled>
                </div>
                <div class="form-column">
                    <label for="user_dept">Department:</label>
                    <input type="text" id="user_dept" name="user_dept" value="<?php echo isset($row['user_dept']) ? $row['user_dept'] : ''; ?>" disabled>
                </div>
            </div>
            
            <div class="form-group">
                <label for="fingerprint_id">Assign Fingerprint ID:</label>
                <input type="text" id="fingerprint_id" name="fingerprint_id" value="<?php echo isset($row['fingerprint_id']) ? $row['fingerprint_id'] : ''; ?>">
            </div>
            
            <button type="submit" name="assign_fingerprint">Submit</button>
        </form>
        <?php if (!empty($updateMessage)) echo "<p>$updateMessage</p>"; ?>
    </div>
</div>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        padding: 20px;
    }
    .container {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        max-width: 900px;
        margin: 0 auto;
    }
    h2 {
        color: #333;
    }
    form {
        display: flex;
        flex-direction: column;
    }
    label {
        margin-top: 10px;
    }
    input[type="text"], input[type="email"] {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box; /* Ensures padding doesn't affect overall width */
    }
    button {
        cursor: pointer;
        padding: 10px 20px;
        margin-top: 20px;
        width: 150px;
        border: none;
        border-radius: 4px;
        background-color: #388994;
        color: white;
        transition: background-color 0.3s;
    }
    button:hover {
        background-color: #0056b3;
    }
    p {
        color: green;
        margin-top: 15px;
    }
    .form-row {
        display: flex;
        justify-content: space-between;
        margin-top: 10px;
    }
    .form-column {
        flex: 1;
        margin-right: 20px;
    }
    .form-column:last-child {
        margin-right: 0;
    }
</style>

    </main>
</body>
</html>


