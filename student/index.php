<?php
session_start();
include_once '../includes/session_check.php';
include '../includes/db_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Student') {
    header("Location: ../php/login.php");
    exit();
}

// Check if login was successful
if (isset($_SESSION['login_success']) && $_SESSION['login_success']) {
    unset($_SESSION['login_success']);

    echo "<script>
          document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                //   title: 'Hi !,  " . ucwords(strtolower($_SESSION['name'])) . "',
                  text: 'Welcome back !, " . ucwords(strtolower($_SESSION['name'])) . "',
                  icon: 'success',
                  confirmButtonText: 'OK',
                  timer: 15000, 
                  timerProgressBar: true 
              });
          });
      </script>";
}

$name = $_SESSION['name'];
$user_id = $_SESSION['id'];

// Select student id from students table
$select_student_stmt = $conn->prepare("SELECT student_id 
                                                FROM students 
                                                WHERE user_id = ?");
$select_student_stmt->bind_param("i", $user_id);
$select_student_stmt->execute();
$select_student_stmt_result = $select_student_stmt->get_result();
$student_id = $select_student_stmt_result->fetch_assoc()["student_id"] ?? "";

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
$enrolled_subjects_count_stmt->bind_param("i", $student_id);
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
        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link rel="stylesheet" href="../assets/css/styles.css">
        <style>
            .swal2-popup {
                font-size: 13px !important;
                width: 300px !important;
                background-color: rgba(255, 255, 255, 0.9) !important;
            }
        </style>
    </head>

<body>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
        <div class="sidebar-content">
            <!-- Profile Picture -->
            <div class="profile-picture-container">
                <img src="../uploads/profile_pictures/<?= $current_picture ?? '../uploads/profile_pictures/default.png'; ?>"
                    alt="Profile Picture">
                <p style="margin-top: 1px; font-weight: bold;"><?= $name; ?></p>
            </div>
            <ul class="menu-list">
                <li><a href="index.php" class="active"><i class="bi bi-house-door"></i><span class="menu-text">Dashboard</span></a></li>
                <li><a href="enroll_subject.php"><i class="bi bi-journal-plus"></i><span class="menu-text">Enrollment</span></a></li>
                <li><a href="view_announcements.php"><i class="bi bi-megaphone"></i><span class="menu-text">View Announcements</span></a></li>
                <!-- <li><a href="view_profile.php"><i class="bi bi-person-circle"></i><span class="menu-text">View Profile</span></a></li> -->
                <!-- <li><a href="../php/logout.php"><i class="bi bi-box-arrow-right"></i><span class="menu-text">Logout</span></a></li> -->
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
                <a href="javascript:void(0);" id="profileDropdownBtn">
                    <i class="bi bi-person-circle"></i>
                </a>
                <div id="profileDropdown" style="display:none; position:absolute; right:0; top:40px; background:#fff; border-radius:8px; box-shadow:0 2px 12px rgba(0,0,0,0.12); min-width:180px; z-index:1000; padding:16px; text-align:center;">
                    <img src="../uploads/profile_pictures/<?= $current_picture ?? '../uploads/profile_pictures/default.png'; ?>" alt="Profile Picture" style="width:48px; height:48px; border-radius:50%; border:2px solid #3498db; margin-bottom:8px;">
                    <div style="font-weight:bold;"><?= $name; ?></div>
                    <hr style="margin:10px 0;">
                    <a href="../php/change_profile_picture.php" style="display:block; color:#3498db; margin-bottom:8px; text-decoration:none; font-size:15px;">
                        <i class="bi bi-camera"></i> Change Picture
                    </a>
                    <a href="view_profile.php" style="display:block; color:#3498db; text-decoration:none; font-size:15px;">
                        <i class="bi bi-pencil-square"></i> Edit Info
                    </a>
                </div>
                <a href="../php/logout.php"><i class="bi bi-box-arrow-right"></i></a>
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
    <script src="../assets/js/main.js"></script>
</body>

</html>