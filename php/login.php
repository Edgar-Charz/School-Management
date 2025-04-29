<?php
session_start();
include '../includes/db_connection.php';

$msg = "";

if (isset($_POST["loginBTN"])) {
    $email = trim($_POST['user_email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE user_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();
        if (password_verify($password, $user['hashed_password'])) {
            $_SESSION['id'] = $user['user_id'];
            $_SESSION['name'] = $user['username'];
            $_SESSION['role'] = $user['user_role'];

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
            $msg = "Incorrect password!";
        }
    } else {
        $msg = "No account found with that email.";
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