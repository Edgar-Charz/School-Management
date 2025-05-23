<?php
session_start();
include '../includes/db_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Student') {
    header("Location: ../php/login.php");
    exit();
}

$name = $_SESSION['name'];
$user_id = $_SESSION['id'];

// Profile picture
$stmt = $conn->prepare("SELECT user_profile_pic FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$current_picture = $result->fetch_assoc()['user_profile_pic'] ?? '../uploads/profile_pictures/default.png';

// Select student's class
$student_class_stmt = $conn->prepare("SELECT classes.class_name 
                        FROM students 
                        JOIN classes ON students.class_id = classes.class_id 
                        WHERE students.user_id = ?");
$student_class_stmt->bind_param("i", $user_id);
$student_class_stmt->execute();
$class_result = $student_class_stmt->get_result();
$class_name = $class_result->fetch_assoc()["class_name"] ?? "";

// Total enrolled subjects
$enrolled_subjects_count_stmt = $conn->prepare("SELECT COUNT(*) AS total_enrolled_subjects 
                        FROM student_subjects 
                        WHERE student_id = ?");
$enrolled_subjects_count_stmt->bind_param("i", $user_id);
$enrolled_subjects_count_stmt->execute();
$enrolled_subjects_count_result = $enrolled_subjects_count_stmt->get_result();
if ($enrolled_subjects_count = $enrolled_subjects_count_result->fetch_assoc()) {
    $total_enrolled_subjects = $enrolled_subjects_count['total_enrolled_subjects'];
} else {
    $total_enrolled_subjects = 0;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dream School | Dashboard</title>
        <!-- Bootstrap Icons CDN -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
        <link rel="stylesheet" href="../assets/css/styles.css">
    </head>

<body>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
        <div class="sidebar-content">
            <!-- Profile Picture -->
            <div class="profile-picture-container" style="text-align: center; margin-bottom: 15px;">
                <img src="../uploads/profile_pictures/<?= $current_picture ?? '../uploads/profile_pictures/default.png'; ?>"
                    alt="Profile Picture"
                    style="width: 50px; height: 50px; border-radius: 50%; border: 2px solid #ccc;">
                <p style="margin-top: 1px; font-weight: bold;"><?= $name; ?></p>
            </div>
            <ul class="menu-list">
                <li><a href="index.php"><i class="bi bi-house-door"></i><span class="menu-text">Dashboard</span></a></li>
                <li><a href="enroll_subject.php"><i class="bi bi-house-door"></i><span class="menu-text">Enrollment</span></a></li>
                <li><a href="view_announcements.php"><i class="bi bi-person"></i><span class="menu-text">View Announcements</span></a></li>
                <li><a href="view_profile.php"><i class="bi bi-gear"></i><span class="menu-text">View Profile</span></a></li>
                <li><a href="../php/logout.php"><i class="bi bi-box-arrow-right"></i><span class="menu-text">Logout</span></a></li>
            </ul>
        </div>
    </div>
    <!-- Main area (Top bar + Content) -->
    <div class="main-area" id="main-area">

        <!-- Top Navbar -->
        <div class="navbar">
            <div class="navbar-left">
                <h2>Dream School</h2>
            </div>
            <div class="nav-links">
                <a href="../php/change_profile_picture.php">
                    <i class="bi bi-person-circle"></i>
                </a>
                <a href="logout.php"><i class="bi bi-box-arrow-right"></i></a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h1>Welcome, <?= $name; ?> (Student)</h1>
            <div class="card-container">
                <div class="card">
                    <h3>Class: <br><br>
                        <?= $class_name; ?>
                    </h3>
                </div>
                <div class="card">
                    <h3>Total Subjects Enrolled: <br><br>
                        <?= $total_enrolled_subjects; ?></h3>
                </div>

            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
        }
    </script>

</body>

</html>