<?php
session_start();
include '../includes/db_connection.php';

$msg = "";

if (isset($_GET['timeout']) && $_GET['timeout'] === "true") {
    echo "<script>
    alert('You have been logged out due to inactivity!!');
    </script>";
}

if (isset($_POST["loginBTN"])) {
    $email = trim($_POST['user_email']);
    $password = trim($_POST['password']);

    $login_stmt = $conn->prepare("SELECT * FROM users WHERE user_email = ?");
    $login_stmt->bind_param("s", $email);
    $login_stmt->execute();
    $result = $login_stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['hashed_password'])) {
            $_SESSION['id'] = $user['user_id'];
            $_SESSION['name'] = $user['username'];
            $_SESSION['role'] = $user['user_role'];
            $_SESSION['login_success'] = true;

            // Redirect based on role
            if ($user['user_role'] == 'Admin') {
                header("Location: ../admin/index.php");
            } elseif ($user['user_role'] == 'Teacher') {
                header("Location: ../teacher/index.php");
            } else {
                header("Location: ../student/index.php");
            }
            exit();
            
        } else {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'warning',
                    // title: 'Oops...',
                    text: 'Incorrect password!'
                });
        });
              </script>";
        }
    } else {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'warning',
                    // title: 'Oops...',
                    text: 'No account found with that email.!'
                });
        });
              </script>";
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login | Dream School</title>
    <link rel="stylesheet" href="../assets/css/form-styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .swal2-popup {
            font-size: 13px !important;
            width: 300px !important;
            background-color: rgba(255, 255, 255, 0.9) !important;
        }
    </style>
</head>

<body>

    <div class="container">
        <form method="POST" action="" class="login-form">
            <h2>Welcome Back</h2>

            <div class="input-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="user_email" required>
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" name="loginBTN" class="login-btn">Login</button>
            <p style="margin-top: 10px; text-align: center;">
                Don't have an account?<a href="register.php">Sign up</a>
            </p>
            <?php if (!empty($msg)) {
                echo "<p class='error-msg'>$msg</p>";
            } ?>
        </form>
    </div>

</body>

</html>