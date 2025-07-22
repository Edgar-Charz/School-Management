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

// Students query
$student_stmt = $conn->prepare("
              SELECT students.student_id, users.user_id, users.username, users.user_email, users.created_at 
                FROM students, users
                WHERE users.user_id = students.user_id");
$student_stmt->execute();
$student_result = $student_stmt->get_result();

// Students count
$student_count_query = "SELECT COUNT(*) AS total_students FROM students";
$student_count_query_result = $conn->query($student_count_query);
$total_students = $student_count_query_result->fetch_assoc()['total_students'];

// Add Student
if (isset($_POST["add_student_btn"])) {
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name = trim($_POST['last_name']);
    $username = $first_name . " " .  $last_name;
    $email = trim($_POST['user_email']);

    // Check if student already exists
    $check_query = "SELECT * FROM users WHERE username = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $_SESSION['swal'] = [
            'icon' => 'warning',
            'text' => 'Student already exists.'
        ];
    } else {
        // Insert new student
        $insert_query = "INSERT INTO users (username, user_email, role) VALUES (?, ?, 'Student')";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("ss", "$first_name $middle_name", $user_email);

        if ($insert_stmt->execute()) {
            // Get the last inserted user_id
            $user_id = $conn->insert_id;

            // Insert into students table
            $student_insert_query = "INSERT INTO students (user_id, student_id) VALUES (?, ?)";
            $student_insert_stmt = $conn->prepare($student_insert_query);
            $student_id = uniqid('STU'); // Generate unique student ID
            $student_insert_stmt->bind_param("is", $user_id, $student_id);

            if ($student_insert_stmt->execute()) {
                $_SESSION['swal'] = [
                    'icon' => 'success',
                    'text' => 'Student added successfully!'
                ];
            } else {
                $_SESSION['swal'] = [
                    'icon' => 'error',
                    'text' => 'Failed to add student! ' . $conn->error
                ];
            }
        } else {
            $_SESSION['swal'] = [
                'icon' => 'error',
                'text' => 'Failed to add user! ' . $conn->error
            ];
        }
    }
    // Redirect to avoid resubmission
    header("Location: manage_students.php");
    exit();
}

