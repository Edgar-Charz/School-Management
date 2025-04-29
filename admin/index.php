<?php
session_start();
include '../includes/db_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    header("Location: ../php/login.php");
    exit();
}

// Count total teachers
$teachers_query = $conn->query("SELECT COUNT(*) AS total_teachers FROM teachers");
$total_teachers = $teachers_query->fetch_assoc()['total_teachers'];

// Count total students
$students_query = $conn->query("SELECT COUNT(*) AS total_students FROM students");
$total_students = $students_query->fetch_assoc()['total_students'];

// Count total Classes
$classes_count_query = $conn->query("SELECT COUNT(*) AS total_classes FROM classes");
$total_classes = $classes_count_query->fetch_assoc()["total_classes"];

// COunt total subjects 
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
        <link rel="stylesheet" href="../assets/css/styles.css">
    </head>

<body>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
        <div class="sidebar-content">
            <ul class="menu-list">
                <li><a href="index.php"><i class="bi bi-house-door"></i><span class="menu-text">Dashboard</span></a></li>
                <li><a href="manage_classes.php"><i class="bi bi-person"></i><span class="menu-text">Manage Classes</span></a></li>
                <li><a href="manage_subjects.php"><i class="bi bi-gear"></i><span class="menu-text">Manage Subjects</span></a></li>
                <li><a href="manage_teachers.php"><i class="bi bi-gear"></i><span class="menu-text">View Teachers</span></a></li>
                <li><a href="manage_students.php"><i class="bi bi-gear"></i><span class="menu-text">View Students</span></a></li>
                <li><a href="manage_admins.php"><i class="bi bi-gear"></i><span class="menu-text">View Admins</span></a></li>
                <li><a href="../php/logout.php"><i class="bi bi-box-arrow-right"></i><span class="menu-text">Logout</span></a></li>
            </ul>
        </div>
    </div>

    <!-- Main area (Top bar + Content) -->
    <div class="main-area" id="main-area">

        <!-- Top Navbar -->
        <div class="navbar">
            <div class="navbar-left">
                <!-- <button class="toggle-btn" onclick="toggleSidebar()">☰</button> -->
                <h2>Dream School</h2>
            </div>
            <div class="nav-links">
                <a href="logout.php"><i class="bi bi-box-arrow-right"></i></a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h1>Welcome, <?= $_SESSION['name']; ?> (Admin)</h1>

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