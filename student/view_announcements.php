<?php
session_start();
include '../includes/db_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Student') {
    header("Location: ../php/login.php");
    exit();
}

$name = $_SESSION['name'];
$student_id = $_SESSION['id'];

// Profile picture
$stmt = $conn->prepare("SELECT user_profile_pic FROM users WHERE user_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$current_picture = $result->fetch_assoc()['user_profile_pic'] ?? 'default.png';


// Retrieve announcements
$announcements_query = $conn->query("SELECT * FROM announcements");
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
                <img src="../uploads/profile_pictures/<?= $current_picture ?? 'default.png'; ?>"
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
            <h2>My Announcements</h2>
            <table>
                <tr>
                    <th>Title</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
                <?php while ($announce = $announcements_query->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $announce['title'] ?></td>
                        <td><?= $announce['message'] ?></td>
                        <td><?= $announce['created_at'] ?></td>
                        <td></td>
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