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
                <!-- Profile Dropdown -->
                <div class="profile-dropdown-container" style="position: relative; display: inline-block;">
                    <a href="#" class="profile-icon" onclick="toggleDropdown(event)">
                        <i class="bi bi-person-circle"></i>
                    </a>
                    <div class="dropdown-menu" id="profileDropdown" style="display: none; position: absolute; right: 0; background: #fff; border: 1px solid #ccc; border-radius: 5px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); z-index: 1000; min-width: 150px;">
                        <ul style="list-style: none; margin: 0; padding: 10px;">
                            <li style="margin-bottom: 10px;"><a href="view_profile.php" style="text-decoration: none; color: #333;">View Profile</a></li>
                            <li><a href="logout.php" style="text-decoration: none; color: #333;">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h1>Welcome, <?= $_SESSION['name']; ?> (Student)</h1>

        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
        }
    </script>
    <script>
        // Toggle the dropdown menu
        function toggleDropdown(event) {
            event.preventDefault();
            const dropdown = document.getElementById('profileDropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        // Close the dropdown if clicked outside
        window.addEventListener('click', function(e) {
            const dropdown = document.getElementById('profileDropdown');
            if (!e.target.closest('.profile-dropdown-container')) {
                dropdown.style.display = 'none';
            }
        });
    </script>
</body>

</html>