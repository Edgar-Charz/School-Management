<?php
session_start();
include '../includes/db_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    header("Location: ../php/login.php");
    exit();
}

$id = $_GET['id'];
$msg = "";

if (isset($_POST["updateBTN"])) {
    $name = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $update_student_stmt = $conn->prepare("UPDATE users SET username=?, user_email=?, user_password=? WHERE user_id=?");
    $update_student_stmt->bind_param("sssi", $name, $email, $password, $id);
    $update_student_stmt->execute();
    $msg = "Updated successfully!";
    
    header("Location: manage_students.php");
    exit();
}

$edit_student_stmt = $conn->query("SELECT * FROM users WHERE user_id = $id");
$student = $edit_student_stmt->fetch_assoc();
?>

<h2>Edit Teacher</h2>
<form method="POST">
    Name: <input type="text" name="username" value="<?= $student['username'] ?>" required><br>
    Email: <input type="email" name="email" value="<?= $student['user_email'] ?>" required><br>
    Phone: <input type="text" name="password" value="<?= $student['user_password'] ?>" required><br>
    <button type="submit" name="updateBTN">Update</button>
</form>
<p><?= $msg ?></p>
