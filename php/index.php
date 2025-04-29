<?php
session_start();
include '../includes/db_connection.php';

if (!isset($_SESSION["role"])) {
    header("Location: login.php");
    exit();
}