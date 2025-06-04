<?php
session_start();
include_once '../includes/session_check.php';
include '../includes/db_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    header("Location: ../php/login.php");
    exit();
}

// Handle subject submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject_name = trim($_POST['subject_name']);
    $subject_description = trim($_POST['description']);

    // Check if subject already exists
    $check = $conn->prepare("SELECT * FROM subjects WHERE subject_name = ?");
    $check->bind_param("s", $subject_name);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
        document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'warning',
                    // title: 'Oops...',
                    text: 'Subject already exists!'
                });
        });
              </script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO subjects (subject_name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $subject_name, $subject_description);

        if ($stmt->execute()) {
            echo "<script>
            document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        icon: 'success',
                        // title: 'Success',
                        text: 'Subject added successfully!'
                    });
            });
                  </script>";
        } else {
            echo "<script>
            document.addEventListener('DOMContentLoaded', function () {        
                    Swal.fire({
                        icon: 'error',
                        // title: 'Error',
                        text: 'Failed to add subject!'
                    });
            });
                  </script>";
        }
    }
}

// Fetch all subjects
$subjects = $conn->query("SELECT * FROM subjects ORDER BY subject_id ASC");

$subjects_count_query = "SELECT COUNT(*) AS total_subjects FROM subjects";
$subjects_count_query_result = $conn->query("$subjects_count_query");
$total_subjects = $subjects_count_query_result->fetch_assoc()['total_subjects'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dream School | Manage Subjects</title>
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .swal2-popup {
            font-size: 13px !important;
            width: 300px !important;
            background-color: rgba(255, 255, 255, 0.9) !important;
        }

        h3 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }

        /* Modal styles */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            /* Black background with transparency */
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 300px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #333;
        }

        input[type="text"] {
            width: 90%;
            padding: 8px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .btn {
            width: 100%;
            padding: 10px;
            background: rgb(76, 78, 78);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }
    </style>
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
                <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i><span class="menu-text">Logout</span></a></li>
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
                <a href="logout.php"><i class="bi bi-box-arrow-right"></i></a>
            </div>
        </div>

        <div class="main-content">
            <br>
            <div class="card-container">
                <div class="card">
                    <h3>Total Subjects: <br><br>
                        <?= $total_subjects; ?>
                    </h3>
                </div>
            </div>

            <br>

            <button id="openModalBtn" style="margin-left: 5px; padding: 8px 5px; background-color: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer;">Add Subject</button>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Subject Name</th>
                    <th>Description</th>
                    <th>Created</th>
                </tr>
                <?php while ($subject = $subjects->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $subject['subject_id']; ?></td>
                        <td><?= $subject['subject_name']; ?></td>
                        <td><?= $subject['description']; ?></td>
                        <td><?= $subject['created_at']; ?></td>
                    </tr>
                <?php } ?>
            </table>

            <!-- Modal -->
            <div id="myModal" class="modal">
                <div class="modal-content">
                    <span id="closeModalBtn" class="close">&times;</span>
                    <h4>Add New Subject</h4>
                    <form action="" method="POST">
                        <input type="text" id="subject_name" name="subject_name" placeholder="Subject Name" required>
                        <input type="text" id="description" name="description" placeholder="Subject Description" required>
                        <button type="submit" style="margin-top: 10px;" class="btn">Add Subject</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
        }

        // Get elements
        var modal = document.getElementById("myModal");
        var openBtn = document.getElementById("openModalBtn");
        var closeBtn = document.getElementById("closeModalBtn");

        // Open modal
        openBtn.onclick = function() {
            modal.style.display = "block";
        }

        // Close modal
        closeBtn.onclick = function() {
            modal.style.display = "none";
        }

        // Close modal if user clicks outside the modal
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>

</html>