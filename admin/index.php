<?php
session_start();
include_once '../includes/session_check.php';
include '../includes/db_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
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
// Get the admin's name from the session
$name = $_SESSION['name'];

// Count total teachers
$teachers_query = $conn->query("SELECT COUNT(*) AS total_teachers FROM teachers");
$total_teachers = $teachers_query->fetch_assoc()['total_teachers'];

// Count total students
$students_query = $conn->query("SELECT COUNT(*) AS total_students FROM students");
$total_students = $students_query->fetch_assoc()['total_students'];

// Count total Classes
$classes_count_query = $conn->query("SELECT COUNT(*) AS total_classes FROM classes");
$total_classes = $classes_count_query->fetch_assoc()["total_classes"];

// Count total subjects 
$subjects_count_query = $conn->query("SELECT COUNT(*) AS total_subjects FROM subjects");
$total_subjects = $subjects_count_query->fetch_assoc()["total_subjects"];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dream School | Dashboard</title>

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
                <li><a href="manage_classes.php"><i class="bi bi-building"></i><span class="menu-text">Manage Classes</span></a></li>
                <li><a href="manage_subjects.php"><i class="bi bi-journal-bookmark"></i><span class="menu-text">Manage Subjects</span></a></li>
                <li><a href="manage_teachers.php"><i class="bi bi-person-badge"></i><span class="menu-text">View Teachers</span></a></li>
                <li><a href="manage_students.php"><i class="bi bi-people"></i><span class="menu-text">View Students</span></a></li>
                <li><a href="manage_admins.php"><i class="bi bi-person-gear"></i><span class="menu-text">View Admins</span></a></li>
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
            <h1>Welcome, <?= $name; ?> (Admin)</h1>

            <div class="card-container">
                <div class="card">
                    <h3>Total Teachers: <br><br>
                        <?= $total_teachers; ?>
                    </h3>
                </div>
                <div class="card">
                    <h3>Total Students: <br><br>
                        <?= $total_students; ?></h3>
                </div>
                <div class="card">
                    <h3>Total Classes: <br><br>
                        <?= $total_classes; ?></h3>
                </div>
                <div class="card">
                    <h3>Total Subjects: <br><br>
                        <?= $total_subjects; ?></h3>
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