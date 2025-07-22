<?php
session_start();
include("../includes/db_connection.php");

if(!isset($_SESSION["role"]) || $_SESSION['role'] != 'Admin') {
    header("Location: '../php/login.php'");
    exit();
}

$id = $_GET['id'];

// Prevent current admin from deleting themselves
if ($_SESSION['user_id'] != $id) {
    $conn->query("DELETE FROM users 
                                WHERE user_id = $id 
                                AND user_role = 'Admin'");
}

header("Location: manage_admins.php");