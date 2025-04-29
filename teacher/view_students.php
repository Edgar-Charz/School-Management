<?php
session_start();
include '../includes/db_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Teacher') {
    header("Location: ../php/login.php");
    exit();
}

$students_stmt = $conn->prepare("
             SELECT students.student_id, users.user_id, users.username, users.user_email, users.created_at 
                FROM students, users
                WHERE users.user_id = students.user_id");
$students_stmt->execute();
$student_result = $students_stmt->get_result();

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
            <ul class="menu-list">
                <li><a href="index.php"><i class="bi bi-house-door"></i><span class="menu-text">Dashboard</span></a></li>
                <li><a href="add_announcement.php"><i class="bi bi-person"></i><span class="menu-text">Add Announcement</span></a></li>
                <li><a href="view_announcements.php"><i class="bi bi-person"></i><span class="menu-text">View Announcement</span></a></li>
                <li><a href="view_students.php"><i class="bi bi-gear"></i><span class="menu-text">View Students</span></a></li>
                <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i><span class="menu-text">Logout</span></a></li>
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
            <h2>Students List</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>

                <?php while ($student = $student_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $student['student_id']; ?></td>
                        <td><?= $student['username']; ?></td>
                        <td><?= $student['user_email']; ?></td>
                        <td><?= $student['created_at'] ?></td>
                        <td><button style="background-color: green;" class="button">
                                <a href="view_student_profile.php?id=<?= $student['student_id']; ?>">View Profile</a></button>
                        </td>
                    </tr>
                <?php } ?>
            </table>

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