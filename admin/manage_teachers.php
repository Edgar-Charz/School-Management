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

// Fetch data for teachers
$teacher_stmt = $conn->prepare("
                SELECT teachers.teacher_id, users.user_id, users.username, users.user_email, users.created_at 
                FROM teachers, users
                WHERE users.user_id = teachers.user_id");
$teacher_stmt->execute();
$teacher_result = $teacher_stmt->get_result();

// Count total teachers
$teacher_count_query = "SELECT COUNT(*) AS total_teachers FROM teachers";
$teacher_count_query_result = $conn->query($teacher_count_query);
$total_teachers = $teacher_count_query_result->fetch_assoc()['total_teachers'];

// Fetch data for class subjects teacher
$student_stmt = $conn->prepare("
                SELECT teachers.teacher_id, users.*, classes.*, subjects.* 
                FROM class_subject_teachers, classes, subjects, teachers, users
                WHERE class_subject_teachers.class_id = classes.class_id 
                  AND class_subject_teachers.subject_id = subjects.subject_id 
                  AND class_subject_teachers.teacher_id = teachers.teacher_id 
                  AND users.user_id = teachers.user_id");
$student_stmt->execute();
$student_result = $student_stmt->get_result();

// Count total students
$student_count_query = "SELECT COUNT(*) AS total_students FROM students";
$student_count_query_result = $conn->query($student_count_query);
$total_students = $student_count_query_result->fetch_assoc()['total_students'];

// Fetch classes
$classes = mysqli_query($conn, "SELECT * FROM classes");
// Fetch subjects
$subjects = mysqli_query($conn, "SELECT * FROM subjects");
// Fetch teachers
$teachers = mysqli_query($conn, "SELECT teachers.*, users.username 
                                       FROM teachers, users
                                       WHERE teachers.user_id = users.user_id");

//Assign Teachers to subjects in classes
if (isset($_POST['assignBTN'])) {
    $class_id = trim($_POST['class_id']);
    $subject_id = trim($_POST['subject_id']);
    $teacher_id = trim($_POST['teacher_id']);

    // Prevent duplicate subject assignment
    $check_subject = $conn->prepare("SELECT * FROM class_subject_teachers 
                                              WHERE class_id = ? 
                                                AND subject_id = ?");
    $check_subject->bind_param("ii", $class_id, $subject_id);
    $check_subject->execute();
    $check_subject_result = $check_subject->get_result();

    if ($check_subject_result->num_rows > 0) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'warning',
                // title: 'Oops...',
                text: 'This subject is already assigned to this class.'
            });
    });
          </script>";
    } else {

        $insert_subject_teacher = $conn->prepare("INSERT INTO class_subject_teachers (class_id, subject_id, teacher_id) 
                                                        VALUES (?, ?, ?)");
        $insert_subject_teacher->bind_param("iii", $class_id, $subject_id, $teacher_id);

        if ($insert_subject_teacher->execute()) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    // title: 'Oops...',
                    text: 'Assignment successfully!'
                });
        });
              </script>";
        } else {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    // title: 'Oops...',
                    text: 'Error occured'
                }).then(() => {
                    // Redirect after SweetAlert is closed
                    window.location.href = 'manage_teachers.php';
                });
            });
              </script>";
        }
        // header("Location: manage_teachers.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dream School | Manage Data</title>
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
        select {
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

        .nav-bar {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .nav-bar button {
            margin: 0 10px;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .nav-bar button.active {
            background-color: #2c3e50;
        }

        .table-container {
            display: none;
        }

        .table-container.active {
            display: block;
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
                <li><a href="manage_teachers.php" class="active"><i class="bi bi-person-badge"></i><span class="menu-text">View Teachers</span></a></li>
                <li><a href="manage_students.php"><i class="bi bi-people"></i><span class="menu-text">View Students</span></a></li>
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
        <!-- Main content -->
        <div class="main-content">
            <br>
            <div class="card-container">
                <div class="card">
                    <h3>Total Teachers: <br><br>
                        <?= $total_teachers; ?>
                    </h3>
                </div>
            </div>

            <?php if ($msg != "") echo "<p style='color:green;'>$msg</p>"; ?>
            <br>

            <!-- Navigation Bar -->
            <div class="nav-bar">
                <button class="nav-link active" data-target="teachers-table">Teachers</button>
                <button class="nav-link" data-target="students-table">Class Subject Teachers</button>
            </div>

            <!-- Teachers Table -->
            <div id="teachers-table" class="table-container active">
                <div style="">
                    <!-- Add Teacher Button -->
                    <button id="openTeacherModalBtn" style="padding: 8px 5px; background-color: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer;">Add Teacher</button>

                    <!-- Search Bar -->
                    <input type="text" id="searchBar" placeholder="Search Teachers..." style="padding: 8px; width: 300px; border: 1px solid #ccc; border-radius: 5px;">
                </div>

                <table>
                    <tr>
                        <th>User ID</th>
                        <th>Teacher ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                    <?php while ($row = $teacher_result->fetch_assoc()) { ?>
                        <tr class="teacher-row">
                            <td><?= $row['user_id'] ?></td>
                            <td><?= $row['teacher_id']; ?></td>
                            <td><?= $row['username']; ?></td>
                            <td><?= $row['user_email']; ?></td>
                            <td><?= $row['created_at']; ?></td>
                            <td>
                                <button style="background-color: green;" class="button"><a href="edit_teacher.php?id=<?= $row['teacher_id']; ?>">Edit</a></button> |
                                <button style="background-color: red;" class="button"><a href="delete_teacher.php?id=<?= $row['teacher_id']; ?>" onclick="return confirm('Delete this teacher?')">Delete</a></button> |
                                <button style="background-color: blue;" class="button"><a href="promote_teacher.php?id=<?= $row['teacher_id']; ?>">Promote</a></button>
                            </td>
                        </tr>
                    <?php } ?>
                </table>

                <!--Teacher's Modal -->
                <div id="teacherModal" class="modal">
                    <div class="modal-content">
                        <span id="closeTeacherModalBtn" class="close">&times;</span>
                        <h4>Add New Teacher</h4>
                        <form action="" method="POST">
                            <input type="text" id="first_name" name="first_name" placeholder="First Name" required>
                            <input type="text" id="last_name" name="last_name" placeholder="Last Name" required>
                            <input type="text" id="class_name" name="class_name" placeholder="Class Name" required>
                            <button type="submit" style="margin-top: 10px;" class="btn">Add Teacher</button>
                        </form>
                    </div>
                </div>

            </div>

            <!-- Class Subjects Teachers Table -->
            <div id="students-table" class="table-container">
                <button id="openStudentModalBtn" style="margin-left: 5px; padding: 8px 5px; background-color: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer;">Assign</button>
                <!-- Search Bar -->
                <input type="text" id="searchBar" placeholder="Search Teachers..." style="padding: 8px; width: 300px; border: 1px solid #ccc; border-radius: 5px;">

                <table>
                    <tr>
                        <th>User ID</th>
                        <th>Teacher ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Class</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                    <?php while ($row = $student_result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row['user_id'] ?></td>
                            <td><?= $row['teacher_id']; ?></td>
                            <td><?= $row['username']; ?></td>
                            <td><?= $row['user_email']; ?></td>
                            <td><?= $row['class_name']; ?></td>
                            <td><?= $row['subject_name']; ?></td>
                            <td><?= $row['created_at']; ?></td>
                            <td>
                                <button style="background-color: green;" class="button"><a href="edit_student.php?id=<?= $row['teacher_id']; ?>">Edit</a></button> |
                                <button style="background-color: red;" class="button"><a href="delete_student.php?id=<?= $row['teacher_id']; ?>" onclick="return confirm('Delete this student?')">Delete</a></button>
                            </td>
                        </tr>
                    <?php } ?>
                </table>

                <!-- Teacher's Modal -->
                <div id="studentModal" class="modal">
                    <div class="modal-content">
                        <span id="closeStudentModalBtn" class="close">&times;</span>
                        <h4>Assign Teacher To Class</h4>
                        <form action="" method="POST">
                            <select name="class_id" required>
                                <option value="">--Select Class--</option>
                                <?php while ($row = mysqli_fetch_assoc($classes)) { ?>
                                    <option value="<?= $row['class_id'] ?>"><?= $row['class_name'] ?></option>
                                <?php } ?>
                            </select>
                            <select name="subject_id" id="subjectDropdown" required>
                                <option value="">--Select Subject--</option>
                                <?php while ($row = mysqli_fetch_assoc($subjects)) { ?>
                                    <option value="<?= $row['subject_id'] ?>"><?= $row['subject_name'] ?></option>
                                <?php } ?>
                            </select>
                            <!-- <select name="teacher_id" required>
                                <option value="">--Select Teacher--</option>
                                <?php while ($row = mysqli_fetch_assoc($teachers)) { ?>
                                    <option value="<?= $row['teacher_id'] ?>"><?= $row['username'] ?></option>
                                <?php } ?>
                            </select> -->
                            <select name="teacher_id" id="teacherDropdown" required>
                                <option value="">--Select Teacher--</option>
                                <!-- Teachers will be dynamically populated here -->
                            </select>
                            <button type="submit" style="margin-top: 10px;" class="btn" name="assignBTN">Assign</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
        }
    </script>
    <script>
        // Get elements
        var modal = document.getElementById("teacherModal");
        var openBtn = document.getElementById("openTeacherModalBtn");
        var closeBtn = document.getElementById("closeTeacherModalBtn");

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
    <script>
        // Get elements for the student modal
        var studentModal = document.getElementById("studentModal");
        var openStudentBtn = document.getElementById("openStudentModalBtn");
        var closeStudentBtn = document.getElementById("closeStudentModalBtn");

        // Open student modal
        openStudentBtn.onclick = function() {
            studentModal.style.display = "block";
        }

        // Close student modal
        closeStudentBtn.onclick = function() {
            studentModal.style.display = "none";
        }

        // Close student modal if user clicks outside the modal
        window.onclick = function(event) {
            if (event.target == studentModal) {
                studentModal.style.display = "none";
            }
        }
    </script>
    <script>
        // Handle navigation bar clicks
        const navLinks = document.querySelectorAll('.nav-link');
        const tableContainers = document.querySelectorAll('.table-container');

        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                // Remove active class from all links and tables
                navLinks.forEach(link => link.classList.remove('active'));
                tableContainers.forEach(table => table.classList.remove('active'));

                // Add active class to the clicked link and corresponding table
                link.classList.add('active');
                document.getElementById(link.getAttribute('data-target')).classList.add('active');
            });
        });
    </script>
    <script>
        // Search functionality
        document.getElementById('searchBar').addEventListener('input', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('.teacher-row');

            rows.forEach(row => {
                const name = row.cells[2].textContent.toLowerCase();
                const email = row.cells[3].textContent.toLowerCase();

                if (name.includes(searchValue) || email.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
    <script>
        document.getElementById('subjectDropdown').addEventListener('change', function() {
            const subjectId = this.value;
            const teacherDropdown = document.getElementById('teacherDropdown');

            // Clear the teacher dropdown
            teacherDropdown.innerHTML = '<option value="">--Select Teacher--</option>';

            if (subjectId) {
                // Make an AJAX request to fetch teachers
                fetch('get_teachers.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `subject_id=${subjectId}`,
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Populate the teacher dropdown with the fetched data
                        data.forEach(teacher => {
                            const option = document.createElement('option');
                            option.value = teacher.teacher_id;
                            option.textContent = teacher.username;
                            teacherDropdown.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error fetching teachers:', error));
            }
        });
    </script>
</body>

</html>