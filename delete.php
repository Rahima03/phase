<?php
include 'db_config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: student_lists.php");
        exit();
    } else {
        echo "Error deleting record: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
} else {
    header("Location: student_lists.php");
    exit();
}
?>