<?php
session_start();
include_once '../includes/session_check.php';
include '../includes/db_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    header("Location: ../php/login.php");
    exit();
}

// Handle add class
$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_name = trim($_POST['class_name']);

    // Check if class exists
    $check = $conn->prepare("SELECT * FROM classes WHERE class_name = ?");
    $check->bind_param("s", $class_name);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'warning',
                // title: 'Oops...',
                text: 'Class already exists.'
            });
    });
          </script>";
    } else {
        $insert = $conn->prepare("INSERT INTO classes (class_name) VALUES (?)");
        $insert->bind_param("s", $class_name);
        if ($insert->execute()) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    // title: 'Oops...',
                    text: 'Class added successfully.'
                });
        });
              </script>";
        } else {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    // title: 'Oops...',
                    text: 'Failed to add class.'
                });
        });
              </script>";
        }
    }
}

// Fetch all classes
$classes_stmt = $conn->prepare("SELECT class_id, class_name, created_at FROM classes ORDER BY class_name ASC");
$classes_stmt->execute();
$classes_stmt_result = $classes_stmt->get_result();

$classes_count_query = "SELECT COUNT(*) AS total_classes FROM classes";
$classes_count_query_result = $conn->query($classes_count_query);
$total_classes = $classes_count_query_result->fetch_assoc()['total_classes'];

?>

<!DOCTYPE html>
<html lang="en">

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
                <!-- <button class="toggle-btn" onclick="toggleSidebar()">☰</button> -->
                <h2>Dream School</h2>
            </div>
            <div class="nav-links">
                <a href="logout.php"><i class="bi bi-box-arrow-right"></i></a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <br>
            <div class="card-container">
                <div class="card">
                    <h3>Total Classes: <br><br>
                        <?= $total_classes; ?>
                    </h3>
                </div>
            </div>

            <?php if ($msg != "") echo "<p style='color:green;'>$msg</p>"; ?>
            <br>
            <!-- <h3 >All Classes</h3> -->
            <button id="openModalBtn" style="margin-left: 5px; padding: 8px 5px; background-color: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer;">Add Class</button>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Class Name</th>
                    <th>Created</th>
                    <th>Action</th>
                </tr>
                <?php while ($class = $classes_stmt_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $class['class_id']; ?></td>
                        <td><?= $class['class_name']; ?></td>
                        <td><?= $class['created_at']; ?></td>
                        <td></td>
                    </tr>
                <?php } ?>
            </table>

            <!-- Modal -->
            <div id="myModal" class="modal">
                <div class="modal-content">
                    <span id="closeModalBtn" class="close">&times;</span>
                    <h4>Add New Class</h4>
                    <form action="" method="POST">
                        <!-- <label for="class_name">Class Name:</label> -->
                        <input type="text" id="class_name" name="class_name" placeholder="Class Name" required>

                        <button type="submit" style="margin-top: 10px;" class="btn">Add Class</button>
                    </form>
                </div>
            </div>

            <!-- <p><a href="index.php">Back to Dashboard</a></p> -->

        </div>
    </div>


    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
        }
    </script>
    <script>
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