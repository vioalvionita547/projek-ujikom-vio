<?php
require 'koneksi.php';

if (isset($_GET['id'])) {
    $task_id = intval($_GET['id']);
    $query = "UPDATE task SET status='completed' WHERE id=$task_id";
    
    if (mysqli_query($conn, $query)) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>