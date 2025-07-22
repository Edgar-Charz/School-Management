<?php
session_start();
include '../includes/db_connection.php';

$msg = "";

// User registration
if (isset($_POST["registerBTN"])) {
    $firstname = trim($_POST["first_name"]);
    $middlename = trim($_POST["middle_name"]);
    $lastname = trim($_POST["last_name"]);
    $username = $firstname . " " . $lastname;
    $phone = trim($_POST["phone"]);
    $email = trim($_POST['email']);
    $user_password = trim($_POST['password']);
    $hashed_password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $class = $_POST['class'] ?? null;
    $subject = $_POST['subject'] ?? null;
    // $default_picture = '../uploads/profile_pictures/default.png';

    // Check if email already exists
    $check = $conn->prepare("SELECT * FROM users WHERE user_email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'info',
                    // title: 'Oops...',
                    text: 'E-mail already registered!'
                });
        });
              </script>";
    } else {

        // Insert user into the users table
        $stmt = $conn->prepare("INSERT INTO users (first_name, middle_name, last_name, username, phone_no, user_email, user_password, hashed_password, user_role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $firstname, $middlename,  $lastname, $username, $phone, $email, $user_password, $hashed_password, $role);

        if ($stmt->execute()) {
            $userId = $stmt->insert_id;

            // Add to student or teacher table
            if ($role == 'teacher') {
                $insert_teacher = $conn->prepare("INSERT INTO teachers (user_id, subject_id) VALUES (?, ?)");
                $insert_teacher->bind_param("ii", $userId, $subject);

                if (!$insert_teacher->execute()) {
                    echo "<script>
                document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    // title: 'Oops...',
                    text: 'Failed to register teacher'
                });
        });
              </script>";
                }
            } elseif ($role == 'student') {
                $insert_student = $conn->prepare("INSERT INTO students (user_id, class_id) VALUES (?, ?)");
                $insert_student->bind_param("ii", $userId, $class);

                if (!$insert_student->execute()) {
                    echo "<script>
                document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    // title: 'Oops...',
                    text: 'Failed to register student'
                });
        });
              </script>";
                }
            }

            if (empty($msg)) {
                echo "<script>
                document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    // title: 'Oops...',
                    text: 'User registered successfully!'
                });
        });
              </script>";
            }
        } else {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    // title: 'Oops...',
                    text: 'Failed to register user'
                });
        });
              </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Dream School | Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../assets/css/form-styles.css">
    <style>
        .swal2-popup {
            font-size: 13px !important;
            width: 300px !important;
            background-color: rgba(255, 255, 255, 0.9) !important;
        }

        .step {
            display: none;
        }

        .step.active {
            display: block;
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
            background-color: #334f62ff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }

        .buttons button[disabled] {
            background-color: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Register</h2>
        <form method="POST" action="">
            <!-- Step 1 -->
            <div class="step active" id="step-1">
                <label>First Name</label>
                <input type="text" name="first_name" oninput="convertToUpperCase(this)" required><br><br>
                <label>Middle Name</label>
                <input type="text" name="middle_name" oninput="convertToUpperCase(this)" required><br><br>
                <label>Last Name</label>
                <input type="text" name="last_name" oninput="convertToUpperCase(this)" required><br><br>
                <label>Gender</label>
                <input type="radio" name="gender" value="Male" required>Male
                <input type="radio" name="gender" value="Female" required>Female<br>
            </div>

            <!-- Step 2 -->
            <div class="step" id="step-2">
                <label>Phone No.</label>
                <input type="text" name="phone" pattern="[0-9] {10, 15}" required><br><br>
                <label>Email</label>
                <input type="email" name="email" required><br><br>
                <label>Password</label>
                <input type="password" name="password" required><br><br>
                <label>Role</label>
                <select name="role" id="role_select" required>
                    <option value="">-- Select Role --</option>
                    <option value="admin">Admin</option>
                    <option value="teacher">Teacher</option>
                    <option value="student">Student</option>
                </select><br><br>

                <div id="class_field" style="display: none;">
                    <label>Class</label>
                    <select name="class">
                        <option value="">-- Select Class --</option>
                        <?php
                        $classes_query = $conn->query("SELECT class_id, class_name FROM classes");
                        if ($classes_query->num_rows > 0) {
                            while ($row = $classes_query->fetch_assoc()) {
                                echo "<option value='" . $row['class_id'] . "'>" . $row['class_name'] . "</option>";
                            }
                        }
                        ?>
                    </select><br>
                </div>
                <div id="subject_field" style="display: none;">
                    <label>Subject</label>
                    <select name="subject">
                        <option value="">-- Select Subject --</option>
                        <?php
                        $subjects_query = $conn->query("SELECT subject_id, subject_name FROM subjects");
                        if ($subjects_query->num_rows > 0) {
                            while ($row = $subjects_query->fetch_assoc()) {
                                echo "<option value='" . $row['subject_id'] . "'>" . $row['subject_name'] . "</option>";
                            }
                        }
                        ?>
                    </select><br>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="buttons">
                <button type="button" id="prevBtn" onclick="changeStep(-1)" disabled>Previous</button>
                <button type="button" id="nextBtn" onclick="changeStep(1)">Next</button>
                <button type="submit" id="submitBtn" name="registerBTN" style="display: none;">Submit</button>
            </div>
        </form>
        <?php if ($msg != "") {
            echo "<p style='color:green;'>$msg</p>";
        } ?>
        <p style="margin-top: 10px; text-align: center;">
            Have an account? <a href="login.php">Sign in</a>
        </p>
    </div>

    <script>
        let currentStep = 1;
        const totalSteps = 2;

        function changeStep(step) {
            // Hide current step
            document.getElementById(`step-${currentStep}`).classList.remove('active');

            // Update current step
            currentStep += step;

            // Show new step
            document.getElementById(`step-${currentStep}`).classList.add('active');

            // Update button states
            document.getElementById('prevBtn').disabled = currentStep === 1;
            document.getElementById('nextBtn').style.display = currentStep === totalSteps ? 'none' : 'inline-block';
            document.getElementById('submitBtn').style.display = currentStep === totalSteps ? 'inline-block' : 'none';
        }

        // Show class field if role is student
        document.getElementById('role_select').addEventListener('change', function() {
            const classField = document.getElementById('class_field');
            classField.style.display = this.value === 'student' ? 'block' : 'none';
        });

        // Show class field if role is teacher
        document.getElementById('role_select').addEventListener('change', function() {
            const subjectField = document.getElementById('subject_field');
            subjectField.style.display = this.value === 'teacher' ? 'block' : 'none';
        });
    </script>
    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>

    <script>
        function convertToUpperCase(input) {
            input.value = input.value.toUpperCase();
        }
    </script>

</body>

</html>