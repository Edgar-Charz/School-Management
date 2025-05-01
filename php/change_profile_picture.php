<?php
session_start();
include '../includes/db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Student') {
    header("Location: ../php/login.php");
    exit();
}

$user_id = $_SESSION['id'];
$msg = "";

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['profile_picture'])) {
    $file = $_FILES['profile_picture'];
    $upload_dir = "../uploads/profile_pictures/";
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];

    // Check for errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $msg = "Error uploading file.";
    } elseif (!in_array($file['type'], $allowed_types)) {
        $msg = "Only JPG and PNG files are allowed.";
    } else {
        // Generate a unique file name
        $file_name = $user_id . "_" . time() . "_" . basename($file['name']);
        $target_file = $upload_dir . $file_name;

        // Move the uploaded file
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            // Update the database with the new profile picture path
            $stmt = $conn->prepare("UPDATE users SET user_profile_pic = ? WHERE user_id = ?");
            $stmt->bind_param("si", $file_name, $user_id);
            if ($stmt->execute()) {
                $msg = "Profile picture updated successfully.";
            } else {
                $msg = "Failed to update profile picture in the database.";
            }
        } else {
            $msg = "Failed to move uploaded file.";
        }
    }
}

// Fetch the current profile picture
$stmt = $conn->prepare("SELECT user_profile_pic FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$current_picture = $result->fetch_assoc()['user_profile_pic'] ?? "../uploads/profile_pictures/default.png";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Change Profile Picture</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body>
    <div class="main-content">
        <h1>Change Profile Picture</h1>
        <div class="profile-picture-container">
            <img src="../uploads/profile_pictures/<?= htmlspecialchars($current_picture); ?>" alt="Profile Picture" style="width: 150px; height: 150px; border-radius: 50%;">
        </div>
        <form method="POST" enctype="multipart/form-data">
            <label for="profile_picture">Upload New Profile Picture:</label><br>
            <input type="file" name="profile_picture" id="profile_picture" required><br><br>
            <button type="submit">Update Profile Picture</button>
        </form>
        <p><?= htmlspecialchars($msg); ?></p>
    </div>
</body>

</html>