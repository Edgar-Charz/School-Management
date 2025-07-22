<?php
session_start();
include_once '../includes/session_check.php';
include '../includes/db_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    header("Location: ../php/login.php");
    exit();
}

$msg = "";
$name = $_SESSION['name'];
// Admins query
$admins_query = $conn->query("SELECT * FROM users WHERE user_role = 'Admin'");

// Count total admins
$admins_count_query = "SELECT COUNT(*) AS total_admins FROM users WHERE user_role = 'Admin'";
$admins_count_query_result = $conn->query($admins_count_query);
$total_admins = $admins_count_query_result->fetch_assoc()['total_admins'];

// Edit admin functionality
if (isset($_POST['edit_admin_btn'])) {
    $admin_id = $_POST['admin_id'];
    $admin_name = $_POST['admin_name'];
    $admin_email = $_POST['admin_email'];

    // Update admin details in the database
    $update_query = "UPDATE users SET username = ?, user_email = ? WHERE user_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssi", $admin_name, $admin_email, $admin_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows === 0) {
            $_SESSION['swal'] = [
                'icon' => 'info',
                'text' => 'No changes made.'
            ];
        } else {
            $_SESSION['swal'] = [
                'icon' => 'success',
                'text' => 'Admin details updated successfully.'
            ];
        }
    } else {
        $_SESSION['swal'] = [
            'icon' => 'error',
            'text' => 'Error updating admin details: ' . $conn->error
        ];
    }
    // Redirect to avoid resubmission
    header("Location: manage_admins.php");
    exit();
}

?>
<!-- Sweetalert -->
<?php if (isset($_SESSION['swal'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '<?= $_SESSION['swal']['icon'] ?>',
                text: '<?= $_SESSION['swal']['text'] ?>',
                confirmButtonText: 'OK',
                timer: 15000,
                timerProgressBar: true
            });
        });
    </script>
<?php unset($_SESSION['swal']);
endif; ?>


<!DOCTYPE html>
<html>

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

        input[type="text"],
        [type="email"] {
            width: auto;
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

        .buttons {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .buttons button {
            width: 48%;
            /* Adjust width to fit within the container with spacing */
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }
    </style>
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
                <li><a href="index.php"><i class="bi bi-house-door"></i><span class="menu-text">Dashboard</span></a></li>
                <li><a href="manage_classes.php"><i class="bi bi-building"></i><span class="menu-text">Manage Classes</span></a></li>
                <li><a href="manage_subjects.php"><i class="bi bi-journal-bookmark"></i><span class="menu-text">Manage Subjects</span></a></li>
                <li><a href="manage_teachers.php"><i class="bi bi-person-badge"></i><span class="menu-text">View Teachers</span></a></li>
                <li><a href="manage_students.php"><i class="bi bi-people"></i><span class="menu-text">View Students</span></a></li>
                <li><a href="manage_admins.php" class="active"><i class="bi bi-person-gear"></i><span class="menu-text">View Admins</span></a></li>
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
                            <button style="background-color: red;" class="button">
                                <a href="demote_admin.php?id=<?= $admin['user_id']; ?>" onclick="return confirm('Demote this admin?')">Demote</a>
                            </button> |
                            <button style="background-color: blue;" class="button edit-btn" data-id="<?= $admin['user_id']; ?>" data-name="<?= $admin['username']; ?>" data-email="<?= $admin['user_email']; ?>">Edit</button> |
                            <?php if ($_SESSION['id'] != $admin['user_id']) { ?>
                                <button style="background-color: green;" class="button">
                                    <a href="delete_admin.php?id=<?= $admin['user_id']; ?>" onclick="return confirm('Delete this admin?')">Delete</a>
                                </button>
                            <?php } else { ?>
                                <button style="background-color: #ccc; color: #666;" class="button" disabled>Delete</button>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>

            <!-- Edit Admin Modal -->
            <div id="editAdminModal" class="modal">
                <div class="modal-content">
                    <span id="closeEditModalBtn" class="close">&times;</span>
                    <h4>Edit Admin</h4>
                    <form id="editAdminForm" method="POST" action="">
                        <input type="hidden" id="edit_admin_id" name="admin_id">
                        <label>Name: </label>
                        <input type="text" id="edit_admin_name" name="admin_name" placeholder="Name" required><br>
                        <label>Email: </label>
                        <input type="email" id="edit_admin_email" name="admin_email" placeholder="Email" required>
                        <div class="buttons">
                            <button type="submit" style="margin-top: 10px;" class="" name="edit_admin_btn">Save Changes</button>
                            <button type="button" id="cancelEditBtn" style="margin-top: 10px; background-color: #ccc;" class="btn">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script>
            function toggleSidebar() {
                document.getElementById('sidebar').classList.toggle('collapsed');
            }
        </script>
        <script>
            // Get modal elements
            const editModal = document.getElementById("editAdminModal");
            const closeEditModalBtn = document.getElementById("closeEditModalBtn");
            const cancelEditBtn = document.getElementById("cancelEditBtn");
            const editAdminForm = document.getElementById("editAdminForm");

            // Open modal when "Edit" button is clicked
            document.querySelectorAll(".edit-btn").forEach(button => {
                button.addEventListener("click", function() {
                    const adminId = this.getAttribute("data-id");
                    const adminName = this.getAttribute("data-name");
                    const adminEmail = this.getAttribute("data-email");

                    // Populate the form with admin data
                    document.getElementById("edit_admin_id").value = adminId;
                    document.getElementById("edit_admin_name").value = adminName;
                    document.getElementById("edit_admin_email").value = adminEmail;

                    // Show the modal
                    editModal.style.display = "block";
                });
            });

            // Close modal when "X" button is clicked
            closeEditModalBtn.onclick = function() {
                editModal.style.display = "none";
            };

            // Close modal when "Cancel" button is clicked
            cancelEditBtn.onclick = function() {
                editModal.style.display = "none";
            };

            // Close modal when clicking outside the modal
            window.onclick = function(event) {
                if (event.target == editModal) {
                    editModal.style.display = "none";
                }
            };
        </script>
</body>

</html>