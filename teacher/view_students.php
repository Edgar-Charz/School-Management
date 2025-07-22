<?php
session_start();
include_once '../includes/session_check.php';
include '../includes/db_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Teacher') {
    header("Location: ../php/login.php");
    exit();
}
$name = $_SESSION['name'];
$user_id = $_SESSION['id'];

// Fetch all students
$students_stmt = $conn->prepare("
             SELECT students.student_id, users.user_id, users.username, users.user_email, users.created_at 
                FROM students, users
                WHERE users.user_id = students.user_id");
$students_stmt->execute();
$student_result = $students_stmt->get_result();

// Profile picture
$stmt = $conn->prepare("SELECT user_profile_pic FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$current_picture = $result->fetch_assoc()['user_profile_pic'] ?? '../uploads/profile_pictures/default.png';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dream School | Add Announcement</title>
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"> -->
    <link rel="stylesheet" href="../assets/css/styles.css">

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
                <li><a href="index.php"><i class="bi bi-house-door"></i> <span class="menu-text">Dashboard</span></a></li>
                <li><a href="add_announcement.php"><i class="bi bi-megaphone"></i> <span class="menu-text">Add Announcement</span></a></li>
                <li><a href="view_announcements.php"><i class="bi bi-card-list"></i> <span class="menu-text">View Announcements</span></a></li>
                <li><a href="view_students.php" class="active"><i class="bi bi-people"></i> <span class="menu-text">View Students</span></a></li>
                <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> <span class="menu-text">Logout</span></a></li>
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