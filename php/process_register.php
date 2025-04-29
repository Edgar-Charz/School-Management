<?php
include '../includes/db_connection.php';

$msg = "";


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $user_password = trim($_POST['password']);
    $hashed_password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role     = $_POST['role'];

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
                $conn->query("INSERT INTO students (user_id) VALUES ($userId)");
            }

            $msg = "User registered successfully!";
        } else {
            $msg = "Failed to register user.";
        }
    }
}
?>