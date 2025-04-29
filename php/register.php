<?php
session_start();
include '../includes/db_connection.php';

$msg = "";

// User registration
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $user_password = trim($_POST['password']);
    $hashed_password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role     = $_POST['role'];
    $class = $_POST['class'];

    // Check if email already exists
    $check = $conn->prepare("SELECT * FROM users WHERE user_email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $msg = "Email already registered!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, user_email, user_password, hashed_password, user_role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $user_password, $hashed_password, $role);

        if ($stmt->execute()) {
            $userId = $stmt->insert_id;

            // Add to student or teacher table
            if ($role == 'teacher') {
                $conn->query("INSERT INTO teachers (user_id) VALUES ($userId)");
            } elseif ($role == 'student') {
                // 
                $conn->query("INSERT INTO students (user_id, class_id) VALUES ($userId, $class)");
            }

            $msg = "User registered successfully!";
        } else {
            $msg = "Failed to register user.";
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Dream School | Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/form-styles.css">
    <style>
        #class_field {
            display: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Register </h2>
        <form method="POST" action="">
            <label>Name:</label>
            <input type="text" name="name" required><br><br>

            <label>Email:</label>
            <input type="email" name="email" required><br><br>

            <label>Password:</label>
            <input type="password" name="password" required><br><br>

            <label>Role:</label>
            <select name="role" id="role_select">
                <option value="">-- Select Role --</option>
                <option value="teacher">Teacher</option>
                <option value="student">Student</option>
            </select><br><br>

            <div id="class_field">
                <label>Class: </label>
                <select name="class" required>
                    <option value="">-- Select Class --</option>
                    <?php
                    require_once "../includes/db_connection.php";
                    $classes_query = $conn->query("SELECT class_id, class_name FROM classes");
                    if (mysqli_num_rows($classes_query) > 0) {
                        while ($row = mysqli_fetch_assoc($classes_query)) {
                            echo "<option value='" . $row['class_id'] . "'>" . $row['class_name'] . "</option>";
                        }
                    }
                    ?>
                </select><br><br>
            </div>

            <button type="submit" class="login-btn">Register</button>
            <?php if ($msg != "") {
                echo "<p style='color:green;'>$msg</p>";
            } ?>
            <p style="margin-top: 10px; text-align: center;">
                Have an account?<a href="login.php">Sign in</a>
            </p>
        </form>
    </div>
    <script>
        document.getElementById('role_select').addEventListener('change', function() {
            var class_field = document.getElementById('class_field');
            if (this.value === 'student') {
                class_field.style.display = 'block';
            } else {
                class_field.style.display = 'none';
            }
        });
    </script>
</body>

</html>