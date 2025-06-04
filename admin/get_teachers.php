<?php
include '../includes/db_connection.php';

if (isset($_POST['subject_id'])) {
    $subject_id = $_POST['subject_id'];

    // Fetch teachers teaching the selected subject
    $query = $conn->prepare("
        SELECT teachers.teacher_id, users.username 
        FROM teachers
        INNER JOIN users ON teachers.user_id = users.user_id
        WHERE teachers.subject_id = ?
    ");
    $query->bind_param("i", $subject_id);
    $query->execute();
    $result = $query->get_result();

    $teachers = [];
    while ($row = $result->fetch_assoc()) {
        $teachers[] = $row;
    }

    // Return teachers as JSON
    echo json_encode($teachers);
}
