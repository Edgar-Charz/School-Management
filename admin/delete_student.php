<?php
session_start();
include '../includes/db_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    header("Location: ../php/login.php");
    exit();
}

$id = $_GET['id'];
$conn->query("DELETE FROM users WHERE user_id = $id");

header("Location: manage_students.php");
