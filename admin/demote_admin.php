<?php
session_start();
include '../includes/db_connection.php';

// Only allow admins to perform this action
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $admin_id = intval($_GET['id']);

    // Prevent demoting yourself
    if ($admin_id == $_SESSION['id']) {
        $_SESSION['swal'] = [
            'icon' => 'warning',
            'text' => 'You cannot demote yourself!'
        ];
        header("Location: manage_admins.php");
        exit();
    }

    // Demote admin to 'teacher' 
    $stmt = $conn->prepare("UPDATE users SET user_role = 'teacher' WHERE user_id = ?");
    $stmt->bind_param("i", $admin_id);

    if ($stmt->execute()) {
        $_SESSION['swal'] = [
            'icon' => 'success',
            'text' => 'Admin demoted successfully.'
        ];
    } else {
        $_SESSION['swal'] = [
            'icon' => 'error',
            'text' => 'Failed to demote admin.'
        ];
    }
} else {
    $_SESSION['swal'] = [
        'icon' => 'error',
        'text' => 'Invalid request.'
    ];
}

header("Location: manage_admins.php");
exit();
?>