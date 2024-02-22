<?php 

if (isset($_POST['login'])) {

    require 'connectDB.php';

    $Usermail = $_POST['email']; 
    $Userpass = $_POST['pwd']; 

    if (empty($Usermail) || empty($Userpass) ) {
        header("location: login.php?error=emptyfields");
        exit();
    }
    else if (!filter_var($Usermail,FILTER_VALIDATE_EMAIL)) {
        header("location: login.php?error=invalidEmail");
        exit();
    }
    else {
        $sql_admin = "SELECT * FROM admin WHERE admin_email=?";
        $result_admin = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($result_admin, $sql_admin)) {
            header("location: login.php?error=sqlerror");
            exit();
        }
        else {
            mysqli_stmt_bind_param($result_admin, "s", $Usermail);
            mysqli_stmt_execute($result_admin);
            $result_admin = mysqli_stmt_get_result($result_admin);

            if ($row_admin = mysqli_fetch_assoc($result_admin)) {
                // Check password for admin table
                $pwdCheck_admin = password_verify($Userpass, $row_admin['admin_pwd']);
                if ($pwdCheck_admin == true) {
                    session_start();
                    $_SESSION['Admin-name'] = $row_admin['admin_name'];
                    $_SESSION['Admin-email'] = $row_admin['admin_email'];
                    header("location: index.php?login=success");
                    exit();
                }
            }

            // If not found in admin table, check admins table
            $sql_admins = "SELECT * FROM admins WHERE admin_email=?";
            $result_admins = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($result_admins, $sql_admins)) {
                header("location: login.php?error=sqlerror");
                exit();
            }
            else {
                mysqli_stmt_bind_param($result_admins, "s", $Usermail);
                mysqli_stmt_execute($result_admins);
                $result_admins = mysqli_stmt_get_result($result_admins);

                if ($row_admins = mysqli_fetch_assoc($result_admins)) {
                    // Check password for admins table
                    $pwdCheck_admins = password_verify($Userpass, $row_admins['admin_password']);
                    if ($pwdCheck_admins == true) {
                        session_start();
                        $_SESSION['Admin-name'] = $row_admins['admin_name'];
                        $_SESSION['Admin-email'] = $row_admins['admin_email'];
                        header("location: index2.php?login=success");
                        exit();
                    }
                }
            }

            header("location: login.php?error=wrongpassword");
            exit();
        }
    }
    mysqli_stmt_close($result_admin);    
    mysqli_close($conn);
}
else {
    header("location: login.php");
    exit();
}
?>