// Handle edit student form submission
if (isset($_POST['save_student_btn'])) {
    $user_id = $_POST['edit_user_id'];
    $student_id = $_POST['edit_student_id'];
    $username = $_POST['edit_username'];
    $user_email = $_POST['edit_user_email'];

    $stmt = $conn->prepare("UPDATE users SET username=?, user_email=? WHERE user_id=?");
    $stmt->bind_param("ssi", $username, $user_email, $user_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows === 0) {
            $_SESSION['swal'] = [
                'icon' => 'info',
                'text' => 'No changes made.'
            ];
        } else {
            $_SESSION['swal'] = [
                'icon' => 'success',
                'text' => 'Student details updated!'
            ];
        }
    } else {
        $_SESSION['swal'] = [
            'icon' => 'error',
            'text' => 'Failed to update student! ' . $conn->error
        ];
    }
    // Redirect to avoid resubmission
    header("Location: manage_students.php");
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
        .form-step {
            margin-top: 10px;
        }

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
            width: 500px;
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

        label {
            display: inline-block;
            min-width: 120px;
            /* or any width you prefer */
            margin-bottom: 6px;
            text-align: right;
            margin-right: 10px;
            /* space between label and input */
        }

        input[type="text"],
        [type="email"],
        [type="number"] {
            width: 50%;
            padding: 8px;
            margin-top: 10px;
            margin-left: 10px;
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

        .form-group {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 10px;
        }

        .form-group label {
            min-width: 90px;
            margin-bottom: 0;
            text-align: right;
        }

        .gender-row {
            display: flex;
            align-items: right;
            margin-bottom: 15px;
        }

        .gender-options-vertical {
            display: flex;
            flex-direction: column;
            gap: 10px;
            /* margin-left: 10px; */
        }

        .gender-option {
            display: flex;
            align-items: center;
        }

        label {
            display: block;
            font-size: 14px;
            color: #555;
            margin-bottom: 6px;
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
                <li><a href="manage_students.php" class="active"><i class="bi bi-people"></i><span class="menu-text">View Students</span></a></li>
                <li><a href="manage_admins.php"><i class="bi bi-person-gear"></i><span class="menu-text">View Admins</span></a></li>
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

        <div class="main-content">
            <br>
            <div class="card-container">
                <div class="card">
                    <h3>Total Students: <br><br>
                        <?= $total_students; ?>
                    </h3>
                </div>
            </div>

            <?php if ($msg != "") echo "<p style='color:green;'>$msg</p>"; ?>
            <br>
            <!-- Students table -->
            <button id="openModalBtn" style="margin-left: 5px; padding: 8px 5px; background-color: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer;">Add Student</button>
            <h3>All Registered Students</h3>
            <table>
                <tr>
                    <th>User ID</th>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                <?php while ($student = $student_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $student['user_id'] ?></td>
                        <td><?= $student['student_id']; ?></td>
                        <td><?= $student['username']; ?></td>
                        <td><?= $student['user_email']; ?></td>
                        <td><?= $student['created_at']; ?></td>
                        <td>
                            <button
                                style="background-color: green;"
                                class="button edit-student-btn"
                                data-userid="<?= $student['user_id']; ?>"
                                data-studentid="<?= $student['student_id']; ?>"
                                data-username="<?= $student['username']; ?>"
                                data-email="<?= $student['user_email']; ?>">Edit</button> |
                            <button style="background-color: red;" class="button">
                                <a href="delete_student.php?id=<?= $student['user_id']; ?>" onclick="return confirm('Delete this student?')">Delete</a>
                            </button>
                        </td>
                    </tr>
                <?php } ?>
            </table>

            <!-- Edit Student Modal -->
            <div id="editStudentModal" class="modal">
                <div class="modal-content">
                    <span id="closeEditStudentModalBtn" class="close">&times;</span>
                    <h4>Edit Student</h4>
                    <form id="editStudentForm" method="POST" action="">
                        <input type="hidden" id="edit_user_id" name="edit_user_id">
                        <input type="hidden" id="edit_student_id" name="edit_student_id">
                        <label>Name:</label>
                        <input type="text" id="edit_username" name="edit_username" required><br>
                        <label>Email:</label>
                        <input type="email" id="edit_user_email" name="edit_user_email" required>
                        <div class="buttons">
                            <button type="submit" name="save_student_btn" class="">Save Changes</button>
                            <button type="button" id="cancelEditStudentBtn" class="" style="background:#ccc;color:#333;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            <!--Add Student Modal -->
            <div id="myModal" class="modal">
                <div class="modal-content">
                    <span id="closeModalBtn" class="close">&times;</span>
                    <h4>Add New Student</h4>
                    <form id="multiStepStudentForm" action="" method="POST">
                        <!-- Step 1 -->
                        <div class="form-step" id="step1">
                            <div class="form-group">
                                <input type="text" id="first_name" name="first_name" placeholder="Student First Name..." required>
                                <input type="text" id="middle_name" name="middle_name" placeholder="Student Middle Name" required>
                                <input type="text" id="last_name" name="last_name" placeholder="Student Last Name" required>
                            </div>
                            <div class="form-group">
                                <input type="email" id="user_email" name="user_email" placeholder="Student E-mail...">
                                <input type="number" name="phone_number" placeholder="Phone Number...">
                            </div>
                            <div class="gender-row">
                                <label>Gender:</label>
                                <div class="gender-options-vertical">
                                    <label class="gender-option">
                                        <input type="radio" name="gender" value="Male" required> Male
                                    </label>
                                    <label class="gender-option">
                                        <input type="radio" name="gender" value="Female" required> Female
                                    </label>
                                </div>
                            </div>
                            <div class="buttons" style="margin-top:20px;">
                                <button type="button" class="" style="background-color: #ccc; cursor: not-allowed;" disabled>Back</button>
                                <button type="button" class="btn" id="nextStepBtn">Next</button>
                            </div>
                        </div>
                        <!-- Step 2 -->
                        <div class="form-step" id="step2" style="display:none;">
                            <label for="">E-mail:</label>
                            <input type="email" id="email" name="user_email" required><br>
                            <div class="buttons">
                                <button type="button" class="btn" id="prevStepBtn" style="background:#ccc;color:#333;">Back</button>
                                <button type="submit" class="btn" name="add_student_btn">Add Student</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
        }
    </script>

    <!-- Add student modal script -->
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
        // Multi-step logic
        const step1 = document.getElementById('step1');
        const step2 = document.getElementById('step2');
        document.getElementById('nextStepBtn').onclick = function() {
            // Optionally validate step 1 fields here
            step1.style.display = 'none';
            step2.style.display = 'block';
        };
        document.getElementById('prevStepBtn').onclick = function() {
            step2.style.display = 'none';
            step1.style.display = 'block';
        };
    </script>

    <!-- Edit Student Modal Script-->
    <script>
        // Open Edit Modal and fill data
        document.querySelectorAll('.edit-student-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('edit_user_id').value = this.getAttribute('data-userid');
                document.getElementById('edit_student_id').value = this.getAttribute('data-studentid');
                document.getElementById('edit_username').value = this.getAttribute('data-username');
                document.getElementById('edit_user_email').value = this.getAttribute('data-email');
                document.getElementById('editStudentModal').style.display = 'block';
            });
        });

        // Close modal on X or Cancel
        document.getElementById('closeEditStudentModalBtn').onclick = function() {
            document.getElementById('editStudentModal').style.display = 'none';
        };
        document.getElementById('cancelEditStudentBtn').onclick = function() {
            document.getElementById('editStudentModal').style.display = 'none';
        };
        // Close modal if user clicks outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('editStudentModal')) {
                document.getElementById('editStudentModal').style.display = 'none';
            }
        };
    </script>

</body>

</html>