<?php
session_start();
require_once "connectDB.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_name = mysqli_real_escape_string($conn, $_POST['admin_name']);
    $admin_email = mysqli_real_escape_string($conn, $_POST['admin_email']);
    $admin_password = mysqli_real_escape_string($conn, $_POST['admin_password']);
    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
    $admin_dept = mysqli_real_escape_string($conn, $_POST['admin_dept']);

    $sql_check_email = "SELECT * FROM admins WHERE admin_email = ?";
    $stmt = mysqli_prepare($conn, $sql_check_email);
    mysqli_stmt_bind_param($stmt, 's', $admin_email);
    mysqli_stmt_execute($stmt);
    $result_check_email = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result_check_email) > 0) {
        //echo '<div class="alert alert-danger">This email already exists!!</div>';
        header("location: admins.php?error=email_error");
        exit();
    } else {
        $sql_insert_admin = "INSERT INTO admins (admin_name, admin_email, admin_password, admin_dept) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql_insert_admin);
        mysqli_stmt_bind_param($stmt, 'ssss', $admin_name, $admin_email, $hashed_password, $admin_dept );
        if (mysqli_stmt_execute($stmt)) {
            header("location: admins.php?success=registered");
            exit();
        } else {
            header("location: admins.php?error=sql_error");
            exit();
        }
    }
} else {
    header("location: admin_registration.php");
    exit();
}
?>
