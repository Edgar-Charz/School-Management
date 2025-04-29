<?php
session_start();
include '../includes/db_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    header("Location: ../login.php");
    exit();
}

$msg = "";

$admins_query = $conn->query("SELECT * FROM users WHERE user_role = 'Admin'");

$admins_count_query = "SELECT COUNT(*) AS total_admins FROM users WHERE user_role = 'Admin'";
$admins_count_query_result = $conn->query($admins_count_query);
$total_admins = $admins_count_query_result->fetch_assoc()['total_admins'];

?>

<!DOCTYPE html>
<html>

<head><meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dream School | Dashboard</title>
        <!-- Bootstrap Icons CDN -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
        <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
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
            width: 50%;
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

        .button {
            width: auto;
            padding: 7px;
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .button a {
            display: block;
            color: white;
            text-decoration: none;
            transition: background 0.3s, padding-left 0.3s;
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

        <!-- Main content -->
        <div class="main-content">
            <br>
            <div class="card-container">
                <div class="card">
                    <h3>Total Admins: <br><br>
                        <?= $total_admins; ?>
                    </h3>
                </div>
            </div>

            <?php if ($msg != "") echo "<p style='color:green;'>$msg</p>"; ?>
            <br>

            <h3>All Registered Admins</h3>
            <table>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                <?php while ($admin = $admins_query->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $admin['user_id'] ?></td>
                        <td><?= $admin['username']; ?></td>
                        <td><?= $admin['user_email']; ?></td>
                        <td><?= $admin['created_at']; ?></td>
                        <td>
                            <button style="background-color: red;" class="button"><a href="demote_admin.php?id=<?= $admin['user_id']; ?>" onclick="return confirm('Demote this admin?')">Demote</a></button> |
                            <button style="background-color: green;" class="button"><a href="delete_admin.php?id=<?= $admin['user_id']; ?>" onclick="return confirm('Delete this admin?')">Delete</a></button>
                        </td>
                    </tr>
                <?php } ?>
            </table>
            <!-- <p><a href="index.php">Back to Dashboard</a></p> -->
        </div>
        <script>
            function toggleSidebar() {
                document.getElementById('sidebar').classList.toggle('collapsed');
            }
        </script>

</body>

</html>