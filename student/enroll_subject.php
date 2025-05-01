<?php
session_start();
include '../includes/db_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Student') {
    header("Location: ../php/login.php");
    exit();
}

$name = $_SESSION['name'];
$user_id = $_SESSION['id'];
$msg = "";

// Fetch student_id from students table
$stmt = $conn->prepare("SELECT student_id FROM students WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if ($student) {
    $student_id = $student['student_id'];
} else {
    die("Student not found for the given user ID.");
}

// Profile picture
$picture_stmt = $conn->prepare("SELECT user_profile_pic FROM users WHERE user_id = ?");
$picture_stmt->bind_param("i", $user_id);
$picture_stmt->execute();
$picture_result = $picture_stmt->get_result();
$current_picture = $picture_result->fetch_assoc()['user_profile_pic'] ?? 'default.png';

// Enrollment student in a subject
if (isset($_POST['enrollBTN']) && isset($_POST['subject'])) {
    $subject_id = $_POST['subject'];

    // Check if the student is already enrolled
    $check_student_stmt = $conn->prepare("SELECT * FROM student_subjects WHERE student_id = ? AND subject_id = ?");
    $check_student_stmt->bind_param("ii", $student_id, $subject_id);
    $check_student_stmt->execute();
    $check_result = $check_student_stmt->get_result();

    if ($check_result->num_rows == 0) {
        // Enroll the student
        $enroll_student_stmt = $conn->prepare("INSERT INTO student_subjects (student_id, subject_id) VALUES (?, ?)");
        $enroll_student_stmt->bind_param("ii", $student_id, $subject_id);
        $enroll_student_stmt->execute();
    }
}

// Fetch all subjects
$subjects_query = $conn->query("SELECT * FROM subjects");
if (!$subjects_query) {
    die("Error fetching subjects: " . $conn->error);
}

// Fetch enrolled subjects
$enrolled_subjects = [];
$enrolled_subjects_stmt = $conn->prepare("SELECT subject_id FROM student_subjects WHERE student_id = ?");
$enrolled_subjects_stmt->bind_param("i", $student_id);
$enrolled_subjects_stmt->execute();
$result = $enrolled_subjects_stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $enrolled_subjects[] = $row['subject_id'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dream School | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .enroll-btn {
            padding: 7px;
            border: none;
            border-radius: 8px;
            color: #fff;
            background: rgb(15, 71, 130);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .enrolled-btn {
            padding: 7px;
            border: none;
            border-radius: 8px;
            color: #fff;
            background: rgb(19, 101, 53);
            font-size: 14px;
            font-weight: 600;
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <div class="sidebar" id="sidebar">
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
        <div class="sidebar-content">
            <div class="profile-picture-container" style="text-align: center; margin-bottom: 15px;">
                <img src="../uploads/profile_pictures/<?= htmlspecialchars($current_picture); ?>" alt="Profile Picture" style="width: 50px; height: 50px; border-radius: 50%; border: 2px solid #ccc;">
                <p style="margin-top: 1px; font-weight: bold;"><?= htmlspecialchars($name); ?></p>
            </div>
            <ul class="menu-list">
                <li><a href="index.php"><i class="bi bi-house-door"></i> Dashboard</a></li>
                <li><a href="enroll_subject.php"><i class="bi bi-house-door"></i> Enrollment</a></li>
                <li><a href="view_announcements.php"><i class="bi bi-person"></i> View Announcements</a></li>
                <li><a href="view_profile.php"><i class="bi bi-gear"></i> View Profile</a></li>
                <li><a href="../php/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </div>
    </div>

    <div class="main-area" id="main-area">
        <div class="navbar">
            <div class="navbar-left">
                <h2>Dream School</h2>
            </div>
        </div>

        <div class="main-content">
            <h2>Enroll in a Subject</h2>
            <table>
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($subject = $subjects_query->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $subject['subject_name']; ?></td>
                            <td><?= $subject['description']; ?></td>
                            <td>
                                <?php if (in_array($subject['subject_id'], $enrolled_subjects)) { ?>
                                    <button class="enrolled-btn" disabled>Enrolled</button>
                                <?php } else { ?>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to enroll in this subject?');">
                                        <input type="hidden" name="subject" value="<?= $subject['subject_id']; ?>">
                                        <button type="submit" class="enroll-btn" name="enrollBTN">Enroll</button>
                                    </form>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
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