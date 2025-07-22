<?php
session_start();
include_once '../includes/session_check.php';
include '../includes/db_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Student') {
    header("Location: ../php/login.php");
    exit();
}

$student_id = $_SESSION['id'];
$name = $_SESSION['name'];
// Fetch user info
$stmt = $conn->prepare("SELECT u.user_profile_pic, u.username, u.user_email, s.student_id, s.class_id, c.class_name
                        FROM users u
                        JOIN students s ON u.user_id = s.user_id
                        JOIN classes c ON s.class_id = c.class_id
                        WHERE u.user_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$current_picture = $user['user_profile_pic'] ?? 'default.png';
$username = $user['username'] ?? '';
$user_email = $user['user_email'] ?? '';
$class_name = $user['class_name'] ?? '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = trim($_POST['username']);
    $new_email = trim($_POST['user_email']);
    // Handle profile picture upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_pic']['tmp_name'];
        $fileName = $_FILES['profile_pic']['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExt, $allowed)) {
            $newFileName = uniqid('profile_', true) . '.' . $fileExt;
            $destPath = '../uploads/profile_pictures/' . $newFileName;
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Update profile picture in DB
                $stmt = $conn->prepare("UPDATE users SET user_profile_pic = ? WHERE user_id = ?");
                $stmt->bind_param("si", $newFileName, $student_id);
                $stmt->execute();
                header("Location: view_profile.php");
                exit();
            }
        }
    }
    // Update name and email
    $stmt = $conn->prepare("UPDATE users SET username = ?, user_email = ? WHERE user_id = ?");
    $stmt->bind_param("ssi", $new_name, $new_email, $student_id);
    $stmt->execute();
    header("Location: view_profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dream School | Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .profile-card {
            max-width: 400px;
            margin: 40px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(52, 152, 219, 0.15);
            padding: 32px 24px;
        }

        .profile-card img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 2px solid #3498db;
            margin-bottom: 16px;
        }

        .profile-card label {
            font-weight: 500;
            margin-bottom: 4px;
        }

        .profile-card input,
        .profile-card select {
            width: 100%;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #3498db;
            margin-bottom: 16px;
            font-size: 15px;
        }

        .profile-card .btn {
            width: 100%;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
        }

        .profile-card .edit-btn {
            background: #ffd700;
            color: #333;
            margin-bottom: 10px;
        }

        .profile-card .save-btn {
            background: #3498db;
            color: #fff;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
        <div class="sidebar-content">
            <div class="profile-picture-container" style="text-align: center; margin-bottom: 15px;">
                <img src="../uploads/profile_pictures/<?= htmlspecialchars($current_picture); ?>"
                    alt="Profile Picture"
                    style="width: 50px; height: 50px; border-radius: 50%; border: 2px solid #ccc;">
                <p style="margin-top: 1px; font-weight: bold;"><?= htmlspecialchars($username); ?></p>
            </div>
            <ul class="menu-list">
                <li><a href="index.php"><i class="bi bi-house-door"></i><span class="menu-text">Dashboard</span></a></li>
                <li><a href="enroll_subject.php"><i class="bi bi-journal-plus"></i><span class="menu-text">Enrollment</span></a></li>
                <li><a href="view_announcements.php"><i class="bi bi-megaphone"></i><span class="menu-text">View Announcements</span></a></li>
                <li><a href="view_profile.php" class="active"><i class="bi bi-person-circle"></i><span class="menu-text">View Profile</span></a></li>
                <!-- <li><a href="../php/logout.php"><i class="bi bi-box-arrow-right"></i><span class="menu-text">Logout</span></a></li> -->
            </ul>
        </div>
    </div>
    <!-- Main area -->
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
                    <a href="edit_profile.php" style="display:block; color:#3498db; text-decoration:none; font-size:15px;">
                        <i class="bi bi-pencil-square"></i> Edit Info
                    </a>
                </div>
                <a href="../php/logout.php"><i class="bi bi-box-arrow-right"></i></a>
            </div>
        </div>

        <!-- Profile Card -->
        <div class="main-content">
            <div class="profile-card">
                <form id="profileForm" method="POST" enctype="multipart/form-data">
                    <div style="text-align:center;">
                        <img src="../uploads/profile_pictures/<?= $current_picture; ?>" alt="Profile Picture">
                        <br>
                        <label for="profile_pic" style="cursor:pointer; color:#3498db;">
                            <i class="bi bi-camera"></i> Change Picture
                        </label>
                        <input type="file" name="profile_pic" id="profile_pic" style="display:none;">
                    </div>
                    <label for="username">Name</label>
                    <input type="text" name="username" id="username" value="<?= $username; ?>" disabled>
                    <label for="user_email">Email</label>
                    <input type="email" name="user_email" id="user_email" value="<?= $user_email; ?>" disabled>
                    <label for="class_name">Class</label>
                    <input type="text" name="class_name" id="class_name" value="<?= $class_name; ?>" disabled>
                    <button type="button" class="btn edit-btn" id="editBtn"><i class="bi bi-pencil-square"></i> Edit</button>
                    <button type="submit" class="btn save-btn" id="saveBtn" style="display:none;"><i class="bi bi-save"></i> Save Changes</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
        }
        // Enable editing
        document.getElementById('editBtn').onclick = function() {
            document.getElementById('username').disabled = false;
            document.getElementById('user_email').disabled = false;
            document.getElementById('saveBtn').style.display = 'block';
            this.style.display = 'none';
        };
        // Show file input when camera label is clicked
        document.querySelector('label[for="profile_pic"]').onclick = function() {
            document.getElementById('profile_pic').click();
        };
    </script>
    <script src="../assets/js/main.js"></script>

</body>

</html>